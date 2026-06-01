<template>
  <div class="product-page">
    <div class="product-container">
      <!-- Header -->
      <div class="product-header">
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

      <!-- Main Content -->
      <div class="product-content" v-if="item">
        <!-- Left: Image Gallery -->
        <div class="product-gallery">
          <div class="main-image-wrapper">
            <img
              :src="item.images?.[slide]?.path_url || ''"
              class="main-image"
              :alt="item.name"
              @click="openZoomModal(item.images?.[slide]?.path_url || '')"
            />
            <q-btn
              icon="zoom_in"
              class="zoom-btn"
              round
              flat
              @click="openZoomModal(item.images?.[slide]?.path_url || '')"
            />
          </div>
          <div class="thumbnail-list" v-if="item.images && item.images.length > 1">
            <div
              v-for="(image, index) in item.images"
              :key="image.id"
              class="thumbnail-item"
              :class="{ active: slide === index }"
              @click="slide = index"
            >
              <img :src="image.path_url" :alt="item.name" />
            </div>
          </div>
        </div>

        <!-- Right: Product Info -->
        <div class="product-info">
          <h1 class="product-name">{{ item.name }}</h1>
          <div class="product-description" v-html="sanitizeHtml(item.description || '')"></div>

          <div class="info-section">
            <div class="section-label">Select Unit</div>
            <div class="unit-selector" v-if="units.length > 0">
              <button
                v-for="unit in units"
                :key="unit.id"
                class="unit-btn"
                :class="{ active: selectedUnit === unit.id }"
                @click="selectedUnit = unit.id"
              >
                {{ unit.name }}
              </button>
            </div>
          </div>

          <div class="info-section">
            <div class="section-label">Price</div>
            <div class="price-display">{{ getPriceRange(filteredItemPrice) }}</div>
          </div>

          <div class="purchase-section">
            <div class="quantity-wrapper">
              <div class="section-label">Quantity</div>
              <div class="quantity-control">
                <q-btn
                  icon="remove"
                  flat
                  dense
                  round
                  size="sm"
                  @click="qty > 1 ? qty-- : null"
                  :disable="qty <= 1"
                />
                <span class="quantity-value">{{ qty }}</span>
                <q-btn
                  icon="add"
                  flat
                  dense
                  round
                  size="sm"
                  @click="qty++"
                />
              </div>
            </div>

            <div class="action-buttons">
              <q-btn
                color="primary"
                @click="userAddCart"
                size="lg"
                unelevated
                class="add-cart-btn"
                icon="shopping_cart"
                label="Add to Cart"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Zoom Modal -->
    <q-dialog v-model="isZoomModalOpen" maximized>
      <q-card class="zoom-modal-card">
        <q-card-section class="zoom-modal-header">
          <q-space />
          <q-btn icon="close" flat round dense @click="closeZoomModal" />
        </q-card-section>
        <q-separator />
        <q-card-section class="zoom-modal-content">
          <div
            class="zoom-modal-container"
            :class="{ 'is-zoomed': isZoomed, 'is-dragging': isDragging }"
            @click="toggleZoom"
            @mousedown.prevent="onDragStart"
            @mousemove.prevent="onDragMove"
            @mouseup="onDragEnd"
            @mouseleave="onDragEnd"
            @touchstart.passive="onTouchStart"
            @touchmove.passive="onTouchMove"
            @touchend="onDragEnd"
          >
            <img
              v-if="zoomImageUrl"
              :src="zoomImageUrl"
              class="zoom-modal-image"
              :class="{ 'is-zoomed': isZoomed }"
              :style="zoomStyle"
              :alt="item.name"
              draggable="false"
            />
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>

    <!-- Agent Call Dialog -->
    <q-dialog
      v-model="agentCallDialogOpen"
      maximized
      transition-show="slide-up"
      transition-hide="slide-down"
      @show="onAgentCallDialogShow"
      @hide="onAgentCallDialogHide"
    >
      <q-card class="agent-call-dialog-card">
        <q-inner-loading :showing="agentCallWebRtcBusy" color="primary" label="Connecting camera and call room…" />
        <q-bar class="bg-primary text-white">
          <q-icon name="support_agent" class="q-mr-sm" />
          <div>Talk to Agent — Live call</div>
          <q-space />
          <q-btn flat dense round icon="close" v-close-popup />
        </q-bar>
        <q-card-section class="q-pa-md">
          <p class="text-body2 text-grey-8 q-mb-md">
            Allow camera and microphone. Your agent can join using the link below (no login required for you).
          </p>
          <div class="row q-col-gutter-sm q-mb-md">
            <div class="col-12 col-md-6">
              <div class="text-caption text-grey-7 q-mb-xs">You</div>
              <video ref="agentLocalVideoRef" class="agent-call-video" autoplay playsinline muted />
            </div>
            <div class="col-12 col-md-6">
              <div class="text-caption text-grey-7 q-mb-xs">Agent</div>
              <video ref="agentRemoteVideoRef" class="agent-call-video" autoplay playsinline />
            </div>
          </div>
          <div v-if="agentJoinUrl" class="text-caption q-mb-sm">
            <strong>Agent link:</strong> {{ agentJoinUrl }}
          </div>
        </q-card-section>
        <q-card-actions align="right" class="q-pa-md">
          <q-btn
            v-if="agentJoinUrl"
            flat
            color="primary"
            label="Copy agent link"
            icon="content_copy"
            @click="copyAgentJoinLink"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Store Conflict Dialog -->
    <q-dialog v-model="storeConflictDialog" persistent>
      <q-card>
        <q-card-section class="row items-center">
          <div class="text-h6">Multiple Stores Detected</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section>
          Your cart contains items from another store. Adding this item will remove the existing items from your cart. Do you want to proceed?
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="grey-7" v-close-popup @click="handleStoreConflictCancel" />
          <q-btn unelevated label="OK" color="primary" @click="handleStoreConflictOk" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>
