<script setup>
import {onMounted, ref, watch, watchEffect} from "vue";
import {CalendarIcon, ClockRewindIcon} from "@/Components/Icons/outline.jsx";
import { generalFormat } from "@/Composables/format.js";
import debounce from "lodash/debounce.js";
import { usePage, useForm, router} from "@inertiajs/vue3";
import dayjs from "dayjs";
import {
    IconCircleXFilled,
    IconSearch,
    IconX,
    IconAdjustments,
    IconAdjustmentsHorizontal,
    IconCloudDownload,
} from "@tabler/icons-vue";
import Empty from "@/Components/Empty.vue";
import {
    Button,
    Column,
    DataTable,
    Tag,
    InputText,
    Select,
    MultiSelect,
    DatePicker,
    ColumnGroup,
    Row,
    ProgressSpinner,
    Popover,
    RadioButton,
    Dialog,
    IconField,
} from "primevue";
import { FilterMatchMode } from '@primevue/core/api';
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PendingAction from "@/Pages/Pending/Partials/PendingAction.vue";
import HandleRequest from "@/Pages/Pending/Partials/HandleRequest.vue";

const props = defineProps({
    products: Array,
})

const isLoading = ref(false);
const dt = ref(null);
const pendingRequests = ref();
const selectedProducts = ref();
const { formatRgbaColor, formatAmount, formatDateTime, formatNameLabel } = generalFormat();
const totalRecords = ref(0);
const first = ref(0);
const visible = ref(false);
const pendingData = ref(null);

const rowClicked = (data) => {
    pendingData.value = data;
    visible.value = true;
}

const filters = ref({
  global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});

const initialLoaded = ref(true);

watch(filters, debounce(() => {
    if (initialLoaded.value) {
        initialLoaded.value = false;
        return;
    }

    first.value = 0;
    loadLazyData();
}, 300), { deep: true });

const abortController = ref(null);
const lazyParams = ref({});

const loadLazyData = (event) => {
    isLoading.value = true;

    // Abort previous request
    if (abortController.value) {
        abortController.value.abort();
    }

    // Create new controller
    abortController.value = new AbortController();

    lazyParams.value = {
        ...lazyParams.value,
        first: event?.first || first.value,
        filters: filters.value,
    };

    setTimeout(async () => {
        try {
            const params = {
                page: JSON.stringify(event?.page + 1),
                sortField: event?.sortField,
                sortOrder: event?.sortOrder,
                include: [],
                lazyEvent: JSON.stringify(lazyParams.value),
            };

            const url = route('pending.getRedemptionCodeRequest', params);

            const response = await fetch(url, {
                signal: abortController.value.signal,
            });

            const results = await response.json();
            pendingRequests.value = results?.data?.data;
            totalRecords.value = results?.data?.total;
        } catch (error) {
            if (error.name === 'AbortError') {
                console.log('Previous request aborted');
            } else {
                console.error('Fetch error:', error);
                pendingRequests.value = [];
                totalRecords.value = 0;
            }
        } finally {
            isLoading.value = false;
        }
    }, 100);
};

const onPage = (event) => {
    lazyParams.value = event;
    loadLazyData(event);
};

const onSort = (event) => {
    lazyParams.value = event;
    loadLazyData(event);
};

const onFilter = (event) => {
    lazyParams.value.filters = filters.value;
    loadLazyData(event);
};

onMounted(() => {
    lazyParams.value = {
        first: dt.value.first,
        rows: dt.value.rows,
        sortField: null,
        sortOrder: null,
        filters: filters.value
    };

    loadLazyData();
});

// const op = ref();
// const toggle = (event) => {
//     op.value.toggle(event);
// }

const clearFilterGlobal = () => {
    filters.value['global'].value = null;
}

// const clearFilter = () => {
//     filters.value = {
//         global: { value: null, matchMode: FilterMatchMode.CONTAINS },
//     };
// };

watch(() => usePage().props.toast, (toast) => {
        if (toast !== null) {
            first.value = 0;
            loadLazyData();
        }
    }
);
</script>

