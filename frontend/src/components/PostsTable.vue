<script setup lang="ts">
import { formatTimestamp } from '@/utils/general.ts'
import TypeTag from '@/components/ui/TypeTag.vue'
import type { Post } from '@/types/post.ts'
import { Heart, MessageSquare, MoveUpRight, Star } from 'lucide-vue-next'

defineProps<{
  posts: Post[]
  title: string
  limit: boolean
  top: boolean
}>()
</script>

<template>
  <div class="w-full rounded-xl border border-[#333333] bg-[#232323] p-5 text-[#858585]">
    <h2 class="pb-4 text-2xl text-gray-100">{{ title }}</h2>
    <div class="rounded-xl" :class="{ 'h-145 overflow-y-auto': limit }">
      <table class="w-full border-separate border-spacing-0">
        <thead class="sticky top-0 z-10 bg-[#232323]">
          <tr class="text-left">
            <th
              class="rounded-l-xl border-t border-b border-l border-[#333333] px-2 py-3 font-normal"
            >
              #
            </th>
            <th class="hidden border-t border-b border-[#333333] p-2 font-normal md:table-cell">
              Дата
            </th>
            <th class="border-t border-b border-[#333333] p-2 font-normal">Тип</th>
            <th class="w-full border-t border-b border-[#333333] p-2 font-normal">Текст</th>
            <th
              class="hidden border-t border-b border-[#333333] p-2 font-normal md:table-cell"
            ></th>
            <th
              class="hidden border-t border-b border-[#333333] p-2 font-normal md:table-cell"
            ></th>
            <th
              class="hidden border-t border-b border-[#333333] p-2 font-normal md:table-cell"
            ></th>
            <th
              class="rounded-r-xl border-t border-r border-b border-[#333333] p-2 font-normal"
            ></th>
          </tr>
          <tr class="h-4"></tr>
        </thead>
        <tbody class="">
          <tr
            v-for="(post, index) in posts"
            :key="post.id"
            class="overflow-hidden border-[#333333] text-nowrap last:*:border-b odd:bg-[#202020] first:[&>td:first-child]:rounded-tl-xl last:[&>td:first-child]:rounded-bl-xl first:[&>td:last-child]:rounded-tr-xl last:[&>td:last-child]:rounded-br-xl"
          >
            <td class="border-t border-l border-[#333333] px-2 py-4">
              {{ top ? `#${index + 1}` : index + 1 }}
            </td>
            <td class="hidden border-t border-[#333333] p-2 py-4 md:table-cell">
              {{ formatTimestamp(post.date) }}
            </td>
            <td class="border-t border-[#333333] p-2 py-3">
              <TypeTag :type="post.type" />
            </td>
            <td class="max-w-0 border-t border-[#333333] p-2 py-4 text-gray-100" :title="post.text">
              <div class="truncate">{{ post.text }}</div>
            </td>
            <td class="hidden border-t border-[#333333] p-2 py-4 md:table-cell">
              <div class="flex items-center gap-1">
                <Heart :size="15" class="fill-current text-red-500" />{{ post.likes }}
              </div>
            </td>
            <td class="hidden border-t border-[#333333] p-2 py-4 md:table-cell">
              <div class="flex items-center gap-1">
                <MessageSquare :size="15" class="fill-current text-gray-100" />
                {{ post.comments }}
              </div>
            </td>
            <td class="hidden border-t border-[#333333] p-2 py-4 md:table-cell">
              <div class="flex items-center gap-1">
                <MoveUpRight :size="15" class="fill-current text-gray-100" />
                {{ post.reposts }}
              </div>
            </td>
            <td class="border-t border-r border-[#333333] p-2 py-4">
              <div class="flex items-center gap-1">
                <Star :size="15" class="fill-current text-blue-500" />
                {{ post.engagement }}
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<style scoped></style>
