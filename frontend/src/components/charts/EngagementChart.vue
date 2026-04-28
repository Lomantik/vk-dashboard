<script setup lang="ts">
import type { Analytics } from '@/types/analytics.ts'
import { computed } from 'vue'
import VueApexCharts from 'vue3-apexcharts'
import { formatTimestamp } from '@/utils/general.ts'
import type { ApexOptions } from 'apexcharts'

const props = defineProps<{
  analytics: Analytics
}>()

const chartOptions = computed(
  (): ApexOptions => ({
    chart: {
      type: 'line',
      toolbar: { show: false },
      background: 'transparent',
      zoom: {
        enabled: false,
      },
      selection: {
        enabled: false,
      },
    },
    theme: { mode: 'dark' },
    stroke: {
      curve: 'smooth',
      width: [3, 2],
      dashArray: [0, 5],
    },
    colors: ['#2673CD', '#9DC1A5'],
    xaxis: {
      categories: Object.keys(props.analytics).map((timestamp) =>
        formatTimestamp(parseInt(timestamp)),
      ),
      tooltip: { enabled: false },
      axisTicks: { show: false },
      labels: { style: { colors: '#6b7280' } },
    },
    yaxis: [{ title: { text: 'Engagement' } }, { opposite: true, title: { text: 'Посты' } }],
    grid: {
      borderColor: '#333',
      strokeDashArray: 4,
      xaxis: {
        lines: {
          show: true,
        },
      },
      yaxis: {
        lines: {
          show: true,
        },
      },
      padding: {
        left: 20,
        right: 20,
      },
    },
    legend: { position: 'bottom' },
  }),
)

const chartSeries = computed(() => [
  {
    name: 'Cp. engagement',
    type: 'line',
    data: Object.values(props.analytics).map((item) => item.avg_engagement),
  },
  {
    name: 'Кол-во постов',
    type: 'line',
    data: Object.values(props.analytics).map((item) => item.posts_count),
  },
])
</script>

<template>
  <div class="rounded-xl border border-[#333333] bg-[#232323] p-5">
    <h3 class="mb-4 text-lg text-gray-100">Динамика вовлечённости</h3>
    <VueApexCharts width="100%" height="350" :options="chartOptions" :series="chartSeries" />
  </div>
</template>

<style scoped></style>
