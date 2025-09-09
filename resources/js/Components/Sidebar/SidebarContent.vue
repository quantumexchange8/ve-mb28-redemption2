<script setup>
import SidebarLink from '@/Components/Sidebar/SidebarLink.vue'
import SidebarCollapsible from '@/Components/Sidebar/SidebarCollapsible.vue'
import SidebarCollapsibleItem from '@/Components/Sidebar/SidebarCollapsibleItem.vue'
import { sidebarState } from '@/Composables'
import {onMounted, ref, watchEffect} from "vue";
import {usePage} from "@inertiajs/vue3";
import {
    IconLayoutDashboard,
    IconClipboardText,
    IconUsers,
    IconUsersGroup,
    IconDatabaseDollar,
    IconCategory,
    IconCoinMonero,
    IconHistory,
    IconClockDollar,
    IconAdjustmentsDollar,
    IconTag,
    IconClipboardData,
    IconPhotoCog,
    IconChartPie,
    IconId,
    IconServerCog,
    IconSettingsDollar,
    IconTicket
} from '@tabler/icons-vue';
import SidebarCategoryLabel from "@/Components/Sidebar/SidebarCategoryLabel.vue";

const page = usePage();
const pendingRedemptionCodeRequest = ref(0);

const getPendingCounts = async () => {
    try {
        const response = await axios.get('/getPendingCounts');
        pendingRedemptionCodeRequest.value = response.data.pendingRedemptionCodeRequest
    } catch (error) {
        console.error('Error pending counts:', error);
    }
};

onMounted(() => {
    getPendingCounts();
})

watchEffect(() => {
    if (usePage().props.toast !== null) {
        getPendingCounts();
    }
});
</script>

<template>
    <nav
        class="relative flex flex-col flex-1 max-h-full gap-1 items-center overflow-y-auto"
        :class="{
            'p-3': sidebarState.isOpen || sidebarState.isHovered,
            'px-5 py-3': !sidebarState.isOpen && !sidebarState.isHovered,
        }"
    >
        <!-- Dashboard -->
        <SidebarLink
            :title="$t('public.dashboard')"
            :href="route('dashboard')"
            :active="route().current('dashboard')"
        >
            <template #icon>
                <IconLayoutDashboard :size="20" stroke-width="1.25" />
            </template>
        </SidebarLink>

        <!-- Code Redemption -->
        <SidebarLink
            :title="$t('public.code_redemption')"
            :href="route('redeem')"
            :active="route().current('redeem')"
        >
            <template #icon>
                <IconClipboardText :size="20" stroke-width="1.25" />
            </template>
        </SidebarLink>

        <!-- Member -->
        <SidebarLink
            :title="$t('public.member_listing')"
            :href="route('member.listing')"
            :active="route().current('member.listing')"
        >
            <template #icon>
                <IconUsers :size="20" stroke-width="1.25" />
            </template>
        </SidebarLink>

        <!-- Pending -->
        <SidebarLink
            :title="$t('public.pending')"
            :href="route('pending')"
            :active="route().current('pending')"
            :pendingCounts="pendingRedemptionCodeRequest"
        >
            <template #icon>
                <IconClockDollar :size="20" stroke-width="1.25" />
            </template>
        </SidebarLink>

        <!-- Redemption Codes -->
        <SidebarLink
            :title="$t('public.redemption_codes')"
            :href="route('redeem.redemptionCodes')"
            :active="route().current('redeem.redemptionCodes')"
        >
            <template #icon>
                <IconTicket :size="20" stroke-width="1.25" />
            </template>
        </SidebarLink>

        <!-- Version Control -->
        <SidebarLink
            :title="$t('public.version')"
            :href="route('version')"
            :active="route().current('version')"
        >
            <template #icon>
                <IconClipboardData :size="20" stroke-width="1.25" />
            </template>
        </SidebarLink>

    </nav>
</template>
