<script setup>
import { watch, computed } from 'vue'

import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    show: Boolean,
    selectedDate: String,
    event: Object,
})

const emit = defineEmits([
    'close',
])

/*
|--------------------------------------------------------------------------
| FORM
|--------------------------------------------------------------------------
*/

const form = useForm({

    title: '',

    description: '',

    start_at: props.selectedDate || '',

    end_at: '',

    type: '',

    sync_to_google: false,
})

/*
|--------------------------------------------------------------------------
| SUBMIT
|--------------------------------------------------------------------------
*/
const submit = () => {

    if (isEditing.value) {

        form.put(`/calendar/events/${props.event.id}`, {

            onSuccess: () => {

                emit('close')
            },
        })
    }

    else {

        form.post('/calendar/events', {

            onSuccess: () => {

                emit('close')
            },
        })
    }
}



const formatDateForInput = (date) => {

    if (!date) {
        return ''
    }

    return new Date(date)
        .toISOString()
        .slice(0, 16)
}
/*
|--------------------------------------------------------------------------
| ESC
|--------------------------------------------------------------------------
*/

watch(
    () => props.show,

    (value) => {

        if (value) {

            document.body.style.overflow = 'hidden'
        }

        else {

            document.body.style.overflow = ''
        }
    }
)

watch(
    () => props.selectedDate,

    (value) => {

        form.start_at = value
    }
)

const isEditing = computed(() => !!props.event)
watch(
    () => props.event,

    (event) => {

        if (!event) {
            return
        }

        form.title = event.title || ''

        form.description = event.description || ''

        form.start_at = formatDateForInput(event.start)

        form.end_at = formatDateForInput(event.end)

        form.type = event.type || ''
    },
    {
        immediate: true,
    }
)
</script>

