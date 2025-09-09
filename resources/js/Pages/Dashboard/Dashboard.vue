<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { InputText, Button, Select, DatePicker } from 'primevue';
import BarChart from '@/Pages/Dashboard/Partials/BarChart.vue';
import PieChart from '@/Pages/Dashboard/Partials/PieChart.vue';

// Example data for charts
// const barData = ref({
//     labels: ['Product A', 'Product B', 'Product C'],
//     values: [25, 100, 320]
// });

// const pieData = ref({
//     labels: ['Product A', 'Product B', 'Product C'],
//     values: [5, 10, 3]
// });

const barData = ref({ labels: [], values: [] });
const pieData = ref({ labels: [], values: [] });

// Function to fetch chart data
const getChartData = async () => {
    try {
        const response = await fetch('/getChartData');
        if (!response.ok) throw new Error('Failed to fetch chart data');

        const data = await response.json();
        barData.value = data.bar;
        pieData.value = data.pie;
    } catch (error) {
        console.error('Error fetching chart data:', error);
    }
};

// Call it immediately if you want
getChartData();
</script>

<template>
    <AuthenticatedLayout :title="$t('public.dashboard')">
        <div class="flex flex-col items-center justify-center p-5 gap-5">

            <!-- Header -->
            <!-- <div class="flex flex-col justify-center gap-1">
                <span class="font-semibold text-lg">{{ $t('public.redemption') }}</span>
                <span class="font-medium">{{ $t('public.redemption_condition') }}</span>
            </div> -->

            <!-- Charts Row -->
            <div class="flex flex-row flex-wrap w-full gap-5 justify-center items-center">

                <!-- Bar Chart -->
                <BarChart :chartData="barData" title="Redeemed Status"  class="w-full max-w-[600px] h-[300px]"/>

                <!-- Pie Chart -->
                <PieChart :chartData="pieData" title="Expiring in 7 days"  class="w-full max-w-[600px] h-[300px]"/>

            </div>

        </div>
    </AuthenticatedLayout>
</template>
