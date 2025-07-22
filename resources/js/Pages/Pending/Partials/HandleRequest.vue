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
} from "primevue";
import {
    IconX,
} from "@tabler/icons-vue";
import dayjs from 'dayjs';

const props = defineProps({
  visible: Boolean,
  data: Object,
  products: Array,
});

const emit = defineEmits(['update:visible']);

const approvalAction = ref(null);
const visibleDialog = ref(false); // Local state mirror
const expiredDate = ref(null);

// Get current date
const today = new Date();

const clearDate = () => {
    expiredDate.value = null;
};

const form = useForm({
  id: null,
  action: null,
  remarks: null,
  expired_date: null,
});

// Sync local state with parent
watch(() => props.visible, (val) => {
    visibleDialog.value = val;
    if (!val || !props.data) return;

    approvalAction.value = null;
    form.remarks = null;
    form.expired_date = null;
  }
);

// Emit back to parent if closed from inside
watch(visibleDialog, (val) => {
  if (!val) emit('update:visible', false);
});

const closeDialog = () => {
  visibleDialog.value = false; // This triggers the watcher above
  form.reset();
  approvalAction.value = null;
};

const handleApproval = (action) => {
    if (action) {
        approvalAction.value = action;
    } else {
        approvalAction.value = null;
        expiredDate.value = null;
        form.reset();
        form.clearErrors();
    }
};

const submitForm = () => {
    if (approvalAction.value === 'approve' && (!form.remarks || form.remarks.trim() === '')) {
        form.remarks = 'Request approved.';
    }

    // Only assign expired_date to the form if a valid date is selected
    if (expiredDate.value) {
        form.expired_date = dayjs(expiredDate.value).format('YYYY-MM-DD');
    }

    form.id = props.data.id;
    form.action = approvalAction.value;

    form.post(route('pending.handleRedemptionCodeRequest'), {
      preserveScroll: true,
      onSuccess: () => closeDialog(),
    });
};
</script>

<template>
    <Dialog
        v-model:visible="visibleDialog"
        modal
        :header="approvalAction === 'approve' ? $t('public.approve_pending_request') : approvalAction === 'reject' ? $t('public.reject_pending_request') : $t('public.review_pending_request')"
        class="dialog-xs md:dialog-md"
    >
        <template v-if="!approvalAction">
            <div class="flex flex-col items-center gap-8 self-stretch">
                <div class="flex flex-col items-center gap-3 self-stretch">
                    <div class="w-full flex flex-col gap-1">
                        <div class="grid justify-center items-start content-start gap-3 self-stretch flex-wrap grid-cols-1 md:grid-cols-2 md:gap-5">
                            <div class="flex flex-col items-start gap-1 flex-1">
                                <InputLabel for="name" :invalid="!!form.errors.name" >{{ $t('public.name') }}</InputLabel>
                                <span class="text-sm font-medium">{{ props.data?.name }}</span>
                            </div>
                            <div class="flex flex-col items-start gap-1 flex-1">
                                <InputLabel for="meta_login" :invalid="!!form.errors.meta_login" >{{ $t('public.meta_login') }}</InputLabel>
                                <span class="text-sm font-medium">{{ props.data?.meta_login }}</span>
                            </div>
                            <div class="flex flex-col items-start gap-1 flex-1 md:col-span-2">
                                <InputLabel for="product_ids" :invalid="!!form.errors.product_ids">{{ $t('public.products') }}</InputLabel>
                                <span class="text-sm font-medium">{{ props.data?.products?.map(p => p.label).join(', ') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <template v-else>
            <div class="flex flex-col gap-4">
                <div class="flex flex-col items-center self-stretch gap-2">
                    <div class="text-gray-500 text-sm">
                        {{ $t('public.redemption_code_request_caption_1') }}
                        <span class="font-semibold lowercase" :class="[approvalAction === 'approve' ? 'text-green-500' : 'text-red-500']">{{ $t(`public.${approvalAction}`) }}</span>
                        {{ $t('public.redemption_code_request_caption_2') }}
                    </div>
                </div>

                <div class="flex flex-col items-start gap-1">
                    <InputLabel v-if="approvalAction === 'approve'" for="remarks">{{ $t('public.remarks') }}</InputLabel>
                    <InputLabel v-else for="remarks" :value="$t('public.remarks')" />
                    <Textarea
                        id="remarks"
                        type="text"
                        class="flex flex-1 self-stretch"
                        v-model="form.remarks"
                        :placeholder="approvalAction === 'approve' ? 'Request approved' : $t('public.enter_remarks')"
                        :invalid="!!form.errors.remarks"
                        rows="5"
                        cols="30"
                    />
                    <InputError :message="form.errors.remarks" />
                </div>

                <div v-if="approvalAction === 'approve'" class="flex flex-col items-start gap-1">
                    <InputLabel for="expired_date" :invalid="!!form.errors.expired_date">{{ $t('public.expired_date') }}</InputLabel>
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
                    <InputError :message="form.errors.expired_date" />
                </div>

            </div>

        </template>


        <div class="flex justify-end items-center pt-5 gap-4 self-stretch sm:pt-7">
            <template v-if="!approvalAction">
                <Button
                    type="button"
                    severity="danger"
                    class="w-full md:w-[120px]"
                    @click="handleApproval('reject')"
                    :disabled="form.processing"
                >
                    {{ $t('public.reject') }}
                </Button>
                <Button
                    type="button"
                    severity="success"
                    class="w-full md:w-[120px]"
                    @click="handleApproval('approve')"
                    :disabled="form.processing"
                >
                    {{ $t('public.approve') }}
                </Button>
            </template>
            <template v-else>
                <Button
                    type="button"
                    severity="secondary"
                    class="w-full md:w-[120px]"
                    @click="handleApproval()"
                >
                    {{ $t('public.back') }}
                </Button>
                <Button
                    type="button"
                    :severity="approvalAction === 'approve' ? 'success' : 'danger'"
                    class="w-full md:w-[120px]"
                    @click="submitForm()"
                    :disabled="form.processing"
                >
                    {{ $t('public.' + approvalAction) }}
                </Button>
            </template>
        </div>

    </Dialog>
</template>