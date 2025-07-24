<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import {
    Textarea,
    Button,
    DatePicker,
    Dialog,
    InputText,
    MultiSelect,
} from "primevue";
import {
    IconX,
} from "@tabler/icons-vue";
import dayjs from 'dayjs';

// Props
const props = defineProps({
    visible: Boolean,
    data: Object,
    products: Array,
    isLoading: Boolean,
});

// Emits
const emit = defineEmits(['update:visible']);

// Local states
const visibleDialog = ref(false);
const today = new Date();
const expiredDate = ref(null);
const selectedProducts = ref([]);

// Form
const form = useForm({
    id: null,
    action: null,
    name: null,
    meta_login: null,
    product_ids: [],
    remarks: null,
    expired_date: null,
});

// Sync dialog open state with props
watch(() => props.visible, (val) => {
    visibleDialog.value = val;

    if (val && props.data) {
        form.reset();
        form.clearErrors();
        expiredDate.value = null;

        form.id = props.data.id;
        form.name = props.data.name;
        form.meta_login = props.data.meta_login;
        selectedProducts.value = props.data.products || [];
        form.remarks = null;
    }
});

// Sync back to parent when closed
watch(visibleDialog, (val) => {
    if (!val) emit('update:visible', false);
});

// Clear date
const clearDate = () => {
    expiredDate.value = null;
};

// Submit form for either approve or reject
const submitForm = (action) => {

    form.id = props.data.id;
    form.product_ids = selectedProducts.value.map(p => p.value);
    form.action = action;

    if (action === 'approve') {
        if (expiredDate.value) {
            form.expired_date = dayjs(expiredDate.value).format('YYYY-MM-DD');
        }
    } else {
        form.expired_date = null;
    }

    form.post(route('pending.handleRedemptionCodeRequest'), {
        preserveScroll: true,
        onSuccess: () => {
            visibleDialog.value = false;
            form.reset();
        }
    });
};
</script>

<template>
    <Dialog
        v-model:visible="visibleDialog"
        modal
        :header="$t('public.review_pending_request')"
        class="dialog-xs md:dialog-md"
    >
        <div class="flex flex-col gap-4">
            <div class="grid justify-center items-start content-start gap-3 self-stretch flex-wrap grid-cols-1 md:grid-cols-2 md:gap-5">
                <!-- Name -->
                <div class="flex flex-col items-start gap-1 flex-1">
                    <InputLabel for="name" :value="$t('public.name')" :invalid="!!form.errors.name" />
                    <InputText
                        v-model="form.name"
                        id="name"
                        type="text"
                        :placeholder="$t('public.enter_name')"
                        class="w-full"
                        :invalid="!!form.errors.name"
                    />
                    <InputError :message="form.errors.name" />
                </div>

                <!-- Meta Login -->
                <div class="flex flex-col items-start gap-1 flex-1">
                    <InputLabel for="meta_login" :value="$t('public.meta_login')" :invalid="!!form.errors.meta_login" />
                    <InputText
                        v-model="form.meta_login"
                        id="meta_login"
                        type="text"
                        :placeholder="$t('public.enter_meta_login')"
                        class="w-full"
                        :invalid="!!form.errors.meta_login"
                    />
                    <InputError :message="form.errors.meta_login" />
                </div>

                <!-- Products -->
                <div class="flex flex-col items-start gap-1 flex-1">
                    <InputLabel for="product_ids" :value="$t('public.products')" :invalid="!!form.errors.product_ids" />
                    <MultiSelect
                        v-model="selectedProducts"
                        :options="props.products"
                        class="w-full"
                        :placeholder="$t('public.select_product')"
                        :maxSelectedLabels="1"
                        :selectedItemsLabel="`${selectedProducts?.length} ${$t('public.products_selected')}`"
                        :disabled="props.isLoading"
                        :invalid="!!form.errors.product_ids"
                    >
                        <template #option="{ option }">
                            <span>{{ option.label }}</span>
                        </template>
                        <template #value>
                            <span v-if="selectedProducts?.length === 1">{{ selectedProducts[0].label }}</span>
                            <span v-else-if="selectedProducts?.length > 1">{{ selectedProducts.length }} {{ $t('public.products_selected') }}</span>
                            <span v-else class="text-gray-400">{{ $t('public.select_product') }}</span>
                        </template>
                    </MultiSelect>
                    <InputError :message="form.errors.product_ids" />
                </div>

                <!-- Expired Date -->
                <div class="flex flex-col items-start gap-1">
                    <InputLabel for="expired_date" :value="$t('public.expired_date')" :invalid="!!form.errors.expired_date" />
                    <div class="relative w-full">
                        <DatePicker
                            v-model="expiredDate"
                            selectionMode="single"
                            :manualInput="false"
                            :minDate="today"
                            dateFormat="dd/mm/yy"
                            showIcon
                            iconDisplay="input"
                            placeholder="yyyy/mm/dd"
                            class="w-full"
                            :invalid="!!form.errors.expired_date"
                        />
                        <div
                            v-if="expiredDate"
                            class="absolute top-2/4 -mt-[60px] right-8 text-gray-400 select-none cursor-pointer bg-white"
                            @click="clearDate"
                        >
                            <IconX size="20" />
                        </div>
                    </div>
                    <InputError :message="form.errors.expired_date" />
                </div>

                <!-- Remarks -->
                <div class="flex flex-col items-start gap-1 md:col-span-2">
                    <InputLabel for="remarks" :invalid="!!form.errors.remarks">{{ $t('public.remarks') }}</InputLabel>
                    <Textarea
                        id="remarks"
                        v-model="form.remarks"
                        :placeholder="$t('public.enter_remarks')"
                        :invalid="!!form.errors.remarks"
                        rows="5"
                        class="w-full"
                    />
                    <InputError :message="form.errors.remarks" />
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="flex justify-end items-center pt-5 gap-4 self-stretch sm:pt-7">
                <Button
                    type="button"
                    severity="danger"
                    class="w-full md:w-[120px]"
                    :disabled="form.processing"
                    @click="submitForm('reject')"
                >
                    {{ $t('public.reject') }}
                </Button>
                <Button
                    type="button"
                    severity="success"
                    class="w-full md:w-[120px]"
                    :disabled="form.processing"
                    @click="submitForm('approve')"
                >
                    {{ $t('public.approve') }}
                </Button>
            </div>
        </div>
    </Dialog>
</template>
