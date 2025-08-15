<script setup>
import { generalFormat } from "@/Composables/format.js"
import { Card } from "primevue";
import {IconUsers, IconTrendingDown, IconTrendingUp, IconUserCheck, IconUserX} from "@tabler/icons-vue";

const props = defineProps({
    totalUsers: Number,
})

const { formatAmount } = generalFormat()
</script>

<template>
    <div class="grid grid-cols-1 w-full gap-3 md:gap-5">
        <Card class="w-full">
            <template #content>
                <div class="flex gap-3 items-center">
                    <div class="flex flex-col w-full justify-center items-center gap-2">
                        <div class="bg-cyan-100 dark:bg-cyan-900/30 rounded-md text-cyan-500 p-2">
                            <IconUsers size="28" stroke-width="1.5" />
                        </div>
                        <span class="text-sm w-full text-center text-surface-400 dark:text-surface-500">{{ $t('public.total_user') }}</span>
                        <div
                            v-if="totalUsers !== null"
                            class="flex gap-2 items-end"
                        >
                            <span class="text-xl font-medium">{{ formatAmount(totalUsers, 0, '') }}</span>
                        </div>
                        <div v-else class="text-xl">
                            {{ $t('public.calculating') }}...
                        </div>
                    </div>
                </div>
            </template>
        </Card>
    </div>
</template>
