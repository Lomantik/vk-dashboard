import type { Group } from '@/types/group.ts'
import type { Post } from '@/types/post.ts'
import type { Analytics } from '@/types/analytics.ts'
import type { Kpi } from '@/types/kpi.ts'

export interface Data {
  group_info: Group
  posts: Post[]
  top_posts: Post[]
  analytics: Analytics
  kpi: Kpi
  content_types: Record<string, number>
  report_timestamp: string
}
