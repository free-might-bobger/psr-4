<template>
  <div class="item-price-container">

    <!-- Hero Header -->
    <div class="page-hero q-mb-xl">
      <div class="hero-accent-overlay"></div>
      <div class="hero-inner">
        <div class="hero-left">
          <q-btn flat round dense icon="arrow_back" @click="handleBack" class="hero-back-btn">
            <q-tooltip>Back</q-tooltip>
          </q-btn>
          <div class="hero-icon-wrap">
            <q-icon name="sell" size="24px" color="white" />
          </div>
          <div>
            <h1 class="page-title">Item Prices</h1>
            <div class="page-subtitle">Manage pricing options and inventory</div>
          </div>
        </div>
      </div>
    </div>

    <q-form @reset="onReset" class="price-form" ref="myForm">

      <!-- Item Details section -->
      <div class="section-card q-mb-lg">
        <div class="section-card-header">
          <div class="section-icon-wrap">
            <q-icon name="inventory_2" size="17px" color="white" />
          </div>
          <div>
            <div class="section-title">Item Details</div>
            <div class="section-sub">Read-only item info</div>
          </div>
        </div>
        <div class="card-divider"></div>
        <div class="section-body">
          <q-input v-model="item.name" outlined dense label="Item Name" hide-bottom-space disable
            class="dark-field item-name-input" />
        </div>
      </div>

      <!-- Price Variations section -->
      <div class="section-card q-mb-lg">
        <div class="section-card-header">
          <div class="section-icon-wrap section-icon-wrap--green">
            <q-icon name="attach_money" size="17px" color="white" />
          </div>
          <div class="section-header-text">
            <div class="section-title">Price Variations</div>
            <div class="section-sub">Add or edit price options per unit</div>
          </div>
          <q-btn unelevated icon="add" label="Add Price" @click="addItemPrice" class="add-price-btn" />
        </div>
        <div class="card-divider"></div>

        <!-- Empty state -->
        <div v-if="item.item_price?.length === 0" class="empty-state">
          <div class="empty-icon-wrap">
            <q-icon name="sell" size="36px" color="white" />
          </div>
          <div class="empty-text">No prices added yet</div>
          <div class="empty-subtext">Click "Add Price" to begin</div>
        </div>

        <!-- Price cards -->
        <div class="price-cards-list">
          <div v-for="(itemPrice, index) in item.item_price" :key="itemPrice.id || index" class="price-card">
            <div class="price-card-header">
              <div class="price-card-num">
                <div class="price-num-badge">{{ Number(index) + 1 }}</div>
                <span class="price-card-title">Price Option {{ Number(index) + 1 }}</span>
              </div>
              <q-btn flat round dense icon="close" @click="deleteItemPrice(Number(index))" class="delete-btn">
                <q-tooltip>Remove</q-tooltip>
              </q-btn>
            </div>
            <div class="price-card-divider"></div>
            <div class="price-fields">
              <q-select dense v-model="itemPrice.unit" :options="units" label="Unit" hide-bottom-space use-input
                outlined :rules="[(val) => !!val || 'Unit is required.']" class="dark-field field" />
              <input-amount label="Original Price" :value="itemPrice.original_price"
                @input="(amount) => changeOriginalPrice(itemPrice, amount)" class="dark-field field" />
              <input-amount label="Online Price" :value="itemPrice.online_price"
                @input="(amount) => changeOnlinePrice(itemPrice, amount)" class="dark-field field" />
              <input-amount label="Selling Price" :value="itemPrice.selling_price"
                @input="(amount) => changeSellingPrice(itemPrice, amount)" class="dark-field field" />
              <q-input v-model="itemPrice.qty" label="Quantity" outlined dense type="number" min="0"
                :rules="[(val) => (val !== null && val !== undefined && val !== '') || 'Quantity is required.']"
                class="dark-field field" />
            </div>
          </div>
        </div>
      </div>

      <!-- Form Actions -->
      <div class="form-actions">
        <q-btn unelevated icon="save" label="Save Changes" @click="createItemPrice" class="save-btn" />
        <q-btn flat icon="cancel" label="Cancel" @click="handleBack" class="cancel-btn" />
      </div>

    </q-form>
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
// ── Dark theme tokens ──────────────────────────────────────────────────────
$dark-base: #0f172a;
$dark-card: #1e293b;
$dark-elevated: #273549;
$border: rgba(255, 255, 255, 0.08);
$accent: #6366f1;
$accent-2: #7c3aed;
$green: #10b981;
$green-2: #059669;
$white: #ffffff;
$muted: rgba(255, 255, 255, 0.5);

