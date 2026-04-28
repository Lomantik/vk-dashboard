<script setup lang="ts">
import InputData from '@/components/InputData.vue'
import { useInputStore } from '@/stores/input.store.ts'
import { computed, ref, watchEffect } from 'vue'
import { analyze, report } from '@/api/analisis.api.ts'
import PostsTable from '@/components/PostsTable.vue'
import GroupInfo from '@/components/GroupInfo.vue'
import KpiCards from '@/components/KpiCards.vue'
import EngagementChart from '@/components/charts/EngagementChart.vue'
import type { Data } from '@/types/data.ts'
import Top10Chart from '@/components/charts/Top10Chart.vue'
import ContentTypesChart from '@/components/charts/ContentTypesChart.vue'
import ReportDate from '@/components/ReportDate.vue'

const inputStore = useInputStore()
const dataExists = computed(() => {
  return inputStore.groupId !== ''
})
const data = ref<Data>()
watchEffect(async () => {
  if (inputStore.groupId !== '') {
    try {
      data.value = await report(inputStore.groupId, inputStore.from ?? 0, inputStore.to ?? 0)
      console.log(data.value)
      // eslint-disable-next-line @typescript-eslint/no-unused-vars
    } catch (e) {
      await analyze(inputStore.groupId, inputStore.from ?? 0, inputStore.to ?? 0)
      data.value = await report(inputStore.groupId, inputStore.from ?? 0, inputStore.to ?? 0)
    }
  }
})
</script>

<template>
  <main class="flex flex-1 p-5">
    <div v-if="!dataExists" class="flex flex-1 items-center">
      <InputData />
    </div>
    <div v-else-if="data" class="container mx-auto">
      <GroupInfo class="mb-7" :group-info="data.group_info" :data="data" />
      <KpiCards :data="data" class="mb-7" />
      <EngagementChart :analytics="data.analytics" class="mb-7" />
      <div class="mb-7 grid grid-cols-1 gap-4 md:grid-cols-2">
        <Top10Chart :posts="data.top_posts" />
        <ContentTypesChart :content-types="data.content_types" />
      </div>
      <PostsTable
        class="mb-7"
        v-if="data"
        title="Топ-10 постов"
        :posts="data.top_posts"
        :limit="false"
        :top="true"
      />
      <PostsTable v-if="data" title="Все посты" :posts="data.posts" :limit="true" :top="false" />
      <ReportDate :date-string="data.report_timestamp" />
    </div>
  </main>
</template>
