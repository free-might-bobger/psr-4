<template>
  <div class="item-price-container">
    <!-- Header Section -->
    <div class="header-section">
      <div class="header-content">
        <div class="header-left">
          <q-btn flat round dense color="grey-7" icon="arrow_back" @click="handleBack" class="back-btn" />
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
            <q-input v-model="item.name" outlined dense label="Item Name" hide-bottom-space disable
              class="item-name-input" />
          </div>

          <!-- Price Variations -->
          <div class="section-group">
            <div class="section-header">
              <div class="section-label">Price Variations</div>
              <q-btn unelevated color="primary" icon="add" label="Add Price" @click="addItemPrice"
                class="add-price-btn" />
            </div>

            <div v-if="item.item_price?.length === 0" class="empty-state">
              <q-icon name="sell" size="64px" color="grey-3" />
              <p class="empty-text">No prices added yet</p>
              <p class="empty-subtext">Click "Add Price" to begin</p>
            </div>

            <div v-for="(itemPrice, index) in item.item_price" :key="itemPrice.id || index" class="price-card">
              <div class="price-card-header">
                <span class="price-card-title">Price Option {{ index + 1 }}</span>
                <q-btn flat round dense color="grey-6" icon="close" @click="deleteItemPrice(Number(index))"
                  class="delete-btn">
                  <q-tooltip>Remove</q-tooltip>
                </q-btn>
              </div>

              <div class="price-fields">
                <q-select dense v-model="itemPrice.unit" :options="units" label="Unit" hide-bottom-space use-input
                  outlined :rules="[(val) => !!val || 'Unit is required.']" class="field" />
                <input-amount label="Original Price" :value="itemPrice.original_price"
                  @input="(amount) => changeOriginalPrice(itemPrice, amount)" class="field" />
                <input-amount label="Online Price" :value="itemPrice.online_price"
                  @input="(amount) => changeOnlinePrice(itemPrice, amount)" class="field" />
                <input-amount label="Selling Price" :value="itemPrice.selling_price"
                  @input="(amount) => changeSellingPrice(itemPrice, amount)" class="field" />
                <q-input v-model="itemPrice.qty" label="Quantity" outlined dense type="number" min="0"
                  :rules="[(val) => (val !== null && val !== undefined && val !== '') || 'Quantity is required.']"
                  class="field" />
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="form-actions">
            <q-btn unelevated color="primary" icon="save" label="Save Changes" @click="createItemPrice"
              class="save-btn" />
            <q-btn outline color="grey-7" icon="cancel" label="Cancel" @click="handleBack" class="cancel-btn" />
          </div>
        </q-form>
      </q-card-section>
    </q-card>
  </div>
</template>

<script setup lang="ts">
import { ref, onBeforeMount } from 'vue';
import { show, get, create } from 'src/boot/axios-call';
import { useRouter } from 'vue-router';
import type { QForm } from 'quasar';
import { Notify } from 'quasar';
import InputAmount from 'src/components/inputs/InputAmount.vue';

interface ItemPriceProps {
  storeId: number;
  itemId: number;
}

const props = defineProps<ItemPriceProps>();

const emit = defineEmits<{
  (e: 'submit'): void;
  (e: 'cancel'): void;
}>();

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
      optimus_id: props.itemId,
      query: {
        filters: `store_id:${props.storeId}`,
        with: 'itemPrice.unit',
      },
    },
    true
  );
};

const units = ref<any[]>([]);

const listingApi = async () => {
  const result: any = await get(
    {
      entity: 'listing_api',
      query: {
        listingApi: 'units',
      },
    },
    false
  );
  units.value = result.data.data.units;
};

// Function to add an attribute with an index
const addItemPrice = () => {
  if (item.value.item_price) {
    const nextId = Number(item.value.item_price.length) + 1;
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
      unit_id: v.unit?.id,
      original_price: v.original_price,
      online_price: v.online_price,
      selling_price: v.selling_price,
      qty: v.qty,
    };
  });

  try {
    await create(
      {
        entity: 'item-prices',
        data: {
          item_id: item.value.id,
          item_prices: itemPrices,
        },
      },
      false
    );
    Notify.create({
      position: 'bottom',
      type: 'positive',
      message: 'Item prices updated successfully.',
    });
    emit('submit');
  } catch (error: any) {
    Notify.create({
      position: 'bottom',
      type: 'negative',
      message: error.response?.data?.message || 'Failed to update item prices.',
    });
  }
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

const handleBack = () => {
  emit('cancel');
  router.back();
};
</script>

<style scoped lang="scss">
// Premium Color Palette
$primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
$primary-color: #667eea;
$secondary-color: #764ba2;
$surface-color: #ffffff;
$background-color: #f8fafc;
$text-primary: #1e293b;
$text-secondary: #64748b;
$text-muted: #94a3b8;
$border-color: #e2e8f0;
$border-hover: #cbd5e1;
$shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
$shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
$shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
$shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
$error-color: #ef4444;
$error-bg: #fef2f2;

.item-price-container {
  max-width: 1100px;
  margin: 0 auto;
  padding: 32px;
  background: $background-color;
  min-height: 100vh;
}

// Header Section with Premium Styling
.header-section {
  margin-bottom: 28px;
  position: relative;
}

.header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 16px;
}

