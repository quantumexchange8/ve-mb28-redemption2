<script setup>
import { ref } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import AddMember from "@/Pages/Member/Listing/Partials/AddMember.vue";
import MemberOverview from "@/Pages/Member/Listing/MemberOverview.vue";
import MemberListView from "@/Pages/Member/Listing/MemberListView.vue";

const totalUsers = ref(null);
const usersTrend = ref(null);
const verifiedUsers = ref(null);
const unverifiedUsers = ref(null);

const handleUpdateTotals = (data) => {
    totalUsers.value = data.totalUsers;
    usersTrend.value = data.usersTrend;
    verifiedUsers.value = data.verifiedUsers;
    unverifiedUsers.value = data.unverifiedUsers;
};
</script>

<template>
    <AuthenticatedLayout :title="$t('public.member_listing')">
        <div class="flex flex-col gap-5 items-center">
            <!-- <div class="flex justify-end w-full">
                <AddMember
                    :groups="groups"
                    :countries="countries"
                />
            </div> -->

            <!-- data overview -->
            <MemberOverview
                :totalUsers="totalUsers"
            />

            <!-- data table -->
            <MemberListView
                @update-totals="handleUpdateTotals"
            />
        </div>
    </AuthenticatedLayout>
</template>
