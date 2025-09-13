<template>
    <div style="position: relative;">
        <GoogleMap ref="mapRef" :api-key="GOOGLE_MAP_API_KEY" :map-id="GOOGLE_MAP_ID" :center="origin" :zoom="14"
            style="width:100%;height:500px">
            <AdvancedMarker :options="{ position: origin, title: 'Start', gmpDraggable: true }"
                :pin-options="{ background: '#FBBC04' }" @drag="markerDrag" />
            <AdvancedMarker :options="{ position: destination, title: 'End' }"
                :pin-options="{ background: '#34A853' }" />
        </GoogleMap>


    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick, watch } from 'vue'
import { GoogleMap, AdvancedMarker } from 'vue3-google-map'
import { GOOGLE_MAP_API_KEY, GOOGLE_MAP_ID } from 'src/boot/constant';

const mapRef = ref(null)
const directions = ref(null)
const directionsRenderer = ref(null)

const origin = ref({ lat: 14.5995, lng: 120.9842 })
const destination = ref({ lat: 14.609, lng: 120.994 })

onMounted(async () => {
    // Wait for the next tick to ensure the GoogleMap component is mounted
    await nextTick()

    // Wait for Google Maps API to be fully loaded
    await waitForGoogleMaps()

    // Wait for the map to be fully initialized
    await waitForMapReady()

    // Request directions after everything is ready
    requestDirections()
})

// Cleanup on component unmount
onUnmounted(() => {
    // Cleanup if needed
})

// Watch for when the map becomes available
watch(() => {
    const map = mapRef.value?.$mapObject || mapRef.value?.map || mapRef.value?.$map
    return map
}, (map) => {
    if (map && directions.value) {
        setupDirectionsRenderer(map)
    }
})

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
                resolve()
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
                resolve()
            } else {
                setTimeout(checkMapReady, 200)
            }
        }
        checkMapReady()
    })
}

const setupDirectionsRenderer = (map) => {
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

const markerDrag = (e) => {
    origin.value.lat = e.latLng.lat();
    origin.value.lng = e.latLng.lng();

    // Update directions when marker is dragged
    requestDirections();

};



</script>
