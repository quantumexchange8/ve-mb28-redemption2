<script setup>
import { onMounted, ref, watch, watchEffect } from "vue";
import {
    InputText,
    RadioButton,
    Popover,
    DataTable,
    Column,
    Select,
    Avatar,
    Button,
    Card,
    Tag,
    MultiSelect, DatePicker
} from "primevue";
import {
    IconSearch,
    IconAdjustments,
    IconAlertCircleFilled,
    IconCloudDownload, IconXboxX,
} from '@tabler/icons-vue';
import MemberTableActions from "@/Pages/Member/Listing/Partials/MemberTableActions.vue";
import { generalFormat } from "@/Composables/format.js";
import Empty from "@/Components/Empty.vue";
import debounce from "lodash/debounce.js";
import { useLangObserver } from "@/Composables/localeObserver.js";
import {FilterMatchMode} from "@primevue/core/api";
import {usePage} from "@inertiajs/vue3";
import BarLoader from "@/Components/BarLoader.vue";
import ActiveFilterBadge from "@/Components/ActiveFilterBadge.vue";

const isLoading = ref(false);
const dt = ref(null);
const users = ref([]);
const { formatRgbaColor, formatAmount, formatNameLabel } = generalFormat();
const totalRecords = ref(0);
const first = ref(0);
const totalUsers = ref();
const { locale } = useLangObserver();

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    start_date: { value: null, matchMode: FilterMatchMode.EQUALS },
    end_date: { value: null, matchMode: FilterMatchMode.EQUALS },
});

const abortController = ref(null);
const lazyParams = ref({});

const loadLazyData = (event) => {
    isLoading.value = true;

    // Abort previous request if exists
    if (abortController.value) {
        abortController.value.abort();
    }

    // Create new controller for this request
    abortController.value = new AbortController();

    lazyParams.value = { ...lazyParams.value, first: event?.first || first.value };
    lazyParams.value.filters = filters.value;

    setTimeout(async () => {
        try {
            const params = {
                page: JSON.stringify(event?.page + 1),
                sortField: event?.sortField,
                sortOrder: event?.sortOrder,
                include: [],
                lazyEvent: JSON.stringify(lazyParams.value),
            };

            const url = route('member.getMemberListingData', params);

            const response = await fetch(url, {
                signal: abortController.value.signal
            });

            const results = await response.json();

            users.value = results?.data?.data;
            totalRecords.value = results?.data?.total;
            totalUsers.value = results?.totalUsers;
        } catch (e) {
            if (e.name === 'AbortError') {
                console.log('Skip');
            } else {
                console.error('Fetch error', e);
                users.value = [];
                totalRecords.value = 0;
            }
        } finally {
            isLoading.value = false;
        }
    },100);
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
    lazyParams.value.fitlers = filters.value;
    loadLazyData(event);
};

const op = ref();
const toggle = (event) => {
    op.value.toggle(event);
}

const selectedDate = ref([]);

const clearDate = () => {
    selectedDate.value = [];
}

watch(selectedDate, (newDateRange) => {
    if (Array.isArray(newDateRange)) {
        const [startDate, endDate] = newDateRange;
        filters.value['start_date'].value = startDate;
        filters.value['end_date'].value = endDate;

        if (startDate !== null && endDate !== null) {
            loadLazyData();
        }
    } else {
        console.warn('Invalid date range format:', newDateRange);
    }
})

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

watch(
    filters.value['global'],
    debounce(() => {
        loadLazyData();
    }, 300)
);

const clearAll = () => {
    filters.value['global'].value = null;
    filters.value['start_date'].value = null;
    filters.value['end_date'].value = null;

    selectedDate.value = [];
};

const clearFilterGlobal = () => {
    filters.value['global'].value = null;
}

const emit = defineEmits(['update-totals']);

watch([totalUsers], () => {
    emit('update-totals', {
        totalUsers: totalUsers.value,
    });
});

const exportStatus = ref(false);

// Optimized exportMember function
const exportMember = async () => {
    exportStatus.value = true;
    isLoading.value = true;

    lazyParams.value = { ...lazyParams.value, first: event?.first || first.value };
    lazyParams.value.filters = filters.value;

    const params = {
        page: JSON.stringify(event?.page + 1),
        sortField: event?.sortField,
        sortOrder: event?.sortOrder,
        include: [],
        lazyEvent: JSON.stringify(lazyParams.value),
        exportStatus: true,
    };
    
    const url = route('member.getMemberListingData', params);

    try {
        window.location.href = url;
    } catch (e) {
        console.red('Error occured during export:', e);
    } finally {
        isLoading.value = false;
        exportStatus.value = false;
    }
};

watchEffect(() => {
    if (usePage().props.toast !== null) {
        loadLazyData();
    }
});
</script>

