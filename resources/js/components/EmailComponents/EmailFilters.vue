<script setup>
import { router } from '@inertiajs/vue3'
import { reactive } from 'vue'
import debounce from 'lodash/debounce'
import { Search } from 'lucide-vue-next'

const props = defineProps({
    statuses: {
        type: Array,
        default: () => [],
    },

    statusCounts: {
        type: Object,
        default: () => ({}),
    },

    companies: {
        type: Array,
        default: () => [],
    },

    filters: {
        type: Object,
        default: () => ({}),
    },
})

const form = reactive({
    search: props.filters.search || '',
    status: props.filters.status || 'all',
    company: props.filters.company || '',
    sort: props.filters.sort || 'newest',
})

const updateFilters = debounce(() => {

    router.get('/emails', {

        ...form,

    }, {

        preserveState: true,
        preserveScroll: true,
        replace: true,

        only: [
            'emails',
            'filters',
            'statusCounts',
        ],
    })

}, 300)

const setStatus = (status) => {

    form.status = status

    updateFilters()
}

/*
|--------------------------------------------------------------------------
| REUSABLE CLASSES
|--------------------------------------------------------------------------
*/

const selectClass = `
    rounded-2xl
    border border-black/10
    bg-white

    px-4 py-3
    text-sm text-gray-800

    shadow-sm
    outline-none
    transition-all

    focus:border-indigo-500/30
    focus:ring-4
    focus:ring-indigo-500/10

    dark:border-white/10
    dark:bg-[#0B1120]
    dark:text-white
`

const inactiveTab = `
    bg-black/[0.03]
    text-gray-600

    hover:bg-black/[0.05]

    dark:bg-white/5
    dark:text-gray-300
    dark:hover:bg-white/10
`

const statusColors = {
    interview: 'text-yellow-500 dark:text-yellow-400',
    applied: 'text-indigo-500 dark:text-indigo-400',
    offer: 'text-green-500 dark:text-green-400',
    rejected: 'text-red-500 dark:text-red-400',
}
</script>

<template>
    <div class="flex flex-col gap-4 rounded-2xl bg-gray-100 p-2 py-4 dark:bg-transparent">

        <!-- TOP FILTERS -->
        <div class="flex flex-wrap items-center gap-3">

            <!-- SEARCH -->
            <div class="relative min-w-[260px] flex-1">

                <Search
                    class="
                        absolute left-3 top-1/2
                        h-4 w-4 -translate-y-1/2
                        text-gray-400
                        dark:text-gray-500
                    "
                />

                <input
                    v-model="form.search"
                    type="text"
                    @input="updateFilters"
                    placeholder="Search emails, companies, keywords..."
                    class="
                        w-full rounded-2xl
                        border border-black/10
                        bg-white

                        py-3.5 pl-10 pr-4
                        text-sm text-gray-900

                        shadow-sm
                        outline-none
                        transition-all

                        placeholder:text-gray-400

                        focus:border-indigo-500/30
                        focus:ring-4
                        focus:ring-indigo-500/10

                        dark:border-white/10
                        dark:bg-[#0B1120]
                        dark:text-white
                        dark:placeholder:text-gray-500
                    "
                >
            </div>

            <!-- STATUS -->
            <select
                v-model="form.status"
                @change="updateFilters"
                :class="selectClass"
            >
                <option value="all">
                    All statuses
                </option>

                <option
                    v-for="status in statuses"
                    :key="status"
                    :value="status"
                >
                    {{ status }}
                </option>
            </select>

            <!-- COMPANY -->
            <select
                v-model="form.company"
                @change="updateFilters"
                :class="selectClass"
            >
                <option value="">
                    All companies
                </option>

                <option
                    v-for="company in companies"
                    :key="company"
                    :value="company"
                >
                    {{ company }}
                </option>
            </select>

            <!-- SORT -->
            <select
                v-model="form.sort"
                @change="updateFilters"
                :class="selectClass"
            >
                <option value="newest">
                    Newest
                </option>

                <option value="oldest">
                    Oldest
                </option>

                <option value="priority">
                    Priority
                </option>

                <option value="confidence">
                    Confidence
                </option>
            </select>

        </div>

        <!-- STATUS TABS -->
        <div
            class="
                flex flex-wrap items-center gap-2
                rounded-2xl

                border border-black/5
                bg-black/2

                p-2

                dark:border-white/5
                dark:bg-[#0b1120]
            "
        >

            <!-- ALL -->
            <button
                type="button"
                @click="setStatus('all')"
                :class="
                    form.status === 'all'
                        ? `
                            border border-indigo-500/20
                            bg-indigo-500/10
                            text-indigo-500
                            dark:text-indigo-400
                          `
                        : inactiveTab
                "
                class="
                    inline-flex items-center
                    rounded-xl
                    px-4 py-2
                    text-sm font-medium
                    transition-all
                "
            >
                All
                
                <span
                    class="
                        ml-2 inline-flex items-center justify-center
                        min-w-[22px] h-[22px]
                        rounded-full
                
                        bg-black/5
                        px-2
                
                        text-[11px]
                        font-semibold
                
                        text-gray-600
                
                        dark:bg-white/10
                        dark:text-gray-300
                    "
                >
                    {{ Object.values(statusCounts).reduce((a, b) => a + b, 0) }}
                </span>
            </button>

            <!-- STATUS BUTTONS -->
            <button
                v-for="status in statuses"
                :key="status"
                type="button"
                @click="setStatus(status)"
                :class="[
                    form.status === status
                        ? 'border border-black/10 bg-black/4 dark:border-white/10 dark:bg-white/10'
                        : inactiveTab,

                    statusColors[status],
                ]"
                class="
                    rounded-xl
                    px-4 py-2
                    text-sm font-medium
                    transition-all
                "
            >
                {{ status }}

                <span
                    class="
                        ml-2 inline-flex items-center justify-center
                        min-w-[22px] h-[22px]
                        rounded-full

                        bg-black/5
                        px-2

                        text-[11px]
                        font-semibold

                        text-gray-600

                        dark:bg-white/10
                        dark:text-gray-300
                    "
                >
                    {{ statusCounts[status] || 0 }}
                </span>
            </button>

        </div>

    </div>
</template>