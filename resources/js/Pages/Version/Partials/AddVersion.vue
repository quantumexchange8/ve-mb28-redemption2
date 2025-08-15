<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3';
import {
    Dialog,
    Button,
    InputText,
    Textarea,
    Select,
} from "primevue";
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';

defineProps({
    licenses: Array
})

const visible = ref(false)

const form = useForm({
  product_id: null,
  version: null,
  remarks: null,
  download_url: null,
})

const openDialog = () => {
  form.reset()
  visible.value = true
}

const closeDialog = () => {
  visible.value = false
  form.reset()
}

const submitForm = () => {
    form.post(route('version.addVersion'), {
        onSuccess: () => {
            closeDialog();
        },
        onError: (errors) => {
            // console.log(errors)
        }
    })
}
</script>

<template>
    <Button
        type="button"
        variant="primary-flat"
        size="base"
        class="w-full md:w-auto"
        @click="openDialog()"
    >
        {{ $t('public.add_new_version') }}
    </Button>

    <Dialog 
        v-model:visible="visible" 
        modal 
        :header="$t('public.add_new_version')" 
        class="dialog-xs md:dialog-md"
        @hide="closeDialog"
    >
        <form @submit.prevent="submitForm()">

            <div class="flex flex-col items-center gap-3 self-stretch">

                <div class="w-full flex flex-col gap-1">
                    <InputLabel for="product" :value="$t('public.select_product')" :invalid="!!form.errors.product_id" />
                    <Select
                        id="product"
                        v-model="form.product_id"
                        :options="licenses"
                        optionLabel="name"
                        optionValue="value"
                        placeholder="Select product"
                        class="w-full"
                        :invalid="!!form.errors.product_id"
                    />
                    <InputError :message="form.errors.product_id" />
                </div>

                <div class="w-full flex flex-col gap-1">
                    <InputLabel for="version" :value="$t('public.version')" :invalid="!!form.errors.version" />
                    <InputText id="version" v-model="form.version" class="w-full" :invalid="!!form.errors.version" />
                    <InputError :message="form.errors.version" />
                </div>

                <div class="w-full flex flex-col gap-1">
                    <InputLabel for="remarks" :invalid="!!form.errors.remarks">{{ $t('public.remarks') }}</InputLabel>
                    <Textarea id="remarks" v-model="form.remarks" rows="5" class="w-full" style="resize: none"  :invalid="!!form.errors.remarks" />
                    <InputError :message="form.errors.remarks" />
                </div>

                <div class="w-full flex flex-col gap-1">
                    <InputLabel for="download_url" :value="$t('public.download_url')" :invalid="!!form.errors.download_url" />
                    <InputText id="download_url" v-model="form.download_url" class="w-full" :invalid="!!form.errors.download_url" />
                    <InputError :message="form.errors.download_url" />
                </div>
            </div>

            <div class="pt-5 md:pt-7 flex flex-col items-end self-stretch">
                <Button
                    type="button"
                    variant="primary-flat"
                    :disabled="form.processing"
                    @click="submitForm"
                >
                    {{ $t('public.save') }}
                </Button>
            </div>
        </form>
    </Dialog>
</template>
