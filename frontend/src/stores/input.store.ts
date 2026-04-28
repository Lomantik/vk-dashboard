import 'pinia-plugin-persistedstate'
import { ref } from 'vue'
import { defineStore } from 'pinia'
import type { PersistenceOptions } from 'pinia-plugin-persistedstate'

export const useInputStore = defineStore(
  'input',
  () => {
    const groupId = ref<string>('')
    const from = ref<number | null>(null)
    const to = ref<number | null>(null)

    function setInputData(newGroupId: string, newFrom: number, newTo: number): void {
      groupId.value = newGroupId
      from.value = newFrom
      to.value = newTo
    }

    function clearInputData(): void {
      groupId.value = ''
      from.value = null
      to.value = null
    }

    return {
      groupId,
      from,
      to,
      setInputData,
      clearInputData,
    }
  },
  {
    persist: {
      key: 'input_data',
      storage: sessionStorage,
      paths: ['groupId', 'from', 'to'],
    } as PersistenceOptions,
  },
)
