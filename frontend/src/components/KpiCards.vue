<script setup lang="ts">
import KpiCard from '@/components/KpiCard.vue'
import type { Data } from '@/types/data.ts'
import { formatTimestamp } from '@/utils/general.ts'

defineProps<{
  data: Data
}>()
</script>

<template>
  <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
    <KpiCard top-text="Всего постов" :main-text="data.kpi.posts_count.toString()" bottom-text="" />
    <KpiCard
      top-text="Ср. вовлеченность"
      :main-text="data.kpi.avg_engagement.toString()"
      bottom-text="лайки + комменты + репосты"
    />
    <KpiCard
      top-text="Самый активный день"
      :main-text="formatTimestamp(data.kpi.most_active_day)"
      :bottom-text="`${data.kpi.most_active_day_posts_count} постов`"
    />
    <KpiCard
      top-text="Макс. engagement"
      :main-text="data.kpi.max_engagement_day_value.toString()"
      :bottom-text="formatTimestamp(data.kpi.max_engagement_day)"
    />
  </div>
</template>

<style scoped></style>
