<template>
  <q-dialog v-model="localStatus" persistent>
    <q-card square class="shadow-24 rounded-borders" style="width: 325px">
      <q-toolbar style="height: 80px" class="bg-primary">
        <q-toolbar-title class="text-white text-center" > New Account</q-toolbar-title>
        <q-btn flat round dense icon="close" class="text-white" v-close-popup />
      </q-toolbar>
      <q-separator />
      <q-card-section>
        <q-form @submit="create" ref="myForm">
          <br />
          <q-input
            square
            clearable
            label="Firstname"
            v-model="user.firstname"
            :rules="[
              (val) => (val && val.length > 0) || 'Firstname must be filled in.',
            ]"
            dense
          />
          <q-input
            square
            clearable
            label="Lastname"
            v-model="user.lastname"
            :rules="[
              (val) => (val && val.length > 0) || 'Lastname must be filled in.',
            ]"
            dense
          />
          <q-input
            square
            clearable
            v-model="user.email"
            label="Email"
            :rules="[async (val) => (await onValidateEmail(val)) || emailError]"
            debounce="500"
            dense
          />
          <q-input
            class="q-mb-lg"
            square
            clearable
            v-model="user.password"
            :type="typePassword"
            label="Password"
            bottom-slots
            :rules="[
              async (val) => (await onValidatePassword(val)) || passwordError,
            ]"
            dense
          >
            <template v-slot:before>
              <q-btn round dense flat :icon="eyeIcon" @click="toggleEye()">
                <q-tooltip v-if="eyeIcon == 'fas fa-eye-slash'">
                  Show Password
                </q-tooltip>
                <q-tooltip v-if="eyeIcon != 'fas fa-eye-slash'">
                  Hide Password
                </q-tooltip>
              </q-btn>
            </template>
          </q-input>
          <q-btn
            glossy
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
import { computed, ref, nextTick } from 'vue';
import { register } from 'boot/axios-call';

const user = ref({ password: '', email: '', firstname: '', lastname: '' });
const props = defineProps({
  status: Boolean,
});

const emit = defineEmits(['changeStatus']);

const localStatus = computed({
  get: () => props.status,
  set: (v) => emit('changeStatus', v),
});

const eyeIcon = ref('fas fa-eye-slash');
const typePassword = ref<string>('password');
const toggleEye = () => {
  if (eyeIcon.value == 'fas fa-eye-slash') {
    eyeIcon.value = 'fas fa-eye';
    typePassword.value = 'text';
  } else {
    typePassword.value = 'password';
    eyeIcon.value = 'fas fa-eye-slash';
  }
};

const myForm = ref(null)
const create = async () => {
  const result = await register(user.value);
  if(result){
    user.value = { password: '', email: '', firstname: '', lastname: '' }
    await nextTick()
        myForm.value?.resetValidation()
  }
};

const emailError = ref<string | null>();
const onValidateEmail = async (email: string): Promise<boolean> => {
  const result = await validateEmail(email);
  emailError.value = result.message;
  return result.status;
};

const passwordError = ref<string | null>();
const onValidatePassword = async (password: string): Promise<boolean> => {
  const result = await validatePassword(password);
  passwordError.value = result.message;
  return result.status;
};
</script>
