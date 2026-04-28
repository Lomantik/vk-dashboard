<script setup lang="ts">
import VueApexCharts from 'vue3-apexcharts'
import type { ApexOptions } from 'apexcharts'
import type { Post } from '@/types/post.ts'
import { computed } from 'vue'

const props = defineProps<{
  posts: Post[]
}>()

const chartOptions = computed(
  (): ApexOptions => ({
    chart: {
      type: 'bar',
      toolbar: { show: false },
      background: 'transparent',
      zoom: { enabled: false },
    },
    theme: { mode: 'dark' },
    plotOptions: {
      bar: {
        borderRadius: 4,
        columnWidth: '90%',
        distributed: false,
      },
    },
    colors: ['#3b82f6'],
    dataLabels: {
      enabled: false,
    },
    xaxis: {
      categories: Object.keys(props.posts).map((num) => `#${parseInt(num) + 1}`),
      axisBorder: { show: false },
      axisTicks: { show: false },
    },
    grid: {
      borderColor: '#333',
      strokeDashArray: 4,
      xaxis: { lines: { show: false } },
    },
  }),
)

const chartSeries = computed(() => [
  {
    name: 'Количество постов',
    data: props.posts.map((s) => s.engagement),
  },
])
</script>

<template>
  <div class="rounded-xl border border-[#333333] bg-[#232323] p-5">
    <h3 class="mb-4 text-lg font-medium text-gray-100">Топ-10 постов по engagement</h3>
    <VueApexCharts width="100%" height="300" :options="chartOptions" :series="chartSeries" />
  </div>
</template>

<style scoped></style>
