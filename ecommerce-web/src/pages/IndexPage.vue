<template>
  <div style="margin: 5px">
    <GoogleMap ref="mapRef" class="q-mt-sm" :api-key="GOOGLE_MAP_API_KEY" :map-id="GOOGLE_MAP_ID" style="height: 450px"
      :center="{ lat: lat, lng: lng }" :zoom="15" :draggable="true" :clickable-icons="false">
      <AdvancedMarker :options="{
        position: { lat: lat, lng: lng },
        gmpDraggable: false,
        title: 'My location',
      }" @drag="markerDrag" :pin-options="{ background: '#FBBC04' }">
        <InfoWindow :options="{ headerContent: 'My Location', disableAutoPan: false }">
          <div>
          </div>
        </InfoWindow>
      </AdvancedMarker>
      <AdvancedMarker v-for="store in nearestStores" :key="store.id" :options="{
        position: { lat: store.latitude, lng: store.longitude },
        gmpDraggable: false,
        title: store.name,
      }" :pin-options="{ background: '#34A853' }" @click="handleClickStoreAdvanceMarker(store)">
        <InfoWindow :options="{ headerContent: store.name, disableAutoPan: false }">
          <div class="my-window">
            <a :href="`/public_stores/${store.optimus_id}`">View to Store</a>
          </div>
        </InfoWindow>
      </AdvancedMarker>
    </GoogleMap>
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
