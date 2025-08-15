<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Dialog, Button, InputText, Textarea } from 'primevue'
import InputLabel from '@/Components/InputLabel.vue'
import InputError from '@/Components/InputError.vue'
import { IconEdit } from '@tabler/icons-vue'

const props = defineProps({
    data: Object,
})

const visible = ref(false)

const form = useForm({
  id: null,
  version: null,
  remarks: null,
  download_url: null,
})

const openDialog = () => {
  // assign the data directly
  form.id = props.data?.id
  form.version = props.data?.version
  form.remarks = props.data?.remarks
  form.download_url = props.data?.download_url

  visible.value = true
}

const closeDialog = () => {
  visible.value = false
  form.reset()
}

const submitForm = () => {
  form.post(route('version.updateVersion'), {
      onSuccess: () => closeDialog(),
    //   onError: (errors) => console.log(errors)
  })
}
</script>

<template>
  <div>
    <Button
      type="button"
      rounded
      severity="secondary"
      icon="IconDownload"
      @click="openDialog"
    >
        <IconEdit class="w-5 h-5" />
    </Button>

    <Dialog 
      v-model:visible="visible" 
      modal 
      :header="$t('public.edit_version')" 
      class="dialog-xs md:dialog-md"
      @hide="closeDialog"
    >
        <form @submit.prevent="submitForm">
            <div class="flex flex-col gap-3">
                <div class="flex flex-col gap-1 w-full">
                    <InputLabel :value="$t('public.version')" :invalid="!!form.errors.version" />
                    <InputText v-model="form.version" class="w-full" :invalid="!!form.errors.version" />
                    <InputError :message="form.errors.version" />
                </div>

                <div class="flex flex-col gap-1 w-full">
                    <InputLabel :invalid="!!form.errors.remarks">{{ $t('public.remarks') }}</InputLabel>
                    <Textarea v-model="form.remarks" datas="5" class="w-full" style="resize: none" :invalid="!!form.errors.remarks" />
                    <InputError :message="form.errors.remarks" />
                </div>

                <div class="flex flex-col gap-1 w-full">
                    <InputLabel :value="$t('public.download_url')" :invalid="!!form.errors.download_url" />
                    <InputText v-model="form.download_url" class="w-full" :invalid="!!form.errors.download_url" />
                    <InputError :message="form.errors.download_url" />
                </div>
            </div>

            <div class="mt-4 flex justify-end gap-2">
                <Button label="Cancel" severity="secondary" @click="closeDialog" />
                <Button label="Save" severity="primary" :disabled="form.processing" @click="submitForm" />
            </div>
        </form>
    </Dialog>
  </div>
</template>
