<?php

namespace App\Services;

use App\Exceptions\VkApiException;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VkService
{
    protected string $apiUrl = 'https://api.vk.com/method/';

    protected string $token;

    protected string $version;

    public function __construct()
    {
        $this->token = config('services.vk.token');
        $this->version = config('services.vk.version');
    }

    /**
     * @param  array<string|mixed>  $post
     */
    private function determinePostType(array $post): string
    {
        $attachments = data_get($post, 'attachments', []);

        if (empty($attachments)) {
            return 'text';
        }

        $types = array_column($attachments, 'type');

        return match (true) {
            in_array('video', $types) => 'video',
            in_array('photo', $types) => 'photo',
            in_array('link', $types) => 'link',
            default => 'other',
        };
    }

    /**
     * @return array <string|mixed>
     */
    private function getGroupInfo(int|string $groupId): array
    {
        $entityId = is_numeric($groupId) ? (($groupId < 0) ? -$groupId : $groupId) : $groupId;

        return Cache::remember("group:$entityId", 604800, function () use ($entityId) {
            Log::info('getGroupInfo VK Request started');
            $response = Http::retry(3, 100)->get($this->apiUrl.'groups.getById', [
                'group_id' => $entityId,
                'fields' => 'members_count',
                'access_token' => config('services.vk.token'),
                'v' => config('services.vk.version'),
            ]);
            Log::info('getGroupInfo VK Request ended');

            if ($response->failed()) {
                throw new VkApiException('VK service unavailable', $response->status());
            }

            $data = $response->json();

            if (isset($data['error'])) {
                throw new VkApiException(data_get($data, 'error.error_msg', 500), data_get($data, 'error.error_code', 500));
            }

            return $data;
        });
    }

    /**
     * @return array <string|mixed>
     */
    private function getAnalytics(mixed $posts): array
    {
        /** @var array<int, array{engagement: int, type: string}> $posts */
        $topPosts = collect($posts)->sortByDesc('engagement')->take(10)->all();

        $analytics = collect($posts)->groupBy(function ($post) {
            return Carbon::createFromTimestamp(data_get($post, 'date'), 'Europe/Moscow')->format('Y-m-d');
        })->mapWithKeys(function ($dayPosts, $date) {
            $timestamp = (int) Carbon::parse($date)->timezone('Europe/Moscow')->timestamp;

            return [
                $timestamp => [
                    'avg_engagement' => round($dayPosts->avg('engagement') ?? 0),
                    'sum_engagement' => $dayPosts->sum('engagement'),
                    'posts_count' => $dayPosts->count(),
                ],
            ];
        });

        $mostActiveDay = $analytics->sortByDesc('posts_count')->first();
        $mostActiveDayDate = $analytics->sortByDesc('posts_count')->keys()->first();
        $maxEngagementDay = $analytics->sortByDesc('sum_engagement')->first();
        $maxEngagementDayDate = $analytics->sortByDesc('sum_engagement')->keys()->first();

        $kpi = [
            'posts_count' => count($posts),
            'avg_engagement' => round(collect($posts)->avg('engagement') ?? 0),
            'most_active_day' => $mostActiveDayDate,
            'most_active_day_posts_count' => $mostActiveDay['posts_count'] ?? 0,
            'max_engagement_day' => $maxEngagementDayDate,
            'max_engagement_day_value' => $maxEngagementDay['sum_engagement'] ?? 0,
        ];

        $contentTypes = collect($posts)->groupBy(function ($post) {
            return $post['type'];
        })->map->count();

        return [
            'top_posts' => $topPosts,
            'analytics' => $analytics,
            'kpi' => $kpi,
            'content_types' => $contentTypes,
        ];
    }

    /**
     * @return array<string, mixed>
     *
     * @throws VkApiException|ConnectionException|Exception
     */
    public function storeWallPosts(int|string $groupId, int $count, int $from, int $to): array
    {
        $from = Carbon::parse($from)->timezone('Europe/Moscow')->startOfDay()->timestamp;
        $to = Carbon::parse($to)->timezone('Europe/Moscow')->endOfDay()->timestamp;

        $groupInfo = $this->getGroupInfo($groupId);

        $ownerId = -data_get($groupInfo, 'response.groups.0.id');

        $itemsFrom = $to;
        $resultItems = [];
        $offset = 0;
        Log::info('storeWallPosts VK Request started');
        while ($itemsFrom > $from) {
            $response = Http::retry(3, 100)->get($this->apiUrl.'wall.get', [
                'owner_id' => $ownerId,
                'count' => $count,
                'access_token' => config('services.vk.token'),
                'offset' => $offset,
                'v' => config('services.vk.version'),
            ]);

            if ($response->failed()) {
                throw new VkApiException('VK API Connection Error', $response->status());
            }

            $data = $response->json();

            if (isset($data['error'])) {
                throw new VkApiException(data_get($data, 'error.error_msg', 500), data_get($data, 'error.error_code', 500));
            }

            $items = data_get($data, 'response.items', []);

            if (empty($items)) {
                break;
            }

            $itemsFrom = data_get(last($items), 'date');
            $notPinnedItem = head($items);
            $i = 1;
            while ($i < count($items) && isset($notPinnedItem['is_pinned'])) {
                $notPinnedItem = $items[$i];
                $i++;
            }
            $itemsTo = data_get($notPinnedItem, 'date');

            if ($itemsFrom < $to || $itemsTo > $from) {
                array_push($resultItems, ...$items);
            }

            $offset += $count;
        }
        Log::info('storeWallPosts VK Request ended');

        $result = collect($resultItems)->filter(function ($item) use ($from, $to) {
            return data_get($item, 'type') == 'post' && data_get($item, 'marked_as_ads') == 0
                && ($from <= data_get($item, 'date') && data_get($item, 'date') <= $to);
        })->map(function ($item) {
            $comments = data_get($item, 'comments.count', 0);
            $likes = data_get($item, 'likes.count', 0);
            $reposts = data_get($item, 'reposts.count', 0);

            $type = $this->determinePostType($item);

            $text = data_get($item, 'text', '');
            if ($text === '') {
                if (isset($item['copy_history'])) {
                    $text = data_get($item['copy_history'], '0.text', '');
                    $type = 'link';
                }
            }

            return [
                'id' => data_get($item, 'id'),
                'is_pinned' => data_get($item, 'is_pinned', 0),
                'comments' => $comments,
                'type' => $type,
                'date' => data_get($item, 'date', 0),
                'likes' => $likes,
                'reposts' => $reposts,
                'text' => $text,
                'views' => data_get($item, 'views.count', 0),
                'engagement' => $comments + $likes + $reposts,
            ];
        })->all();

        if (empty($result)) {
            throw new VkApiException('Empty data', 400);
        }

        $isCached = Cache::put("report:$ownerId:$from:$to", $result, 1800);

        if ($isCached) {
            Cache::put("report_timestamp:$ownerId:$from:$to", now()->toIso8601String(), 1800);

            return ['message' => 'Analysis completed and cached'];
        } else {
            throw new Exception('Cache Error', 500);
        }
    }

    /**
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function getWallPosts(int|string $groupId, int $fromSt, int $toSt): array
    {
        $from = Carbon::parse($fromSt)->timezone('Europe/Moscow')->startOfDay()->timestamp;
        $to = Carbon::parse($toSt)->timezone('Europe/Moscow')->endOfDay()->timestamp;

        $groupInfo = $this->getGroupInfo($groupId);
        $ownerId = -data_get($groupInfo, 'response.groups.0.id');

        $posts = Cache::get("report:$ownerId:$from:$to");

        if ($posts === null) {
            throw new Exception('Report not found', 404);
        }

        $additionalAnalytics = $this->getAnalytics($posts);

        return [
            'group_info' => data_get($groupInfo, 'response.groups.0', []),
            'posts' => $posts,
            'report_timestamp' => Cache::get("report_timestamp:$ownerId:$from:$to"),
        ] + $additionalAnalytics;
    }
}
