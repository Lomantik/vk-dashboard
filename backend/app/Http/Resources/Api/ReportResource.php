<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'group_info' => data_get($this->resource, 'group_info'),
            'posts' => data_get($this->resource, 'posts'),
            'top_posts' => data_get($this->resource, 'top_posts'),
            'analytics' => data_get($this->resource, 'analytics'),
            'kpi' => data_get($this->resource, 'kpi'),
            'content_types' => data_get($this->resource, 'content_types'),
            'report_timestamp' => data_get($this->resource, 'report_timestamp'),
        ];
    }
}
