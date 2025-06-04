<script setup>
import SidebarLink from '@/Components/Sidebar/SidebarLink.vue'
import SidebarCollapsible from '@/Components/Sidebar/SidebarCollapsible.vue'
import SidebarCollapsibleItem from '@/Components/Sidebar/SidebarCollapsibleItem.vue'
import { sidebarState } from '@/Composables'
import {onMounted, ref, watchEffect} from "vue";
import {usePage} from "@inertiajs/vue3";
import {
    IconLayoutDashboard,
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
    IconSettingsDollar
} from '@tabler/icons-vue';
import SidebarCategoryLabel from "@/Components/Sidebar/SidebarCategoryLabel.vue";

const page = usePage();
const pendingKYC = ref(0);

const getPendingCounts = async () => {
    try {
        const response = await axios.get('/getPendingCounts');
        pendingKYC.value = response.data.pendingKYC
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
    </nav>
</template>
