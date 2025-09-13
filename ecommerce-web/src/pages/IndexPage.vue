<template>
  <div style="margin: 5px">
    <section>
      <q-btn label="Search Nearest Store" color="primary" @click="getNearestStore" outline />
    </section>
    <GoogleMap ref="mapRef" class="q-mt-sm" :api-key="GOOGLE_MAP_API_KEY" :map-id="GOOGLE_MAP_ID" style="height: 450px"
      :center="{ lat: lat, lng: lng }" :zoom="15" :draggable="true" :clickable-icons="false">
      <AdvancedMarker :options="{
        position: { lat: lat, lng: lng },
        gmpDraggable: false,
        title: 'My location',
      }" @drag="markerDrag" :pin-options="{ background: '#FBBC04' }" >
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
            <a :href="`/public_stores/${store.id}`">View to Store</a>
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
  }
};

const handleClickStoreAdvanceMarker = (store: StoreInterface) => {
  console.log(store)
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
        resolve(void 0)
      } else {
        setTimeout(checkMapReady, 200)
      }
    }
    checkMapReady()
  })
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
