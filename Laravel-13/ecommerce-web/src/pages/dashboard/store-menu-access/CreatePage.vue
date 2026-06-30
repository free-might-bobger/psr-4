<template>
  <div class="q-ma-sm">
    <p class="q-my-md" style="font-size: 22px">{{ store.name }} </p>
    <FormAccess :storeMenuAccess="storeMenuAccess" @submit="submit" ref="myForm"/>
  </div>
</template>

<script lang="ts" setup>
import { onMounted, ref} from 'vue';
import { show, create} from 'src/boot/axios-call';
import { useRoute } from 'vue-router';
import FormAccess from './FormAccess.vue';

const route = useRoute();

const storeMenuAccess = ref({
  access_right: null,
  store_menu: null,
});

const myForm = ref(null)
const submit = async (data: object) => {
 await create({
    entity: 'store-menu-access',
    data: data
  })
myForm.value?.resetValidation()
};

onMounted(() => {
  getStore();
});

const store = ref({
  name: ''
});
const getStore = async () => {
  store.value = await show({
    entity: 'my-stores',
    optimus_id: Number(route.params.storeId),
  }, true);
};

</script>
