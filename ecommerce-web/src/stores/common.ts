import { defineStore } from 'pinia';
import { get } from 'boot/axios-call';
import { DEFAULT_LAT, DEFAULT_LNG } from 'boot/constant'
import { CommonState } from 'src/boot/interfaces';

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
    async setResultPagination(payload: any, loading = true) {
      const response = await get(payload, loading);
      if (response && typeof response === 'object' && 'data' in response) {
        const meta = (response as any).data.meta
        const data = (response as any).data.data
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
