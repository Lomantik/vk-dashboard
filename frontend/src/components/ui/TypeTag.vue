<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  type: string
}>()
const typeText = computed(() => {
  switch (props.type) {
    case 'photo':
      return 'Фото'
    case 'video':
      return 'Видео'
    case 'link':
      return 'Ссылка'
    default:
      return 'Текст'
  }
})
</script>

<template>
  <div
    class="rounded-2xl px-2 pt-0.5 pb-1 text-center text-white"
    :class="{
      'bg-blue-500': type === 'photo',
      'bg-red-500': type === 'video',
      'bg-yellow-500': type === 'link',
      'bg-gray-500': type === 'text',
    }"
  >
    {{ typeText }}
  </div>
</template>

<style scoped></style>
