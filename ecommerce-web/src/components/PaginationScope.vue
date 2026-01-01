<template>
    <div v-if="scope">
        {{ scope.pagination.from }} - {{ scope.pagination.to }} of
        {{ scope.pagination.rowsNumber }}
        <q-btn v-if="scope.pagesNumber > 2" icon="first_page" color="grey-8" round dense flat
            :disable="scope.isFirstPage" @click="firstPage(entityQuery)" />

        <q-btn icon="chevron_left" color="grey-8" round dense flat :disable="scope.isFirstPage"
            @click="previousPage(entityQuery)" />

        <q-btn icon="chevron_right" color="grey-8" round dense flat :disable="scope.isLastPage"
            @click="nextPage(entityQuery)" />

        <q-btn v-if="scope.pagesNumber > 2" icon="last_page" color="grey-8" round dense flat :disable="scope.isLastPage"
            @click="lastPage(entityQuery, pagination)" />
    </div>
</template>

<script setup lang="ts">
import { nextPage, previousPage, firstPage, lastPage } from "boot/axios-call";
import { storeToRefs } from "pinia";
import { useCommonStore } from "src/stores/common"

const useCommon = useCommonStore()
const { pagination, entityQuery } = storeToRefs(useCommon)

interface PaginationScope {
    pagination: {
        from: number;
        to: number;
        rowsNumber: number;
    };
    pagesNumber: number;
    isFirstPage: boolean;
    isLastPage: boolean;
}

const props = defineProps<{
    scope?: PaginationScope;
}>()

</script>