
import { defineStore } from 'pinia'

export const useDeliveryChargeStore = defineStore('deliveryCharge', {
    state: () => ({
        deliveryCharges: [],
        deliveryCharge: {
           name:'',
           amount: 0,
           upscale_amount: 0
        }
    }),
    actions: {
      setDeliveryCharge(payload){
        this.deliveryCharge = payload
      }
    }
})
