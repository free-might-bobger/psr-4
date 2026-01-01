<template>
  <div class="q-ma-sm">
    <div class="row q-col-gutter-xs">
      <div class="col-xs-12">
        <div class="flex items-center breadcrumbs-container">
          <BreadCrumbsWrapper :bread-crumbs="[
            {
              name: store.name,
              path: `/public_stores/${route.params.id}`,
            },
            {
              name: item.name || '',
              path: '',
            },
          ]" />
        </div>

      </div>
    </div>

    <div class="row q-col-gutter-md" v-if="item">
      <div class="col-lg-5 col-md-5 col-xs-12">
        <q-card class="product-image-card q-mt-sm" flat bordered>
          <q-carousel v-model="slide" vertical transition-prev="slide-down" transition-next="slide-up" swipeable
            animated control-color="primary" navigation-icon="radio_button_unchecked" navigation padding height="400px"
            class="rounded-borders">
            <q-carousel-slide :name="index" class="column no-wrap flex-center" v-for="(image, index) in item.images"
              :key="image.id">
              <div class="q-pa-md text-center full-width full-height flex flex-center">
                <img :src="image.path_url" class="product-image" :alt="item.name" />
              </div>
            </q-carousel-slide>
          </q-carousel>
        </q-card>
      </div>

      <div class="col-lg-7 col-md-7 col-xs-12">
        <q-card class="product-info-card q-mt-sm" flat bordered>
          <q-card-section>
            <div class="product-title text-h4 text-weight-bold q-mb-md">
              {{ item.name }}
            </div>

            <div class="product-description text-body1 text-grey-8 q-mb-lg">
              {{ item.description }}
            </div>

            <q-separator class="q-mb-lg" />

            <div class="q-mb-lg" v-if="units.length > 0">
              <div class="text-subtitle2 text-weight-medium q-mb-sm">Select Unit:</div>
              <div class="row q-col-gutter-sm">
                <div class="col-auto" v-for="unit in units" :key="unit.id">
                  <q-radio v-model="selectedUnit" :val="unit.id" :label="unit.name" color="primary" size="md"
                    class="unit-radio" />
                </div>
              </div>
            </div>

            <div class="price-section q-mb-lg">
              <div class="text-subtitle2 text-weight-medium q-mb-sm">Price:</div>
              <div class="price-display text-h5 text-weight-bold text-primary">
                {{ getPriceRange(filteredItemPrice) }}
              </div>
            </div>

            <q-separator class="q-mb-lg" />

            <div class="purchase-section">
              <div class="row q-col-gutter-md items-center">
                <div class="col-auto">
                  <div class="text-subtitle2 text-weight-medium q-mb-xs">Quantity:</div>
                  <q-input type="number" v-model="qty" outlined dense style="width: 120px" label="Qty" min="1"
                    :rules="[val => val >= 1 || 'Quantity must be at least 1']" class="quantity-input">
                    <template v-slot:prepend>
                      <q-btn icon="remove" size="sm" flat dense @click="qty > 1 ? qty-- : null" />
                    </template>
                    <template v-slot:append>
                      <q-btn icon="add" size="sm" flat dense @click="qty++" />
                    </template>
                  </q-input>
                </div>
                <div class="col">
                  <q-btn color="primary" @click="userAddCart" size="lg" unelevated class="full-width add-to-cart-btn"
                    icon="shopping_cart" label="Add to Cart" />
                </div>
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </div>
</template>
<script setup lang="ts">
import { useRoute } from 'vue-router';
import { onMounted, ref, watch, type Ref } from 'vue';
import { show } from 'src/boot/axios-call';
import { getPriceRange } from 'src/boot/utilities';
import { useUserCartStore } from 'src/stores/userCart';
import { useQuasar } from 'quasar';
import BreadCrumbsWrapper from 'src/components/BreadCrumbsWrapper.vue';

interface ItemPrice {
  id: number;
  unit_id: number;
  price: number;
  unit: {
    id: number;
    name: string;
  };
  online_price: number;
  selling_price: number;
}

interface Item {
  id?: number;
  optimus_id?: number;
  name?: string;
  description?: string;
  images?: Array<{ id: number; path_url: string }>;
  item_price?: ItemPrice[];
  store?: { optimus_id: number };
}

