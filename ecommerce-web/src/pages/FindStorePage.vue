<template>
  <div class="map-page-container">
    <!-- Header Section -->
    <q-card flat bordered class="map-header-card q-mb-lg">
      <q-card-section class="map-header-section">
        <div class="map-header-main">
          <div class="map-header-icon-wrapper">
            <q-icon name="explore" size="40px" color="primary" />
          </div>
          <div class="map-header-text">
            <div class="map-header-title-wrapper">
              <h1 class="map-header-title">Find Nearby Stores</h1>
              <q-chip color="primary" text-color="white" size="sm" class="map-header-badge">
                <q-icon name="store" size="xs" class="q-mr-xs" />
                {{ nearestStores.length }} {{ nearestStores.length === 1 ? 'store' : 'stores' }} found
              </q-chip>
            </div>
            <p class="map-header-description">
              Discover stores near you and get directions instantly. Click on store markers to view
              details.
            </p>
          </div>
        </div>
        <div class="map-header-actions">
          <q-btn color="primary" icon="my_location" label="Refresh Location" unelevated @click="localGetLocation"
            class="refresh-location-btn" size="md" />
          <q-btn color="secondary" icon="search" label="Find Stores" outline @click="getNearestStore"
            class="find-stores-btn" size="md" :loading="false" />
        </div>
      </q-card-section>
    </q-card>

    <!-- Map and Store List Container -->
    <div class="map-store-container">
      <!-- Store List Section -->
      <div class="store-list-section" v-if="showStoreList">
        <q-card flat bordered class="store-list-card">
          <q-card-section class="store-list-header">
            <div class="store-list-title" @click="getNearestStore">
              <q-icon name="store" color="primary" size="md" class="q-mr-sm" />
              <div>
                <div class="text-h6 text-weight-bold">Nearby Stores</div>
                <div class="text-caption text-grey-6">Click to search for stores</div>
              </div>
            </div>
          </q-card-section>
          <q-separator />
          <q-card-section class="store-list-content">
            <div v-if="nearestStores.length === 0" class="empty-store-list">
              <q-icon name="store" size="48px" color="grey-4" />
              <div class="text-body2 text-grey-6 q-mt-md">No stores found yet</div>
              <div class="text-caption text-grey-5 q-mt-xs">Click "Nearby Stores" above to search</div>
            </div>
            <div v-else class="store-list-items">
              <div v-for="store in nearestStores" :key="store.id" class="store-list-item"
                @click="handleClickStoreAdvanceMarker(store)">
                <div class="store-item-content">
                  <q-icon name="store" color="positive" size="sm" class="store-item-icon" />
                  <div class="store-item-info">
                    <div class="text-body1 text-weight-medium store-item-name">{{ store.name }}</div>
                    <div class="text-caption text-grey-6" v-if="store.distance">
                      <q-icon name="straighten" size="xs" class="q-mr-xs" />
                      {{ store.distance }} away
                    </div>
                  </div>
                </div>
                <q-icon name="chevron_right" color="grey-5" size="sm" />
              </div>
            </div>
          </q-card-section>
        </q-card>
      </div>

      <!-- Map Section -->
      <div class="map-section" ref="mapSectionRef">
        <q-card flat bordered class="map-card">
          <q-card-section class="q-pa-none">
            <div class="map-wrapper">
              <GoogleMap ref="mapRef" :api-key="GOOGLE_MAP_API_KEY" :map-id="GOOGLE_MAP_ID" class="google-map"
                :center="{ lat: lat, lng: lng }" :zoom="currentZoom" :draggable="true" :clickable-icons="false">
                <!-- My Location Marker -->
                <AdvancedMarker :options="getLocationMarkerOptions()" @drag="markerDrag">
                  <InfoWindow v-model="showInfoWindow"
                    :options="{ position: { lat: lat, lng: lng }, headerContent: '&nbsp;&nbsp;&nbsp;Your Location', disableAutoPan: false }">
                    <div class="info-window-content">
                      <div class="info-window-header">
                        <q-icon name="my_location" color="primary" size="sm" class="q-mr-xs" />
                        <span class="text-weight-bold">Your Location</span>
                      </div>
                      <div class="info-window-body">
                        <p class="text-caption text-grey-7 q-ma-none">
                          This is your current location on the map
                        </p>
                      </div>
                    </div>
                  </InfoWindow>
                </AdvancedMarker>

                <!-- Store Markers -->
                <AdvancedMarker v-for="store in nearestStores" :key="store.id" :options="getStoreMarkerOptions(store)"
                  @click="handleClickStoreAdvanceMarker(store)">
                  <InfoWindow
                    :options="{ headerContent: '&nbsp;&nbsp;&nbsp;' + store.name, disableAutoPan: false, closeButton: true } as any">
                    <div class="info-window-content store-info-window">
                      <div class="info-window-header">
                        <q-icon name="store" color="positive" size="sm" class="q-mr-xs" />
                        <span class="text-weight-bold">{{ store.name }}</span>
                      </div>
                      <div class="info-window-body">
                        <div class="store-details" v-if="store.distance">
                          <q-icon name="straighten" size="xs" class="q-mr-xs" />
                          <span class="text-caption">{{ store.distance }} away</span>
                        </div>
                        <q-btn :to="`/public_stores/${store.optimus_id}`" color="primary" size="sm" unelevated
                          class="q-mt-sm full-width" label="View Store" icon="arrow_forward" />
                      </div>
                    </div>
                  </InfoWindow>
                </AdvancedMarker>
              </GoogleMap>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>

    <!-- Instructions Card -->
    <q-card flat bordered class="instructions-card q-mt-md">
      <q-card-section>
        <div class="instructions-content">
          <q-icon name="info" color="info" size="md" class="q-mr-md" />
          <div>
            <div class="text-subtitle2 text-weight-bold q-mb-xs">How to use the map</div>
            <ul class="instructions-list">
              <li>Click on "Nearby Stores" in the map to find stores in your area</li>
              <li>Click on any green store marker to view store details and get directions</li>
              <li>Drag the map to explore different areas</li>
              <li>Use the refresh button to update your current location</li>
            </ul>
          </div>
        </div>
      </q-card-section>
    </q-card>
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
import { StoreInterface } from 'src/boot/interfaces';


