<script setup lang="ts">
defineProps<{
  topText: string
  mainText: string
  bottomText: string
}>()
</script>

<template>
  <div class="rounded-xl border border-[#333333] bg-[#232323] p-5 text-[#858585]">
    <div>{{ topText }}</div>
    <div class="text-2xl text-gray-100">{{ mainText }}</div>
    <div>{{ bottomText }}</div>
  </div>
</template>

<style scoped></style>
