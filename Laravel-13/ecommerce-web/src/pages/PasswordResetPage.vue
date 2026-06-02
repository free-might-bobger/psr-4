<template>
  <div class="q-pa-md">
    <br />
    <br />
    <div class="flex justify-center">
      <div style="width: 450px">
        <p class="text-h5 text-primary"><strong>Password Recovery</strong></p>
        <q-banner
          class="bg-white"
          inline-actions
          rounded
          :class="textColor"
          dark
        >
          <q-icon :name="iconName" size="md"></q-icon>
          {{ message }}
        </q-banner>
        <br />
        <span v-if="isError === false">
          <q-form @submit.prevent="onSubmit" class="row" ref="myForm">
            <div class="col-12">
              <q-input
                outlined
                clearable
                v-model="password"
                :type="typePassword"
                label="New Password"
                bottom-slots
                :rules="[
                  async (val) =>
                    (await onValidatePassword(val)) || passwordError,
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
            </div>

            <div class="col-12">
              <q-btn
                glossy
                type="submit"
                color="secondary"
                class="text-white"
                label="Submit"
              />
            </div>
          </q-form>
        </span>
      </div>
    </div>
    <br />
    <br />
  </div>
</template>
<script setup lang="ts">
import { onMounted, ref, computed, nextTick } from 'vue';
import axios from 'axios';
import { useRoute } from 'vue-router';
import { validatePassword } from 'boot/validators';
import { useQuasar } from 'quasar'
const $q = useQuasar()
const typePassword = ref<string>('password');
const route = useRoute();
const isError = ref(true);
const message = ref('');
const password = ref('');

const eyeIcon = ref('fas fa-eye-slash');
const toggleEye = () => {
  if (eyeIcon.value == 'fas fa-eye-slash') {
    eyeIcon.value = 'fas fa-eye';
    typePassword.value = 'text';
  } else {
    typePassword.value = 'password';
    eyeIcon.value = 'fas fa-eye-slash';
  }
};

onMounted(() => {
  axios
    .post(`recover_password/${route.params.id}`)
    .then((response) => {
      message.value = response?.data?.message;
      isError.value = false;
    })
    .catch((response) => {
      isError.value = true;
      message.value = response.response?.data?.message;
    });
});

const textColor = computed(() => {
  if (isError.value) {
    return 'text-negative';
  }
  return 'text-positive';
});

const iconName = computed(() => {
  if (isError.value) {
    return 'warning';
  }
  return 'fa-solid fa-circle-check';
});

const passwordError = ref<string | null>();
const onValidatePassword = async (password: string): Promise<boolean> => {
  const result = await validatePassword(password);
  passwordError.value = result.message;
  return result.status;
};

const myForm = ref(null);
const onSubmit = async () => {
  myForm.value?.validate().then(async (success: any) => {
    if (success) {
      axios
        .post(`create_new_password/${route.params.id}`, {
            password: password.value 
        })
        .then(async (response) => {
          message.value = response?.data?.message;
          isError.value = false;
          password.value = ''
          await nextTick()
          myForm.value.resetValidation();
          $q.notify({
            message: message.value,
            color: 'positive'
          })
        })
        .catch((response) => {
          isError.value = true;
          message.value = response.response?.data?.message;
        });
    } else {
      // oh no, user has filled in
      // at least one invalid value
    }
  });
};
</script>