<template>
    <AuthenticatedLayout :title="$t('public.redemption_code_request')">
        <div class="flex flex-col items-center px-4 py-6 gap-5 self-stretch rounded-2xl border border-gray-200 bg-white shadow-table md:px-6 md:gap-5">
            <DataTable
                v-model:first="first"
                v-model:filters="filters"
                :value="pendingRequests"
                :rowsPerPageOptions="[10, 20, 50, 100]"
                lazy
                :paginator="pendingRequests?.length > 0"
                removableSort
                paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport"
                :currentPageReportTemplate="$t('public.paginator_caption')"
                :rows="10"
                ref="dt"
                dataKey="id"
                selectionMode="single"
                :totalRecords="totalRecords"
                :loading="isLoading"
                @row-click="rowClicked($event.data)"
                @page="onPage($event)"
                @sort="onSort($event)"
                @filter="onFilter($event)"
            >
                <template #header>
                    <div class="flex flex-col md:flex-row gap-3 items-center self-stretch pb-3 md:pb-5">
                        <div class="relative w-full md:w-60">
                            <div class="absolute top-2/4 -mt-[9px] left-4 text-gray-400">
                                <IconSearch size="20" stroke-width="1.25" />
                            </div>
                            <InputText v-model="filters['global'].value" :placeholder="$t('public.keyword_search')" class="font-normal pl-12 w-full md:w-60" />
                            <div
                                v-if="filters['global'].value !== null && filters['global'].value !== ''"
                                class="absolute top-2/4 -mt-2 right-4 text-gray-300 hover:text-gray-400 select-none cursor-pointer"
                                @click="clearFilterGlobal"
                            >
                                <IconCircleXFilled size="16" />
                            </div>
                        </div>
                        <!-- <div class="w-full flex flex-col gap-3 md:flex-row">
                            <div class="w-full md:w-[272px]">
                                <DatePicker
                                    v-model="selectedDate"
                                    selectionMode="range"
                                    :manualInput="false"
                                    :minDate="minDate"
                                    :maxDate="maxDate"
                                    dateFormat="dd/mm/yy"
                                    showIcon
                                    iconDisplay="input"
                                    placeholder="yyyy/mm/dd - yyyy/mm/dd"
                                    class="w-full md:w-[272px]"
                                />
                                <div
                                    v-if="selectedDate && selectedDate.length > 0"
                                    class="absolute top-2/4 -mt-2.5 right-4 text-gray-400 select-none cursor-pointer bg-white"
                                    @click="clearDate"
                                >
                                    <IconX size="20" />
                                </div>
                                <Button
                                    type="button"
                                    severity="secondary"
                                    rounded
                                    @click="toggle"
                                    class="flex gap-3 items-center justify-center py-3 w-full md:w-[130px]"
                                >
                                    <IconAdjustments size="20" color="#0C111D" stroke-width="1.25" />
                                    <div class="text-sm text-gray-950 font-medium">
                                        {{ $t('public.filter') }}
                                    </div>
                                </Button>
                            </div>
                            <div class="w-full flex flex-col md:flex-row justify-end gap-2">
                                <Button
                                    v-if="selectedFiles?.length > 0"
                                    variant="primary-flat"
                                    :disabled="selectedFiles?.length === 0"
                                    @click="openDialog(null, 'update');"
                                >
                                    {{ $t('public.update_status') }}
                                </Button>

                                <Button
                                    @click="openDialog(null, 'export');"
                                    class="w-full md:w-auto"
                                >
                                    {{ $t('public.export') }}
                                </Button>
                            </div>
                        </div> -->
                    </div>
                </template>
                <template #empty>
                    <Empty
                        :title="$t('public.empty_pending_request_title')"
                        :message="$t('public.empty_pending_request_message')"
                    />
                </template>
                <template #loading>
                    <div class="flex flex-col gap-2 items-center justify-center">
                        <ProgressSpinner strokeWidth="4" />
                        <span class="text-sm text-gray-700">{{ $t('public.loading_data_caption') }}</span>
                    </div>
                </template>
                <template v-if="pendingRequests?.length > 0">
                    <Column field="name" header="Name" />
                    <Column field="meta_login" header="Meta Login" />
                    <Column field="product_ids" header="Products">
                        <template #body="{ data }">
                            <span>
                                {{ data.products?.map(p => p.label).join(', ') }}
                            </span>
                        </template>
                    </Column>
                    <Column header="Actions">
                        <template #body="slotProps">
                            <PendingAction 
                                :pendingRequests="slotProps.data" 
                                :products="props.products"
                                :isLoading="isLoading"
                            />
                        </template>
                    </Column>
                </template>

            </DataTable>

            <HandleRequest
                :visible="visible"
                :data="pendingData"
                :products="props.products"
                @update:visible="visible = $event"
            />

        </div>
    </AuthenticatedLayout>
</template>
    