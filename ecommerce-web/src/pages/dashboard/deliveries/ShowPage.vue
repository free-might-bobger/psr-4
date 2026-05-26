<template>
  <div class="delivery-detail-container">
    <div class="page-header q-mb-lg">
      <q-btn flat color="primary" icon="arrow_back" label="Back" @click="$router.back()" class="q-mr-md" />
      <div class="header-title">
        <h2 class="text-h4 text-weight-bold">Delivery Navigation</h2>
        <div class="text-body2 text-grey-7">Navigate to store with Google Maps</div>
      </div>
    </div>

    <div v-if="loading" class="loading-container">
      <q-spinner size="50px" color="primary" />
      <div class="text-body1 q-mt-md">Loading delivery details...</div>
    </div>

    <div v-else-if="error" class="error-container">
      <q-icon name="error" size="50px" color="negative" />
      <div class="text-h6 q-mt-md text-negative">{{ error }}</div>
    </div>

    <div v-else-if="transaction" class="delivery-content">
      <q-card flat bordered class="delivery-card q-mb-md">
        <q-card-section class="card-header">
          <div class="row items-center">
            <q-icon name="store" size="32px" color="primary" class="q-mr-sm" />
            <div>
              <div class="text-h6 text-weight-bold">{{ transaction.store?.name || 'Unknown Store' }}</div>
              <div class="text-body2 text-grey-7">Reference: {{ transaction.reference_id }}</div>
            </div>
          </div>
        </q-card-section>

        <q-separator />

        <q-card-section class="card-content">
          <div class="info-row">
            <q-icon name="location_on" color="primary" class="q-mr-sm" />
            <span class="info-label">Store Location:</span>
            <span class="info-value">{{ transaction.store?.latitude }}, {{ transaction.store?.longitude }}</span>
          </div>
          <div class="info-row">
            <q-icon name="local_shipping" color="primary" class="q-mr-sm" />
            <span class="info-label">Delivery Location:</span>
            <span class="info-value">{{ transaction.lat }}, {{ transaction.lng }}</span>
          </div>
          <div class="info-row">
            <q-icon name="attach_money" color="primary" class="q-mr-sm" />
            <span class="info-label">Delivery Charge:</span>
            <span class="info-value">{{ transaction.delivery_charge }}</span>
          </div>
          <div class="info-row">
            <q-icon name="receipt_long" color="primary" class="q-mr-sm" />
            <span class="info-label">Total:</span>
            <span class="info-value">{{ transaction.grand_total }}</span>
          </div>
        </q-card-section>

        <q-separator />

        <q-card-section class="card-actions">
          <q-btn
            v-if="!userLocation"
            color="grey-7"
            icon="my_location"
            label="Get My Location"
            size="lg"
            class="full-width"
            @click="getCurrentLocation"
          />
          <template v-else>
            <q-btn
              color="primary"
              icon="store"
              label="Navigate to Store"
              size="lg"
              class="full-width navigate-btn q-mb-sm"
              @click="navigateToStore"
            >
              <q-tooltip>Open Google Maps to navigate to store</q-tooltip>
            </q-btn>
            <q-btn
              color="secondary"
              icon="local_shipping"
              label="Navigate to Delivery"
              size="lg"
              class="full-width navigate-btn"
              @click="navigateToDelivery"
            >
              <q-tooltip>Open Google Maps to navigate to delivery location</q-tooltip>
            </q-btn>
          </template>
        </q-card-section>
      </q-card>

      <q-card flat bordered class="info-card">
        <q-card-section>
          <div class="text-subtitle1 text-weight-bold q-mb-sm">
            <q-icon name="info" color="primary" class="q-mr-xs" />
            Navigation Instructions
          </div>
          <ul class="instruction-list">
            <li>Click "Get My Location" to enable location services</li>
            <li>Then click "Navigate with Motorcycle" to open Google Maps</li>
            <li>Google Maps will open with motorcycle as the travel mode</li>
            <li>Follow the navigation to reach the store</li>
          </ul>
        </q-card-section>
      </q-card>
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
      optimus_id: Number(route.params.id),
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
  try {
    const position = await getLocation();
    userLocation.value = {
      latitude: position.coords.latitude,
      longitude: position.coords.longitude,
    };
  } catch (err) {
    error.value = 'Failed to get your location. Please enable location services.';
    console.error(err);
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
});
</script>

<style scoped lang="scss">
.delivery-detail-container {
  padding: 24px;
  max-width: 800px;
  margin: 0 auto;
}

.page-header {
  display: flex;
  align-items: center;
}

.header-title {
  flex: 1;
}

.loading-container,
.error-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 24px;
}

.delivery-content {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.delivery-card,
.info-card {
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.card-header {
  background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 100%);
  padding: 20px;
}

.card-content {
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.info-row {
  display: flex;
  align-items: center;
  padding: 8px 0;
}

.info-label {
  font-weight: 600;
  color: #666;
  margin-left: 8px;
  margin-right: 8px;
}

.info-value {
  color: #1a1a1a;
  font-weight: 500;
}

.card-actions {
  padding: 20px;
}

.navigate-btn {
  font-weight: 600;
  border-radius: 8px;
  transition: all 0.3s ease;

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(25, 118, 210, 0.3);
  }
}

.instruction-list {
  margin: 0;
  padding-left: 20px;
  color: #666;

  li {
    margin-bottom: 8px;
    line-height: 1.5;
  }
}

@media (max-width: 768px) {
  .delivery-detail-container {
    padding: 16px;
  }

  .page-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }
}
</style>
