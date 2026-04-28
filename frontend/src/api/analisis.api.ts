import api from '@/api/axios.ts'
import type { Data } from '@/types/data.ts'

export async function analyze(groupId: string, from: number, to: number): Promise<void> {
  await api.post(`/api/analyze`, {
    group_id: groupId,
    from: from,
    to: to,
  })
}

export async function report(groupId: string, from: number, to: number): Promise<Data> {
  const response = await api.get(`/api/report/${groupId}`, {
    params: {
      from: from,
      to: to,
    },
  })

  return response.data.data
}
