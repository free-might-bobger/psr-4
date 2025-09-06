<template>
  <q-form
    @submit.prevent="onSubmit"
    class="q-gutter-sm"
    ref="myForm"
  >
    <q-select
      dense
      v-model="localStoreMenuAccess.access_right"
      :options="accessRights"
      label="Select Access Rights"
      hide-bottom-space
      use-input
      outlined
      use-chips
      :rules="[(val) => validateRequired(val) || 'Access Right is required.']"
    />

    <q-select
      dense
      v-model="localStoreMenuAccess.store_menu"
      :options="storeMenus"
      label="Select Store Menus"
      hide-bottom-space
      use-input
      outlined
      use-chips
      :rules="[(val) => validateRequired(val) || 'Store menu is required.']"
    />
    <div class="flex justify-end">
      <q-btn :label="btnLabel" type="submit" color="primary" outline />
    </div>
  </q-form>
</template>
<script lang="ts" setup>
import { computed, ref, onMounted, nextTick } from 'vue';
import { get } from 'src/boot/axios-call';
import type { QForm } from 'quasar';
import { useRoute } from 'vue-router';
import { validateRequired } from 'src/boot/validators';

onMounted(() => {
  listingApi();
});

const route = useRoute();
const props = withDefaults(defineProps<{
  storeMenuAccess: { access_right?: object | null; store_menu?: object | null };
  btnLabel?: string;
}>(), {
  btnLabel: 'Submit'
});

const emit = defineEmits(['change', 'submit']);

const localStoreMenuAccess = computed({
  get: () => props.storeMenuAccess,
  set: (v) => emit('change', v),
});

const accessRights = ref([]);
const storeMenus = ref([]);
const listingApi = async () => {
  const result = await get(
    {
      entity: 'listing_api',
      query: {
        listingApi: 'accessRights,storeMenus',
      },
    },
    false
  );
  accessRights.value = result.data.data.accessRights;
  storeMenus.value = result.data.data.storeMenus;
};

const myForm = ref<QForm | null>(null);
const onSubmit = async () => {
  myForm.value?.validate().then(async (success: any) => {
    if (success) {
      emit('submit', {
          access_right_id: localStoreMenuAccess.value?.access_right?.id,
          store_menu_id: localStoreMenuAccess.value?.store_menu?.id,
          store_id: Number(route.params.storeId),
          user_id: Number(route.params.userId)

        })
    }
  });
};

const resetValidation = async () => {
  localStoreMenuAccess.value.access_right = null
  localStoreMenuAccess.value.store_menu = null
  await nextTick()
  myForm.value?.resetValidation();
}

defineExpose({
  resetValidation
});


</script>
