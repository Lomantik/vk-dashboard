<script setup lang="ts">
import { RouterView } from 'vue-router'
import MainHeader from '@/components/MainHeader.vue'
</script>

<template>
  <div class="flex flex-col min-h-screen bg-[#191919]">
    <MainHeader />
    <RouterView />
  </div>
</template>

<style scoped></style>
