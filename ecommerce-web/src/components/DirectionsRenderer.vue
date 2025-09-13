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
    console.log('DirectionsRenderer mounted, map:', props.map, 'directions:', props.directions)
    if (props.map && window.google) {
        directionsRenderer.value = new window.google.maps.DirectionsRenderer({
            ...props.options
        })
        directionsRenderer.value.setMap(props.map)
        console.log('DirectionsRenderer created and set on map')

        // If directions are already available, set them
        if (props.directions) {
            directionsRenderer.value.setDirections(props.directions)
            console.log('Directions set on renderer')
        }
    }
})

watch(
    () => props.map,
    (newMap) => {
        console.log('Map prop changed:', newMap)
        if (newMap && window.google && !directionsRenderer.value) {
            directionsRenderer.value = new window.google.maps.DirectionsRenderer({
                ...props.options
            })
            directionsRenderer.value.setMap(newMap)
            console.log('DirectionsRenderer created with new map')

            if (props.directions) {
                directionsRenderer.value.setDirections(props.directions)
                console.log('Directions set on renderer with new map')
            }
        }
    }
)

watch(
    () => props.directions,
    (newDirections) => {
        console.log('Directions prop changed:', newDirections)
        if (directionsRenderer.value && newDirections) {
            directionsRenderer.value.setDirections(newDirections)
            console.log('Directions updated on renderer')
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