<script setup lang="ts">
import { useRoute } from 'vue-router';
import { onMounted, ref, watch, computed, nextTick, type Ref } from 'vue';
import { show } from 'src/boot/axios-call';
import { getPriceRange } from 'src/boot/utilities';
import { useUserCartStore } from 'src/stores/userCart';
import { useQuasar } from 'quasar';
import BreadCrumbsWrapper from 'src/components/BreadCrumbsWrapper.vue';
import { startAgentCallSession } from 'src/helpers/agentCall';
import { isFirebaseConfigured } from 'src/helpers/firebaseCore';
import { startCallerSession } from 'src/helpers/webrtcFirebaseCall';

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
const isZoomModalOpen = ref(false);
const zoomImageUrl = ref('');
const isZoomed = ref(false);
const isDragging = ref(false);
const dragMoved = ref(false);
const zoomScale = ref(2);
const pan = ref({ x: 0, y: 0 });
const dragStart = ref({ x: 0, y: 0 });
const panStart = ref({ x: 0, y: 0 });
const store = ref({
  name: '',
  logo: { path_url: '' },
  default_address: {
    complete_address: '',
  },
});

const route = useRoute();

const agentCallLoading = ref(false);
const agentCallWebRtcBusy = ref(false);
const agentCallDialogOpen = ref(false);
const agentRoomId = ref('');
const agentJoinUrl = ref('');
const agentLocalVideoRef = ref<HTMLVideoElement | null>(null);
const agentRemoteVideoRef = ref<HTMLVideoElement | null>(null);
let agentCallHangUp: (() => void) | null = null;

const storeConflictDialog = ref(false);

const copyAgentJoinLink = async () => {
  if (!agentJoinUrl.value) return;
  try {
    await navigator.clipboard.writeText(agentJoinUrl.value);
    $q.notify({ message: 'Agent link copied.', type: 'positive' });
  } catch {
    $q.notify({ message: agentJoinUrl.value, type: 'info', timeout: 8000 });
  }
};

/** QDialog mounts dialog content on @show — refs are often null on first nextTick() only. */
const onAgentCallDialogShow = async () => {
  agentCallWebRtcBusy.value = true;
  try {
    await nextTick();
    let local = agentLocalVideoRef.value;
    let remote = agentRemoteVideoRef.value;
    if (!local || !remote) {
      await new Promise((r) => setTimeout(r, 100));
      local = agentLocalVideoRef.value;
      remote = agentRemoteVideoRef.value;
    }
    if (!agentRoomId.value || !local || !remote) {
      $q.notify({
        message: 'Video not ready. Close and try “Talk to Agent” again.',
        type: 'negative',
      });
      return;
    }
    agentCallHangUp?.();
    agentCallHangUp = null;
    try {
      const handles = await startCallerSession(agentRoomId.value, local, remote, (msg) => {
        $q.notify({ message: msg, type: 'negative', timeout: 8000 });
      });
      agentCallHangUp = handles.hangUp;
    } catch (e: unknown) {
      const msg =
        e instanceof Error
          ? e.message
          : 'Could not start the call. Check camera/microphone permissions and try again.';
      $q.notify({ message: msg, type: 'negative', timeout: 8000 });
    }
  } finally {
    agentCallWebRtcBusy.value = false;
  }
};

