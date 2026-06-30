<template>
  <div class="delivery-detail-container">
    <!-- Hero Header -->
    <div class="page-hero q-mb-xl">
      <div class="hero-accent-overlay"></div>
      <div class="hero-inner">
        <q-btn flat round icon="arrow_back" color="white" class="back-btn" @click="$router.back()">
          <q-tooltip>Back</q-tooltip>
        </q-btn>
        <div class="hero-center">
          <div class="hero-icon-wrap">
            <q-icon name="route" size="28px" color="white" />
          </div>
          <div>
            <h1 class="page-title">Delivery Navigation</h1>
            <div class="page-subtitle">Navigate to store and delivery locations</div>
          </div>
        </div>
        <div class="hero-right-actions">
          <q-btn flat round dense :icon="locationLoading ? '' : (userLocation ? 'my_location' : 'location_disabled')"
            class="location-refresh-btn" :loading="locationLoading" @click="getCurrentLocation">
            <q-tooltip>{{ userLocation ? 'Refresh Location' : 'Get Location' }}</q-tooltip>
          </q-btn>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <div class="empty-icon-wrap">
        <q-spinner size="36px" color="white" />
      </div>
      <div class="empty-title q-mt-md">Loading delivery details…</div>
      <div class="empty-subtitle">Getting store and location information</div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-container">
      <div class="error-icon-wrap">
        <q-icon name="error_outline" size="40px" color="white" />
      </div>
      <div class="error-title q-mt-md">{{ error }}</div>
      <q-btn flat label="Go Back" icon="arrow_back" color="white" class="q-mt-md" @click="$router.back()" />
    </div>

    <!-- Main Content -->
    <div v-else-if="transaction" class="delivery-content">
      <!-- Left: Delivery Card -->
      <div class="main-card">
        <div class="card-accent-strip"></div>
        <div class="card-body">
          <!-- Header -->
          <div class="card-header">
            <div class="store-icon-wrap">
              <q-icon name="store" size="24px" color="white" />
            </div>
            <div class="header-text">
              <div class="store-name">{{ transaction.store?.name || 'Unknown Store' }}</div>
              <div class="reference">Ref: {{ transaction.reference_id }}</div>
            </div>
          </div>

          <!-- Info Grid -->
          <div class="info-grid">
            <div class="info-item">
              <div class="info-icon-wrap">
                <q-icon name="location_on" size="18px" color="white" />
              </div>
              <div class="info-text">
                <div class="info-label">Store Location</div>
                <div class="info-value">{{ transaction.store?.latitude ?? 'N/A' }}, {{ transaction.store?.longitude ??
                  'N/A' }}</div>
              </div>
            </div>
            <div class="info-item">
              <div class="info-icon-wrap info-icon-wrap--violet">
                <q-icon name="local_shipping" size="18px" color="white" />
              </div>
              <div class="info-text">
                <div class="info-label">Delivery Location</div>
                <div class="info-value">{{ transaction.lat ?? 'N/A' }}, {{ transaction.lng ?? 'N/A' }}</div>
              </div>
            </div>
            <div class="info-item">
              <div class="info-icon-wrap info-icon-wrap--green">
                <q-icon name="attach_money" size="18px" color="white" />
              </div>
              <div class="info-text">
                <div class="info-label">Delivery Charge</div>
                <div class="info-value highlight-value">{{ transaction.delivery_charge }}</div>
              </div>
            </div>
            <div class="info-item">
              <div class="info-icon-wrap info-icon-wrap--amber">
                <q-icon name="receipt_long" size="18px" color="white" />
              </div>
              <div class="info-text">
                <div class="info-label">Grand Total</div>
                <div class="info-value highlight-value">{{ transaction.grand_total }}</div>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="card-actions">
            <q-btn unelevated icon="store" label="Navigate to Store" size="md"
              class="navigate-btn navigate-btn--primary" @click="navigateToStore">
              <q-tooltip>Open Google Maps directions to store</q-tooltip>
            </q-btn>
            <q-btn unelevated icon="local_shipping" label="Navigate to Delivery" size="md"
              class="navigate-btn navigate-btn--secondary" @click="navigateToDelivery">
              <q-tooltip>Open Google Maps directions to delivery</q-tooltip>
            </q-btn>
          </div>
        </div>
      </div>

      <!-- Right: Instructions -->
      <div class="side-card">
        <div class="card-body">
          <div class="side-header">
            <div class="info-icon-wrap">
              <q-icon name="info" size="18px" color="white" />
            </div>
            <div class="side-title">Navigation Instructions</div>
          </div>
          <ul class="instruction-list">
            <li>
              <span class="instruction-bullet"></span>
              Your location is automatically retrieved when the page loads.
            </li>
            <li>
              <span class="instruction-bullet"></span>
              Click <strong>Navigate to Store</strong> to open Google Maps for store navigation.
            </li>
            <li>
              <span class="instruction-bullet"></span>
              Click <strong>Navigate to Delivery</strong> to open Google Maps for delivery navigation.
            </li>
            <li>
              <span class="instruction-bullet"></span>
              Follow the directions to reach your destination safely.
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { show } from 'src/boot/axios-call';
import { onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import { getLocation } from 'src/boot/utilities';

const route = useRoute();
const loading = ref(true);
const error = ref('');
const transaction = ref<any>(null);
const userLocation = ref<{ latitude: number; longitude: number } | null>(null);
const locationLoading = ref(false);

interface TransactionDetail {
  id: number;
  reference_id: string;
  lat: number | null;
  lng: number | null;
  delivery_charge: string;
  total: number;
  grand_total: number;
  store?: {
    id: number;
    name: string;
    latitude: number;
    longitude: number;
  };
}

async function fetchTransactionData() {
  try {
    const result = await show<TransactionDetail>({
      message: 'Getting transaction...',
      entity: 'all-transactions',
      optimus_id: Number(route.params.deliveryId),
      query: {
        with: 'store',
      },
    });
    if (result) {
      transaction.value = result;
    } else {
      error.value = 'Transaction not found';
    }
  } catch (err) {
    error.value = 'Failed to load transaction details';
    console.error(err);
  } finally {
    loading.value = false;
  }
}

async function getCurrentLocation() {
  locationLoading.value = true;
  try {
    const position = await getLocation();
    userLocation.value = {
      latitude: position.coords.latitude,
      longitude: position.coords.longitude,
    };
    error.value = '';
  } catch (err) {
    console.error('Location error:', err);
    userLocation.value = null;
  } finally {
    locationLoading.value = false;
  }
}

function navigateToStore() {
  if (!userLocation.value || !transaction.value?.store) {
    error.value = 'Location or store information not available';
    return;
  }

  const { latitude: originLat, longitude: originLng } = userLocation.value;
  const { latitude: destLat, longitude: destLng } = transaction.value.store;

  // Google Maps URL with directions in driving mode to store
  const url = `https://www.google.com/maps/dir/${originLat},${originLng}/${destLat},${destLng}?dirflg=d`;

  window.open(url, '_blank');
}

function navigateToDelivery() {
  if (!userLocation.value || !transaction.value?.lat || !transaction.value?.lng) {
    error.value = 'Location or delivery information not available';
    return;
  }

  const { latitude: originLat, longitude: originLng } = userLocation.value;
  const destLat = transaction.value.lat;
  const destLng = transaction.value.lng;

  // Google Maps URL with directions in driving mode to delivery location
  const url = `https://www.google.com/maps/dir/${originLat},${originLng}/${destLat},${destLng}?dirflg=d`;

  window.open(url, '_blank');
}

onMounted(async () => {
  await fetchTransactionData();
  await getCurrentLocation();
});
</script>

<style scoped lang="scss">
// ── Dark theme tokens (matching DashboardLayout and ProfilePage) ──────────────
$dark-base: #0f172a;
$dark-card: #1e293b;
$dark-elevated: #273549;
$border: rgba(255, 255, 255, 0.08);
$accent: #6366f1;
$accent-2: #7c3aed;
$green: #10b981;
$amber: #f59e0b;
$red: #ef4444;
$white: #ffffff;
$muted: rgba(255, 255, 255, 0.55);

.delivery-detail-container {
  padding: 28px 24px;
  max-width: 1000px;
  margin: 0 auto;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  color: $white;
}

.page-hero {
  position: relative;
  background: $dark-card;
  border-radius: 20px;
  border: 1px solid $border;
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.3);
  overflow: hidden;
  margin-bottom: 32px;
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
  justify-content: space-between;
  padding: 28px 32px;
  gap: 16px;
}

