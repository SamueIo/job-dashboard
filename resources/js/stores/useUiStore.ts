import { defineStore } from 'pinia'

export const useUiStore = defineStore('ui', {

    state: () => ({
        statsOpen: JSON.parse(
            localStorage.getItem('stats-open') ?? 'true'
        ),
    }),

    actions: {

        toggleStats() {

            this.statsOpen = !this.statsOpen

            localStorage.setItem(
                'stats-open',
                JSON.stringify(this.statsOpen)
            )
        },
    },
})