<template>
    <!-- data table -->
    <Card class="w-full">
        <template #content>
            <DataTable
                :value="users"
                :rowsPerPageOptions="[10, 20, 50, 100]"
                :paginator="users?.length > 0"
                lazy
                removableSort
                paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport"
                :currentPageReportTemplate="$t('public.paginator_caption')"
                :first="first"
                :rows="10"
                v-model:filters="filters"
                ref="dt"
                dataKey="id"
                :totalRecords="totalRecords"
                :loading="isLoading"
                @page="onPage($event)"
                @sort="onSort($event)"
                @filter="onFilter($event)"
                :globalFilterFields="['name', 'email', 'username']"
            >
                <template #header>
                    <div class="flex flex-col md:flex-row gap-3 items-center self-stretch">
                        <div class="relative w-full md:w-60">
                            <div class="absolute top-2/4 -mt-[9px] left-4 text-surface-400">
                                <IconSearch size="20" stroke-width="1.5"/>
                            </div>
                            <InputText
                                v-model="filters['global'].value"
                                :placeholder="$t('public.keyword_search')"
                                class="font-normal pl-12 w-full md:w-60"
                            />
                            <div
                                v-if="filters['global'].value !== null"
                                class="absolute top-2/4 -mt-2 right-4 text-surface-300 hover:text-surface-400 dark:text-surface-500 dark:hover:text-surface-400 select-none cursor-pointer"
                                @click="clearFilterGlobal"
                            >
                                <IconXboxX size="16"/>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 w-full gap-3">
                            <div class="relative inline-block w-full md:w-fit">
                                <Button
                                    class="flex gap-3 items-center w-full md:w-fit font-bold"
                                    severity="secondary"
                                    @click="toggle"
                                >
                                    <IconAdjustments :size="20" stroke-width="1.5" />
                                    {{ $t('public.filter') }}
                                </Button>

                                <ActiveFilterBadge :filters="filters" :exclude="['global']"/>
                            </div>
                            <div class="w-full flex justify-end">
                                <Button
                                    @click="exportMember"
                                    class="w-full md:w-auto"
                                >
                                    <IconCloudDownload size="20" stroke-width="1.5" />
                                    {{ $t('public.export') }}
                                </Button>
                            </div>
                        </div>
                    </div>
                </template>
                
                <template #empty>
                    <Empty v-if="!isLoading" :title="$t('public.empty_user_title')" :message="$t('public.empty_user_message')" />
                </template>

                <template #loading>
                    <div class="flex flex-col gap-2 items-center justify-center">
                        <BarLoader strokeWidth="4" />
                        <span class="text-sm text-surface-700 dark:text-white">{{ $t('public.loading_data_caption') }}</span>
                    </div>
                </template>

                <template v-if="users?.length > 0">
                    <Column
                        field="name"
                        sortable
                        :header="$t('public.name')"
                        class="hidden md:table-cell"
                    >
                        <template #body="{data}">
                            <div class="flex items-center gap-3 max-w-60">
                                <div class="flex flex-col items-start truncate">
                                    <div class="font-medium">
                                        {{ data.name }}
                                    </div>
                                    <div class="text-surface-500 text-xs max-w-48 truncate">
                                        {{ data.email }}
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Column>

                    <Column
                        field="action"
                        header=""
                        class="hidden md:table-cell"
                    >
                        <template #body="slotProps">
                            <MemberTableActions
                                :member="slotProps.data"
                            />
                        </template>
                    </Column>
                    
                    <Column class="md:hidden" headerClass="hidden">
                        <template #body="slotProps">
                            <div class="flex flex-col items-start gap-1 self-stretch">
                                <div class="flex items-center gap-2 self-stretch w-full">
                                    <div class="flex items-center gap-3 w-full">
                                        <Avatar
                                            v-if="slotProps.data.profile_photo_url"
                                            :image="slotProps.data.profile_photo_url"
                                            shape="circle"
                                            class="w-7 h-7 rounded-full overflow-hidden grow-0 shrink-0"
                                        />
                                        <Avatar
                                            v-else
                                            :label="formatNameLabel(slotProps.data.full_name)"
                                            shape="circle"
                                            class="w-7 h-7 rounded-full overflow-hidden grow-0 shrink-0 text-sm"
                                        />

                                        <div class="flex flex-col items-start">
                                            <div class="font-medium flex items-center justify-between max-w-[120px] xxs:max-w-[140px] min-[390px]:max-w-[180px] xs:max-w-[220px] truncate">
                                                <span class="truncate">{{ slotProps.data.full_name }}</span>
                                                <IconAlertCircleFilled
                                                    :size="20"
                                                    stroke-width="1.25"
                                                    class="text-red-500 flex-shrink-0 ml-2"
                                                    v-if="slotProps.data.kycs?.some(kyc => kyc.kyc_status === 'pending')"
                                                />
                                            </div>
                                            <div class="text-surface-500 text-xs max-w-[120px] xxs:max-w-[140px] min-[390px]:max-w-[180px] xs:max-w-[220px] truncate">
                                                {{ slotProps.data.email }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-end">
                                        <MemberTableActions
                                            :member="slotProps.data"
                                        />
                                    </div>
                                </div>
                            </div>
                        </template>
                    </Column>
                </template>
            </DataTable>
        </template>
    </Card>

    <Popover ref="op">
        <div class="flex flex-col gap-6 w-60">
            <!-- Filter Date -->
            <div class="flex flex-col gap-2 items-center self-stretch">
                <div class="flex self-stretch text-xs text-surface-950 dark:text-white font-semibold">
                    {{ $t('public.filter_by_date') }}
                </div>
                <div class="relative w-full">
                    <DatePicker
                        v-model="selectedDate"
                        dateFormat="yy-mm-dd"
                        class="w-full"
                        selectionMode="range"
                        placeholder="YYYY-MM-DD - YYYY-MM-DD"
                    />
                    <div
                        v-if="selectedDate && selectedDate.length > 0"
                        class="absolute top-2/4 -mt-1.5 right-2 text-surface-400 select-none cursor-pointer bg-transparent"
                        @click="clearDate"
                    >
                        <IconXboxX size="12" stoke-width="1.5" />
                    </div>
                </div>
            </div>

            <Button
                type="button"
                severity="info"
                class="w-full"
                @click="clearAll"
                :label="$t('public.clear_all')"
                :disabled="isLoading"
            />
        </div>
    </Popover>
</template>
