import {
    Calendar,
    TrendingUp,
    Mail,
    Clock,
} from 'lucide-vue-next'

export const useCalendarStats = (stats) => {

    return [
        {
            title: 'Upcoming',
            value: stats.upcoming,
            trend: 12,
            icon: Calendar,
            color: 'blue',
        },

        {
            title: 'This Week',
            value: stats.thisWeek,
            trend: 8,
            icon: TrendingUp,
            color: 'emerald',
        },

        {
            title: 'Total',
            value: stats.total,
            trend: 5,
            icon: Mail,
            color: 'indigo',
        },

        {
            title: 'Next Interview',
            value: stats.nextInterview?.date || 'No interview',
            subtitle: stats.nextInterview?.company || null,
            trend: 0,
            icon: Clock,
            color: 'fuchsia',
        },
    ]
}