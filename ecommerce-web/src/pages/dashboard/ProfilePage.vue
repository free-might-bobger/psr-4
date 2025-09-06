<template>
  <div class="q-px-sm">
    <p class="q-my-md main-title">Profile</p>
    <q-form
      @submit.prevent="onSubmit"
      @reset="onReset"
      class="q-gutter-sm"
      ref="myForm"
    >
      <q-input
        v-model="profile.name"
        outlined
        dense
        label="Enter Name"
        :rules="[(val) => (val && val.length > 0) || 'Name is required.']"
        hide-bottom-space
      />
      <q-input
        v-model="profile.mobile"
        outlined
        dense
        label="Enter Mobile"
        :rules="[(val) => (val && val.length > 0) || 'Mobile is required.']"
      />
      <div class="flex justify-end">
        <q-btn label="Update" type="submit" color="primary" outline />
      </div>
    </q-form>
  </div>
</template>
<script setup lang="ts">
import { ref } from 'vue';
import { useUserStore } from 'src/stores/user';
import { storeToRefs } from 'pinia';
import { update } from 'src/boot/axios-call';
import { useRoute } from 'vue-router';
import type { QForm } from 'quasar';

const route = useRoute()
const useUser = useUserStore();
const { profile } = storeToRefs(useUser);


const myForm = ref<QForm | null>(null);

const onSubmit = async () => {
  myForm.value?.validate().then(async (success: any) => {
    if (success) {
      await update({
          entity: 'profile',
          optimus_id: profile.value.optimus_id,
          data: {
            name: profile.value.name,
            mobile: profile.value.mobile
          }
        });
    }
  });
};

const onReset = () => {
  myForm.value?.resetValidation();
};

</script>