const onAgentCallDialogHide = () => {
  agentCallWebRtcBusy.value = false;
  agentCallHangUp?.();
  agentCallHangUp = null;
  agentRoomId.value = '';
  agentJoinUrl.value = '';
};

const needToAssist = async () => {
  if (!isFirebaseConfigured()) {
    $q.notify({
      message: 'Live call requires Firebase. Add VITE_FIREBASE_* keys in .env and enable Anonymous sign-in.',
      type: 'negative',
      timeout: 6000,
    });
    return;
  }
  agentCallLoading.value = true;
  try {
    const { roomId, joinUrl } = startAgentCallSession({
      storeOptimusId: route.params.id as string,
      itemOptimusId: route.params.item_id as string,
      storeName: store.value.name,
      itemName: item.value.name,
    });
    agentRoomId.value = roomId;
    agentJoinUrl.value = joinUrl;
    agentCallDialogOpen.value = true;
    $q.notify({
      message:
        'Live call opened. Allow camera/microphone when prompted. Copy the agent link for your team to join.',
      type: 'positive',
      timeout: 5000,
    });
  } catch {
    $q.notify({
      message: 'Could not start the call. Please try again.',
      type: 'negative',
    });
  } finally {
    agentCallLoading.value = false;
  }
};

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

watch(slide, () => {
  resetZoom();
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

  const currentStoreId = item.value.store?.optimus_id || Number(route.params.id);

  // Check if cart is not empty and store IDs don't match
  if (useUserCart.cart.length > 0) {
    const existingStoreId = useUserCart.cart[0].store_id;
    if (existingStoreId !== currentStoreId) {
      storeConflictDialog.value = true;
      return;
    }
  }

  // If cart is empty or store IDs match, add the item directly
  addItemToCart(currentStoreId);
};

const handleStoreConflictOk = () => {
  const currentStoreId = item.value.store?.optimus_id || Number(route.params.id);
  useUserCart.emptyCart();
  addItemToCart(currentStoreId);
  storeConflictDialog.value = false;
};

const handleStoreConflictCancel = () => {
  $q.notify({
    message: 'Item was not added to the cart.',
    type: 'negative',
  });
  storeConflictDialog.value = false;
};

const addItemToCart = (storeId: number) => {
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
    store_id: storeId,
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
      optimus_id: storeId,
    },
  };
  useUserCart.addQty(cartItem);

  $q.notify({
    message: 'You have successfully added the item to the cart.',
    type: 'positive',
  });
};

const resetZoom = () => {
  isZoomed.value = false;
  isDragging.value = false;
  dragMoved.value = false;
  pan.value = { x: 0, y: 0 };
};

const openZoomModal = (url: string) => {
  zoomImageUrl.value = url;
  isZoomModalOpen.value = true;
  isZoomed.value = true;
  pan.value = { x: 0, y: 0 };
};

const closeZoomModal = () => {
  isZoomModalOpen.value = false;
  zoomImageUrl.value = '';
  resetZoom();
};

const toggleZoom = () => {
  if (isDragging.value || dragMoved.value) {
    return;
  }
  if (isZoomed.value) {
    resetZoom();
  } else {
    isZoomed.value = true;
    pan.value = { x: 0, y: 0 };
  }
};

const getPointer = (event: MouseEvent | TouchEvent) => {
  if ('touches' in event && event.touches.length > 0) {
    return { x: event.touches[0].clientX, y: event.touches[0].clientY };
  }
  const mouseEvent = event as MouseEvent;
  return { x: mouseEvent.clientX, y: mouseEvent.clientY };
};

const clampPan = (container: HTMLElement, image: HTMLImageElement, nextX: number, nextY: number) => {
  const containerWidth = container.clientWidth;
  const containerHeight = container.clientHeight;
  const naturalWidth = image.naturalWidth || image.clientWidth;
  const naturalHeight = image.naturalHeight || image.clientHeight;
  const imageRatio = naturalWidth / naturalHeight;
  const containerRatio = containerWidth / containerHeight;
  let baseWidth = containerWidth;
  let baseHeight = containerHeight;

  if (imageRatio > containerRatio) {
    baseHeight = containerWidth / imageRatio;
  } else {
    baseWidth = containerHeight * imageRatio;
  }

  const scaledWidth = baseWidth * zoomScale.value;
  const scaledHeight = baseHeight * zoomScale.value;
  const maxX = Math.max(0, (scaledWidth - containerWidth) / 2);
  const maxY = Math.max(0, (scaledHeight - containerHeight) / 2);

  return {
    x: Math.min(maxX, Math.max(-maxX, nextX)),
    y: Math.min(maxY, Math.max(-maxY, nextY)),
  };
};