const useCommon = useCommonStore();
const { lat, lng } = storeToRefs(useCommon);
const showInfoWindow = ref(true);


const mapRef = ref<any>(null)
const mapSectionRef = ref<HTMLElement | null>(null)
const directions = ref<google.maps.DirectionsResult | null>(null)
const directionsRenderer = ref<google.maps.DirectionsRenderer | null>(null)
const currentZoom = ref(15)
const showStoreList = ref(true)

const origin = ref({ lat: lat.value, lng: lng.value })
const destination = ref({ lat: 14.609, lng: 120.994 })

// Create animated location marker element
const createLocationMarkerElement = (): HTMLElement => {
  const markerDiv = document.createElement('div')
  markerDiv.className = 'custom-marker location-marker'
  markerDiv.innerHTML = `
        <div class="marker-pulse"></div>
        <div class="marker-icon">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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

// Get store marker options
const getStoreMarkerOptions = (store: StoreInterface) => {
  return {
    position: { lat: store.latitude, lng: store.longitude },
    gmpDraggable: false,
    title: store.name,
    content: createStoreMarkerElement(),
  }
}

onMounted(async () => {
  localGetLocation();
  // Wait for the next tick to ensure the GoogleMap component is mounted
  await nextTick()

  // Wait for Google Maps API to be fully loaded
  await waitForGoogleMaps()

  // Wait for the map to be fully initialized
  await waitForMapReady()

});

const localGetLocation = () => {
  getLocation().then((position) => {
    lat.value = position.coords.latitude;
    lng.value = position.coords.longitude;
    origin.value = { lat: lat.value, lng: lng.value }
  });
}
const markerDrag = (e: { latLng: google.maps.LatLng }) => {
  lat.value = e.latLng.lat();
  lng.value = e.latLng.lng();
};

const kmRadius = ref(30);
const nearestStores = ref<Array<StoreInterface>>([]);

const getNearestStore = async () => {

  localGetLocation();
  const result = await get(
    {
      message: 'Searching nearest store',
      entity: 'public_stores',
      query: {
        orderBy: 'name:asc',
        columns: 'id,name',
        latitude: lat.value,
        longitude: lng.value,
        radius: kmRadius.value,
      },
    },
    true
  );

  if (result && typeof result === 'object' && 'data' in result) {
    nearestStores.value = (result as any).data.data;
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

</script>

<style scoped lang="scss">
.map-page-container {
  padding: 24px;
  max-width: 1400px;
  margin: 0 auto;
}

.map-header-card {
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  background: white;
  overflow: hidden;
  transition: all 0.3s ease;

  &:hover {
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.12);
  }
}

.map-header-section {
  padding: 32px;
  background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
}

.map-header-main {
  display: flex;
  align-items: flex-start;
  gap: 24px;
  margin-bottom: 24px;
}

.map-header-icon-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 64px;
  height: 64px;
  border-radius: 16px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
  flex-shrink: 0;

  .q-icon {
    color: white !important;
  }
}

.map-header-text {
  flex: 1;
  min-width: 0;
}

.map-header-title-wrapper {
  display: flex;
  align-items: center;
  gap: 16px;
  flex-wrap: wrap;
  margin-bottom: 12px;
}

.map-header-title {
  font-size: 28px;
  font-weight: 700;
  color: #1a1a1a;
  margin: 0;
  line-height: 1.2;
  letter-spacing: -0.5px;
}

.map-header-badge {
  font-weight: 600;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.map-header-description {
  font-size: 15px;
  color: #666;
  line-height: 1.6;
  margin: 0;
  max-width: 600px;
}

.map-header-actions {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}

.refresh-location-btn,
.find-stores-btn {
  min-width: 160px;
  height: 44px;
  font-weight: 600;
  border-radius: 10px;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);

  &:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  }
}

.refresh-location-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;

  &:hover {
    background: linear-gradient(135deg, #5568d3 0%, #6a3f8f 100%);
  }
}

.map-store-container {
  display: grid;
  grid-template-columns: 400px 1fr;
  gap: 24px;
  align-items: start;

  @media (max-width: 1024px) {
    grid-template-columns: 1fr;
  }
}

.map-section {
  min-width: 0;
}

.map-card {
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
  transition: box-shadow 0.3s ease;

  &:hover {
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
  }
}

.map-wrapper {
  position: relative;
  width: 100%;
  height: 600px;
  border-radius: 12px;
  overflow: hidden;
}

.store-list-section {
  min-width: 0;
}

.store-list-card {
  border-radius: 12px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
  max-height: 600px;
  display: flex;
  flex-direction: column;
}

.store-list-header {
  padding: 20px;
  background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 100%);
  cursor: pointer;
  transition: all 0.3s ease;

  &:hover {
    background: linear-gradient(135deg, #e8f0f5 0%, #f5f7fa 100%);
  }
}

.store-list-title {
  display: flex;
  align-items: center;
  gap: 12px;
}

.store-list-content {
  padding: 16px;
  overflow-y: auto;
  flex: 1;
  max-height: 520px;
}

.empty-store-list {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
  text-align: center;
  min-height: 200px;
}

.store-list-items {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.store-list-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 16px;
  background: #f8f9fa;
  border-radius: 8px;
  border-left: 3px solid #34a853;
  cursor: pointer;
  transition: all 0.2s ease;

  &:hover {
    background: #e8f5e8;
    transform: translateX(4px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  }
}

.store-item-content {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
  min-width: 0;
}

.store-item-icon {
  flex-shrink: 0;
}

.store-item-info {
  flex: 1;
  min-width: 0;
}

.store-item-name {
  color: #1a1a1a;
  line-height: 1.4;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.google-map {
  width: 100%;
  height: 100%;
  border-radius: 12px;
}

// Custom Animated Markers
:deep(.custom-marker) {
  position: relative;
  width: 48px;
  height: 48px;
  cursor: pointer;
  transform-origin: center bottom;
  animation: markerBounce 2s ease-in-out infinite;
}

:deep(.marker-icon) {
  position: relative;
  width: 48px;
  height: 48px;
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
  .map-page-container {
    padding: 16px;
  }

  .map-header-section {
    padding: 24px;
  }

  .map-header-main {
    flex-direction: column;
    gap: 20px;
    margin-bottom: 20px;
  }

  .map-header-icon-wrapper {
    width: 56px;
    height: 56px;

    .q-icon {
      font-size: 32px !important;
    }
  }

  .map-header-title {
    font-size: 24px;
  }

  .map-header-title-wrapper {
    flex-direction: column;
    align-items: flex-start;
    gap: 12px;
  }

  .map-header-actions {
    width: 100%;
  }

  .refresh-location-btn,
  .find-stores-btn {
    flex: 1;
    min-width: 0;
  }

  .map-wrapper {
    height: 500px;
  }

  .instructions-content {
    flex-direction: column;
  }
}

@media (max-width: 600px) {
  .map-header-section {
    padding: 20px;
  }

  .map-header-icon-wrapper {
    width: 48px;
    height: 48px;

    .q-icon {
      font-size: 28px !important;
    }
  }

  .map-header-title {
    font-size: 22px;
  }

  .map-header-description {
    font-size: 14px;
  }

  .map-header-actions {
    flex-direction: column;
  }

  .refresh-location-btn,
  .find-stores-btn {
    width: 100%;
  }

  .map-wrapper {
    height: 400px;
  }
}
</style>