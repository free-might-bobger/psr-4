<template>
  <div class="edit-store-page">
    <!-- Header Section -->
    <q-card flat bordered class="page-header-card q-mb-lg">
      <q-card-section class="header-section">
        <div class="header-content">
          <div class="header-left">
            <div class="header-icon-wrapper">
              <q-icon name="store" size="40px" color="primary" />
            </div>
            <div class="header-text">
              <h1 class="page-title">Edit Store</h1>
              <p class="page-subtitle">Update store information and location</p>
            </div>
          </div>
          <div class="header-actions">
            <q-btn 
              unelevated
              color="primary"
              label="View Items"
              icon="inventory_2"
              :to="`${$route.path}/items`"
              class="view-items-btn"
            >
              <q-tooltip>View store items</q-tooltip>
            </q-btn>
          </div>
        </div>
      </q-card-section>
    </q-card>

    <!-- Form Section -->
    <StoreEditForm 
      :store="store" 
      :is-submitting="isSubmitting" 
      @submit="onSubmit" 
      @cancel="$router.back()" 
    />
  </div>
</template>
  
<script lang="ts" setup>
import { ref, onBeforeMount } from 'vue';
import { update, show } from 'src/boot/axios-call';
import { useRoute } from "vue-router";
import StoreEditForm from 'src/components/StoreEditForm.vue';

interface StoreData {
  name: string;
  desc: string;
  mobile: string;
  latitude: number;
  longitude: number;
  optimus_id: number;
}

const route = useRoute();
const store = ref<StoreData>({
  name: '',
  desc: '',
  mobile: '',
  latitude: 14.5995,
  longitude: 120.9842,
  optimus_id: 0,
});

const isSubmitting = ref(false);

const onSubmit = async (data: StoreData) => {
  isSubmitting.value = true;
  try {
    await update(
      {
        entity: 'all_stores',
        optimus_id: route.params.id, 
        data: {
          name: data.name,
          desc: data.desc,
          mobile: data.mobile,
          latitude: data.latitude,
          longitude: data.longitude,
        },
      },
      true
    );
  } catch (error) {
    console.error('Error updating store:', error);
  } finally {
    isSubmitting.value = false;
  }
};

onBeforeMount(async () => {
  const result = await show({
    entity: 'all_stores',
    optimus_id: Number(route.params.id),
    query: {
      show_mobile: 1
    },
  }) as StoreData;
    
  store.value.name = result.name || '';
  store.value.desc = result.desc || '';
  store.value.mobile = result.mobile || '';
  store.value.latitude = result.latitude || 14.5995;
  store.value.longitude = result.longitude || 120.9842;
  store.value.optimus_id = result.optimus_id || 0;
});
</script>

<style scoped lang="scss">
@import 'src/css/dashboard/all-stores/edit.scss';
</style>
  