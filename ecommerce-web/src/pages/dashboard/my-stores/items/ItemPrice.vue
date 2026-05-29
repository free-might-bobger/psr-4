<template>
  <div class="item-price-container">
    <!-- Header Section -->
    <div class="header-section">
      <div class="header-content">
        <div class="header-left">
          <q-btn
            flat
            round
            dense
            color="grey-7"
            icon="arrow_back"
            @click="router.back()"
            class="back-btn"
          />
          <div class="header-title">
            <h1 class="page-title">Item Prices</h1>
            <p class="page-subtitle">Manage pricing options and inventory</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Content Card -->
    <q-card flat bordered class="main-card">
      <q-card-section class="card-content">
        <q-form @reset="onReset" class="price-form" ref="myForm">
          <!-- Item Details -->
          <div class="section-group">
            <div class="section-label">Item Details</div>
            <q-input
              v-model="item.name"
              outlined
              dense
              label="Item Name"
              hide-bottom-space
              disable
              class="item-name-input"
            />
          </div>

          <!-- Price Variations -->
          <div class="section-group">
            <div class="section-header">
              <div class="section-label">Price Variations</div>
              <q-btn
                unelevated
                color="primary"
                icon="add"
                label="Add Price"
                @click="addItemPrice"
                class="add-price-btn"
              />
            </div>

            <div v-if="item.item_price?.length === 0" class="empty-state">
              <q-icon name="sell" size="64px" color="grey-3" />
              <p class="empty-text">No prices added yet</p>
              <p class="empty-subtext">Click "Add Price" to begin</p>
            </div>

            <div
              v-for="(itemPrice, index) in item.item_price"
              :key="itemPrice.id || index"
              class="price-card"
            >
              <div class="price-card-header">
                <span class="price-card-title">Price Option {{ index + 1 }}</span>
                <q-btn
                  flat
                  round
                  dense
                  color="grey-6"
                  icon="close"
                  @click="deleteItemPrice(Number(index))"
                  class="delete-btn"
                >
                  <q-tooltip>Remove</q-tooltip>
                </q-btn>
              </div>

              <div class="price-fields">
                <q-select
                  dense
                  v-model="itemPrice.unit"
                  :options="units"
                  label="Unit"
                  hide-bottom-space
                  use-input
                  outlined
                  :rules="[(val) => !!val || 'Unit is required.']"
                  class="field"
                />
                <q-select
                  dense
                  v-model="itemPrice.color"
                  :options="colors"
                  label="Color"
                  hide-bottom-space
                  use-input
                  outlined
                  clearable
                  class="field"
                />
                <q-select
                  dense
                  v-model="itemPrice.size"
                  :options="sizes"
                  label="Size"
                  hide-bottom-space
                  use-input
                  outlined
                  clearable
                  class="field"
                />
                <input-amount
                  label="Original Price"
                  :value="itemPrice.original_price"
                  @input="(amount) => changeOriginalPrice(itemPrice, amount)"
                  class="field"
                />
                <input-amount
                  label="Online Price"
                  :value="itemPrice.online_price"
                  @input="(amount) => changeOnlinePrice(itemPrice, amount)"
                  class="field"
                />
                <input-amount
                  label="Selling Price"
                  :value="itemPrice.selling_price"
                  @input="(amount) => changeSellingPrice(itemPrice, amount)"
                  class="field"
                />
                <q-input
                  v-model="itemPrice.qty"
                  label="Quantity"
                  outlined
                  dense
                  type="number"
                  min="0"
                  :rules="[(val) => (val !== null && val !== undefined && val !== '') || 'Quantity is required.']"
                  class="field"
                />
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="form-actions">
            <q-btn
              unelevated
              color="primary"
              icon="save"
              label="Save Changes"
              @click="createItemPrice"
              class="save-btn"
            />
            <q-btn
              outline
              color="grey-7"
              icon="cancel"
              label="Cancel"
              @click="router.back()"
              class="cancel-btn"
            />
          </div>
        </q-form>
      </q-card-section>
    </q-card>
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

const item = ref<any>({
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

const units = ref<any[]>([]);
const colors = ref<any[]>([]);
const sizes = ref<any[]>([]);
const listingApi = async () => {
  const result: any = await get(
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
    const nextId = Number(item.value.item_price.length) + 1; // Calculate the next id/index
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
const deleteItemPrice = (index: number) => {
  item.value.item_price?.splice(index, 1);
  // Recalculate IDs to ensure they are consecutive
  item.value.item_price?.forEach((attr: any, idx: number) => {
    attr.id = idx + 1;
  });
};

const createItemPrice = async () => {
  const isValid = await myForm.value?.validate();
  if (!isValid) {
    return;
  }

  const itemPrices = item.value.item_price?.map((v: any) => {
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

const changeOriginalPrice = (itemPrice: any, amount: number) => {
  itemPrice.original_price = amount;
};

const changeOnlinePrice = (itemPrice: any, amount: number) => {
  itemPrice.online_price = amount;
};

const changeSellingPrice = (itemPrice: any, amount: number) => {
  itemPrice.selling_price = amount;
};
</script>

<style scoped lang="scss">
.item-price-container {
  max-width: 1000px;
  margin: 0 auto;
  padding: 24px;
}

.header-section {
  margin-bottom: 24px;
}

.header-content {
  display: flex;
  align-items: center;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.back-btn {
  margin-right: 0;
}

.header-title {
  .page-title {
    font-size: 24px;
    font-weight: 600;
    margin: 0;
    color: #1a1a1a;
  }

  .page-subtitle {
    font-size: 14px;
    color: #666;
    margin: 4px 0 0 0;
  }
}

.main-card {
  border-radius: 12px;
  border: 1px solid #e0e0e0;
  overflow: hidden;
}

.card-content {
  padding: 32px;
}

.price-form {
  display: flex;
  flex-direction: column;
  gap: 32px;
}

.section-group {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.section-label {
  font-size: 14px;
  font-weight: 600;
  color: #333;
  letter-spacing: 0.25px;
  text-transform: uppercase;
}

.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.item-name-input {
  background: #f5f5f5;
}

.add-price-btn {
  height: 36px;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 48px 24px;
  border: 2px dashed #e0e0e0;
  border-radius: 8px;

  .empty-text {
    font-size: 16px;
    color: #666;
    margin: 16px 0 4px 0;
  }

  .empty-subtext {
    font-size: 13px;
    color: #999;
    margin: 0;
  }
}

.price-card {
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  padding: 20px;
  background: white;
  transition: box-shadow 0.2s ease;

  &:hover {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
  }
}

.price-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 16px;
  padding-bottom: 12px;
  border-bottom: 1px solid #f0f0f0;
}

.price-card-title {
  font-size: 14px;
  font-weight: 600;
  color: #333;
}

.delete-btn {
  &:hover {
    color: #f44336;
    background: #ffebee;
  }
}

.price-fields {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

.field {
  width: 100%;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding-top: 24px;
  border-top: 1px solid #f0f0f0;
}

.save-btn {
  height: 40px;
  padding: 0 24px;
}

.cancel-btn {
  height: 40px;
  padding: 0 24px;
}

@media (max-width: 1024px) {
  .price-fields {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .item-price-container {
    padding: 16px;
  }

  .card-content {
    padding: 20px;
  }

  .header-title .page-title {
    font-size: 20px;
  }

  .section-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }

  .price-fields {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column;
  }

  .save-btn,
  .cancel-btn {
    width: 100%;
  }
}
</style>
