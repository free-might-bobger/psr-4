<template>
  <div class="map-page-container">

    <!-- Hero Header -->
    <div class="map-hero">
      <div class="map-hero-bg">
        <div class="hero-orb orb-1"></div>
        <div class="hero-orb orb-2"></div>
        <div class="hero-orb orb-3"></div>
        <div class="hero-grid"></div>
      </div>
      <div class="map-hero-inner">
        <div class="hero-left">
          <div class="hero-icon-wrap">
            <q-icon name="shopping_bag" size="28px" color="white" />
          </div>
          <div>
            <div class="hero-eyebrow">Live Map Search</div>
            <h1 class="hero-title">Find Nearby Items</h1>
            <p class="hero-desc">
              Search products across local stores and get directions instantly.
              <router-link to="/find-shops" class="find-shops-link">Browse shops instead &rarr;</router-link>
            </p>
          </div>
        </div>
        <div class="hero-stats">
          <div class="hero-stat">
            <div class="hero-stat-value">{{ nearestItems.length }}</div>
            <div class="hero-stat-label">{{ nearestItems.length === 1 ? 'Item' : 'Items' }} Found</div>
          </div>
          <div class="hero-stat-divider"></div>
          <div class="hero-stat">
            <div class="hero-stat-value radius-value">
              <q-select v-model="kmRadius" :options="[5, 10, 15]" dense borderless dropdown-icon="expand_more"
                class="radius-select" options-class="radius-options" />
              <span class="radius-unit">km</span>
            </div>
            <div class="hero-stat-label">Search Radius</div>
          </div>
        </div>
      </div>

      <!-- Action Bar -->
      <div class="action-bar">
        <div class="action-bar-inner">
          <q-btn no-caps unelevated class="action-btn loc-action" icon="my_location" label="My Location"
            @click="localGetLocation" />
          <q-btn no-caps unelevated class="action-btn find-action" icon="search" label="Find Items"
            @click="getNearestItems" :disable="!searchString" />
          <div class="search-wrap">
            <q-input v-model="searchString" placeholder="Type an item name to search..." outlined dense debounce="1000"
              class="hero-search" clearable>
              <template v-slot:prepend>
                <q-icon name="search" size="18px" />
              </template>
            </q-input>
          </div>
        </div>
        <div class="search-hint" v-if="!searchString">
          <q-icon name="info" size="14px" class="q-mr-xs" />
          Type a product name above, then click <strong>Find Items</strong>
        </div>
      </div>
    </div>

    <!-- Map + Panel Container -->
    <div class="map-store-container">

      <!-- Items Panel -->
      <div class="store-list-section" v-if="showStoreList">
        <div class="items-panel">
          <div class="items-panel-header" @click="getNearestItems">
            <div class="panel-header-left">
              <div class="panel-icon"><q-icon name="shopping_bag" size="18px" color="white" /></div>
              <div>
                <div class="panel-title">Nearby Items</div>
                <div class="panel-subtitle">Click to refresh results</div>
              </div>
            </div>
            <div class="panel-count" v-if="nearestItems.length > 0">{{ nearestItems.length }}</div>
          </div>

          <div class="items-panel-body">
            <div v-if="nearestItems.length === 0" class="empty-state">
              <div class="empty-icon-wrap">
                <q-icon name="search_off" size="30px" color="grey-4" />
              </div>
              <div class="empty-title">No items yet</div>
              <div class="empty-sub">Type a product name and click<br />"Find Items" to search</div>
            </div>

            <div v-else class="item-cards">
              <div v-for="(item, idx) in nearestItems" :key="item.id" class="item-card"
                @click="handleClickStoreAdvanceMarker(item.store)">
                <div class="item-card-badge">{{ idx + 1 }}</div>
                <div class="item-card-body">
                  <div class="item-card-name">{{ item.name }}</div>
                  <div class="item-card-store">
                    <q-icon name="storefront" size="12px" class="q-mr-xs" />
                    {{ item.store?.name }}
                  </div>
                  <div class="item-card-dist" v-if="item.store?.distance">
                    <q-icon name="near_me" size="11px" class="q-mr-xs" />
                    {{ item.store.distance }} away
                  </div>
                </div>
                <router-link :to="`/public_stores/${item.store?.optimus_id}/item/${item.optimus_id}`"
                  class="item-card-link" @click.stop>
                  <q-icon name="arrow_forward_ios" size="12px" />
                </router-link>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Map Section -->
      <div class="map-section" ref="mapSectionRef">
        <div class="map-frame">
          <GoogleMap ref="mapRef" :api-key="GOOGLE_MAP_API_KEY" :map-id="GOOGLE_MAP_ID" class="google-map"
            :center="{ lat: lat, lng: lng }" :zoom="currentZoom" :draggable="true" :clickable-icons="false">
            <AdvancedMarker :options="getLocationMarkerOptions()" @drag="markerDrag">
              <InfoWindow v-model="showInfoWindow"
                :options="{ position: { lat: lat, lng: lng }, headerContent: '&nbsp;&nbsp;&nbsp;Your Location', disableAutoPan: false }">
                <div class="info-window-content">
                  <div class="info-window-header">
                    <q-icon name="my_location" color="primary" size="sm" class="q-mr-xs" />
                    <span class="text-weight-bold">Your Location</span>
                  </div>
                  <div class="info-window-body">
                    <p class="text-caption text-grey-7 q-ma-none">This is your current location on the map</p>
                  </div>
                </div>
              </InfoWindow>
            </AdvancedMarker>

            <AdvancedMarker v-for="item in nearestItems" :key="item.id" :options="getStoreMarkerOptions(item.store)"
              @click="handleClickStoreAdvanceMarker(item.store)">
              <InfoWindow
                :options="{ headerContent: '&nbsp;&nbsp;&nbsp;' + item.store.name, disableAutoPan: false, closeButton: true } as any">
                <div class="info-window-content store-info-window">
                  <div class="info-window-header">
                    <q-icon name="store" color="positive" size="sm" class="q-mr-xs" />
                    <span class="text-weight-bold">{{ item.store.name }}</span>
                  </div>
                  <div class="info-window-body">
                    <div class="iw-item-name">{{ item.name }}</div>
                    <div class="store-details" v-if="item.store.distance">
                      <q-icon name="straighten" size="xs" class="q-mr-xs" />
                      <span class="text-caption">{{ item.store.distance }} away</span>
                    </div>
                    <q-btn :to="`/public_stores/${item.store.optimus_id}/item/${item.optimus_id}`" color="primary"
                      size="sm" unelevated class="q-mt-sm full-width" label="View Item" icon="arrow_forward" no-caps />
                  </div>
                </div>
              </InfoWindow>
            </AdvancedMarker>
          </GoogleMap>
        </div>
      </div>
    </div>

    <!-- How-to Strip -->
    <div class="howto-strip">
      <div class="howto-inner">
        <div class="howto-step">
          <div class="howto-icon"><q-icon name="edit" size="18px" color="white" /></div>
          <div>
            <div class="howto-title">Type a Product</div>
            <div class="howto-sub">Enter an item name in the search bar</div>
          </div>
        </div>
        <div class="howto-arrow"><q-icon name="arrow_forward" size="16px" color="grey-4" /></div>
        <div class="howto-step">
          <div class="howto-icon"><q-icon name="search" size="18px" color="white" /></div>
          <div>
            <div class="howto-title">Find Items</div>
            <div class="howto-sub">Click the button to search nearby</div>
          </div>
        </div>
        <div class="howto-arrow"><q-icon name="arrow_forward" size="16px" color="grey-4" /></div>
        <div class="howto-step">
          <div class="howto-icon"><q-icon name="place" size="18px" color="white" /></div>
          <div>
            <div class="howto-title">Tap a Marker</div>
            <div class="howto-sub">See item &amp; store details</div>
          </div>
        </div>
        <div class="howto-arrow"><q-icon name="arrow_forward" size="16px" color="grey-4" /></div>
        <div class="howto-step">
          <div class="howto-icon"><q-icon name="shopping_cart" size="18px" color="white" /></div>
          <div>
            <div class="howto-title">Shop Local</div>
            <div class="howto-sub">Visit the store and grab the item</div>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script setup lang="ts">