.hero-center {
  display: flex;
  align-items: center;
  gap: 18px;
  flex: 1;
  justify-content: center;
}

.hero-icon-wrap {
  width: 56px;
  height: 56px;
  border-radius: 16px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 24px rgba(99, 102, 241, 0.4);
  flex-shrink: 0;
}

.page-title {
  font-size: 26px;
  font-weight: 800;
  color: $white !important;
  margin: 0 0 4px;
  letter-spacing: -0.3px;
  line-height: 1.2;
}

.page-subtitle {
  font-size: 14px;
  color: $muted;
  font-weight: 500;
}

.back-btn {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.06) !important;
  border: 1px solid $border !important;
  color: $white !important;

  &:hover {
    background: rgba(255, 255, 255, 0.12) !important;
  }
}

.hero-right-actions {
  display: flex;
  align-items: center;
}

.location-refresh-btn {
  color: rgba(255, 255, 255, 0.7);
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.12);
  transition: all 0.2s;

  &:hover {
    color: white;
    background: rgba($accent, 0.2);
    border-color: rgba($accent, 0.4);
  }
}

.loading-container,
.error-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 80px 24px;
  text-align: center;
  background: $dark-card;
  border: 1px solid $border;
  border-radius: 20px;
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.25);
}

.empty-icon-wrap,
.error-icon-wrap {
  width: 80px;
  height: 80px;
  border-radius: 24px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.empty-icon-wrap {
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  box-shadow: 0 8px 24px rgba(99, 102, 241, 0.35);
}

.error-icon-wrap {
  background: linear-gradient(135deg, $red 0%, #f87171 100%);
  box-shadow: 0 8px 24px rgba(239, 68, 68, 0.35);
}

.empty-title,
.error-title {
  font-size: 18px;
  font-weight: 700;
  color: $white;
}

.empty-subtitle {
  font-size: 14px;
  color: $muted;
  margin-top: 4px;
}

.delivery-content {
  display: grid;
  grid-template-columns: 1.4fr 1fr;
  gap: 20px;
  align-items: start;
}

.main-card,
.side-card {
  background: $dark-card;
  border: 1px solid $border;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 8px 40px rgba(0, 0, 0, 0.25);
  display: flex;
}

.card-accent-strip {
  width: 4px;
  flex-shrink: 0;
  background: linear-gradient(180deg, $accent 0%, $accent-2 100%);
}

.card-body {
  flex: 1;
  padding: 24px;
  display: flex;
  flex-direction: column;
  gap: 22px;
}

.card-header {
  display: flex;
  align-items: center;
  gap: 14px;
}

.store-icon-wrap {
  width: 48px;
  height: 48px;
  border-radius: 14px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 6px 18px rgba(99, 102, 241, 0.35);
  flex-shrink: 0;
}

.store-name {
  font-size: 18px;
  font-weight: 800;
  color: $white;
  letter-spacing: -0.2px;
}

.reference {
  font-size: 13px;
  color: $muted;
  margin-top: 2px;
}

.info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
}

.info-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid $border;
  border-radius: 14px;
  padding: 14px;
  transition: background 0.18s ease;

  &:hover {
    background: rgba(255, 255, 255, 0.06);
  }
}

