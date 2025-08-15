<script setup>
import { generalFormat } from "@/Composables/format.js"
import { Card } from "primevue";
import {IconUser, IconUsers, IconTrendingDown, IconTrendingUp, IconUserCheck, IconUserX} from "@tabler/icons-vue";

const props = defineProps({
    indicatorsRedeemed: Number,
    easRedeemed: Number,
})

const { formatAmount } = generalFormat()
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 w-full gap-3 md:gap-5">
        <Card class="w-full">
            <template #content>
                <div class="flex gap-3 items-center">
                    <div class="flex flex-col w-full">
                        <span class="text-sm w-full text-surface-400 dark:text-surface-500">{{ $t('public.indicators_redeemed') }}</span>
                        <div
                            v-if="indicatorsRedeemed !== null"
                            class="flex gap-2 items-end"
                        >
                            <span class="text-xl font-medium">{{ formatAmount(indicatorsRedeemed, 0, '') }}</span>
                        </div>
                        <div v-else class="text-xl">
                            {{ $t('public.calculating') }}...
                        </div>
                    </div>
                    <div class="bg-blue-100 dark:bg-blue-900/30 rounded-md text-blue-500 p-2">
                        <IconUser size="28" stroke-width="1.5" />
                    </div>
                </div>
            </template>
        </Card>

        <Card class="w-full">
            <template #content>
                <div class="flex gap-3 items-center">
                    <div class="flex flex-col w-full">
                        <span class="text-sm w-full text-surface-400 dark:text-surface-500">{{ $t('public.eas_redeemed') }}</span>
                        <div
                            v-if="easRedeemed !== null"
                            class="flex gap-2 items-end"
                        >
                            <span class="text-xl font-medium">{{ formatAmount(easRedeemed, 0, '') }}</span>
                        </div>
                        <div v-else class="text-xl">
                            {{ $t('public.calculating') }}...
                        </div>
                    </div>
                    <div class="bg-green-100 dark:bg-green-900/30 rounded-md text-green-500 p-2">
                        <IconUser size="28" stroke-width="1.5" />
                    </div>
                </div>
            </template>
        </Card>
    </div>
</template>
