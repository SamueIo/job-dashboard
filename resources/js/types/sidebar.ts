import type { ComputedRef } from 'vue'
import type { LucideIcon } from 'lucide-vue-next'

export type SidebarActionItem = {
    title: string
    icon?: LucideIcon
    component: any

    show?: boolean | (() => boolean) | ComputedRef<boolean>
}