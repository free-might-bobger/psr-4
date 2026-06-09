<template>
  <EditItemComponent
    :store-id="Number(route.params.id)"
    :item-id="Number(route.params.itemId)"
    @submit="handleSubmit"
    @cancel="handleCancel"
  />
</template>

<script setup lang="ts">
import { useRoute, useRouter } from 'vue-router';
import EditItemComponent from 'src/components/EditItemComponent.vue';

const route = useRoute();
const router = useRouter();

const handleSubmit = () => {
  // Handle successful submission
  // The component already shows a notification
};

const handleCancel = () => {
  // Handle cancel
  // The component already navigates back
};
</script>
