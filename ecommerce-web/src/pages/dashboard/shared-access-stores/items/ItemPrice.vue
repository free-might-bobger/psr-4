<template>
  <ItemPriceComponent
    :store-id="Number(route.params.id)"
    :item-id="Number(route.params.itemId)"
    @submit="handleSubmit"
    @cancel="handleCancel"
  />
</template>

<script setup lang="ts">
import { useRoute } from 'vue-router';
import ItemPriceComponent from 'src/components/ItemPriceComponent.vue';

const route = useRoute();

const handleSubmit = () => {
  // Handle successful submission
  // The component already shows a notification
};

const handleCancel = () => {
  // Handle cancel
  // The component already navigates back
};
</script>
