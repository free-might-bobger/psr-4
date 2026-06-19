<template>
  <div class="product-page">

    <!-- Hero Breadcrumb Banner -->
    <div class="product-hero">
      <div class="hero-bg">
        <div class="hero-orb orb-1"></div>
        <div class="hero-orb orb-2"></div>
        <div class="hero-grid"></div>
      </div>
      <div class="hero-inner">
        <BreadCrumbsWrapper v-if="store.name && item.name" :bread-crumbs="[
          { name: store.name, path: `/public_stores/${route.params.id}` },
          { name: item.name || '', path: '' },
        ]" />
        <div class="hero-store-name" v-if="store.name">
          <q-icon name="storefront" size="16px" class="q-mr-xs" />{{ store.name }}
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="product-container">
      <div class="product-content" v-if="item.name">

        <!-- Left: Image Gallery -->
        <div class="product-gallery">
          <div class="main-image-wrapper" @click="openZoomModal(item.images?.[slide]?.path_url || '')">
            <img :src="item.images?.[slide]?.path_url || ''" class="main-image" :alt="item.name" />
            <div class="zoom-hint">
              <q-icon name="zoom_in" size="18px" />
              <span>Click to zoom</span>
            </div>
          </div>
          <div class="thumbnail-list" v-if="item.images && item.images.length > 1">
            <div v-for="(image, index) in item.images" :key="image.id" class="thumbnail-item"
              :class="{ active: slide === index }" @click="slide = index">
              <img :src="image.path_url" :alt="item.name" />
            </div>
          </div>
        </div>

        <!-- Right: Product Info -->
        <div class="product-info-panel">

          <h1 class="product-name">{{ item.name }}</h1>

          <div class="product-description" v-if="item.description" v-html="sanitizeHtml(item.description)"></div>

          <!-- Unit Selector -->
          <div class="info-block" v-if="units.length > 0">
            <div class="block-label">Select Unit</div>
            <div class="unit-selector">
              <button v-for="unit in units" :key="unit.id" class="unit-chip"
                :class="{ active: selectedUnit === unit.id }" @click="selectedUnit = unit.id">
                {{ unit.name }}
              </button>
            </div>
          </div>

          <!-- Price -->
          <div class="price-block">
            <div class="price-label">Price</div>
            <div class="price-value">{{ getPriceRange(filteredItemPrice) }}</div>
          </div>

          <!-- Quantity + CTA -->
          <div class="purchase-block">
            <div class="qty-row">
              <div class="block-label">Quantity</div>
              <div class="qty-control">
                <button class="qty-btn" @click="qty > 1 ? qty-- : null" :disabled="qty <= 1">
                  <q-icon name="remove" size="16px" />
                </button>
                <span class="qty-value">{{ qty }}</span>
                <button class="qty-btn" @click="qty++">
                  <q-icon name="add" size="16px" />
                </button>
              </div>
            </div>

            <button class="add-cart-btn" @click="userAddCart">
              <q-icon name="shopping_cart" size="20px" class="q-mr-sm" />
              Add to Cart
            </button>
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
          <div class="zoom-modal-container" :class="{ 'is-zoomed': isZoomed, 'is-dragging': isDragging }"
            @click="toggleZoom" @mousedown.prevent="onDragStart" @mousemove.prevent="onDragMove" @mouseup="onDragEnd"
            @mouseleave="onDragEnd" @touchstart.passive="onTouchStart" @touchmove.passive="onTouchMove"
            @touchend="onDragEnd">
            <img v-if="zoomImageUrl" :src="zoomImageUrl" class="zoom-modal-image" :class="{ 'is-zoomed': isZoomed }"
              :style="zoomStyle" :alt="item.name" draggable="false" />
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>

    <!-- Agent Call Dialog -->
    <q-dialog v-model="agentCallDialogOpen" maximized transition-show="slide-up" transition-hide="slide-down"
      @show="onAgentCallDialogShow" @hide="onAgentCallDialogHide">
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
            Allow camera and microphone. Your agent can join using the link below.
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
          <q-btn v-if="agentJoinUrl" flat color="primary" label="Copy agent link" icon="content_copy"
            @click="copyAgentJoinLink" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Store Conflict Dialog -->
    <q-dialog v-model="storeConflictDialog" persistent>
      <q-card class="conflict-dialog-card">
        <div class="conflict-dialog-header">
          <div class="conflict-icon-wrap">
            <q-icon name="warning_amber" size="28px" color="white" />
          </div>
          <div class="conflict-title">Multiple Stores Detected</div>
          <q-btn icon="close" flat round dense v-close-popup class="conflict-close" />
        </div>
        <q-card-section class="conflict-body">
          Your cart contains items from another store. Adding this item will remove existing cart items.
          Do you want to proceed?
        </q-card-section>
        <q-card-actions align="right" class="conflict-actions">
          <q-btn flat label="Cancel" no-caps class="conflict-cancel" v-close-popup @click="handleStoreConflictCancel" />
          <q-btn unelevated label="Yes, Replace Cart" no-caps class="conflict-ok" @click="handleStoreConflictOk" />
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

