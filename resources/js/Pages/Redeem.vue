<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import { InputText, Button, DatePicker } from 'primevue';

const form = useForm({
    redemption_code: '',
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

</script>

<template>
    <AuthenticatedLayout title="Code Redemption">
        <div class="flex flex-col items-center justify-center p-5">
            <div class="flex flex-col justify-center gap-5">
                <div class="flex flex-col justify-center gap-1">
                    <span class="font-semibold text-lg">Redemption</span>
                    <span class="font-medium">Enter your redemption code and email below to redeem it.</span>
                </div>
                <div class="flex flex-col justify-center gap-3">
                    <span class="font-medium">Code Redemption Form</span>

                    <form @submit.prevent="submitForm" novalidate class="flex flex-col justify-center gap-3">
                        <!-- Redemption Code-->
                        <div class="flex flex-col justify-center gap-1">
                            <InputLabel for="redemption_code" value="Redemption Code" :invalid="!!form.errors.redemption_code" />
                            <InputText
                                id="redemption_code"
                                v-model="form.redemption_code"
                                placeholder="Enter your redemption code"
                                class="w-full"
                                :invalid="!!form.errors.redemption_code"
                            />
                            <InputError :message="form.errors.redemption_code" />
                        </div>

                        <!-- Email-->
                        <div class="flex flex-col justify-center gap-1">
                            <InputLabel for="email" value="Email" :invalid="!!form.errors.email" />
                            <InputText
                                id="email"
                                v-model="form.email"
                                placeholder="Enter your email"
                                class="w-full"
                                :invalid="!!form.errors.email"
                            />
                            <InputError :message="form.errors.email" />
                        </div>

                        <!-- Expired Date -->
                        <div class="flex flex-col justify-center gap-1">
                            <InputLabel for="expired_date" value="Expired Date" :invalid="!!form.errors.expired_date" />
                            <DatePicker
                                id="expired_date"
                                v-model="form.expired_date"
                                selectionMode="single"
                                :minDate="minDate"
                                :manualInput="false"
                                placeholder="Select expiry date"
                                class="w-full"
                                dateFormat="yy-mm-dd"
                                :invalid="!!form.errors.expired_date"
                            />
                            <InputError :message="form.errors.expired_date" />
                        </div>

                        <!-- Meta Login -->
                        <div class="flex flex-col justify-center gap-1">
                            <InputLabel for="meta_login" :invalid="!!form.errors.meta_login">Meta Login</InputLabel>
                            <InputText
                                id="meta_login"
                                v-model="form.meta_login"
                                placeholder="Enter your Meta Login"
                                class="w-full"
                                :invalid="!!form.errors.meta_login"
                            />
                            <InputError :message="form.errors.meta_login" />
                        </div>

                        <!-- Submit Button -->
                        <Button
                            label="Submit"
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
