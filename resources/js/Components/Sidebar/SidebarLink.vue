<script setup>
import { Link } from '@inertiajs/vue3'
import { sidebarState } from '@/Composables'
import { IconCircle, IconAlertSquareRoundedFilled, IconBellFilled } from "@tabler/icons-vue";
import Badge from 'primevue/badge';
import OverlayBadge from 'primevue/overlaybadge';

const props = defineProps({
    href: {
        type: String,
        required: false,
    },
    active: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        required: true,
    },
    external: {
        type: Boolean,
        default: false,
    },
    pendingCounts: Number
})

const Tag = !props.external ? Link : 'a'
</script>

<template>
    <component
        :is="Tag"
        v-if="href"
        :href="href"
        :class="[
            'p-3 flex gap-3 items-center rounded-md transition-colors w-full dark:hover:bg-surface-900',
            {
                'text-surface-700 dark:text-white hover:text-primary-500 dark:hover:text-primary-100 hover:bg-primary-50':
                    !active,
                'text-white dark:text-primary bg-primary dark:bg-transparent hover:bg-primary-500 dark:hover:bg-surface-900':
                    active,
            },
        ]"
    >
        <div class="max-w-5">
            <slot name="icon">
                <IconCircle
                    size="20"
                />
            </slot>
        </div>

        <div class="flex items-center gap-2 w-full">
            <span
                class="text-sm font-medium w-full"
                v-show="sidebarState.isOpen || sidebarState.isHovered"
            >
                {{ title }}
            </span>
            <!-- <Badge
                v-if="pendingCounts > 0 && (sidebarState.isOpen || sidebarState.isHovered)"
                :value="pendingCounts"
                severity="info"
            ></Badge> -->
            <OverlayBadge
                v-if="pendingCounts > 0 && (sidebarState.isOpen || sidebarState.isHovered)"
                :value="pendingCounts"
                severity="danger"
                size="small"
            >
                <div class="w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center">
                    <IconBellFilled class="w-4 h-4 text-gray-700" />
                </div>
            </OverlayBadge>

        </div>
    </component>
    <button
        v-else
        type="button"
        :class="[
            'p-3 flex gap-3 items-center rounded-md transition-colors w-full dark:hover:bg-surface-900',
            {
                'text-surface-700 dark:text-white hover:text-primary-500 dark:hover:text-primary-100 hover:bg-primary-50':
                    !active,
                'text-white dark:text-primary bg-primary dark:bg-transparent hover:bg-primary-500 dark:hover:bg-surface-900':
                    active,
            },
        ]"
    >
        <slot name="icon">
            <IconCircle
                size="20"
            />
        </slot>

        <span
            class="text-sm font-medium"
            v-show="sidebarState.isOpen || sidebarState.isHovered"
        >
            {{ title }}
        </span>
        <!-- <div
            v-if="pendingCounts"
            class="text-red-500"
        >
            <IconAlertSquareRoundedFilled
                size="20"
            />
        </div> -->
        <OverlayBadge
            v-if="pendingCounts"
            :value="pendingCounts"
            severity="danger"
            size="small"
        >
            <div class="w-6 h-6 rounded-full bg-gray-300 flex items-center justify-center">
                <IconBellFilled class="w-4 h-4 text-gray-700" />
            </div>
        </OverlayBadge>
        <slot name="arrow" />
    </button>
</template>