const onDragStart = (event: MouseEvent) => {
  if (!isZoomed.value) {
    return;
  }
  const pointer = getPointer(event);
  isDragging.value = true;
  dragMoved.value = false;
  dragStart.value = { x: pointer.x, y: pointer.y };
  panStart.value = { x: pan.value.x, y: pan.value.y };
};

const onDragMove = (event: MouseEvent) => {
  if (!isZoomed.value || !isDragging.value) {
    return;
  }
  const container = event.currentTarget as HTMLElement;
  const image = container.querySelector('img');
  if (!image) {
    return;
  }
  const pointer = getPointer(event);
  const nextX = panStart.value.x + (pointer.x - dragStart.value.x);
  const nextY = panStart.value.y + (pointer.y - dragStart.value.y);
  if (Math.abs(pointer.x - dragStart.value.x) > 4 || Math.abs(pointer.y - dragStart.value.y) > 4) {
    dragMoved.value = true;
  }
  pan.value = clampPan(container, image, nextX, nextY);
};

const onTouchStart = (event: TouchEvent) => {
  if (!isZoomed.value) {
    return;
  }
  const pointer = getPointer(event);
  isDragging.value = true;
  dragMoved.value = false;
  dragStart.value = { x: pointer.x, y: pointer.y };
  panStart.value = { x: pan.value.x, y: pan.value.y };
};

const onTouchMove = (event: TouchEvent) => {
  if (!isZoomed.value || !isDragging.value) {
    return;
  }
  const container = event.currentTarget as HTMLElement;
  const image = container.querySelector('img');
  if (!image) {
    return;
  }
  const pointer = getPointer(event);
  const nextX = panStart.value.x + (pointer.x - dragStart.value.x);
  const nextY = panStart.value.y + (pointer.y - dragStart.value.y);
  if (Math.abs(pointer.x - dragStart.value.x) > 4 || Math.abs(pointer.y - dragStart.value.y) > 4) {
    dragMoved.value = true;
  }
  pan.value = clampPan(container, image, nextX, nextY);
};

const onDragEnd = () => {
  isDragging.value = false;
  if (dragMoved.value) {
    setTimeout(() => {
      dragMoved.value = false;
    }, 0);
  }
};

const zoomStyle = computed(() => {
  if (!isZoomed.value) {
    return {};
  }
  return {
    transform: `translate(${pan.value.x}px, ${pan.value.y}px) scale(${zoomScale.value})`,
    transformOrigin: 'center center',
  };
});

// Sanitize HTML to prevent XSS attacks
const sanitizeHtml = (html: string) => {
  if (!html) return '';

  // Create a temporary div to parse HTML
  const tempDiv = document.createElement('div');
  tempDiv.innerHTML = html;

  // Remove dangerous tags and attributes
  const dangerousTags = ['script', 'iframe', 'object', 'embed', 'form', 'input', 'button'];
  dangerousTags.forEach(tag => {
    const elements = tempDiv.getElementsByTagName(tag);
    while (elements[0]) {
      elements[0].parentNode?.removeChild(elements[0]);
    }
  });

  // Remove dangerous attributes
  const allElements = tempDiv.getElementsByTagName('*');
  for (let i = 0; i < allElements.length; i++) {
    const element = allElements[i];
    const attributes = element.attributes;
    const dangerousAttrs = ['onclick', 'onload', 'onerror', 'onmouseover', 'onmouseout', 'onfocus', 'onblur'];
    for (let j = attributes.length - 1; j >= 0; j--) {
      const attr = attributes[j];
      if (dangerousAttrs.some(da => attr.name.toLowerCase().startsWith(da)) ||
          attr.name.toLowerCase().startsWith('on') ||
          attr.name.toLowerCase() === 'href' && attr.value?.toLowerCase().startsWith('javascript:')) {
        element.removeAttribute(attr.name);
      }
    }
  }

  return tempDiv.innerHTML;
};

</script>

<style scoped lang="scss">
.product-page {
  background: #f8f9fa;
  min-height: 100vh;
}

.product-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 24px;
}

.product-header {
  margin-bottom: 24px;
}

.product-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 40px;
  align-items: start;
}

.product-gallery {
  background: #ffffff;
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
}

.main-image-wrapper {
  position: relative;
  width: 100%;
  aspect-ratio: 1;
  background: #f5f5f5;
  border-radius: 12px;
  overflow: hidden;
  margin-bottom: 16px;
  cursor: zoom-in;
}

.main-image {
  width: 100%;
  height: 100%;
  object-fit: contain;
  transition: transform 0.3s ease;
}

