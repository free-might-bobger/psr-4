<template>
  <div class="q-ma-sm">
    <p class="q-my-md" style="font-size: 22px">{{ store.name }}</p>
    <FormAccess :storeMenuAccess="storeMenuAccess" @submit="submit" btnLabel="Update"/>
  </div>
</template>

<script lang="ts" setup>
import { onMounted, Ref, ref } from 'vue';
import { show, update } from 'src/boot/axios-call';
import { useRoute } from 'vue-router';
import FormAccess from './FormAccess.vue';
import {
  AccessRightInterface,
  StoreMenuAccessInterface,
  StoreMenuInterface,
} from 'src/boot/interfaces';

const route = useRoute();

const storeMenuAccess: Ref<{
  access_right: AccessRightInterface | null;
  store_menu: StoreMenuInterface | null;
}> = ref({
  access_right: null,
  store_menu: null,
});

const submit = async (data: object) => {
  await update({
    entity: 'store-menu-access',
    optimus_id: route.params.id,
    data: data,
  });
};

onMounted(async () => {
  await getStore();
});

const store = ref({
  name: '',
});
const getStore = async () => {
  const result = await show<StoreMenuAccessInterface>({
    entity: 'store-menu-access',
    optimus_id: Number(route.params.id),
    query: {
      with: 'store,accessRight,storeMenu',
    },
  }, true);

  store.value = result.store;
  storeMenuAccess.value.access_right = result.access_right;
  storeMenuAccess.value.store_menu = result.store_menu;
};

</script>