import { GOOGLE_MAP_API_KEY, GOOGLE_MAP_ID } from 'src/boot/constant';
import { GoogleMap, AdvancedMarker, InfoWindow } from 'vue3-google-map';
import { onMounted, ref, nextTick, watch } from 'vue';
import { useCommonStore } from 'src/stores/common';
import { storeToRefs } from 'pinia';
import { getLocation } from 'src/boot/utilities';
import { get } from 'src/boot/axios-call';
import { ItemInterface, StoreInterface } from 'src/boot/interfaces';


const useCommon = useCommonStore();
const { lat, lng } = storeToRefs(useCommon);
const showInfoWindow = ref(true);


const mapRef = ref<any>(null)
const mapSectionRef = ref<HTMLElement | null>(null)
const directions = ref<google.maps.DirectionsResult | null>(null)
const directionsRenderer = ref<google.maps.DirectionsRenderer | null>(null)
const currentZoom = ref(15)
const showStoreList = ref(true)
const searchString = ref('')
const origin = ref({ lat: lat.value, lng: lng.value })
const destination = ref({ lat: 14.609, lng: 120.994 })
const initialLat = ref<number | null>(null)
const initialLng = ref<number | null>(null)
const initialZoom = ref(currentZoom.value)
const initialOrigin = ref({ lat: origin.value.lat, lng: origin.value.lng })
const initialDestination = ref({ lat: destination.value.lat, lng: destination.value.lng })
const initialShowInfoWindow = ref(showInfoWindow.value)