.back-btn {
  background: $surface-color;
  border: 1px solid $border-color;
  box-shadow: $shadow-sm;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);

  &:hover {
    background: $background-color;
    border-color: $border-hover;
    transform: translateX(-2px);
    box-shadow: $shadow-md;
  }
}

.header-title {
  .page-title {
    font-size: 28px;
    font-weight: 700;
    margin: 0;
    background: $primary-gradient;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    letter-spacing: -0.02em;
  }

  .page-subtitle {
    font-size: 15px;
    color: $text-secondary;
    margin: 6px 0 0 0;
    font-weight: 400;
  }
}

// Main Card with Premium Glass Effect
.main-card {
  border-radius: 20px;
  border: none;
  background: $surface-color;
  box-shadow: $shadow-xl;
  overflow: hidden;
  position: relative;

  &::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: $primary-gradient;
  }
}

.card-content {
  padding: 40px;
}

.price-form {
  display: flex;
  flex-direction: column;
  gap: 40px;
}

// Section Styling
.section-group {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.section-label {
  font-size: 13px;
  font-weight: 700;
  color: $text-primary;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  display: flex;
  align-items: center;
  gap: 8px;

  &::before {
    content: '';
    width: 4px;
    height: 16px;
    background: $primary-gradient;
    border-radius: 2px;
  }
}

.section-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-bottom: 8px;
  border-bottom: 1px solid $border-color;
}

// Item Name Input Styling
.item-name-input {
  background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
  border-radius: 12px;

  :deep(.q-field__control) {
    background: transparent;
    border-radius: 12px;
    height: 52px;
  }

  :deep(.q-field__label) {
    font-weight: 500;
    color: $text-secondary;
  }
}

// Add Price Button with Gradient
.add-price-btn {
  height: 42px;
  padding: 0 20px;
  border-radius: 10px;
  font-weight: 600;
  letter-spacing: 0.25px;
  background: $primary-gradient;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
  }
}

// Empty State with Premium Design
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 64px 32px;
  border: 2px dashed $border-color;
  border-radius: 16px;
  background: linear-gradient(135deg, #fafafa 0%, #f8fafc 100%);
  position: relative;
  overflow: hidden;

  &::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(102, 126, 234, 0.03) 0%, transparent 70%);
    animation: pulse-glow 4s ease-in-out infinite;
  }

  @keyframes pulse-glow {

    0%,
    100% {
      transform: scale(1);
      opacity: 0.5;
    }

    50% {
      transform: scale(1.1);
      opacity: 0.8;
    }
  }

  .q-icon {
    background: $primary-gradient;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    filter: drop-shadow(0 4px 6px rgba(102, 126, 234, 0.2));
  }

  .empty-text {
    font-size: 18px;
    font-weight: 600;
    color: $text-primary;
    margin: 20px 0 6px 0;
  }

  .empty-subtext {
    font-size: 14px;
    color: $text-muted;
    margin: 0;
  }
}

// Price Cards with Premium Styling
.price-card {
  border: 1px solid $border-color;
  border-radius: 16px;
  padding: 24px;
  background: $surface-color;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  margin-bottom: 20px;
  position: relative;
  overflow: hidden;

  &::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: $primary-gradient;
    transform: scaleY(0);
    transition: transform 0.3s ease;
  }

  &:hover {
    box-shadow: $shadow-lg;
    border-color: $border-hover;
    transform: translateY(-2px);

    &::after {
      transform: scaleY(1);
    }
  }
}

.price-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
  padding-bottom: 16px;
  border-bottom: 1px solid $border-color;
}

.price-card-title {
  font-size: 15px;
  font-weight: 700;
  color: $text-primary;
  display: flex;
  align-items: center;
  gap: 8px;

  &::before {
    content: '#';
    width: 24px;
    height: 24px;
    background: $primary-gradient;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
    font-weight: 700;
  }
}

.delete-btn {
  transition: all 0.2s ease;

  &:hover {
    color: $error-color;
    background: $error-bg;
    transform: rotate(90deg);
  }
}

// Price Fields Grid
.price-fields {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;

  :deep(.q-field) {
    .q-field__control {
      border-radius: 10px;
      transition: all 0.2s ease;
    }

    &.q-field--focused .q-field__control {
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
    }
  }
}

.field {
  width: 100%;
}

// Form Actions with Premium Buttons
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 16px;
  padding-top: 32px;
  border-top: 1px solid $border-color;
}

.save-btn {
  height: 48px;
  padding: 0 32px;
  border-radius: 12px;
  font-weight: 600;
  font-size: 15px;
  letter-spacing: 0.25px;
  background: $primary-gradient;
  box-shadow: 0 4px 14px rgba(102, 126, 234, 0.4);
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
  }

  &:active {
    transform: translateY(0);
  }
}

.cancel-btn {
  height: 48px;
  padding: 0 32px;
  border-radius: 12px;
  font-weight: 600;
  font-size: 15px;
  letter-spacing: 0.25px;
  border: 2px solid $border-color;
  color: $text-secondary;
  transition: all 0.3s ease;

  &:hover {
    border-color: $text-muted;
    color: $text-primary;
    background: #f8fafc;
  }
}

// Responsive Design
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
    padding: 24px;
  }

  .header-title .page-title {
    font-size: 22px;
  }

  .section-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 16px;
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

  .price-card {
    padding: 20px;
  }
}

@media (max-width: 480px) {
  .header-left {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }

  .price-card-title {
    font-size: 14px;
  }
}
</style>