const route = useRoute();

const store = ref({
  name: '',
  logo: { path_url: '' },
  default_address: {
    complete_address: '',
  },
});

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
    // WebRTC call functionality removed - webrtcFirebaseCall.ts deleted
    $q.notify({ message: 'Call functionality has been removed', type: 'info', timeout: 3000 });
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

const showStore = async () => {
  store.value = await show({
    message: 'Getting item...',
    entity: 'public_stores',
    optimus_id: Number(route.params.id),
  });
};

const item = ref<Item>({});
const getItem = async () => {
  item.value = await show<Item>({
    message: 'Getting item...',
    entity: 'public_store_items',
    optimus_id: Number(route.params.item_id),
    query: {
      with: 'itemPrice.unit,store'
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
// ── Page ──────────────────────────────────────────────────────────────────
.product-page {
  background: #f4f5f7;
  min-height: 100vh;
}

// ── Hero Banner ───────────────────────────────────────────────────────────
.product-hero {
  position: relative;
  background: linear-gradient(145deg, #1e1b4b 0%, #312e81 55%, #4c1d95 100%);
  overflow: hidden;
}

.hero-bg {
  position: absolute;
  inset: 0;
  pointer-events: none;
}

.hero-orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(70px);
  animation: heroOrb 12s ease-in-out infinite;

  &.orb-1 {
    width: 400px;
    height: 400px;
    background: rgba(139, 92, 246, 0.3);
    top: -160px;
    right: -80px;
  }

  &.orb-2 {
    width: 200px;
    height: 200px;
    background: rgba(99, 102, 241, 0.2);
    bottom: -40px;
    left: -40px;
    animation-delay: 5s;
  }
}

.hero-grid {
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(rgba(255, 255, 255, 0.04) 1px, transparent 1px),
    linear-gradient(90deg, rgba(255, 255, 255, 0.04) 1px, transparent 1px);
  background-size: 48px 48px;
}

@keyframes heroOrb {

  0%,
  100% {
    transform: translate(0, 0);
  }

  50% {
    transform: translate(16px, -20px);
  }
}

.hero-inner {
  position: relative;
  z-index: 1;
  padding: 20px 32px 24px;

  :deep(.q-breadcrumbs) {
    color: rgba(255, 255, 255, 0.5);
  }

  :deep(.q-breadcrumbs__el) {
    color: rgba(255, 255, 255, 0.5);
  }

  :deep(.q-breadcrumbs__separator) {
    color: rgba(255, 255, 255, 0.3);
  }

  :deep(a) {
    color: rgba(255, 255, 255, 0.7) !important;
  }
}

.hero-store-name {
  display: flex;
  align-items: center;
  font-size: 12px;
  font-weight: 600;
  color: rgba(255, 255, 255, 0.4);
  text-transform: uppercase;
  letter-spacing: 1px;
  margin-top: 8px;
}

// ── Main Layout ───────────────────────────────────────────────────────────
.product-container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 32px;
}

.product-content {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 32px;
  align-items: start;
}

// ── Gallery ───────────────────────────────────────────────────────────────
.product-gallery {
  background: white;
  border-radius: 20px;
  padding: 24px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.07);
  position: sticky;
  top: 16px;
}

.main-image-wrapper {
  position: relative;
  width: 100%;
  aspect-ratio: 1;
  background: #f8f9fa;
  border-radius: 14px;
  overflow: hidden;
  margin-bottom: 16px;
  cursor: zoom-in;

  &:hover .zoom-hint {
    opacity: 1;
  }
}

.main-image {
  width: 100%;
  height: 100%;
  object-fit: contain;
  transition: transform 0.4s ease;
}

.main-image-wrapper:hover .main-image {
  transform: scale(1.03);
}

.zoom-hint {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background: linear-gradient(transparent, rgba(0, 0, 0, 0.55));
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 20px 12px 12px;
  font-size: 13px;
  font-weight: 600;
  opacity: 0;
  transition: opacity 0.25s ease;
  pointer-events: none;
}

.thumbnail-list {
  display: flex;
  gap: 10px;
  overflow-x: auto;
  padding: 4px;
}

.thumbnail-item {
  width: 76px;
  height: 76px;
  border-radius: 10px;
  overflow: hidden;
  cursor: pointer;
  border: 2px solid transparent;
  transition: all 0.2s ease;
  flex-shrink: 0;
  background: #f3f4f6;

  &.active {
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
  }

  &:hover {
    transform: scale(1.06);
  }

  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
}

// ── Product Info Panel ────────────────────────────────────────────────────
.product-info-panel {
  background: white;
  border-radius: 20px;
  padding: 36px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.07);
}

.product-name {
  font-size: 30px;
  font-weight: 900;
  color: #111827;
  margin: 0 0 16px;
  line-height: 1.25;
  letter-spacing: -0.5px;
}

.product-description {
  font-size: 15px;
  line-height: 1.7;
  color: #6b7280;
  margin: 0 0 28px;
  padding-bottom: 28px;
  border-bottom: 1px solid #f3f4f6;
}

.info-block {
  margin-bottom: 28px;
}

.block-label {
  font-size: 11px;
  font-weight: 800;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  margin-bottom: 12px;
}

.unit-selector {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.unit-chip {
  padding: 9px 20px;
  border: 2px solid #e5e7eb;
  background: white;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  color: #374151;
  cursor: pointer;
  transition: all 0.2s ease;

  &:hover {
    border-color: #a5b4fc;
    background: #f5f3ff;
    color: #4f46e5;
  }

  &.active {
    border-color: #4f46e5;
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
  }
}

// ── Price Block ───────────────────────────────────────────────────────────
.price-block {
  margin-bottom: 32px;
  padding: 20px 24px;
  background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
  border-radius: 14px;
  border: 1px solid #e9d5ff;
}

.price-label {
  font-size: 11px;
  font-weight: 800;
  color: #7c3aed;
  text-transform: uppercase;
  letter-spacing: 1.5px;
  margin-bottom: 6px;
}

.price-value {
  font-size: 32px;
  font-weight: 900;
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  line-height: 1;
}

// ── Purchase Block ────────────────────────────────────────────────────────
.purchase-block {
  padding-top: 28px;
  border-top: 1px solid #f3f4f6;
}

.qty-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 20px;
}

.qty-control {
  display: flex;
  align-items: center;
  gap: 0;
  border: 1.5px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
}

.qty-btn {
  width: 40px;
  height: 40px;
  background: #f9fafb;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #374151;
  transition: background 0.15s;

  &:hover:not(:disabled) {
    background: #ede9fe;
    color: #4f46e5;
  }

  &:disabled {
    opacity: 0.35;
    cursor: not-allowed;
  }
}

.qty-value {
  font-size: 16px;
  font-weight: 800;
  color: #111827;
  min-width: 44px;
  text-align: center;
}

.add-cart-btn {
  width: 100%;
  height: 54px;
  border: none;
  border-radius: 14px;
  background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
  color: white;
  font-size: 16px;
  font-weight: 800;
  letter-spacing: 0.3px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 6px 20px rgba(99, 102, 241, 0.35);
  transition: all 0.25s ease;

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(99, 102, 241, 0.45);
  }

  &:active {
    transform: translateY(0);
  }
}

