import { defineStore } from 'pinia';
import { get } from 'boot/axios-call';
import { DEFAULT_LAT, DEFAULT_LNG } from 'boot/constant'
import { CommonState, GetParams } from 'src/boot/interfaces';

interface PaginationMeta {
  current_page: number
  per_page: number
  total: number
  from: number
  to: number
  last_page: number
}

interface PaginatedResponse {
  data: {
    data: Array<object>
    meta?: PaginationMeta
  }
}

export const useCommonStore = defineStore('common', {
  state: (): CommonState => ({
    mobile: '',
    entityQuery: {
      message: '',
      entity: '',
      query: {
        limit: 12,
        page: 1
      }
    },
    pagination: {
      from: 0,
      to: 0,
      page: 1,
      rowsPerPage: 12,
      rowsNumber: 1,
      lastPage: 0,
    },
    result: [],
    currentPage: 1,
    lastPage: 1,
    lat: DEFAULT_LAT,
    lng: DEFAULT_LNG,
  }),
  actions: {
    async setResultPagination(payload: GetParams, loading = true) {
      const response = await get(payload, loading);
      if (response && typeof response === 'object' && 'data' in response) {
        const { data, meta } = (response as PaginatedResponse).data;
        this.result = data
        if (meta) {
          this.pagination.page = meta.current_page;
          this.pagination.rowsPerPage = meta.per_page;
          this.pagination.rowsNumber = meta.total;
          this.pagination.from = meta.from;
          this.pagination.to = meta.to;
          this.pagination.lastPage = meta.last_page;
        }
      }
    }
  },
  
  persist: true,
});
