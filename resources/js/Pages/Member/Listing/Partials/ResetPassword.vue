<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { useForm } from "@inertiajs/vue3";
import {
    Password,
    Button,
    Avatar,
} from "primevue";
import { generalFormat } from "@/Composables/format.js";
import { ref } from 'vue';
import toast from '@/Composables/toast';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
    member: Object,
})

const emit = defineEmits(['update:visible'])

const form = useForm({
    id: props.member.id,
    password: '',
    password_confirmation: '',
})

const closeDialog = () => {
    emit('update:visible', false);
}

const submitForm = () => {
    form.post(route('member.resetPassword'), {
        onSuccess: () => {
            closeDialog();
            form.reset();
        },
    });
}

// Shows toast without triggering page refresh
// const formProcessing = ref(false);

// const submitForm = async () => {
//     formProcessing.value = true;
//     form.errors = {};

//     try {
//         const response = await axios.post(route('member.resetPassword'), form.value);

//         closeDialog();
        
//         toast.add({
//             type: 'success',
//             title: trans('public.success'),
//             message: response.data.message,
//         });
//     } catch (error) {
//         if (error.response?.status === 422) {
//             form.value.errors = error.response.data.errors;
//         } else {
//             closeDialog();

//             const message = error.response?.data?.message || error.message || 'Something went wrong.';
//             toast.add({
//                 type: 'error',
//                 title: message,
//             });
//         }
//     } finally {
//         formProcessing.value = false;
//     }
// }

</script>

<template>
    <form class="flex flex-col gap-5 items-center self-stretch">
        <div class="flex flex-col gap-5 items-center self-stretch">
            <div class="flex items-center gap-3 self-stretch">
                <div class="flex flex-col items-start">
                    <div class="text-sm font-medium">
                        {{ props.member.name }}
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3 self-stretch">
                <div class="flex flex-col gap-1 items-start self-stretch">
                    <InputLabel for="password" :value="$t('public.password')" :invalid="!!form.errors.password" />
                    <Password
                        v-model="form.password"
                        toggleMask
                        :feedback="false"
                        placeholder="••••••••"
                        :invalid="!!form?.errors?.password"
                        class="block w-full"
                        :inputStyle="{'width': '100%'}"
                        :style="{'width': '100%'}"
                    />
                    <InputError :message="form?.errors?.password" />
                    <span class="self-stretch text-surface-500 text-xs">{{ $t('public.password_desc') }}</span>
                </div>
    
                <div class="flex flex-col gap-1 items-start self-stretch">
                    <InputLabel for="password_confirmation" :value="$t('public.confirm_password')" :invalid="!!form?.errors?.password_confirmation" />
                    <Password
                        v-model="form.password_confirmation"
                        toggleMask
                        :feedback="false"
                        placeholder="••••••••"
                        :invalid="!!form?.errors?.password_confirmation"
                        class="block w-full"
                        :inputStyle="{'width': '100%'}"
                        :style="{'width': '100%'}"
                    />
                    <InputError :message="form?.errors?.password_confirmation" />
                </div>
            </div>
        </div>

        <div class="flex gap-3 justify-end self-stretch w-full">
            <Button
                severity="secondary"
                text
                type="button"
                class="w-full md:w-fit"
                :disabled="form.processing"
                @click.prevent="closeDialog"
            >
                {{ $t('public.cancel') }}
            </Button>
            <Button
                class="w-full md:w-fit"
                :disabled="form.processing"
                @click.prevent="submitForm"
            >
                {{ $t('public.confirm') }}
            </Button>
        </div>
    </form>
</template>
