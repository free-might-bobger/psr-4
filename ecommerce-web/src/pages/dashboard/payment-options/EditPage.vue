<template>
  <div>
    <q-form @submit.prevent="onSubmit" class="row" ref="myForm">
      <div>Edit Payment Option</div>
      <div class="col-12">
        <q-input
          clearable
          v-model="name"
          dense
          outlined
          label="Name"
          :rules="[(val) => (val && val.length > 0) || 'Name is required.']"
          hide-bottom-space
        />
      </div>
      <div class="col-12">
        <InputAmount
          class="q-mt-sm"
          :value="amount"
          label="Enter Amount"
          :disabled="false"
          @change="amountChange"
        />
      </div>
      <div class="col-12">
        <q-btn color="primary" type="submit">Submit</q-btn>
      </div>
    </q-form>
  </div>
</template>

<script lang="ts" setup>
import { ref, nextTick, onBeforeMount } from 'vue';
import { update, show } from 'src/boot/axios-call';
import InputAmount from 'src/components/inputs/InputAmount.vue';
import { useRoute } from "vue-router"

const route = useRoute()
const name = ref('');
const amount = ref(0);
const myForm = ref(null);
const onSubmit = async () => {
  myForm.value?.validate().then(async (success: any) => {
    if (success) {
      const result = await update(
        {
          entity: 'payment_options',
          optimus_id: route.params.id, 
          data: {
            name: name.value,
            amount: amount.value
          },
        },
        true
      );
     
    } else {
      // oh no, user has filled in
      // at least one invalid value
    }
  });
};
const amountChange = (v: any) => {
  amount.value = v;
};

onBeforeMount(async () => {
 const result = await show({
    entity: 'payment_options',
    id: route.params.id,
    query: {
    },
  });
  name.value = result.name
  amount.value = result.amount
});
</script>
