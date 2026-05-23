import {
    Calendar,
    Send,
    XCircle,
    ShieldCheck,
    MoreHorizontal,
    Briefcase
} from 'lucide-vue-next'

export const getDate = (date: string) => {
    if (!date) return 'No date'

    return new Date(date).toLocaleDateString('en-US', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    })
}

export const getTime = (date: string) => {
    if (!date) return ''

    return new Date(date).toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
    })
}
export const getSenderName = (from: string) => {
    if (!from) return 'Unknown'

    const match = from.match(/^(.*?)</)

    if (match && match[1].trim()) {
        return match[1].trim()
    }

    return from
}

export const getIcon = (status: string) => {

    const normalized = status?.toLowerCase()

    return {
        interview: Calendar,
        applied: Send,
        rejected: XCircle,
        offer: ShieldCheck,
        other: Briefcase,
    }[normalized] || MoreHorizontal
}

export const getStatusColor = (status: string) => {
    return {
        interview: 'text-indigo-500 bg-indigo-500/10',
        applied: 'text-orange-500 bg-orange-500/10',
        rejected: 'text-red-500 bg-red-500/10',
        offer: 'text-green-500 bg-green-500/10',
        other: 'text-gray-500 bg-gray-500/10',
    }[status] || 'text-gray-500 bg-gray-500/10'
}
export const getPriorityColor = (priority: string) => {
    return {
        high: `
            text-rose-400
            bg-rose-500/10
            border border-rose-500/20
            shadow-[0_0_20px_rgba(244,63,94,0.15)]
        `,

        medium: `
            text-amber-400
            bg-amber-500/10
            border border-amber-500/20
            shadow-[0_0_20px_rgba(245,158,11,0.12)]
        `,

        low: `
            text-sky-300
            bg-sky-500/5
            border border-sky-500/10

            shadow-[0_0_12px_rgba(56,189,248,0.04)]
        `,
    }[priority] || `
        text-gray-400
        bg-gray-500/10
        border border-gray-500/20
    `
}