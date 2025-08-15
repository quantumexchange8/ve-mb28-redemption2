<script setup>
import { computed, toRefs } from 'vue';
import { Badge } from 'primevue';

const props = defineProps({
    filters: {
        type: Object,
        required: true,
    },
    exclude: {
        type: Array,
        default: () => [],
    }
});

const { filters, exclude } = toRefs(props);

// Count active filters, excluding specified keys
const activeCount = computed(() => {
    const groupedKeys = [
        ['start_join_date', 'end_join_date'],
        ['start_date', 'end_date'],
        ['start_unsubscribed_date', 'end_unsubscribed_date'],
    ];

    const counted = new Set();
    let count = 0;

    // Handle grouped filters
    for (const group of groupedKeys) {
        if (exclude.value.some(key => group.includes(key))) continue;

        const isGroupActive = group.some(key => {
            const val = filters.value[key]?.value;
            return val !== null && val !== undefined && val !== '' && !(Array.isArray(val) && val.length === 0);
        });

        if (isGroupActive) {
            count += 1;
            group.forEach(key => counted.add(key)); // mark as counted
        }
    }

    // Handle remaining individual filters
    for (const [key, filter] of Object.entries(filters.value)) {
        if (counted.has(key) || exclude.value.includes(key)) continue;

        const val = filter?.value;
        if (val !== null && val !== undefined && val !== '' && !(Array.isArray(val) && val.length === 0)) {
            count += 1;
        }
    }

    return count;
});
</script>

<template>
  <Badge
        v-if="activeCount > 0"
        :value="activeCount"
        severity="help"
        class="absolute -top-2 -right-2"
  />
</template>
