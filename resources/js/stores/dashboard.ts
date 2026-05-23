import { defineStore } from 'pinia'

export const useDashboardStore = defineStore('dashboard', {
    state: () => ({
        stats: {
            applications: 0,
            interviews: 0,
            responses: 0,
            social_rate: 0,
            positive: 0,
            rejections: 0,
            other: 0,
        },
    }),

    actions: {
        setStats(stats: any) {
            this.stats = stats
        },
    },
})
