<script setup>
import {onMounted, ref, watch, watchEffect} from "vue";
import { useForm} from "@inertiajs/vue3";
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
    InputText,
    MultiSelect,
    Dialog,
} from "primevue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    pendingRequests: Object,
    products: Array,
    isLoading: Boolean,
})

const products = ref(props.products);
const selectedProducts = ref();
const visible = ref(false);

const form = useForm({
    id: null,
    name: null,
    meta_login: null,
    product_ids: [],
});

// dialog
const data = ref({});
const openDialog = (rowData) => {
    form.reset();
    visible.value = true;
    data.value = rowData;

    form.id = rowData.id;
    form.name = rowData.name;
    form.meta_login = rowData.meta_login;
    selectedProducts.value = rowData.products;
};

const closeDialog = () => {
    visible.value = false;
    form.reset();
};

const submitForm = () => {
    form.id = data.value.id;
    form.product_ids = selectedProducts.value.map(product => product.value);

    // Otherwise it's update
    form.post(route('pending.updateRedemptionRequest'), {
        preserveScroll: true,
        onSuccess: () => {
            closeDialog();
        }
    });
};

</script>

<template>
    <Button
        type="button"
        severity="secondary"
        variant="text"
        rounded
        icon="IconAdjustmentsHorizontal"
        @click="openDialog(props.pendingRequests)"
        :disabled="props.isLoading"
    >
        <IconAdjustmentsHorizontal size="16" stroke-width="1.25" color="#667085" />
    </Button>


    <Dialog
        v-model:visible="visible"
        modal
        :header="$t('public.edit_redemption_code_request')"
        class="dialog-xs md:dialog-md"
    >
        <form @submit.prevent="submitForm()">
            <div class="flex flex-col items-center gap-8 self-stretch">
                <div class="flex flex-col items-center gap-3 self-stretch">
                    <div class="w-full flex flex-col gap-1">
                        <div class="grid justify-center items-start content-start gap-3 self-stretch flex-wrap grid-cols-1 md:grid-cols-2 md:gap-5">
                            <div class="flex flex-col items-start gap-1 flex-1">
                                <InputLabel for="name" :value="$t('public.name')" :invalid="!!form.errors.name" />
                                <InputText
                                    v-model="form.name"
                                    id="name"
                                    type="text"
                                    :placeholder="$t('public.enter_name')"
                                    class="w-full"
                                    :invalid="form.errors.name"
                                />
                                <InputError :message="form.errors.name" />
                            </div>
                            <div class="flex flex-col items-start gap-1 flex-1">
                                <InputLabel for="meta_login" :value="$t('public.meta_login')" :invalid="!!form.errors.meta_login" />
                                <InputText
                                    v-model="form.meta_login"
                                    id="meta_login"
                                    type="text"
                                    :placeholder="$t('public.enter_meta_login')"
                                    class="w-full"
                                    :invalid="form.errors.meta_login"
                                />
                                <InputError :message="form.errors.meta_login" />
                            </div>
                            <div class="flex flex-col items-start gap-1 flex-1">
                                <InputLabel for="product_ids" :value="$t('public.products')" :invalid="!!form.errors.product_ids" />
                                <MultiSelect
                                    v-model="selectedProducts"
                                    :options="products"
                                    class="w-full"
                                    :placeholder="$t('public.select_product')"
                                    :maxSelectedLabels="1"
                                    :selectedItemsLabel="`${selectedProducts?.length} ${$t('public.products_selected')}`"
                                    :disabled="props.isLoading"
                                    :invalid="form.errors.product_ids"
                                >
                                    <template #option="{option}">
                                        <div class="flex flex-col">
                                            <span>{{ option.label }}</span>
                                        </div>
                                    </template>
                                    <template #value>
                                        <div v-if="selectedProducts?.length === 1">
                                            <span>{{ selectedProducts[0].label }}</span>
                                        </div>
                                        <span v-else-if="selectedProducts?.length > 1">
                                            {{ selectedProducts?.length }} {{ $t('public.products_selected') }}
                                        </span>
                                        <span v-else class="text-gray-400">
                                            {{ $t('public.select_product') }}
                                        </span>
                                    </template>
                                </MultiSelect>
                                <InputError :message="form.errors.product_ids" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="pt-5 md:pt-7 flex flex-col items-end self-stretch">
                <Button
                    type="submit"
                    severity="primary"
                    :disabled="form.processing || props.isLoading"
                >
                    {{ $t('public.save') }}
                </Button>
            </div>
        </form>
    </Dialog>
</template>