// Create animated location marker element
const createLocationMarkerElement = (): HTMLElement => {
  const markerDiv = document.createElement('div')
  markerDiv.className = 'custom-marker location-marker'
  markerDiv.innerHTML = `
        <div class="marker-pulse"></div>
        <div class="marker-icon">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="#FFFFFF"/>
            </svg>
        </div>
    `
  return markerDiv
}

// Create animated store marker element
const createStoreMarkerElement = (): HTMLElement => {
  const markerDiv = document.createElement('div')
  markerDiv.className = 'custom-marker store-marker'
  markerDiv.innerHTML = `
        <div class="marker-pulse"></div>
        <div class="marker-icon">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z" fill="#FFFFFF"/>
            </svg>
        </div>
    `
  return markerDiv
}

// Get location marker options
const getLocationMarkerOptions = () => {
  return {
    position: { lat: lat.value, lng: lng.value },
    gmpDraggable: true,
    title: 'My location',
    content: createLocationMarkerElement(),
  }
}

// Get items marker options
const getStoreMarkerOptions = (store: StoreInterface) => {
  return {
    position: { lat: store.latitude, lng: store.longitude },
    gmpDraggable: false,
    title: store.name,
    content: createStoreMarkerElement(),
  }
}

onMounted(async () => {
  await localGetLocation();
  if (initialLat.value === null || initialLng.value === null) {
    initialLat.value = lat.value
    initialLng.value = lng.value
  }
  initialOrigin.value = { lat: origin.value.lat, lng: origin.value.lng }
  // Wait for the next tick to ensure the GoogleMap component is mounted
  await nextTick()

  // Wait for Google Maps API to be fully loaded
  await waitForGoogleMaps()

  // Wait for the map to be fully initialized
  await waitForMapReady()

});

const localGetLocation = () => {
  return getLocation().then((position) => {
    lat.value = position.coords.latitude;
    lng.value = position.coords.longitude;
    origin.value = { lat: lat.value, lng: lng.value }
  });
}
const markerDrag = (e: { latLng: google.maps.LatLng }) => {
  lat.value = e.latLng.lat();
  lng.value = e.latLng.lng();
};

const kmRadius = ref(15);
const nearestItems = ref<Array<ItemInterface>>([]);

const getNearestItems = async () => {
  nearestItems.value = []
  localGetLocation();
  const result = await get(
    {
      message: 'Searching nearest items',
      entity: 'public_items',
      query: {
        filters: 'name:' + searchString.value,
        orderBy: 'name:asc',
        latitude: lat.value,
        longitude: lng.value,
        radius: kmRadius.value,
        type: 'collection',
        with: 'store' // Include store details in the response
      },
    },
    true
  );

  if (result && typeof result === 'object' && 'data' in result) {
    nearestItems.value = (result as any).data.data;
    showStoreList.value = true;
  }
};


const handleClickStoreAdvanceMarker = (store: StoreInterface) => {
  destination.value = { lat: store.latitude, lng: store.longitude }
  requestDirections()

  // Scroll to map section
  if (mapSectionRef.value) {
    setTimeout(() => {
      mapSectionRef.value?.scrollIntoView({ behavior: 'smooth', block: 'start' })
    }, 100)
  }
}

// Watch for when directions become available
watch(() => directions.value, (newDirections) => {
  if (newDirections) {
    const map = mapRef.value?.$mapObject || mapRef.value?.map || mapRef.value?.$map
    if (map) {
      setupDirectionsRenderer(map)
    }
  }
})

const waitForGoogleMaps = () => {
  return new Promise((resolve) => {
    const checkGoogleMaps = () => {
      if (window.google &&
        window.google.maps &&
        window.google.maps.DirectionsService &&
        window.google.maps.TravelMode) {
        resolve(void 0)
      } else {
        setTimeout(checkGoogleMaps, 100)
      }
    }
    checkGoogleMaps()
  })
}

const waitForMapReady = () => {
  return new Promise((resolve) => {
    const checkMapReady = () => {
      // Try different ways to access the map
      const map = mapRef.value?.$mapObject || mapRef.value?.map || mapRef.value?.$map
      if (map) {
        addZoomControls(map)
        resolve(void 0)
      } else {
        setTimeout(checkMapReady, 200)
      }
    }
    checkMapReady()
  })
}

const zoomIn = () => {
  const map = mapRef.value?.$mapObject || mapRef.value?.map || mapRef.value?.$map
  if (map) {
    const currentZoomLevel = map.getZoom() || currentZoom.value
    if (currentZoomLevel < 21) {
      const newZoom = currentZoomLevel + 1
      map.setZoom(newZoom)
      currentZoom.value = newZoom
    }
  }
}

