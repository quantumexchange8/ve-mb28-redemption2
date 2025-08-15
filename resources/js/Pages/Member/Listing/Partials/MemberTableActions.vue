<script setup>
import {
    IconDotsVertical,
    IconId,
    IconArrowsRightLeft,
    IconLockCog,
} from "@tabler/icons-vue";
import {
    Button,
    Dialog,
    TieredMenu,
    ToggleSwitch,
    useConfirm,
} from "primevue";
import { computed, h, ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import { trans } from "laravel-vue-i18n";
import ResetPassword from "@/Pages/Member/Listing/Partials/ResetPassword.vue";
import toast from "@/Composables/toast";

const props = defineProps({
    member: Object,
})

const emit = defineEmits(['updated:member'])

const menu = ref();
const visible = ref(false)
const dialogType = ref('')

const items = ref([
    // {
    //     label: 'member_details',
    //     icon: h(IconId),
    //     command: () => {
    //         router.visit(route('member.detail', props.member.id_number));
    //     },
    // },
    // {
    //     label: 'access_portal',
    //     icon: h(IconDeviceLaptop),
    //     command: () => {
    //         window.open(route('member.access_portal', props.member.id), '_blank');
    //     },
    // },
    {
        label: 'reset_password',
        icon: h(IconLockCog),
        command: () => {
            visible.value = true;
            dialogType.value = 'reset_password';
        },
    },
    // {
    //     separator: true,
    // },
    // {
    //     label: 'delete_member',
    //     icon: h(IconTrash),
    //     command: () => {
    //         requireConfirmation('delete_member')
    //     },
    // },
]);

const checked = ref(props.member.status === 'active')

watch(() => props.member.status, (newStatus) => {
    checked.value = newStatus === 'active';
});

const confirm = useConfirm();

const updateStatus = async () => {
    const response = await axios.post(route('member.updateMemberStatus'), {
        id: props.member.id
    });

    emit('updated:member', response.data.user);

    toast.add({
        type: 'success',
        title: trans('public.success'),
        message: response.data.message,
    });
}

const requireConfirmation = (action_type) => {
    const messages = {
        deactivate_member: {
            group: 'headless-error',
            header: trans('public.deactivate_member'),
            text: trans('public.deactivate_member_caption'),
            cancelButton: trans('public.cancel'),
            acceptButton: trans('public.confirm'),
            action: () => {
                updateStatus();
                checked.value = !checked.value;
            }
        },
        activate_member: {
            group: 'headless-primary',
            header: trans('public.activate_member'),
            text: trans('public.activate_member_caption'),
            cancelButton: trans('public.cancel'),
            acceptButton: trans('public.confirm'),
            action: () => {
                updateStatus();
                checked.value = !checked.value;
            }
        },
        delete_member: {
            group: 'headless-error',
            header: trans('public.delete_member'),
            text: trans('public.delete_member_desc'),
            cancelButton: trans('public.cancel'),
            acceptButton: trans('public.delete_confirm'),
            action: () => {
                router.delete(route('member.deleteMember'), {
                    data: {
                        id: props.member.id,
                    },
                })
            }
        },
    };

    const { group, header, text, dynamicText, suffix, actionType, cancelButton, acceptButton, action } = messages[action_type];

    confirm.require({
        group,
        header,
        actionType,
        text,
        dynamicText,
        suffix,
        cancelButton,
        acceptButton,
        accept: action
    });
};

const toggle = (event) => {
    menu.value.toggle(event);
};

const handleMemberStatus = () => {
    if (props.member.status === 'active') {
        requireConfirmation('deactivate_member')
    } else {
        requireConfirmation('activate_member')
    }
}
</script>

<template>
    <div class="flex gap-3 items-center justify-center">
        <!-- <ToggleSwitch
            v-model="checked"
            @click.prevent="handleMemberStatus"
        /> -->
        <Button
            severity="secondary"
            text
            size="small"
            type="button"
            icon="IconDotsVertical"
            rounded
            @click="toggle"
            aria-haspopup="true"
            aria-controls="overlay_tmenu"
        >
            <IconDotsVertical size="16" stroke-width="1.25" class="text-surface-500 dark:text-white" />
        </Button>
        <TieredMenu ref="menu" id="overlay_tmenu" :model="items" popup>
            <template #item="{ item, props, hasSubmenu }">
                <div
                    class="flex items-center gap-3 self-stretch"
                    v-bind="props.action"
                >
                    <component :is="item.icon" size="20" stroke-width="1.25" :color="item.label === 'delete_member' ? '#F04438' : '#667085'" />
                    <span class="font-medium" :class="{'text-red-500': item.label === 'delete_member'}">{{ $t(`public.${item.label}`) }}</span>
                </div>
            </template>
        </TieredMenu>
    </div>

    <Dialog
        v-model:visible="visible"
        modal
        :header="$t(`public.${dialogType}`)"
        class="dialog-xs sm:dialog-sm"
    >
        <template v-if="dialogType === 'reset_password'">
            <ResetPassword
                :member="member"
                @update:visible="visible = false"
            />
        </template>
    </Dialog>
</template>
