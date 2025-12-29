<script setup>
import { onMounted, ref, watch, onUnmounted } from 'vue'

const props = defineProps({
    map: Object,
    directions: Object,
    options: {
        type: Object,
        default: () => ({})
    }
})

const directionsRenderer = ref(null)

onMounted(() => {
    if (props.map && window.google) {
        directionsRenderer.value = new window.google.maps.DirectionsRenderer({
            ...props.options
        })
        directionsRenderer.value.setMap(props.map)

        // If directions are already available, set them
        if (props.directions) {
            directionsRenderer.value.setDirections(props.directions)
        }
    }
})

watch(
    () => props.map,
    (newMap) => {
        if (newMap && window.google && !directionsRenderer.value) {
            directionsRenderer.value = new window.google.maps.DirectionsRenderer({
                ...props.options
            })
            directionsRenderer.value.setMap(newMap)

            if (props.directions) {
                directionsRenderer.value.setDirections(props.directions)
            }
        }
    }
)

watch(
    () => props.directions,
    (newDirections) => {
        if (directionsRenderer.value && newDirections) {
            directionsRenderer.value.setDirections(newDirections)
        }
    }
)

watch(
    () => props.options,
    (newOptions) => {
        if (directionsRenderer.value) {
            directionsRenderer.value.setOptions(newOptions)
        }
    },
    { deep: true }
)

onUnmounted(() => {
    if (directionsRenderer.value) {
        directionsRenderer.value.setMap(null)
    }
})
</script>

<template>
    <div></div>
</template>