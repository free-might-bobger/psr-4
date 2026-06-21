<template>
  <div class="edit-store-page">

    <!-- Hero Header -->
    <div class="detail-hero q-mb-xl">
      <div class="hero-accent"></div>
      <div class="hero-body">
        <div class="hero-left">
          <div class="hero-icon-wrap">
            <q-icon name="store" size="26px" color="white" />
          </div>
          <div>
            <h1 class="hero-title">Edit Store</h1>
            <div class="hero-sub">Update store information and location</div>
          </div>
        </div>
        <div class="hero-actions">
          <q-btn unelevated icon="receipt" label="Transactions" :to="`${$route.path}/transactions`"
            class="hero-action-btn">
            <q-tooltip>View store transactions</q-tooltip>
          </q-btn>
          <q-btn unelevated icon="inventory_2" label="Items" :to="`${$route.path}/items`"
            class="hero-action-btn hero-action-btn--secondary">
            <q-tooltip>View store items</q-tooltip>
          </q-btn>
          <q-btn flat icon="arrow_back" label="Back" @click="$router.back()" class="back-btn" />
        </div>
      </div>
    </div>

    <!-- Form Section -->
    <StoreEditForm :store="store" :is-submitting="isSubmitting" @submit="onSubmit" @cancel="$router.back()" />

  </div>
</template>

<script lang="ts" setup>
import { ref, onBeforeMount } from 'vue';
import { update, show } from 'src/boot/axios-call';
import { useRoute } from "vue-router";
import StoreEditForm from 'src/components/StoreEditForm.vue';

interface StoreData {
  name: string;
  desc: string;
  mobile: string;
  latitude: number;
  longitude: number;
  optimus_id: number;
}

const route = useRoute();
const store = ref<StoreData>({
  name: '',
  mobile: '',
  desc: '',
  latitude: 14.5995,
  longitude: 120.9842,
  optimus_id: 0,
});

const isSubmitting = ref(false);

const onSubmit = async (data: StoreData) => {
  isSubmitting.value = true;
  try {
    await update(
      {
        entity: 'my-stores',
        optimus_id: route.params.id,
        data: {
          name: data.name,
          mobile: data.mobile,
          desc: data.desc,
          latitude: data.latitude,
          longitude: data.longitude,
        },
      },
      true
    );
  } catch (error) {
    console.error('Error updating store:', error);
  } finally {
    isSubmitting.value = false;
  }
};

onBeforeMount(async () => {
  const result = await show({
    entity: 'my-stores',
    optimus_id: Number(route.params.id),
    query: {
      show_mobile: 1
    },
  }) as StoreData;

  store.value.name = result.name || '';
  store.value.mobile = result.mobile || '';
  store.value.desc = result.desc || '';
  store.value.latitude = result.latitude || 14.5995;
  store.value.longitude = result.longitude || 120.9842;
  store.value.optimus_id = result.optimus_id || 0;
});
</script>

<style scoped lang="scss">
// ── Dark theme tokens ──────────────────────────────────────────────────────
$dark-base: #0f172a;
$dark-card: #1e293b;
$dark-elevated: #273549;
$border: rgba(255, 255, 255, 0.08);
$accent: #6366f1;
$accent-2: #7c3aed;
$white: #ffffff;
$muted: rgba(255, 255, 255, 0.5);

// ── Container ──────────────────────────────────────────────────────────────
.edit-store-page {
  padding: 28px 24px;
  max-width: 900px;
  margin: 0 auto;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  color: $white;
}

// ── Hero header ────────────────────────────────────────────────────────────
.detail-hero {
  position: relative;
  background: $dark-card;
  border-radius: 20px;
  border: 1px solid $border;
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.3);
  overflow: hidden;
}

.hero-accent {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(99, 102, 241, 0.18) 0%, rgba(124, 58, 237, 0.10) 60%, transparent 100%);
  pointer-events: none;
}

.hero-body {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 28px 32px;
  gap: 16px;
  flex-wrap: wrap;
}

.hero-left {
  display: flex;
  align-items: center;
  gap: 18px;
}

.hero-icon-wrap {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 16px rgba(99, 102, 241, 0.4);
  flex-shrink: 0;
}

.hero-title {
  font-size: 22px;
  font-weight: 800;
  color: $white;
  margin: 0 0 4px;
  letter-spacing: -0.3px;
  line-height: 1.2;
}

.hero-sub {
  font-size: 13px;
  color: $muted;
  font-weight: 500;
}

.hero-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
}

.hero-action-btn {
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%) !important;
  color: $white !important;
  border-radius: 12px !important;
  font-weight: 700 !important;
  font-size: 13px !important;
  text-transform: none !important;
  letter-spacing: 0 !important;
  height: 40px !important;
  padding: 0 16px !important;
  box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35) !important;
  transition: opacity 0.2s !important;

  &:hover {
    opacity: 0.88;
  }

  &--secondary {
    background: rgba(99, 102, 241, 0.15) !important;
    border: 1px solid rgba(99, 102, 241, 0.35) !important;
    color: #a5b4fc !important;
    box-shadow: none !important;

    &:hover {
      background: rgba(99, 102, 241, 0.25) !important;
      opacity: 1;
    }
  }
}

.back-btn {
  color: $white !important;
  border: 1px solid $border !important;
  border-radius: 12px !important;
  text-transform: none !important;
  font-weight: 600 !important;
  letter-spacing: 0 !important;
  padding: 6px 16px !important;
  height: 40px !important;

  &:hover {
    background: rgba(255, 255, 255, 0.07) !important;
  }
}

// ── Responsive ─────────────────────────────────────────────────────────────
@media (max-width: 768px) {
  .edit-store-page {
    padding: 16px 12px;
  }

  .hero-body {
    padding: 20px;
    flex-direction: column;
    align-items: flex-start;
  }

  .hero-actions {
    width: 100%;
  }

  .hero-action-btn {
    flex: 1;
  }
}
</style>
