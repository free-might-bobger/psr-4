<template>
  <div class="q-ma-sm">
    <div class="row q-gutter-md">
      <div class="col">
        <BreadCrumbsWrapper
          :bread-crumbs="[
            {
              name: 'Login',
              path: '',
            },
          ]"
        />
      </div>
    </div>
    <q-card flat bordered class="q-pa-sm q-mt-sm">
      <q-form @submit.prevent="onSubmit" class="q-gutter-sm" ref="myForm">
        <q-input
          v-model="loginInfo.mobile"
          outlined
          dense
          label="Enter Mobile"
          :rules="[(val) => isValidMobileNumber(val) || 'Mobile is required.']"
          hide-bottom-space
        >
          <template v-slot:append>
            <q-btn
              dense
              flat
              label="Get Passcode"
              color="primary"
              @click="getPassCode"
            />
          </template>
        </q-input>
        <q-input
          type="password"
          v-model="loginInfo.password"
          outlined
          dense
          label="Enter Passcode"
          :rules="[(val) => (val && val.length > 0) || 'Passcode is required.']"
          hide-bottom-space
        />
        <div>
          <q-btn
            label="Submit"
            type="submit"
            color="primary"
            class="full-width"
            unelevated
          />
        </div>
      </q-form>
    </q-card>
  </div>
</template>

<script lang="ts" setup>
import BreadCrumbsWrapper from 'src/components/BreadCrumbsWrapper.vue';
import { ref } from 'vue';
import { login, create } from 'src/boot/axios-call';
import type { QForm } from 'quasar';
import { LoginInterface } from 'boot/interfaces';
import { useQuasar } from 'quasar';
import { isValidMobileNumber } from 'src/boot/validators';
import { useRouter } from 'vue-router';

const router = useRouter();
const loginInfo = ref<LoginInterface>({
  mobile: '',
  password: '',
});
const myForm = ref<QForm | null>(null);

const onSubmit = async () => {
  myForm.value?.validate().then(async (success: any) => {
    if (success) {
      await login({
        mobile: loginInfo.value.mobile,
        password: loginInfo.value.password,
      });
    }
  });
};
const $q = useQuasar();
const getPassCode = async () => {
  if (!isValidMobileNumber(loginInfo.value.mobile)) {
    $q.notify({
      message: 'Please enter a valid mobile number.',
      type: 'negative',
    });
    return;
  }

  const result = await create(
    {
      entity: 'create-new-activation-code',
      data: {
        mobile: loginInfo.value.mobile,
      },
    },
    true,
    'Please verify your passcode.'
  );

  if (result) {
    router.push({
      path: `/public_stores/${optimusId}`,
    });
  }
};
</script>