.info-icon-wrap {
  width: 34px;
  height: 34px;
  border-radius: 10px;
  background: linear-gradient(135deg, $accent 0%, $accent-2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 3px 10px rgba(99, 102, 241, 0.3);

  &--violet {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
  }

  &--green {
    background: linear-gradient(135deg, #34d399 0%, $green 100%);
  }

  &--amber {
    background: linear-gradient(135deg, #fbbf24 0%, $amber 100%);
  }
}

.info-text {
  min-width: 0;
}

.info-label {
  font-size: 12px;
  font-weight: 600;
  color: $muted;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 4px;
}

.info-value {
  font-size: 14px;
  font-weight: 700;
  color: $white;
  word-break: break-word;
}

.highlight-value {
  color: #6ee7b7;
}

.card-actions {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.navigate-btn {
  border-radius: 12px !important;
  font-weight: 700 !important;
  font-size: 14px !important;
  height: 48px !important;
  text-transform: none !important;
  letter-spacing: 0 !important;
  transition: all 0.2s ease;

  &:hover {
    transform: translateY(-2px);
  }

  &--primary {
    background: linear-gradient(135deg, $accent 0%, $accent-2 100%) !important;
    color: $white !important;
    box-shadow: 0 6px 20px rgba(99, 102, 241, 0.35) !important;

    &:hover {
      box-shadow: 0 8px 24px rgba(99, 102, 241, 0.45) !important;
    }
  }

  &--secondary {
    background: rgba(255, 255, 255, 0.08) !important;
    color: $white !important;
    border: 1px solid $border !important;

    &:hover {
      background: rgba(255, 255, 255, 0.14) !important;
    }
  }
}

.side-card {
  flex-direction: column;
}

.side-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 4px;
}

.side-title {
  font-size: 16px;
  font-weight: 800;
  color: $white;
}

.instruction-list {
  margin: 0;
  padding: 0;
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 12px;

  li {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: 14px;
    color: $muted;
    line-height: 1.5;

    strong {
      color: $white;
      font-weight: 700;
    }
  }
}

.instruction-bullet {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: $accent;
  margin-top: 7px;
  flex-shrink: 0;
  box-shadow: 0 0 8px rgba(99, 102, 241, 0.5);
}

@media (max-width: 768px) {
  .delivery-detail-container {
    padding: 16px 12px;
  }

  .hero-inner {
    padding: 20px 18px;
  }

  .hero-center {
    gap: 12px;
  }

  .hero-icon-wrap {
    width: 44px;
    height: 44px;
  }

  .page-title {
    font-size: 20px;
  }

  .hero-right-actions {
    margin-top: 8px;
  }

  .delivery-content {
    grid-template-columns: 1fr;
  }

  .info-grid {
    grid-template-columns: 1fr;
  }

  .card-actions {
    grid-template-columns: 1fr;
  }
}
</style>
