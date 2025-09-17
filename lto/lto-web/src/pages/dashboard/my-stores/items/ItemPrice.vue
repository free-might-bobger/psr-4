<template>
  <div class="q-pa-sm">
    <div>
      <q-btn
        label="Items"
        outline
        class="q-my-sm"
        icon="fa-solid fa-angles-left"
        color="primary"
        @click="router.back()"
      />
    </div>
    <q-form @reset="onReset" class="q-gutter-sm" ref="myForm">
      <q-input
        v-model="item.name"
        outlined
        dense
        label="Item Name"
        hide-bottom-space
        disable
      />
      <q-btn flat @click="addItemPrice" icon="add" label="Item Price" />
    </q-form>
    <q-card
      class="q-pa-sm q-mt-sm"
      flat
      bordered
      v-for="(itemPrice, index) in item.item_price"
      :key="index"
    >
      <div class="row q-gutter-sm">
        <div class="col-2">
          <q-select
            dense
            v-model="itemPrice.unit"
            :options="units"
            label="Units"
            hide-bottom-space
            use-input
            outlined
            class="q-mt-sm"
          />
        </div>
        <div class="col-2">
          <q-select
            dense
            v-model="itemPrice.color"
            :options="colors"
            label="Colors"
            hide-bottom-space
            use-input
            outlined
            class="q-mt-sm"
            clearable
          />
        </div>
        <div class="col-2">
          <q-select
            dense
            v-model="itemPrice.size"
            :options="sizes"
            label="Sizes"
            hide-bottom-space
            use-input
            outlined
            class="q-mt-sm"
            clearable
          />
        </div>
        <div class="col-2">
          <input-amount
            label="Original Price"
            class="q-mt-sm"
            :value="itemPrice.original_price"
            @input="(amount) => changeOriginalPrice(itemPrice, amount)"
          />
        </div>
        <div class="col-2">
          <input-amount
            label="Online Price"
            class="q-mt-sm"
            :value="itemPrice.online_price"
            @input="(amount) => changeOnlinePrice(itemPrice, amount)"
          />
        </div>
        <div class="col-2">
          <input-amount
            label="Selling Price"
            class="q-mt-sm"
            :value="itemPrice.selling_price"
            @input="(amount) => changeSellingPrice(itemPrice, amount)"
          />
        </div>
        <div class="col-2">
          <q-input
            v-model="itemPrice.qty"
            label="Qty"
            outlined
            dense
            class="q-mt-sm"
          />
        </div>
        <div class="col-2">
          <q-btn
            class="q-mt-sm"
            flat
            color="negative"
            icon="delete"
            @click="deleteItemPrice(index)"
          />
        </div>
      </div>
    </q-card>

    <q-btn class="q-mt-sm" @click="createItemPrice" color="primary"
      >Update Item Price</q-btn
    >
  </div>
</template>
<script setup lang="ts">
import { ref, onBeforeMount } from 'vue';
import { show, get, create } from 'src/boot/axios-call';
import { useRoute, useRouter } from 'vue-router';
import type { QForm } from 'quasar';
import { ItemInterface } from 'boot/interfaces';
import InputAmount from 'src/components/inputs/InputAmount.vue';

const route = useRoute();
const router = useRouter();
const myForm = ref<QForm | null>(null);

const onReset = () => {
  myForm.value?.resetValidation();
};

const item = ref<ItemInterface>({
  name: '',
  description: '',
  item_price: [],
  category: null
});
onBeforeMount(async () => {
  await getItem();
  listingApi();
});

const getItem = async () => {
  item.value = await show(
    {
      entity: 'items',
      optimus_id: Number(route.params.itemId),
      query: {
        filters: `store_id:${route.params.id}`,
        with: 'itemPrice.unit,itemPrice.color,itemPrice.size',
      },
    },
    true
  );
};

const units = ref([]);
const colors = ref([]);
const sizes = ref([]);
const listingApi = async () => {
  const result = await get(
    {
      entity: 'listing_api',
      query: {
        listingApi: 'units,colors,sizes',
      },
    },
    false
  );
  units.value = result.data.data.units;
  colors.value = result.data.data.colors;
  sizes.value = result.data.data.sizes;
};

// Function to add an attribute with an index
const addItemPrice = () => {
  if (item.value.item_price) {
    const nextId = item.value.item_price.length + 1; // Calculate the next id/index
    item.value.item_price?.push({
      id: nextId,
      original_price: 0,
      online_price: 0,
      selling_price: 0,
      category: null,
      unit: null
    });
  }
};

// Function to delete an attribute by index
const deleteItemPrice = (index: any) => {
  item.value.item_price?.splice(index, 1);
  // Recalculate IDs to ensure they are consecutive
  item.value.item_price?.forEach((attr, idx) => {
    attr.id = idx + 1;
  });
};

const createItemPrice = async () => {
  const itemPrices = item.value.item_price?.map((v) => {
    return {
      color_id: v.color?.id,
      size_id: v.size?.id,
      unit_id: v.unit?.id,
      original_price: v.original_price,
      online_price: v.online_price,
      selling_price: v.selling_price,
      qty: v.qty,
    };
  });
  const result = await create(
    {
      entity: 'item-prices',
      data: {
        item_id: item.value.id,
        item_prices: itemPrices,
      },
    },
    true
  );
};

const changeOriginalPrice = (itemPrice, amount: number) => {
  itemPrice.original_price = amount;
};

const changeOnlinePrice = (itemPrice, amount: number) => {
  itemPrice.online_price = amount;
};

const changeSellingPrice = (itemPrice, amount: number) => {
  itemPrice.selling_price = amount;
};
</script>
