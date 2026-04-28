<script setup lang="ts">
import { ref } from 'vue'
import axios from 'axios'
import LoadingButton from '@/components/ui/LoadingButton.vue'
import { useInputStore } from '@/stores/input.store.ts'
import { analyze, report } from '@/api/analisis.api.ts'
import { formatTimestamp } from '@/utils/general.ts'
import type { Data } from '@/types/data.ts'

const getLocalFormatDate = () => {
  const date = new Date()
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

const inputStore = useInputStore()

const groupId = ref<string>('')
const date_start = ref<string>(getLocalFormatDate())
const date_end = ref<string>(getLocalFormatDate())

if (inputStore.groupId !== '') groupId.value = inputStore.groupId
if (inputStore.from) date_start.value = formatTimestamp(inputStore.from) ?? ''
if (inputStore.to) date_end.value = formatTimestamp(inputStore.to) ?? ''

const data = ref<Data>()
const loading = ref(false)
const error = ref<string>('')

const getSlug = (input: string) => {
  try {
    const url = new URL(input)
    return url.pathname.split('/').filter(Boolean).pop()
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
  } catch (e) {
    return input.split('/').filter(Boolean).pop()
  }
}

const submit = async () => {
  const parsedGroupId = getSlug(groupId.value) ?? ''
  loading.value = true
  error.value = ''
  const date_from = Math.floor(Date.parse(date_start.value.replace(/-/g, '/')) / 1000)
  const date_to = Math.floor(Date.parse(date_end.value.replace(/-/g, '/')) / 1000)
  try {
    await analyze(parsedGroupId, date_from, date_to)
    data.value = await report(parsedGroupId, date_from, date_to)
    console.log(data.value)
    inputStore.setInputData(parsedGroupId, date_from, date_to)
    console.log(inputStore)
  } catch (e) {
    if (axios.isAxiosError(e)) {
      const error_message = e.response?.data?.data?.message ?? ''
      error.value =
        `Report failed with status code ${e.response?.status}` +
        (error_message ? `: ${error_message}` : '')
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="mx-auto w-full max-w-137.5">
    <div class="w-full text-center">
      <h1 class="pb-2 text-3xl text-white">Аналитика сообществ ВКонтакте</h1>
      <p class="pb-5 text-[#858585]">
        Введите ID или короткое имя сообщества и выберите<br />период для анализа постов
      </p>
    </div>
    <div class="w-full rounded-xl border border-[#333333] bg-[#232323] p-8 text-[#858585]">
      <form>
        <label class="block py-2">ID или имя сообщества</label>
        <input
          type="text"
          name="group_id"
          v-model="groupId"
          placeholder="например: durov, vk, или ссылка на vk.com/durov"
          class="w-full rounded-lg border border-[#333333] bg-[#191919] p-3 text-gray-300 transition outline-none focus:border-blue-600"
        />
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block py-2">Начало периода</label>
            <input
              type="date"
              name="date_start"
              v-model="date_start"
              class="w-full rounded-lg border border-[#333333] bg-[#191919] p-3 text-gray-300 transition outline-none focus:border-blue-600"
            />
          </div>
          <div>
            <label class="block py-2">Конец периода</label>
            <input
              type="date"
              name="date_end"
              v-model="date_end"
              class="w-full rounded-lg border border-[#333333] bg-[#191919] p-3 text-gray-300 transition outline-none focus:border-blue-600"
            />
          </div>
        </div>
        <div
          v-if="error !== ''"
          class="mt-4 rounded-lg border border-[#97292C] bg-[#3F2224] p-3 text-sm text-[#DB6D7A]"
        >
          {{ error }}
        </div>
        <LoadingButton
          @click="submit"
          title="Анализировать"
          :loading="loading"
          class="mt-4 w-full cursor-pointer rounded-lg bg-[#0278FF] py-3 font-medium text-white transition hover:bg-blue-500"
        />
      </form>
    </div>
  </div>
</template>

<style scoped></style>