.zoom-btn {
  position: absolute;
  bottom: 16px;
  right: 16px;
  background: rgba(255, 255, 255, 0.95);
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.thumbnail-list {
  display: flex;
  gap: 12px;
  overflow-x: auto;
  padding: 4px;
}

.thumbnail-item {
  width: 80px;
  height: 80px;
  border-radius: 8px;
  overflow: hidden;
  cursor: pointer;
  border: 2px solid transparent;
  transition: all 0.3s ease;
  flex-shrink: 0;

  &.active {
    border-color: var(--q-primary);
    box-shadow: 0 2px 8px rgba(25, 118, 210, 0.3);
  }

  &:hover {
    transform: scale(1.05);
  }

  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}

.product-info {
  background: #ffffff;
  border-radius: 16px;
  padding: 32px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
}

.product-name {
  font-size: 32px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0 0 16px 0;
  line-height: 1.3;
}

.product-description {
  font-size: 16px;
  line-height: 1.6;
  color: #666;
  margin: 0 0 32px 0;
  white-space: pre-wrap;
}

.info-section {
  margin-bottom: 32px;
}

.section-label {
  font-size: 14px;
  font-weight: 600;
  color: #333;
  margin-bottom: 12px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.unit-selector {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}

.unit-btn {
  padding: 10px 20px;
  border: 2px solid #e0e0e0;
  background: #ffffff;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 500;
  color: #666;
  cursor: pointer;
  transition: all 0.3s ease;

  &:hover {
    border-color: #bdbdbd;
    background: #f5f5f5;
  }

  &.active {
    border-color: var(--q-primary);
    background: var(--q-primary);
    color: #ffffff;
  }
}

.price-display {
  font-size: 28px;
  font-weight: 700;
  color: var(--q-primary);
}

.purchase-section {
  margin-top: 40px;
  padding-top: 32px;
  border-top: 1px solid #f0f0f0;
}

.quantity-wrapper {
  margin-bottom: 24px;
}

.quantity-control {
  display: flex;
  align-items: center;
  gap: 16px;
  background: #f5f5f5;
  padding: 8px 16px;
  border-radius: 8px;
  width: fit-content;
}

.quantity-value {
  font-size: 18px;
  font-weight: 600;
  color: #1a1a1a;
  min-width: 40px;
  text-align: center;
}

.action-buttons {
  display: flex;
}

.add-cart-btn {
  width: 100%;
  height: 52px;
  font-size: 16px;
  font-weight: 600;
  border-radius: 10px;
  transition: all 0.3s ease;

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(25, 118, 210, 0.3);
  }
}

.zoom-modal-card {
  height: 100%;
  border-radius: 0;
}

.zoom-modal-header {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  padding: 12px 16px;
}

.zoom-modal-content {
  height: calc(100% - 56px);
  padding: 0;
}

.zoom-modal-container {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  cursor: zoom-in;
  user-select: none;
  background: #000;
}

.zoom-modal-container.is-zoomed {
  cursor: grab;
}

.zoom-modal-container.is-dragging {
  cursor: grabbing;
}

.zoom-modal-image {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  transition: transform 0.2s ease;
}

.zoom-modal-image.is-zoomed {
  transition: none;
}

.agent-call-dialog-card {
  width: 100%;
  max-width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.agent-call-video {
  width: 100%;
  max-height: 42vh;
  min-height: 200px;
  background: #1a1a1a;
  border-radius: 8px;
  object-fit: cover;
}

.chat-box {
  height: 50vh;
  max-height: 560px;
  overflow-y: auto;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  background: #fafafa;
}

.chat-row {
  display: flex;
}

.chat-row.mine {
  justify-content: flex-end;
}

.chat-row.theirs {
  justify-content: flex-start;
}

.bubble {
  max-width: min(80%, 520px);
  padding: 8px 10px;
  border-radius: 10px;
  background: #fff;
  border: 1px solid #ececec;
}

.chat-row.mine .bubble {
  background: #eaf4ff;
  border-color: #d3e7ff;
}

@media (max-width: 1024px) {
  .product-content {
    grid-template-columns: 1fr;
    gap: 24px;
  }

  .product-info {
    order: -1;
  }
}

@media (max-width: 600px) {
  .product-container {
    padding: 16px;
  }

  .product-gallery,
  .product-info {
    padding: 20px;
  }

  .product-name {
    font-size: 24px;
  }

  .product-description {
    font-size: 14px;
  }

  .price-display {
    font-size: 24px;
  }

  .action-buttons {
    flex-direction: column;
  }

  .thumbnail-item {
    width: 60px;
    height: 60px;
  }
}
</style>
