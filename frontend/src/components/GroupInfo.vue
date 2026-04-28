<script setup lang="ts">
import type { Group } from '@/types/group.ts'
import { useInputStore } from '@/stores/input.store.ts'
import { formatTimestamp } from '@/utils/general.ts'
import { Download } from 'lucide-vue-next'
import { computed } from 'vue'
import type { Data } from '@/types/data.ts'
import type { Post } from '@/types/post.ts'

const props = defineProps<{
  groupInfo: Group
  data: Data
}>()

const inputStore = useInputStore()

const from = computed(() => {
  return formatTimestamp(inputStore.from ?? 0)
})

const to = computed(() => {
  return formatTimestamp(inputStore.to ?? 0)
})

const fileName = computed(() => {
  return `report-${props.groupInfo.screen_name}-${from.value}-${to.value}`
})

const downloadJSON = () => {
  const jsonString = JSON.stringify(props.data, null, 2)
  const blob = new Blob([jsonString], { type: 'application/json' })
  const url = URL.createObjectURL(blob)
  const link = document.createElement('a')
  link.href = url
  link.download = `${fileName.value}.json`
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  URL.revokeObjectURL(url) // Освобождаем память
}

const downloadCSV = () => {
  const items = props.data.posts
  if (!items || items.length === 0) return

  if (!items || items.length === 0 || !items[0]) return

  const headers = Object.keys(items[0])

  const csvRows = [
    headers.join(','),
    ...items.map((row: Post) =>
      headers
        .map((fieldName) => {
          const value = row[fieldName as keyof Post] ?? ''
          const cleanValue = String(value).replace(/"/g, '""')
          return `"${cleanValue}"`
        })
        .join(','),
    ),
  ]

  const csvString = csvRows.join('\n')
  const blob = new Blob(['\uFEFF' + csvString], { type: 'text/csv;charset=utf-8;' })
  const url = URL.createObjectURL(blob)

  const link = document.createElement('a')
  link.href = url
  link.setAttribute('download', `${fileName.value}.csv`)
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  URL.revokeObjectURL(url)
}
</script>

<template>
  <div
    class="flex w-full items-center rounded-xl border border-[#333333] bg-[#232323] p-5 text-[#858585]"
  >
    <img
      :src="groupInfo.photo_50"
      :alt="groupInfo.name"
      class="mr-4 rounded-full border-2 border-blue-500"
    />
    <div>
      <div class="text-gray-100">{{ groupInfo.name }}</div>
      <div>@{{ groupInfo.screen_name }} {{ groupInfo.members_count }} подписчиков</div>
      <div>
        Период: {{ from }} -
        {{ to }}
      </div>
    </div>
    <div class="ml-auto">
      <button
        class="mb-1 flex cursor-pointer items-center gap-2 rounded-xl border border-[#333333] px-4 py-1 text-gray-100 hover:opacity-60"
        @click="downloadJSON"
      >
        <Download :size="15" />
        Экспорт JSON
      </button>
      <button
        class="ml-auto flex cursor-pointer items-center gap-2 rounded-xl border border-[#333333] px-4 py-1 text-gray-100 hover:opacity-60"
        @click="downloadCSV"
      >
        <Download :size="15" />
        Экспорт CSV
      </button>
    </div>
  </div>
</template>

<style scoped></style>
