<template>
  <div class="edit-store-page">

    <!-- Hero Header -->
    <div class="page-hero q-mb-xl">
      <div class="hero-accent-overlay"></div>
      <div class="hero-inner">
        <div class="hero-icon-wrap">
          <q-icon name="store" size="28px" color="white" />
        </div>
        <div class="hero-text">
          <h1 class="hero-title">Edit Store</h1>
          <div class="hero-subtitle">Update store information and location</div>
        </div>
        <router-link :to="`${$route.path}/items`" class="hero-action-btn">
          <q-icon name="inventory_2" size="18px" />
          <span>View Items</span>
        </router-link>
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
  desc: '',
  mobile: '',
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
        entity: 'all_stores',
        optimus_id: route.params.id,
        data: {
          name: data.name,
          desc: data.desc,
          mobile: data.mobile,
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
    entity: 'all_stores',
    optimus_id: Number(route.params.id),
    query: {
      show_mobile: 1
    },
  }) as StoreData;

  store.value.name = result.name || '';
  store.value.desc = result.desc || '';
  store.value.mobile = result.mobile || '';
  store.value.latitude = result.latitude || 14.5995;
  store.value.longitude = result.longitude || 120.9842;
  store.value.optimus_id = result.optimus_id || 0;
});
</script>

<style scoped lang="scss">
// ── Dark theme tokens (matching DashboardLayout / ProfilePage) ───────────────
$dark-base: #0f172a;
$dark-card: #1e293b;
$dark-elevated: #273549;
$border: rgba(255, 255, 255, 0.08);
$accent: #6366f1;
$accent-2: #7c3aed;
$green: #10b981;
$white: #ffffff;
$muted: rgba(255, 255, 255, 0.5);

// ── Page Container ───────────────────────────────────────────────────────────
.edit-store-page {
  max-width: 1000px;
  margin: 0 auto;
  padding: 28px 24px 60px;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  color: $white;
}

// ── Hero Header ──────────────────────────────────────────────────────────────
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
  background: linear-gradient(135deg, rgba($accent, 0.18) 0%, rgba($accent-2, 0.10) 60%, transparent 100%);
  pointer-events: none;
}

.hero-inner {
  position: relative;
  display: flex;
  align-items: center;
  padding: 32px 36px;
  gap: 16px;
  flex-wrap: wrap;
}

.hero-icon-wrap {
  width: 56px;
  height: 56px;
  border-radius: 16px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 24px rgba($accent, 0.4);
  flex-shrink: 0;
}

.hero-text {
  flex: 1;
  min-width: 0;
}

.hero-title {
  font-size: 26px;
  font-weight: 800;
  color: $white !important;
  margin: 0 0 4px;
  letter-spacing: -0.3px;
  line-height: 1.2;
}

.hero-subtitle {
  font-size: 14px;
  color: $muted;
  font-weight: 500;
}

.hero-action-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  border-radius: 14px;
  background: linear-gradient(135deg, $green 0%, darken($green, 8%) 100%);
  color: $white;
  font-size: 14px;
  font-weight: 700;
  text-decoration: none;
  box-shadow: 0 6px 20px rgba($green, 0.35);
  transition: all 0.25s ease;
  flex-shrink: 0;

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 28px rgba($green, 0.45);
  }

  &:active {
    transform: translateY(0);
  }
}

// ── Responsive ───────────────────────────────────────────────────────────────
@media (max-width: 768px) {
  .edit-store-page {
    padding: 16px 12px 48px;
  }

  .hero-inner {
    padding: 24px 20px;
    gap: 14px;
  }

  .hero-title {
    font-size: 22px;
  }

  .hero-action-btn {
    width: 100%;
    justify-content: center;
    padding: 14px 20px;
  }
}

@media (max-width: 600px) {
  .edit-store-page {
    padding: 10px 8px 40px;
  }

  .hero-inner {
    padding: 20px 16px;
    gap: 12px;
  }

  .hero-icon-wrap {
    width: 46px;
    height: 46px;
    border-radius: 13px;
  }

  .hero-title {
    font-size: 20px;
  }

  .hero-action-btn {
    font-size: 13px;
    padding: 12px 18px;
  }
}
</style>