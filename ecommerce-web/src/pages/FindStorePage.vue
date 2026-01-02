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
                Discover stores near you and get directions instantly. Click on store markers to view details.
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
  
      <!-- Map Container -->
      <q-card flat bordered class="map-card">
        <q-card-section class="q-pa-none">
          <div class="map-wrapper">
            <GoogleMap ref="mapRef" :api-key="GOOGLE_MAP_API_KEY" :map-id="GOOGLE_MAP_ID" class="google-map"
              :center="{ lat: lat, lng: lng }" :zoom="15" :draggable="true" :clickable-icons="false">
              <!-- My Location Marker -->
              <AdvancedMarker :options="{
                position: { lat: lat, lng: lng },
                gmpDraggable: true,
                title: 'My location',
              }" @drag="markerDrag"
                :pin-options="{ background: '#FBBC04', borderColor: '#F57F17', glyphColor: '#FFFFFF' }">
                <InfoWindow :options="{ headerContent: 'Your Location', disableAutoPan: false }">
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
              <AdvancedMarker v-for="store in nearestStores" :key="store.id" :options="{
                position: { lat: store.latitude, lng: store.longitude },
                gmpDraggable: false,
                title: store.name,
              }" :pin-options="{ background: '#34A853', borderColor: '#2E7D32', glyphColor: '#FFFFFF' }"
                @click="handleClickStoreAdvanceMarker(store)">
                <InfoWindow :options="{ headerContent: store.name, disableAutoPan: false }">
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
  import ZentenpoLogo from 'src/components/ZentenpoLogo.vue';
  
  
  const useCommon = useCommonStore();
  const { lat, lng } = storeToRefs(useCommon);
  
  
  const mapRef = ref<any>(null)
  const directions = ref<google.maps.DirectionsResult | null>(null)
  const directionsRenderer = ref<google.maps.DirectionsRenderer | null>(null)
  const storeListControl = ref<HTMLElement | null>(null)
  
  const origin = ref({ lat: lat.value, lng: lng.value })
  const destination = ref({ lat: 14.609, lng: 120.994 })
  
  
  
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
  
  const kmRadius = ref(1);
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
      updateStoreList();
    }
  };
  
  const updateStoreList = () => {
    if (!storeListControl.value) return;
  
    if (nearestStores.value.length === 0) {
      storeListControl.value.innerHTML = '<div style="color: #666; font-style: italic;">No stores found in the area</div>';
      return;
    }
  
    let storeListHTML = '';
    nearestStores.value.forEach((store, __index) => {
      storeListHTML += `
        <div style="
          padding: 6px 8px; 
          margin: 4px 0; 
          background-color: #f5f5f5; 
          border-radius: 4px; 
          cursor: pointer;
          border-left: 3px solid #34a853;
          transition: background-color 0.2s;
        " 
        onmouseover="this.style.backgroundColor='#e8f5e8'" 
        onmouseout="this.style.backgroundColor='#f5f5f5'"
        onclick="handleStoreClick(${store.id})">
          <div style="font-weight: 500; color: #333;">${store.name} (${store.distance} )</div>
          <div style="color: #666; font-size: 11px;">Click the marker to view the store</div>
        </div>
      `;
    });
  
    storeListControl.value.innerHTML = storeListHTML;
  };
  
  // Make handleStoreClick available globally for onclick events
  const handleStoreClick = (storeId: number) => {
    const store = nearestStores.value.find(s => s.id === storeId);
    if (store) {
      handleClickStoreAdvanceMarker(store);
    }
  };
  
  // Add to window for onclick access
  if (typeof window !== 'undefined') {
    (window as any).handleStoreClick = handleStoreClick;
  }
  
  const handleClickStoreAdvanceMarker = (store: StoreInterface) => {
    destination.value = { lat: store.latitude, lng: store.longitude }
    requestDirections()
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
          addLocationControl(map)
          resolve(void 0)
        } else {
          setTimeout(checkMapReady, 200)
        }
      }
      checkMapReady()
    })
  }
  
  const addLocationControl = (map: google.maps.Map) => {
    // Create a custom control element
    const controlDiv = document.createElement('div')
    controlDiv.style.cssText = `
      background-color: white;
      border: 1px solid #ccc;
      border-radius: 4px;
      padding: 8px 12px;
      margin: 0;
      font-family: Arial, sans-serif;
      font-size: 14px;
      max-width: 300px;
      max-height: 400px;
      overflow-y: auto;
      box-shadow: 0 2px 6px rgba(0,0,0,0.3);
    `
  
    // Add header with click functionality
    const headerDiv = document.createElement('div')
    headerDiv.style.cssText = `
      display: flex; 
      align-items: center; 
      gap: 4px; 
      margin-bottom: 8px; 
      font-weight: bold; 
      cursor: pointer; 
      padding: 4px 8px; 
      border-radius: 4px; 
      transition: background-color 0.2s;
    `
    headerDiv.innerHTML = `
      <span style="color: #1976d2;">🏪</span>
      <span style="color: #1976d2;">Nearby Stores</span>
      <span style="color: #666; font-size: 11px; margin-left: 8px;">(Click to search)</span>
    `
  
    // Add click functionality to header
    headerDiv.addEventListener('click', () => {
      getNearestStore()
    })
  
    // Add hover effects
    headerDiv.addEventListener('mouseenter', () => {
      headerDiv.style.backgroundColor = '#f0f8ff'
    })
  
    headerDiv.addEventListener('mouseleave', () => {
      headerDiv.style.backgroundColor = 'transparent'
    })
  
    // Add store list container
    const storeListDiv = document.createElement('div')
    storeListDiv.id = 'store-list-container'
    storeListDiv.style.cssText = 'font-size: 12px;'
    storeListDiv.innerHTML = '<div style="color: #666; font-style: italic;">Click "Nearby Stores" above to find stores</div>'
  
    controlDiv.appendChild(headerDiv)
    controlDiv.appendChild(storeListDiv)
  
    // Store reference for updates
    storeListControl.value = storeListDiv
  
    // Position the control below the map type controls (satellite/map toggle)
    // Remove from default controls and position manually
    setTimeout(() => {
      const mapContainer = map.getDiv()
      if (mapContainer) {
        // Position absolutely within the map container
        controlDiv.style.position = 'absolute'
        controlDiv.style.top = '60px' // Position below the map/satellite controls
        controlDiv.style.left = '10px' // Align with left edge
        controlDiv.style.zIndex = '1000'
        controlDiv.style.pointerEvents = 'auto'
  
        // Append directly to map container
        mapContainer.appendChild(controlDiv)
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
  
  .google-map {
    width: 100%;
    height: 100%;
    border-radius: 12px;
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
  