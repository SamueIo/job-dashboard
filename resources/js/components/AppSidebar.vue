<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { BarChart3Icon, LayoutGrid } from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as emails } from '@/routes/emails';
import { calendar } from '@/routes';
import type { NavItem } from '@/types';
import { Mail, Calendar,  } from 'lucide-vue-next'
import EnableSyncCalendar from './CalendarComponents/EnableSyncCalendar.vue';
import HideGridStatsButton from './CalendarComponents/HideGridStatsButton.vue';
import { usePage } from '@inertiajs/vue3';
import type { SidebarActionItem } from '@/types/sidebar';
import SidebarActions from './SidebarActions.vue';
import { computed } from 'vue';
const page = usePage()

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },

    {
        title: 'Emails',
        href: emails(),
        icon: Mail,
    },

    {
        title: 'Calendar',
        href: calendar(),
        icon: Calendar,
    },
];

const isCalendarPage = computed(() =>
    page.url.startsWith('/calendar')
)

const isNotDashboard = computed(() =>
    !page.url.startsWith('/dashboard')
)

const footerActionItems: SidebarActionItem[] = [
    {
        title: 'Google Calendar Sync',
        icon: Calendar,
        component: EnableSyncCalendar,
        show: isCalendarPage,
    },
    {
        title: 'Hide Grid Stats',
        icon: BarChart3Icon,
        component: HideGridStatsButton,
        show: isNotDashboard,
    },
]
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <SidebarActions :items="footerActionItems" />

            <NavUser />
        </SidebarFooter>
        
    </Sidebar>
    <slot />
</template>
