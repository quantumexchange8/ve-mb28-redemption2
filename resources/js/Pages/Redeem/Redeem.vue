<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { InputText, Button, Select, DatePicker } from 'primevue';

const props = defineProps({
    products: Array,
})

const form = useForm({
    product: '',
    email: '',
    expired_date: null,
    meta_login: '',
})

const today = new Date();
const minDate = ref(new Date(today.getFullYear(), today.getMonth(), today.getDate() + 1)); 

function submitForm() {
  form.post('/redeem/redeemCode', {
    preserveScroll: true,
    onSuccess: () => form.reset(),
  });
}

const licenses = ref(props.products)


</script>

<template>
    <AuthenticatedLayout :title="$t('public.code_redemption')">
        <div class="flex flex-col items-center justify-center p-5">
            <div class="flex flex-col justify-center gap-5">
                <div class="flex flex-col justify-center gap-1">
                    <span class="font-semibold text-lg">{{ $t('public.redemption') }}</span>
                    <span class="font-medium">{{ $t('public.redemption_condition') }}</span>
                </div>
                <div class="flex flex-col justify-center gap-3">
                    <span class="font-medium">{{ $t('public.code_redemption') }}</span>

                    <form @submit.prevent="submitForm" novalidate class="flex flex-col justify-center gap-3">
                        <!-- Product -->
                        <div class="flex flex-col justify-center gap-1">
                            <!-- <InputLabel for="redemption_code" value="Redemption Code" :invalid="!!form.errors.redemption_code" />
                            <InputText
                                id="redemption_code"
                                v-model="form.redemption_code"
                                placeholder="Enter your redemption code"
                                class="w-full"
                                :invalid="!!form.errors.redemption_code"
                            />
                            <InputError :message="form.errors.redemption_code" /> -->

                            <InputLabel for="product" :value="$t('public.product')" :invalid="!!form.errors.product" />
                            <Select
                                id="product"
                                v-model="form.product"
                                :options="licenses"
                                optionLabel="label"
                                optionValue="value"
                                :placeholder="$t('public.select_product')"
                                class="w-full"
                                :invalid="!!form.errors.product"
                            />
                            <InputError :message="form.errors.product" />
                        </div>

                        <!-- Email-->
                        <div class="flex flex-col justify-center gap-1">
                            <InputLabel for="email" :value="$t('public.email')" :invalid="!!form.errors.email" />
                            <InputText
                                id="email"
                                v-model="form.email"
                                :placeholder="$t('public.enter_email')"
                                class="w-full"
                                :invalid="!!form.errors.email"
                            />
                            <InputError :message="form.errors.email" />
                        </div>

                        <!-- Expired Date -->
                        <div class="flex flex-col justify-center gap-1">
                            <InputLabel for="expired_date" :value="$t('public.expired_date')" :invalid="!!form.errors.expired_date" />
                            <DatePicker
                                id="expired_date"
                                v-model="form.expired_date"
                                selectionMode="single"
                                :minDate="minDate"
                                :manualInput="false"
                                :placeholder="$t('public.select_expiry_date')"
                                class="w-full"
                                dateFormat="yy-mm-dd"
                                :invalid="!!form.errors.expired_date"
                            />
                            <InputError :message="form.errors.expired_date" />
                        </div>

                        <!-- Meta Login -->
                        <div class="flex flex-col justify-center gap-1">
                            <InputLabel for="meta_login" :invalid="!!form.errors.meta_login">{{ $t('public.meta_login') }}</InputLabel>
                            <InputText
                                id="meta_login"
                                v-model="form.meta_login"
                                :placeholder="$t('public.enter_meta_login')"
                                class="w-full"
                                :invalid="!!form.errors.meta_login"
                            />
                            <InputError :message="form.errors.meta_login" />
                        </div>

                        <!-- Submit Button -->
                        <Button
                            :label="$t('public.submit')"
                            type="submit"
                            class="w-fit ml-auto"
                            :disabled="form.processing"
                        />
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
