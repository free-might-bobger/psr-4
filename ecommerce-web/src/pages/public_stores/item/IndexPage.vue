<template>
  <div style="margin: 5px">
    <div class="row q-gutter-xs">
      <div class="col">
        <BreadCrumbsWrapper
          :bread-crumbs="[
            {
              name: truncateString(store.name, 20),
              path: `/public_stores/${route.params.id}`,
            },
            {
              name: truncateString(item.name, 10),
              path: '',
            },
          ]"
        />
      </div>
    </div>

    <div class="row" v-if="item">
      <div class="col-12">
        <q-carousel
          v-model="slide"
          vertical
          transition-prev="slide-down"
          transition-next="slide-up"
          swipeable
          animated
          control-color="grey"
          navigation-icon="radio_button_unchecked"
          navigation
          padding
          height="200px"
          bordered
          class="q-mt-sm rounded-borders"
        >
          <q-carousel-slide
            :name="index"
            class="column no-wrap flex-center"
            v-for="(image, index) in item.images"
            :key="image.id"
          >
            <div class="q-mt-md text-center">
              <img
                :src="image.path_url"
                style="width: 100%; object-fit: contain; height: 180px"
              />
            </div>
          </q-carousel-slide>
        </q-carousel>
      </div>

      <div class="col-12">
        <br />
        <div class="q-ma-md">
          <div style="font-weight: bold; font-size: medium;" class="text-center">
            {{ item.name }}
          </div>
          <div class="q-mt-sm">{{ item.description }}</div>
        </div>
      </div>

      <div class="col-12">
        <div class="q-mt-md">
          <div class="float-right">
            <div>
              <span
                v-for="(unit, key) in units"
                :key="unit.id"
                style="display: inline-block; width: 100px"
                :class="{ itemMargin: key >= 1 }"
              >
                <q-radio
                  dense
                  v-model="selectedUnit"
                  :val="unit.id"
                  :label="unit.name"
                />
              </span>
            </div>
          </div>
          <br />
        </div>
      </div>

      <div class="col-12">
        <div class="q-ma-md">
          <div class="flex justify-between">
            <span
              style="
                width: 200px;
                display: inline-block;
                margin-top: 10px;
                font-size: large;
                text-align: right;
              "
            >
              {{ getPriceRange(filteredItemPrice) }}</span
            >
            <q-input
              type="number"
              v-model="qty"
              outlined
              dense
              style="width: 100px"
              label="Qty"
              min="1"
            ></q-input>
          </div>
        </div>
      </div>

      <div class="col-12">
        <q-btn color="primary" @click="userAddCart" class="q-mt-sm full-width"
          >Add to cart</q-btn
        >
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { useRoute } from 'vue-router';
import { onMounted, ref, watch } from 'vue';
import { show } from 'src/boot/axios-call';
import { getPriceRange } from 'src/boot/utilities';
import { useUserCartStore } from 'src/stores/userCart';
import { useQuasar } from 'quasar';
import { storeToRefs } from 'pinia';
import BreadCrumbsWrapper from 'src/components/BreadCrumbsWrapper.vue';
import { truncateString } from 'src/boot/common';

const $q = useQuasar();
const useUserCart = useUserCartStore();
const filteredItemPrice: Ref<Array<{ unit_id: number; price: number }>> = ref(
  []
);

const { cart } = storeToRefs(useUserCart);
const slide = ref(0);
const selectedUnit = ref(null);
const qty = ref(1);
const store = ref({
  name: '',
  logo: { path_url: '' },
  default_address: {
    complete_address: '',
  },
});

const route = useRoute();

const showStore = async () => {
  store.value = await show({
    message: 'Getting item...',
    entity: 'public_stores',
    optimus_id: route.params.id,
    query: {
      columns: 'id,name',
    },
  });
};

const item = ref({});
const getItem = async () => {
  item.value = await show({
    message: 'Getting item...',
    entity: 'items',
    optimus_id: route.params.item_id,
    query: {
      with: 'itemPrice.unit,store',
      columns: 'id,name',
    },
  });
  getUnits();
  filteredItemPrice.value = item.value.item_price;
};

onMounted(() => {
  showStore();
  getItem();
});

const units = ref([]);
const getUnits = () => {
  const itemPrice = item.value?.item_price;

  if (!itemPrice) {
    units.value = [];
    return;
  }

  units.value = itemPrice.map((v: { unit: string; id: number }) => v.unit);

  if (units.value.length === 1) {
    selectedUnit.value = units.value[0].id;
  }
};

watch(selectedUnit, (newValue) => {
  if (newValue !== null) {
    const result = item.value.item_price.find(
      (v: object) => v.unit_id === newValue
    );
    filteredItemPrice.value = result ? [result] : [];
  }
});

watch(qty, (newValue) => {
  if (newValue < 1) {
    qty.value = 1;
  }
});

const userAddCart = () => {
  useUserCart.addQty(
    Object.assign(item.value, {
      count: parseInt(qty.value, 10), // Ensure `qty.value` is parsed as a number
      variations: [
        {
          count: qty.value,
          unit: selectedUnit.value,
        },
      ],
    })
  );

  $q.notify({
    message: 'You have successfully added the item to the cart.',
    type: 'positive',
  });
};

</script>
