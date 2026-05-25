<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {  LayoutGrid } from 'lucide-vue-next';
import AppLogo from '@/components/AppLogo.vue';
import NavFooter from '@/components/NavFooter.vue';
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
import { Mail, Calendar } from 'lucide-vue-next'
import EnableSyncCalendar from './CalendarComponents/EnableSyncCalendar.vue';
import HideGridStatsButton from './CalendarComponents/HideGridStatsButton.vue';
import { usePage } from '@inertiajs/vue3'

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

const footerNavItems: NavItem[] = [

];
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
            <EnableSyncCalendar
                v-if="page.url.startsWith('/calendar')"
            />
            <HideGridStatsButton v-if="!page.url.startsWith('/dashboard')"/>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
        
    </Sidebar>
    <slot />
</template>