const $q = useQuasar();
const useUserCart = useUserCartStore();
const filteredItemPrice: Ref<Array<{ unit_id: number; price: number }>> = ref(
  []
);

const slide = ref(0);
const selectedUnit = ref<number | null>(null);
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
    optimus_id: Number(route.params.id),
    query: {
      columns: 'id,name',
    },
  });
};

const item = ref<Item>({});
const getItem = async () => {
  item.value = await show<Item>({
    message: 'Getting item...',
    entity: 'public_store_items',
    optimus_id: Number(route.params.item_id),
    query: {
      with: 'itemPrice.unit,store',
      columns: 'id,name',
    },
  });
  getUnits();
  filteredItemPrice.value = item.value.item_price || [];
};

onMounted(() => {
  showStore();
  getItem();

});

const units = ref<Array<{ id: number; name: string }>>([]);
const getUnits = () => {
  const itemPrice = item.value?.item_price;

  if (!itemPrice) {
    units.value = [];
    return;
  }

  units.value = itemPrice.map((v) => v.unit);

  if (units.value.length === 1) {
    selectedUnit.value = units.value[0].id;
  }

};

watch(selectedUnit, (newValue) => {
  if (newValue !== null && item.value.item_price) {
    const result = item.value.item_price.find(
      (v) => v.unit_id === newValue
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
  if (!item.value.optimus_id || !item.value.name || !selectedUnit.value) {
    $q.notify({
      message: 'Please select a unit before adding to cart.',
      type: 'negative',
    });
    return;
  }
  // Transform item_price to match CartItem structure
  const transformedItemPrice = (item.value.item_price || []).map((price) => ({
    unit_id: price.unit_id,
    online_price: price.online_price,
    price: price.selling_price,
    unit: price.unit,
  }));

  const cartItem = {
    id: item.value.id || 0,
    optimus_id: item.value.optimus_id,
    name: item.value.name,
    count: qty.value,
    store_id: item.value.store?.optimus_id || Number(route.params.id),
    item_price: transformedItemPrice,
    variations: [
      {
        count: qty.value,
        unit: selectedUnit.value,
      },
    ],
    primary_img: {
      path_url: item.value.images && item.value.images.length > 0
        ? item.value.images[0].path_url
        : '',
    },
    store: {
      optimus_id: item.value.store?.optimus_id || Number(route.params.id),
    },
  };
   useUserCart.addQty(cartItem);

  $q.notify({
    message: 'You have successfully added the item to the cart.',
    type: 'positive',
  });
};

</script>

<style scoped lang="scss">
.product-image-card {
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);

  .q-carousel {
    background: #fafafa;
  }
}

.product-image {
  width: 100%;
  max-width: 100%;
  height: auto;
  max-height: 380px;
  object-fit: contain;
  border-radius: 8px;
}

.product-info-card {
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  min-height: 400px;
}

.product-title {
  line-height: 1.3;
  color: #1a1a1a;
}

.product-description {
  line-height: 1.6;
  white-space: pre-wrap;
}

.unit-radio {
  padding: 8px 16px;
  border-radius: 8px;
  background: #f5f5f5;
  transition: all 0.3s ease;

  &:hover {
    background: #eeeeee;
  }
}

.price-section {
  padding: 16px;
  background: #f8f9fa;
  border-radius: 8px;
  border-left: 4px solid var(--q-primary);
}

.price-display {
  letter-spacing: 0.5px;
}

.quantity-input {
  .q-field__control {
    border-radius: 8px;
  }
}

.add-to-cart-btn {
  height: 48px;
  font-weight: 600;
  letter-spacing: 0.5px;
  border-radius: 8px;
  transition: all 0.3s ease;

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }
}

.purchase-section {
  padding-top: 8px;
}

@media (max-width: 600px) {
  .product-image-card .q-carousel {
    height: 300px !important;
  }

  .product-title {
    font-size: 1.5rem;
  }

  .price-display {
    font-size: 1.25rem;
  }

  .add-to-cart-btn {
    margin-top: 16px;
  }
}
</style>