<template>
    <Transition
        enter-active-class="transition duration-200 ease-out"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition duration-150 ease-in"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >

        <div
            v-if="show"
            class="
                fixed inset-0 z-50

                flex items-center justify-center

                bg-black/70

                backdrop-blur-sm
            "
        >

            <!-- MODAL -->
            <div
                class="
                    w-full
                    max-w-lg

                    rounded-[32px]

                    border border-white/10

                    bg-[#0b0b0b]

                    p-6

                    shadow-[0_0_80px_rgba(0,0,0,0.45)]
                "
            >

                <!-- HEADER -->
                <div
                    class="
                        mb-6

                        flex items-start justify-between
                    "
                >

                    <div>

                        <p
                            class="
                                text-[11px]
                                uppercase
                                tracking-[0.18em]

                                text-white/40
                            "
                        >
                            Calendar
                        </p>

                        <h2
                            class="
                                mt-2

                                text-2xl
                                font-semibold
                                tracking-tight

                                text-white
                            "
                        >
                            Create Event
                        </h2>

                    </div>

                    <button
                        @click="emit('close')"
                        class="
                            rounded-xl

                            border border-white/10

                            bg-white/[0.03]

                            px-3 py-2

                            text-sm
                            text-white/70

                            transition

                            hover:bg-white/[0.06]
                        "
                    >
                        Close
                    </button>

                </div>

                <!-- FORM -->
                <form
                    @submit.prevent="submit"
                    class="space-y-5"
                >

                    <!-- TITLE -->
                    <div>

                        <label
                            class="
                                mb-2 block

                                text-xs
                                uppercase
                                tracking-[0.16em]

                                text-white/40
                            "
                        >
                            Title
                        </label>

                        <input
                            v-model="form.title"
                            type="text"
                            required
                            placeholder="Gym, Dentist, Meeting..."

                            class="
                                w-full

                                rounded-2xl

                                border border-white/10

                                bg-white/[0.03]

                                px-4 py-3

                                text-sm
                                text-white

                                outline-none

                                transition

                                placeholder:text-white/25

                                focus:border-indigo-500/40
                            "
                        />

                    </div>

                    <!-- DESCRIPTION -->
                    <div>

                        <label
                            class="
                                mb-2 block

                                text-xs
                                uppercase
                                tracking-[0.16em]

                                text-white/40
                            "
                        >
                            Notes
                        </label>

                        <textarea
                            v-model="form.description"

                            rows="4"

                            placeholder="Optional notes..."

                            class="
                                w-full

                                rounded-2xl

                                border border-white/10

                                bg-white/[0.03]

                                px-4 py-3

                                text-sm
                                text-white

                                outline-none

                                transition

                                placeholder:text-white/25

                                focus:border-indigo-500/40
                            "
                        />

                    </div>

                    <!-- DATES -->
                    <div class="grid grid-cols-2 gap-4">

                        <!-- START -->
                        <div>

                            <label
                                class="
                                    mb-2 block

                                    text-xs
                                    uppercase
                                    tracking-[0.16em]

                                    text-white/40
                                "
                            >
                                Start
                            </label>

                            <input
                                v-model="form.start_at"
                                type="datetime-local"

                                class="
                                    w-full

                                    rounded-2xl

                                    border border-white/10

                                    bg-white/[0.03]

                                    px-4 py-3

                                    text-sm
                                    text-white

                                    outline-none

                                    transition

                                    focus:border-indigo-500/40
                                "
                            />

                        </div>

                        <!-- END -->
                        <div>

                            <label
                                class="
                                    mb-2 block

                                    text-xs
                                    uppercase
                                    tracking-[0.16em]

                                    text-white/40
                                "
                            >
                                End
                            </label>

                            <input
                                v-model="form.end_at"
                                type="datetime-local"

                                class="
                                    w-full

                                    rounded-2xl

                                    border border-white/10

                                    bg-white/[0.03]

                                    px-4 py-3

                                    text-sm
                                    text-white

                                    outline-none

                                    transition

                                    focus:border-indigo-500/40
                                "
                            />

                        </div>

                    </div>

                    <!-- TYPE -->
                    <div>

                        <label
                            class="
                                mb-2 block

                                text-xs
                                uppercase
                                tracking-[0.16em]
                                dark:text-black0
                                text-white/40
                            "
                        >
                            Type
                        </label>

                        <select
                            v-model="form.type"

                            class="
                                w-full

                                rounded-2xl

                                border border-white/10

                                bg-neutral-900

                                px-4 py-3

                                text-sm
                                text-white

                                outline-none
                            "
                        >
                            <option value="reminder">
                                Reminder
                            </option>

                            <option value="meeting">
                                Meeting
                            </option>

                            <option value="personal">
                                Personal
                            </option>

                            <option value="interview">
                                Interview
                            </option>

                        </select>

                    </div>

                    <!-- GOOGLE -->
                    <label
                        class="
                            flex items-center gap-3

                            rounded-2xl

                            border border-white/10

                            bg-white/[0.02]

                            px-4 py-3
                        "
                    >

                        <input
                            v-model="form.sync_to_google"
                            type="checkbox"
                        />

                        <span
                            class="
                                text-sm
                                text-white/80
                            "
                        >
                            Sync with Google Calendar
                        </span>

                    </label>

                    <!-- ACTIONS -->
                    <div
                        class="
                            flex justify-end gap-3

                            pt-4
                        "
                    >

                        <button
                            type="button"

                            @click="emit('close')"

                            class="
                                rounded-2xl

                                border border-white/10

                                bg-white/[0.03]

                                px-5 py-3

                                text-sm
                                text-white/70

                                transition

                                hover:bg-white/[0.06]
                            "
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"

                            class="
                                rounded-2xl

                                bg-indigo-500

                                px-5 py-3

                                text-sm
                                font-medium
                                text-white

                                transition

                                hover:bg-indigo-400
                            "
                        >
                            {{ isEditing ? 'Save Changes' : 'Create Event' }}
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </Transition>
</template>