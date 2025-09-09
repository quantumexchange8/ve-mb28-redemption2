<script setup>
import { ref, onMounted, watch } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps({
    chartData: {
        type: Object,
        required: true, // { labels: [], values: [] }
    },
    title: {
        type: String,
        default: '', 
    }
});

const chartRef = ref(null);
let barChart = null;

const renderChart = () => {
    if (!chartRef.value) return;
    if (barChart) barChart.destroy();

    barChart = new Chart(chartRef.value, {
        type: 'bar',
        data: {
            labels: props.chartData.labels,
            datasets: [{
                data: props.chartData.values,
                backgroundColor: '#176D7C',
                barPercentage: 0.6,       // slightly wider bars
                categoryPercentage: 0.6   // helps with spacing
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,  // key for mobile responsiveness
            plugins: {
                legend: { display: false },
                title: {
                    display: !!props.title,
                    text: props.title,
                    align: 'start',
                    font: { size: 16, weight: 'bold' }
                },
                datalabels: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.raw;
                        }
                    }
                }
            },
            scales: {
                y: { beginAtZero: true },
                x: {
                    ticks: { autoSkip: false }, // prevents skipping labels
                    grid: { drawTicks: false, drawOnChartArea: false }
                }
            }
        }
    });
};

onMounted(() => renderChart());

watch(() => props.chartData, (newData) => {
    if (barChart) {
        barChart.data.labels = newData.labels;
        barChart.data.datasets[0].data = newData.values;
        barChart.update();
    }
}, { deep: true });
</script>

<template>
  <div class="w-full">
      <canvas ref="chartRef"></canvas>
  </div>
</template>