// ── Container ──────────────────────────────────────────────────────────────
.item-price-container {
  max-width: 1000px;
  margin: 0 auto;
  padding: 28px 24px;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  color: $white;
}

// ── Hero header ────────────────────────────────────────────────────────────
.page-hero {
  position: relative;
  background: $dark-card;
  border-radius: 20px;
  border: 1px solid $border;
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.3);
  overflow: hidden;
}

.hero-accent-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(99, 102, 241, 0.18) 0%, rgba(124, 58, 237, 0.10) 60%, transparent 100%);
  pointer-events: none;
}

.hero-inner {
  position: relative;
  display: flex;
  align-items: center;
  padding: 26px 30px;
}

.hero-left {
  display: flex;
  align-items: center;
  gap: 16px;
}

.hero-back-btn {
  color: $muted !important;
  border: 1px solid $border !important;
  border-radius: 10px !important;
  transition: color 0.2s, background 0.2s !important;

  &:hover {
    color: $white !important;
    background: rgba(255, 255, 255, 0.07) !important;
  }
}

.hero-icon-wrap {
  width: 50px;
  height: 50px;
  border-radius: 14px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 16px rgba(99, 102, 241, 0.4);
  flex-shrink: 0;
}

.page-title {
  font-size: 22px;
  font-weight: 800;
  color: $white;
  margin: 0 0 3px;
  letter-spacing: -0.3px;
  line-height: 1.2;
}

.page-subtitle {
  font-size: 13px;
  color: $muted;
  font-weight: 500;
}

// ── Form ───────────────────────────────────────────────────────────────────
.price-form {
  display: flex;
  flex-direction: column;
}

// ── Section cards ──────────────────────────────────────────────────────────
.section-card {
  background: $dark-card;
  border: 1px solid $border;
  border-radius: 20px;
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.25);
  overflow: hidden;
}

.section-card-header {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 18px 24px;
  background: $dark-elevated;
}

.section-header-text {
  flex: 1;
}

.section-icon-wrap {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 3px 12px rgba(99, 102, 241, 0.35);
  flex-shrink: 0;

  &--green {
    background: linear-gradient(135deg, $green 0%, $green-2 100%);
    box-shadow: 0 3px 12px rgba(16, 185, 129, 0.35);
  }
}

.section-title {
  font-size: 14px;
  font-weight: 800;
  color: $white;
  line-height: 1.2;
}

.section-sub {
  font-size: 12px;
  color: $muted;
  margin-top: 2px;
}

.card-divider {
  height: 1px;
  background: $border;
}

.section-body {
  padding: 24px;
}

// ── Dark inputs ────────────────────────────────────────────────────────────
.dark-field {
  :deep(.q-field__control) {
    background: $dark-elevated !important;
    border-radius: 12px !important;
  }

  :deep(.q-field__native),
  :deep(.q-field__input) {
    color: $white !important;
  }

  :deep(.q-field__label) {
    color: $muted !important;
  }

  :deep(.q-field__prepend .q-icon),
  :deep(.q-field__append .q-icon) {
    color: $muted !important;
  }

  :deep(.q-field--outlined .q-field__control:before) {
    border-color: $border !important;
  }

  :deep(.q-field--outlined:hover .q-field__control:before) {
    border-color: rgba(99, 102, 241, 0.4) !important;
  }

  :deep(.q-field--focused .q-field__control:before) {
    border-color: $accent !important;
  }

  :deep(.q-field--disabled .q-field__control) {
    opacity: 0.5 !important;
  }
}

.item-name-input {
  :deep(.q-field__control) {
    cursor: not-allowed;
  }
}

