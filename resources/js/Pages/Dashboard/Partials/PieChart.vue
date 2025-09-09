<script setup>
import { ref, watch, onMounted } from 'vue';
import { Chart, registerables } from 'chart.js';
import ChartDataLabels from 'chartjs-plugin-datalabels';

Chart.register(...registerables, ChartDataLabels);

const props = defineProps({
    chartData: {
        type: Object,
        required: true,
    },
    title: {
        type: String,
        default: '', // e.g., "Expiring in 7 days"
    }
});

const canvasRef = ref(null);
let chartInstance = null;

const getRandomColor = () => {
    const letters = '0123456789ABCDEF';
    let color = '#';
    for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
};

const renderChart = () => {
    if (!canvasRef.value) return;
    if (chartInstance) chartInstance.destroy();

    const colors = props.chartData.labels.map(() => getRandomColor());

    chartInstance = new Chart(canvasRef.value, {
        type: 'pie',
        data: {
            labels: props.chartData.labels,
            datasets: [{
                data: props.chartData.values,
                backgroundColor: colors, // dynamic colors
            }]
        },
        options: {
            plugins: {
                legend: { 
                    display: true,
                    position: 'bottom',
                },
                title: {
                    display: !!props.title,
                    text: props.title,
                    align: 'start',
                    font: { size: 16, weight: 'bold' }
                },
                datalabels: { display: false },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        title: ctx => ctx[0].label,
                        label: ctx => {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const value = ctx.raw;
                            const percent = ((value / total) * 100).toFixed(1);
                            return ' ' + percent + '%';
                        }
                    }
                }
            },
            responsive: true,
            maintainAspectRatio: false
        }
    });
};

watch(() => props.chartData, () => {
    if (canvasRef.value) renderChart();
}, { deep: true });

onMounted(() => {
    renderChart();
});
</script>

<template>
    <div class="relative w-full">
        <canvas ref="canvasRef"></canvas>
    </div>
</template>
