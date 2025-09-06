<template>
    <q-dialog v-model="localStatus" persistent>
      <q-card square class="shadow-24 rounded-borders" style="width: 325px">
        <q-toolbar style="height: 80px" class="bg-primary">
          <q-toolbar-title class="text-white text-center">Password Recovery</q-toolbar-title>
          <q-btn flat round dense icon="close" class="text-white" v-close-popup />
        </q-toolbar>
        <q-separator />
        <q-card-section>
          <q-form @submit="submit">
            <br />
            <q-input square clearable v-model="user.email" label="Email">
              <template v-slot:prepend>
                <q-icon name="fa-solid fa-mobile-screen" />
              </template>
            </q-input>
            <br />
            <br />
            <q-btn
              glossy
              unelevated
              type="submit"
              color="secondary"
              class="full-width text-white q-mt-md no-border-radius"
              label="Submit"
            />
          </q-form>
         
        </q-card-section>
      </q-card>
    </q-dialog>
  </template>
  <script setup lang="ts">
  import { computed } from 'vue';
  import { useUserStore } from 'src/stores/user';
  import { storeToRefs } from 'pinia';
  import { recoverPassword } from 'boot/axios-call';

  const { user } = storeToRefs( useUserStore() );
  const props = defineProps({
    status: Boolean,
  });
  
  const emit = defineEmits(['changeStatus', 'createAccount']);
  
  const localStatus = computed({
    get: () => props.status,
    set: (v: boolean) => emit('changeStatus', v),
  });
  
  
  const submit = async () => {
    const result = await recoverPassword({
      'email' : user.value?.email
    });
    if(result){
      emit('changeStatus', false)
    }
  };
  </script>
  