const zoomOut = () => {
  const map = mapRef.value?.$mapObject || mapRef.value?.map || mapRef.value?.$map
  if (map) {
    const currentZoomLevel = map.getZoom() || currentZoom.value
    if (currentZoomLevel > 1) {
      const newZoom = currentZoomLevel - 1
      map.setZoom(newZoom)
      currentZoom.value = newZoom
    }
  }
}

const addZoomControls = (map: google.maps.Map) => {
  // Create container for zoom controls
  const zoomControlDiv = document.createElement('div')
  zoomControlDiv.style.cssText = `
      display: flex;
      flex-direction: column;
      gap: 2px;
      background: white;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
      overflow: hidden;
      position: absolute;
      top: 10px;
      right: 10px;
      z-index: 1000;
      pointer-events: auto;
    `

  // Zoom In Button
  const zoomInButton = document.createElement('button')
  zoomInButton.style.cssText = `
      width: 40px;
      height: 40px;
      border: none;
      background: white;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s ease;
      padding: 0;
      margin: 0;
      font-size: 20px;
      font-weight: 600;
      color: #333;
      user-select: none;
      border-bottom: 1px solid #e0e0e0;
    `
  zoomInButton.innerHTML = '<span style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; line-height: 1;">+</span>'
  zoomInButton.title = 'Zoom in'
  zoomInButton.addEventListener('click', (e) => {
    e.stopPropagation()
    zoomIn()
  })
  zoomInButton.addEventListener('mouseenter', () => {
    zoomInButton.style.background = '#f5f5f5'
  })
  zoomInButton.addEventListener('mouseleave', () => {
    zoomInButton.style.background = 'white'
  })
  zoomInButton.addEventListener('mousedown', () => {
    zoomInButton.style.background = '#e0e0e0'
    zoomInButton.style.transform = 'scale(0.95)'
  })
  zoomInButton.addEventListener('mouseup', () => {
    zoomInButton.style.background = '#f5f5f5'
    zoomInButton.style.transform = 'scale(1)'
  })

  // Zoom Out Button
  const zoomOutButton = document.createElement('button')
  zoomOutButton.style.cssText = `
      width: 40px;
      height: 40px;
      border: none;
      background: white;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s ease;
      padding: 0;
      margin: 0;
      font-size: 20px;
      font-weight: 600;
      color: #333;
      user-select: none;
    `
  zoomOutButton.innerHTML = '<span style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; line-height: 1;">−</span>'
  zoomOutButton.title = 'Zoom out'
  zoomOutButton.addEventListener('click', (e) => {
    e.stopPropagation()
    zoomOut()
  })
  zoomOutButton.addEventListener('mouseenter', () => {
    zoomOutButton.style.background = '#f5f5f5'
  })
  zoomOutButton.addEventListener('mouseleave', () => {
    zoomOutButton.style.background = 'white'
  })
  zoomOutButton.addEventListener('mousedown', () => {
    zoomOutButton.style.background = '#e0e0e0'
    zoomOutButton.style.transform = 'scale(0.95)'
  })
  zoomOutButton.addEventListener('mouseup', () => {
    zoomOutButton.style.background = '#f5f5f5'
    zoomOutButton.style.transform = 'scale(1)'
  })

  zoomControlDiv.appendChild(zoomInButton)
  zoomControlDiv.appendChild(zoomOutButton)

  // Position the control
  setTimeout(() => {
    const mapContainer = map.getDiv()
    if (mapContainer) {
      mapContainer.appendChild(zoomControlDiv)
    }
  }, 200)
}


const setupDirectionsRenderer = (map: google.maps.Map) => {
  // Clean up existing renderer
  if (directionsRenderer.value) {
    directionsRenderer.value.setMap(null)
  }

  // Create new directions renderer
  directionsRenderer.value = new google.maps.DirectionsRenderer({
    suppressMarkers: true,
    polylineOptions: {
      strokeColor: '#4285F4',
      strokeWeight: 5
    }
  })

  directionsRenderer.value.setMap(map)

  if (directions.value) {
    directionsRenderer.value.setDirections(directions.value)
  }
}

const requestDirections = () => {
  try {
    const directionsService = new google.maps.DirectionsService()

    directionsService.route(
      {
        origin: origin.value,
        destination: destination.value,
        travelMode: google.maps.TravelMode.DRIVING
      },
      (result, status) => {
        if (status === google.maps.DirectionsStatus.OK && result) {
          directions.value = result

          // Try to set up renderer immediately if map is available
          const map = mapRef.value?.$mapObject || mapRef.value?.map || mapRef.value?.$map
          if (map) {
            setupDirectionsRenderer(map)
          }
        } else {
          console.error('Error fetching directions:', status)
        }
      }
    )
  } catch (error) {
    console.error('Error creating DirectionsService:', error)
  }
}