// ── Add Price button ────────────────────────────────────────────────────────
.add-price-btn {
  margin-left: auto;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%) !important;
  color: $white !important;
  border-radius: 10px !important;
  font-weight: 700 !important;
  font-size: 13px !important;
  text-transform: none !important;
  letter-spacing: 0 !important;
  height: 38px !important;
  padding: 0 16px !important;
  box-shadow: 0 4px 14px rgba(99, 102, 241, 0.4) !important;
  flex-shrink: 0;

  &:hover {
    box-shadow: 0 6px 20px rgba(99, 102, 241, 0.55) !important;
  }
}

// ── Empty state ────────────────────────────────────────────────────────────
.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 32px;
  border: 2px dashed rgba(255, 255, 255, 0.10);
  border-radius: 16px;
  margin: 20px 24px 24px;
  text-align: center;
}

.empty-icon-wrap {
  width: 72px;
  height: 72px;
  border-radius: 20px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 24px rgba(99, 102, 241, 0.35);
  margin-bottom: 16px;
}

.empty-text {
  font-size: 16px;
  font-weight: 700;
  color: $white;
  margin-bottom: 6px;
}

.empty-subtext {
  font-size: 13px;
  color: $muted;
}

// ── Price cards list ───────────────────────────────────────────────────────
.price-cards-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
  padding: 20px 24px 24px;
}

.price-card {
  background: $dark-elevated;
  border: 1px solid $border;
  border-radius: 16px;
  overflow: hidden;
  transition: border-color 0.2s, box-shadow 0.2s;

  &:hover {
    border-color: rgba(99, 102, 241, 0.3);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
  }
}

.price-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 18px;
  background: rgba(255, 255, 255, 0.03);
}

.price-card-num {
  display: flex;
  align-items: center;
  gap: 10px;
}

.price-num-badge {
  width: 26px;
  height: 26px;
  border-radius: 8px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  color: $white;
  font-size: 12px;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 8px rgba(99, 102, 241, 0.35);
  flex-shrink: 0;
}

.price-card-title {
  font-size: 14px;
  font-weight: 700;
  color: $white;
}

.delete-btn {
  color: $muted !important;
  border-radius: 8px !important;
  transition: color 0.2s, background 0.2s !important;

  &:hover {
    color: #fca5a5 !important;
    background: rgba(239, 68, 68, 0.12) !important;
  }
}

.price-card-divider {
  height: 1px;
  background: $border;
}

// ── Price fields grid ──────────────────────────────────────────────────────
.price-fields {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
  padding: 20px 18px;
}

.field {
  width: 100%;
}

// ── Form Actions ───────────────────────────────────────────────────────────
.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding-top: 4px;
}

.save-btn {
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%) !important;
  color: $white !important;
  border-radius: 12px !important;
  font-weight: 700 !important;
  font-size: 14px !important;
  text-transform: none !important;
  letter-spacing: 0 !important;
  height: 44px !important;
  padding: 0 28px !important;
  box-shadow: 0 4px 16px rgba(99, 102, 241, 0.4) !important;

  &:hover {
    box-shadow: 0 6px 22px rgba(99, 102, 241, 0.55) !important;
  }
}

.cancel-btn {
  color: $muted !important;
  border: 1px solid $border !important;
  border-radius: 12px !important;
  font-weight: 600 !important;
  font-size: 14px !important;
  text-transform: none !important;
  letter-spacing: 0 !important;
  height: 44px !important;
  padding: 0 24px !important;

  &:hover {
    color: $white !important;
    background: rgba(255, 255, 255, 0.06) !important;
    border-color: rgba(255, 255, 255, 0.15) !important;
  }
}

// ── Responsive ─────────────────────────────────────────────────────────────
@media (max-width: 768px) {
  .item-price-container {
    padding: 16px 12px;
  }

  .hero-inner {
    padding: 20px;
  }

  .page-title {
    font-size: 18px;
  }

  .section-card-header {
    flex-wrap: wrap;
    gap: 10px;
  }

  .add-price-btn {
    margin-left: 0;
    width: 100% !important;
  }

  .price-fields {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column;
  }

  .save-btn,
  .cancel-btn {
    width: 100% !important;
  }

  .section-body {
    padding: 16px;
  }

  .price-cards-list {
    padding: 14px 16px 18px;
  }
}
</style>

<style>
.item-price-container .section-card .q-card__section {
  background: transparent !important;
  color: #ffffff !important;
}
</style>
