import { defineStore } from 'pinia'

export const useUiStore = defineStore('ui', {

    state: () => ({
        statsOpen: true,
    }),

    actions: {

        toggleStats() {

            this.statsOpen = !this.statsOpen

            localStorage.setItem(
                'stats-open',
                JSON.stringify(this.statsOpen)
            )
        },

        initialize() {

            const saved = localStorage.getItem('stats-open')

            if (saved !== null) {
                this.statsOpen = JSON.parse(saved)
            }
        },
    },
})