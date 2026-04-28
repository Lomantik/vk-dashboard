<script setup lang="ts">
import { ArrowLeft } from 'lucide-vue-next'
import { useInputStore } from '@/stores/input.store.ts'

const inputStore = useInputStore()
</script>

<template>
  <header
    class="sticky top-0 z-50 flex items-center border-b border-[#333333] bg-[#232323] px-5 py-4"
  >
    <div class="text-xl text-gray-100">VK Insights</div>
    <button
      v-if="inputStore.groupId !== ''"
      class="ml-auto flex cursor-pointer items-center text-[#858585] hover:opacity-70"
      @click="inputStore.clearInputData"
    >
      <ArrowLeft :size="13" class="mr-1" />Новый поиск
    </button>
  </header>
</template>

<style scoped></style>
