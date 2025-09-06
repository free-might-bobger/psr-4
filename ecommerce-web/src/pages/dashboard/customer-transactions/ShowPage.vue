<template>
  <div>
    <div
      class="q-pa-xs col-xs-12 col-sm-6 col-md-4 col-lg-3 grid-style-transition"
    >
      <q-card flat bordered>
        <q-card-section>
          <div class="flex justify-between items-center" style="height: 35px">
            <span class="text-body1">Orders</span>
            <span class="text-body1">{{  localResult.created_at }}</span>
          </div>
          <div style="clear: both" />
        </q-card-section>
        <q-separator />
        <template v-for="(groupStore, key) in orderGroupByStore" :key="key">
          <q-list
            dense
            class="q-mt-sm"
            v-for="order in groupStore"
            :key="order.id"
          >
            <q-item >
              <q-item-section>
                <q-item-label overline>Store Name: </q-item-label>
              </q-item-section>
              <q-item-section side>
                <q-item-label caption>{{
                  truncateString(order.store.name)
                }}</q-item-label>
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>
                <q-item-label overline>Item: </q-item-label>
              </q-item-section>
              <q-item-section side>
                <q-item-label caption>{{
                  truncateString(order.item_name)
                }}</q-item-label>
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>
                <q-item-label overline>Price: </q-item-label>
              </q-item-section>
              <q-item-section side>
                <q-item-label caption>{{ order.online_price }}</q-item-label>
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>
                <q-item-label overline>Quantity: </q-item-label>
              </q-item-section>
              <q-item-section side>
                <q-item-label caption>{{ order.qty }}</q-item-label>
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>
                <q-item-label overline>Subtotal: </q-item-label>
              </q-item-section>
              <q-item-section side>
                <q-item-label caption>{{ order.subtotal }}</q-item-label>
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>
                <q-separator />
              </q-item-section>
            </q-item>
          </q-list>
        </template>
        <br />
      </q-card>
    </div>
  </div>
</template>

<script setup lang="ts">
import { show } from 'src/boot/axios-call';
import { onMounted, ref, computed } from 'vue';
import { useRoute } from 'vue-router';
import { groupBy, truncateString } from 'src/boot/common';
import { TransactionResultInterface } from 'src/boot/interfaces';
const route = useRoute();

const localResult = ref<TransactionResultInterface>({
  orders: [],
  created_at: ''
});
onMounted(async () => {
  const result = await show<TransactionResultInterface>({
    message: 'Getting transaction...',
    entity: 'customer-transactions',
    optimus_id: route.params.id,
    query: {
      with: 'orders.store,paymentMethod,receiveMethod',
    },
  });
  localResult.value = result;
});

const orderGroupByStore = computed(() => {
  return groupBy(localResult.value.orders, 'store_id');
});
</script>
