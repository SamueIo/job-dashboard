<script setup>
import { getDate, getTime, getStatusColor } from '@/utils/useEmailHelpers'
import { toRef, ref } from 'vue'
import { useEmailSync } from '@/composables/useEmailSync'
import CreateEventModal from '../CalendarComponents/CreateEventModal.vue'
import EmailHeader from '../EmailComponents/EmailHeader.vue'
import EmailSummary from '../EmailComponents/EmailSummary.vue'
import EmailInfoGrid from '../EmailComponents/EmailInfoGrid.vue'
import EmailPreview from '../EmailComponents/EmailPreview.vue'

const props = defineProps({
    email: {
        type: Object,
        default: null,
    },
})



const emit = defineEmits(['closeEmail'])
const closeEmail = () => {
    emit('closeEmail')
}
const { emailSeen } = useEmailSync()

const showCreateEventModal = ref(false)
const eventPrefill = ref(null)
const scheduleFromEmail = () => {

    eventPrefill.value = {

        email_id: props.email.id,

        title: props.email.subject,

        description: props.email.summary,

        start_at: props.email.detected_date,

        end_at: props.email.detected_end_date,

        type: props.status || 'meeting',
    }

    showCreateEventModal.value = true
}

emailSeen(
    toRef(props, 'email')
)


</script>

<template>

    <div
        v-if="email"
        class="
            overflow-hidden "
    >

        <!-- HEADER -->
        <EmailHeader :email="props.email"
                     @schedule="scheduleFromEmail"
                     @closeEmail="closeEmail"
        />

        <!-- AI SUMMARY -->
        <EmailSummary :summary="props.email.summary"/>

        <!-- INFO GRID -->
        <EmailInfoGrid :email="props.email"/>

        <!-- PREVIEW -->
        <EmailPreview :email="props.email"/>


    </div>

    <!-- EMPTY -->
    <div
        v-else
        class="
            flex h-full items-center justify-center
            rounded-3xl

            border border-black/5

            text-gray-500

            dark:border-white/5
        "
    >
        Select an email
    </div>

    <CreateEventModal
        :show="showCreateEventModal"
        :prefill="eventPrefill"
        :event="props.email.event || null"
        @close="showCreateEventModal = false"
    />

</template>