const resetToInitialDefaults = () => {
  if (initialLat.value !== null && initialLng.value !== null) {
    lat.value = initialLat.value
    lng.value = initialLng.value
    origin.value = { lat: initialLat.value, lng: initialLng.value }
  } else {
    origin.value = { lat: origin.value.lat, lng: origin.value.lng }
  }

  destination.value = { lat: initialDestination.value.lat, lng: initialDestination.value.lng }
  currentZoom.value = initialZoom.value
  showInfoWindow.value = initialShowInfoWindow.value
  directions.value = null

  if (directionsRenderer.value) {
    directionsRenderer.value.setMap(null)
    directionsRenderer.value = null
  }

  const map = mapRef.value?.$mapObject || mapRef.value?.map || mapRef.value?.$map
  if (map) {
    map.setCenter({ lat: lat.value, lng: lng.value })
    map.setZoom(currentZoom.value)
  }
}

watch(searchString, async () => {
  if (searchString.value) {
    const result = await get(
      {
        message: 'Searching nearest items',
        entity: 'public_items',
        query: {
          filters: 'name:' + searchString.value,
          orderBy: 'name:asc',
          latitude: lat.value,
          longitude: lng.value,
          radius: kmRadius.value,
          type: 'collection',
          with: 'store' // Include store details in the response
        },
      },
      true
    );

    if (result && typeof result === 'object' && 'data' in result) {
      nearestItems.value = (result as any).data.data;
      showStoreList.value = true;
    }
  }

  if (!searchString.value) {
    nearestItems.value = [];
    resetToInitialDefaults()
  }
})

</script>

<style scoped lang="scss">
// ── Page ──────────────────────────────────────────────────────────────────
.map-page-container {
  max-width: 1400px;
  margin: 0 auto;
  width: 100%;
  overflow-x: hidden;
}

// ── Hero Header ───────────────────────────────────────────────────────────
.map-hero {
  position: relative;
  background: linear-gradient(145deg, #064e3b 0%, #065f46 50%, #0f766e 100%);
  overflow: hidden;
  width: 100%;
}

.map-hero-bg {
  position: absolute;
  inset: 0;
  pointer-events: none;
}

.hero-orb {
  position: absolute;
  border-radius: 50%;
  filter: blur(60px);
  animation: heroOrb 12s ease-in-out infinite;

  &.orb-1 {
    width: 480px;
    height: 480px;
    background: rgba(16, 185, 129, 0.25);
    top: -180px;
    right: -80px;
  }

  &.orb-2 {
    width: 320px;
    height: 320px;
    background: rgba(245, 158, 11, 0.2);
    bottom: 20px;
    left: -60px;
    animation-delay: 4s;
  }

  &.orb-3 {
    width: 200px;
    height: 200px;
    background: rgba(52, 211, 153, 0.15);
    top: 40px;
    left: 40%;
    animation-delay: 8s;
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
    transform: translate(15px, -25px);
  }
}

.map-hero-inner {
  position: relative;
  z-index: 1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 32px;
  padding: 36px 32px 28px;
  flex-wrap: wrap;
}

.hero-left {
  display: flex;
  align-items: flex-start;
  gap: 20px;
}

.hero-icon-wrap {
  width: 60px;
  height: 60px;
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.12);
  border: 1px solid rgba(255, 255, 255, 0.18);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
}

.hero-eyebrow {
  font-size: 11px;
  font-weight: 800;
  color: rgba(255, 255, 255, 0.45);
  text-transform: uppercase;
  letter-spacing: 2px;
  margin-bottom: 6px;
}

.hero-title {
  font-size: 32px;
  font-weight: 900;
  color: white;
  margin: 0 0 10px;
  letter-spacing: -0.8px;
  line-height: 1.1;
}

.hero-desc {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.55);
  margin: 0;
  line-height: 1.6;
}

.find-shops-link {
  color: #fbbf24;
  font-weight: 700;
  text-decoration: none;
  transition: opacity 0.2s;

  &:hover {
    opacity: 0.8;
  }
}

.hero-stats {
  display: flex;
  align-items: center;
  gap: 24px;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 16px;
  padding: 20px 28px;
  backdrop-filter: blur(10px);
}

.hero-stat-value {
  font-size: 32px;
  font-weight: 900;
  color: white;
  line-height: 1;
  margin-bottom: 4px;
}

.hero-stat-label {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.45);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.hero-stat-divider {
  width: 1px;
  height: 40px;
  background: rgba(255, 255, 255, 0.15);
}

.radius-value {
  display: flex;
  align-items: baseline;
  justify-content: center;
  gap: 2px;
}

