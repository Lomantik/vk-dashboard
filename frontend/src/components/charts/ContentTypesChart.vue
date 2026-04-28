<script setup lang="ts">
import VueApexCharts from 'vue3-apexcharts'
import type { ApexOptions } from 'apexcharts'
import { computed } from 'vue'

const props = defineProps<{
  contentTypes: Record<string, number>
}>()

const chartOptions = computed(
  (): ApexOptions => ({
    chart: {
      type: 'donut',
      background: '#232323',
    },
    theme: { mode: 'dark' },
    labels: Object.keys(props.contentTypes).map((v) => {
      switch (v) {
        case 'photo':
          return 'Фото'
        case 'video':
          return 'Видео'
        case 'link':
          return 'Ссылка'
        default:
          return 'Текст'
      }
    }),
    colors: ['#2b7fff', '#6a7282', '#fb2c36', '#f0b100'],
    stroke: {
      show: true,
      width: 2,
      colors: ['#1a1a1a'],
    },
    plotOptions: {
      pie: {
        donut: {
          size: '50%',
          labels: {
            show: true,
            total: {
              show: true,
              label: 'Всего',
              color: '#858585',
              formatter: (w) => {
                return w.globals.seriesTotals.reduce((a: number, b: number) => a + b, 0)
              },
            },
          },
        },
      },
    },
    dataLabels: { enabled: false },
    legend: {
      position: 'bottom',
      markers: {
        offsetX: -5,
        shape: 'rect',
      },
      fontSize: '14px',
      fontFamily: 'inherit',
      labels: { colors: '#858585' },
    },
  }),
)

const chartSeries = computed(() => Object.values(props.contentTypes))
</script>

<template>
  <div class="rounded-xl border border-[#333333] bg-[#232323] p-5">
    <h3 class="mb-4 text-lg font-medium text-gray-100">Распределение типов контента</h3>
    <div class="flex flex-1 items-center justify-center">
      <VueApexCharts width="100%" type="donut" :options="chartOptions" :series="chartSeries" />
    </div>
  </div>
</template>

<style scoped></style>
