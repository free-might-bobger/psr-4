<template>
  <div>
    <q-form @submit.prevent="onSubmit" class="row" ref="myForm">
      <div>Create Payment</div>
      <div class="col-12">
        <q-select
          clearable
          outlined
          v-model="payment.enrollee_id"
          :options="enrollees"
          dense
          label="Select enrollee"
          lazy-rules
          use-chips
          :multiple="false"
        />
      </div>
      <div class="col-12">
        <q-select
          clearable
          outlined
          v-model="payment.payment_option_id"
          :options="paymentOptions"
          dense
          label="Select payment option"
          lazy-rules
          use-chips
          :multiple="false"
          class="q-mt-sm"
        />
      </div>
      <div class="col-12">
        <InputAmount
          class="q-mt-sm"
          :value="payment.amount"
          label="Enter Amount"
          :disabled="false"
          @change="amountChange"
          hide-bottom-space
        />
      </div>
      <div class="col-12">
        <InputAmount
          class="q-mt-sm"
          :value="payment.amount_given"
          label="Given Amount"
          :disabled="false"
          @change="givenAmountChange"
          hide-bottom-space
        />
      </div>
      <div class="col-12">
        <h5 class="q-my-sm">
          <i>Change:</i>
          {{ formatMoney(payment.amount - payment.amount_given) }}
        </h5>
      </div>
      <div class="col-12">
        <q-btn color="primary" type="submit">Submit</q-btn>
      </div>
    </q-form>
  </div>
</template>

<script lang="ts" setup>
import { ref, nextTick, onMounted, watch } from 'vue';
import { create, get } from 'src/boot/axios-call';
import InputAmount from 'src/components/inputs/InputAmount.vue';
import { formatMoney } from 'src/boot/common';
const name = ref('');
const amount = ref(0);
const payment = ref({
  enrollee_id: null,
  payment_option_id: null,
  amount: 0,
  amount_given: 0,
});
const myForm = ref(null);
const onSubmit = async () => {
  myForm.value?.validate().then(async (success: any) => {
    if (success) {
      const result = await create(
        {
          entity: 'payments',
          data: {
            enrollee_id: payment.value?.enrollee_id.value,
            amount: payment.value.amount,
            amount_given: payment.value.amount_given,
            change: payment.value.amount - payment.value.amount_given
          },
        },
        true
      );
      if (result) {
        name.value = '';
        amount.value = 0;
        await nextTick();
        myForm.value.resetValidation();
      }
    } else {
      // oh no, user has filled in
      // at least one invalid value
    }
  });
};
const amountChange = (v: any) => {
  payment.value.amount = v;
};

const givenAmountChange = (v: any) => {
  payment.value.given_amount = v;
};

const paymentOptions = ref([]);
const enrollees = ref([]);
onMounted(async () => {
  const resultEnrollees = await get(
    {
      entity: 'enrollees',
      query: {
        orderBy: 'created_at:desc',
        type: 'collection',
      },
      message: '',
    },
    true
  );
  enrollees.value = resultEnrollees.data.data;

  const resultPaymentOptions = await get(
    {
      entity: 'payment_options',
      query: {
        type: 'collection',
      },
      message: '',
    },
    true
  );
  paymentOptions.value = resultPaymentOptions.data.data;
});

watch(
  () => payment.value.payment_option_id,
  (newVal) => {
    payment.value.amount = newVal.amount;
  },
  {
    deep: true,
  }
);
</script>
