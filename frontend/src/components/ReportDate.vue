<script setup lang="ts">
defineProps<{
  dateString: string
}>()

const formatReportDate = (dateString: string) => {
  if (!dateString) return ''

  const date = new Date(dateString)

  // Проверка на валидность даты
  if (isNaN(date.getTime())) return dateString

  return date.toLocaleDateString('ru-RU', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit',
  })
}
</script>

<template>
  <div class="p-5 text-center text-[#858585]">
    {{ `Отчет сгенерирован: ${formatReportDate(dateString)}` }}
  </div>
</template>

<style scoped></style>