.radius-select {
  width: 64px;
  font-size: 32px;
  font-weight: 900;
  color: white;
  line-height: 1;

  :deep(.q-field__control) {
    padding: 0;
    min-height: auto;
    height: auto;
    color: white;
    overflow: visible;
  }

  :deep(.q-field__native) {
    color: white;
    font-size: 32px;
    font-weight: 900;
    padding: 0;
    min-height: auto;
    height: auto;
    line-height: 1.2;
    text-align: center;
    overflow: visible;
  }

  :deep(.q-field__append) {
    padding: 0;
    height: 24px;
    width: 20px;
    color: white;
  }

  :deep(.q-field__focusable-action) {
    color: white;
  }

  :deep(.q-icon) {
    font-size: 20px;
  }
}

.radius-unit {
  font-size: 16px;
  font-weight: 700;
  color: rgba(255, 255, 255, 0.7);
}

:global(.radius-options) {
  background: #1e293b !important;
  border: 1px solid rgba(255, 255, 255, 0.12) !important;
  border-radius: 12px !important;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.35) !important;

  .q-item {
    color: white;
    font-size: 16px;
    font-weight: 700;
    min-height: 40px;
    padding: 8px 16px;

    &:hover,
    &--active {
      background: rgba(99, 102, 241, 0.2) !important;
    }
  }
}

// ── Action Bar ────────────────────────────────────────────────────────────
.action-bar {
  position: relative;
  z-index: 1;
  padding: 0 32px 28px;
}

.action-bar-inner {
  display: grid;
  grid-template-columns: auto auto 1fr;
  gap: 12px;
  align-items: center;
  margin-bottom: 10px;
}

.action-btn {
  height: 46px;
  padding: 0 20px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 14px;
  white-space: nowrap;
  transition: all 0.25s ease;

  &.loc-action {
    background: rgba(255, 255, 255, 0.12);
    color: white;
    border: 1.5px solid rgba(255, 255, 255, 0.22);

    &:hover {
      background: rgba(255, 255, 255, 0.2);
      transform: translateY(-1px);
    }
  }

  &.find-action {
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    color: #1a1a1a;
    box-shadow: 0 4px 14px rgba(251, 191, 36, 0.35);

    &:hover:not(.disabled) {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(251, 191, 36, 0.45);
    }
  }
}

.search-wrap {
  min-width: 0;
}

.hero-search {
  :deep(.q-field__control) {
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2) !important;
    backdrop-filter: blur(10px);
    height: 46px;
    transition: all 0.25s ease;

    &:hover {
      background: rgba(255, 255, 255, 0.15);
    }
  }

  :deep(.q-field__native),
  :deep(.q-field__native::placeholder) {
    color: rgba(255, 255, 255, 0.7);
    font-size: 14px;
    font-weight: 500;
  }

  :deep(.q-field__prepend) {
    color: rgba(255, 255, 255, 0.45);
  }

  :deep(.q-field--focused .q-field__control) {
    background: rgba(255, 255, 255, 0.18);
    border-color: rgba(255, 255, 255, 0.4) !important;
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.1);
  }
}

.search-hint {
  font-size: 12px;
  color: rgba(255, 255, 255, 0.4);
  display: flex;
  align-items: center;
  font-weight: 500;

  strong {
    color: rgba(255, 255, 255, 0.65);
  }
}

// ── Map + Panel Layout ────────────────────────────────────────────────────
.map-store-container {
  display: grid;
  grid-template-columns: 340px 1fr;
  gap: 0;
  align-items: start;
  background: #f4f5f7;
  border-top: 3px solid rgba(16, 185, 129, 0.2);
  min-width: 0;
  width: 100%;
  overflow: hidden;

  @media (max-width: 1024px) {
    grid-template-columns: 1fr;
  }
}

// ── Items Panel ───────────────────────────────────────────────────────────
.store-list-section {
  border-right: 1px solid #e5e7eb;
  background: white;
  height: 620px;
  max-height: 620px;
  display: flex;
  flex-direction: column;
  min-width: 0;
  width: 100%;
}

.items-panel {
  display: flex;
  flex-direction: column;
  height: 100%;
}

.items-panel-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px;
  background: linear-gradient(135deg, #064e3b 0%, #065f46 100%);
  cursor: pointer;
  flex-shrink: 0;
  transition: opacity 0.2s;

  &:hover {
    opacity: 0.9;
  }
}

.panel-header-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.panel-icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.15);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.panel-title {
  font-size: 15px;
  font-weight: 700;
  color: white;
  line-height: 1.2;
}

.panel-subtitle {
  font-size: 11px;
  color: rgba(255, 255, 255, 0.5);
  font-weight: 500;
  margin-top: 2px;
}

.panel-count {
  background: #fbbf24;
  color: #1a1a1a;
  font-size: 13px;
  font-weight: 800;
  min-width: 28px;
  height: 28px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 8px;
}

.items-panel-body {
  flex: 1;
  overflow-y: auto;
  padding: 12px;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 48px 20px;
  text-align: center;
}

.empty-icon-wrap {
  width: 64px;
  height: 64px;
  border-radius: 16px;
  background: #f3f4f6;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 16px;
}