// ── Zoom Modal ────────────────────────────────────────────────────────────
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

  &.is-zoomed {
    cursor: grab;
  }

  &.is-dragging {
    cursor: grabbing;
  }
}

.zoom-modal-image {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  transition: transform 0.2s ease;

  &.is-zoomed {
    transition: none;
  }
}

// ── Agent Call Dialog ─────────────────────────────────────────────────────
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

// ── Store Conflict Dialog ─────────────────────────────────────────────────
.conflict-dialog-card {
  border-radius: 20px;
  overflow: hidden;
  min-width: 340px;
  max-width: 440px;
}

.conflict-dialog-header {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 20px 20px 20px 24px;
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

.conflict-icon-wrap {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.conflict-title {
  flex: 1;
  font-size: 16px;
  font-weight: 800;
  color: white;
}

.conflict-close {
  color: rgba(255, 255, 255, 0.8) !important;
}

.conflict-body {
  font-size: 14px;
  color: #374151;
  line-height: 1.65;
  padding: 24px !important;
}

.conflict-actions {
  padding: 0 20px 20px !important;
  gap: 10px;
}

.conflict-cancel {
  color: #6b7280 !important;
  font-weight: 600;
  border-radius: 10px;
}

.conflict-ok {
  background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
  color: white !important;
  font-weight: 700;
  border-radius: 10px;
  padding: 0 20px;
  box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
}

// ── Responsive ────────────────────────────────────────────────────────────
@media (max-width: 1024px) {
  .product-content {
    grid-template-columns: 1fr;
    gap: 24px;
  }

  .product-gallery {
    position: static;
  }
}

@media (max-width: 768px) {
  .hero-inner {
    padding: 16px 20px 20px;
  }

  .product-container {
    padding: 20px;
  }

  .product-info-panel {
    padding: 24px;
  }

  .product-name {
    font-size: 24px;
  }

  .price-value {
    font-size: 26px;
  }
}

@media (max-width: 480px) {
  .product-container {
    padding: 12px;
  }

  .product-gallery {
    padding: 16px;
  }

  .product-info-panel {
    padding: 20px;
  }

  .product-name {
    font-size: 22px;
  }

  .thumbnail-item {
    width: 60px;
    height: 60px;
  }

  .add-cart-btn {
    height: 50px;
    font-size: 15px;
  }
}
</style>
