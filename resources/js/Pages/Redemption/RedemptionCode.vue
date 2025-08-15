<script setup>
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import RedemptionCodeOverview from "@/Pages/Redemption/RedemptionCodeOverview.vue";
import RedemptionCodeListing from "@/Pages/Redemption/RedemptionCodeListing.vue";

const indicatorsRedeemed = ref(null);
const easRedeemed = ref(null);

const handleUpdateTotals = (data) => {
    indicatorsRedeemed.value = data.indicatorsRedeemed;
    easRedeemed.value = data.easRedeemed;
};
</script>

<template>
    <AuthenticatedLayout :title="$t('public.redemption_codes')">
        <div class="flex flex-col gap-5 items-center">
            <!-- data overview -->
            <RedemptionCodeOverview
                :indicatorsRedeemed="indicatorsRedeemed"
                :easRedeemed="easRedeemed"
            />

            <!-- data table -->
            <RedemptionCodeListing
                @update-totals="handleUpdateTotals"
            />
        </div>
    </AuthenticatedLayout>
</template>