.empty-title {
  font-size: 15px;
  font-weight: 700;
  color: #374151;
  margin-bottom: 6px;
}

.empty-sub {
  font-size: 13px;
  color: #9ca3af;
  font-weight: 500;
  line-height: 1.6;
}

.item-cards {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.item-card {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 11px 12px;
  background: #f9fafb;
  border-radius: 10px;
  border: 1px solid #f3f4f6;
  cursor: pointer;
  transition: all 0.2s ease;

  &:hover {
    background: #ecfdf5;
    border-color: #6ee7b7;
    transform: translateX(3px);
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.1);
  }
}

.item-card-badge {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: linear-gradient(135deg, #065f46 0%, #10b981 100%);
  color: white;
  font-size: 11px;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.item-card-body {
  flex: 1;
  min-width: 0;
}

.item-card-name {
  font-size: 14px;
  font-weight: 600;
  color: #111827;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  line-height: 1.3;
  margin-bottom: 3px;
}

.item-card-store {
  display: flex;
  align-items: center;
  font-size: 12px;
  color: #6b7280;
  font-weight: 500;
  margin-bottom: 2px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.item-card-dist {
  display: flex;
  align-items: center;
  font-size: 11px;
  color: #9ca3af;
  font-weight: 500;
}

.item-card-link {
  width: 28px;
  height: 28px;
  border-radius: 8px;
  background: #f3f4f6;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #9ca3af;
  flex-shrink: 0;
  text-decoration: none;
  transition: all 0.2s;

  &:hover {
    background: #10b981;
    color: white;
  }
}

// ── Map Frame ─────────────────────────────────────────────────────────────
.map-section {
  min-width: 0;
  width: 100%;
  height: 620px;
}

.map-frame {
  width: 100%;
  height: 100%;
  position: relative;
  overflow: hidden;
}

.google-map {
  width: 100%;
  height: 100%;
}

// ── How-to Strip ──────────────────────────────────────────────────────────
.howto-strip {
  background: white;
  border-top: 1px solid #e9ecef;
  padding: 24px 32px;
}

.howto-inner {
  display: flex;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
  max-width: 900px;
}

.howto-step {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
  min-width: 160px;
}

.howto-icon {
  width: 38px;
  height: 38px;
  border-radius: 10px;
  background: linear-gradient(135deg, #065f46 0%, #10b981 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  box-shadow: 0 4px 10px rgba(16, 185, 129, 0.25);
}

.howto-title {
  font-size: 13px;
  font-weight: 700;
  color: #111827;
  margin-bottom: 2px;
}

.howto-sub {
  font-size: 12px;
  color: #9ca3af;
  font-weight: 500;
  line-height: 1.3;
}

.howto-arrow {
  flex-shrink: 0;
  opacity: 0.5;
}

// ── Info Window ───────────────────────────────────────────────────────────
.info-window-content {
  min-width: 200px;
  padding: 8px;
}

.info-window-header {
  display: flex;
  align-items: center;
  margin-bottom: 8px;
  padding-bottom: 8px;
  border-bottom: 1px solid #e5e7eb;
  font-size: 14px;
  color: #111827;
}

.info-window-body {
  padding-top: 4px;
}

.store-info-window {
  min-width: 220px;
}

.iw-item-name {
  font-size: 13px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 6px;
}

.store-details {
  display: flex;
  align-items: center;
  color: #6b7280;
  margin-bottom: 8px;
  padding: 4px 0;
}

// ── Custom Animated Markers
:deep(.custom-marker) {
  position: relative;
  width: 25px;
  height: 25px;
  cursor: pointer;
  transform-origin: center bottom;
  animation: markerBounce 2s ease-in-out infinite;
}

:deep(.marker-icon) {
  position: relative;
  width: 25px;
  height: 25px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
  transition: transform 0.3s ease;

  svg {
    width: 24px;
    height: 24px;
  }
}

:deep(.location-marker .marker-icon) {
  background: linear-gradient(135deg, #FBBC04 0%, #F57F17 100%);
  border: 3px solid #FFFFFF;
}

:deep(.store-marker .marker-icon) {
  background: linear-gradient(135deg, #34A853 0%, #2E7D32 100%);
  border: 3px solid #FFFFFF;
}

:deep(.marker-pulse) {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 48px;
  height: 48px;
  border-radius: 50%;
  z-index: 1;
  animation: markerPulse 2s ease-out infinite;
}

:deep(.location-marker .marker-pulse) {
  background: rgba(251, 188, 4, 0.4);
  border: 2px solid rgba(251, 188, 4, 0.6);
}

:deep(.store-marker .marker-pulse) {
  background: rgba(52, 168, 83, 0.4);
  border: 2px solid rgba(52, 168, 83, 0.6);
}

:deep(.custom-marker:hover .marker-icon) {
  transform: scale(1.15);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.4);
}

@keyframes markerBounce {

  0%,
  100% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(-8px);
  }
}

@keyframes markerPulse {
  0% {
    transform: translate(-50%, -50%) scale(1);
    opacity: 1;
  }

  100% {
    transform: translate(-50%, -50%) scale(2);
    opacity: 0;
  }
}

// Custom Zoom Controls
:deep(.custom-zoom-controls) {
  display: flex;
  flex-direction: column;
  gap: 2px;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  overflow: hidden;
}

.zoom-button {
  width: 40px;
  height: 40px;
  border: none;
  background: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  padding: 0;
  margin: 0;
  font-size: 20px;
  font-weight: 600;
  color: #333;
  user-select: none;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;

  &:hover {
    background: #f5f5f5;
  }

  &:active {
    background: #e0e0e0;
    transform: scale(0.95);
  }

  &:focus {
    outline: none;
  }
}

.zoom-in-button {
  border-bottom: 1px solid #e0e0e0;
}

.zoom-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
  line-height: 1;
}

.info-window-content {
  min-width: 200px;
  padding: 8px;
}

.info-window-header {
  display: flex;
  align-items: center;
  margin-bottom: 8px;
  padding-bottom: 8px;
  border-bottom: 1px solid #e0e0e0;
  font-size: 14px;
  color: #1a1a1a;
}

.info-window-body {
  padding-top: 4px;
}

.store-info-window {
  min-width: 220px;
}

.store-details {
  display: flex;
  align-items: center;
  color: #666;
  margin-bottom: 8px;
  padding: 4px 0;
}

.instructions-card {
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 100%);
}

.instructions-content {
  display: flex;
  align-items: flex-start;
}

.instructions-list {
  margin: 0;
  padding-left: 20px;
  color: #666;
  font-size: 14px;
  line-height: 1.8;

  li {
    margin-bottom: 8px;

    &:last-child {
      margin-bottom: 0;
    }
  }
}

// Override default InfoWindow styles
:deep(.gm-style .gm-style-iw-c) {
  border-radius: 8px;
  padding: 0;
}

:deep(.gm-style .gm-style-iw-d) {
  overflow: hidden !important;
}

:deep(.gm-style .gm-style-iw-t::after) {
  background: white;
}

@media (max-width: 768px) {
  .map-hero-inner {
    padding: 24px 20px 20px;
    flex-direction: column;
    align-items: flex-start;
    gap: 20px;
  }

  .hero-title {
    font-size: 24px;
  }

  .hero-stats {
    width: 100%;
    justify-content: center;
    padding: 16px 20px;
  }

  .action-bar {
    padding: 0 20px 20px;
  }

  .action-bar-inner {
    grid-template-columns: 1fr 1fr;
    grid-template-rows: auto auto;
  }

  .search-wrap {
    grid-column: 1 / -1;
  }

  .store-list-section {
    height: auto;
    max-height: none;
    border-right: none;
    border-bottom: 1px solid #e5e7eb;
  }

  .items-panel {
    height: auto;
  }

  .items-panel-body {
    overflow-y: visible;
  }

  .map-section {
    height: 420px;
  }

  .howto-strip {
    padding: 20px;
  }

  .howto-inner {
    gap: 12px;
  }

  .howto-arrow {
    display: none;
  }

  .howto-step {
    min-width: 140px;
    flex: 1 1 calc(50% - 12px);
  }
}

@media (max-width: 595px) {
  .map-hero-inner {
    padding: 20px 16px 16px;
  }

  .hero-icon-wrap {
    width: 48px;
    height: 48px;
  }

  .hero-eyebrow {
    font-size: 10px;
  }

  .hero-title {
    font-size: 20px;
  }

  .hero-desc {
    font-size: 13px;
  }

  .hero-stats {
    padding: 14px 16px;
    gap: 16px;
  }

  .hero-stat-value {
    font-size: 24px;
  }

  .radius-select {
    width: 50px;
    line-height: 1;

    :deep(.q-field__control) {
      height: auto;
      overflow: visible;
    }

    :deep(.q-field__native) {
      font-size: 24px;
      height: auto;
      line-height: 1.2;
      overflow: visible;
    }

    :deep(.q-icon) {
      font-size: 16px;
    }
  }

  .radius-unit {
    font-size: 14px;
  }

  .action-bar {
    padding: 0 16px 20px;
  }

  .action-bar-inner {
    grid-template-columns: 1fr;
    gap: 10px;
  }

  .action-btn {
    width: 100%;
  }

  .search-wrap {
    grid-column: 1;
  }

  .store-list-section {
    height: auto;
    max-height: none;
  }

  .items-panel {
    height: auto;
  }

  .items-panel-body {
    overflow-y: visible;
  }

  .map-section {
    height: 360px;
  }

  .howto-step {
    flex: 1 1 100%;
  }

  .howto-strip {
    padding: 16px;
  }
}
</style>