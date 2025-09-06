import { defineStore } from 'pinia';
import { Notify } from 'quasar';



interface CartItem {
  id: number;
  optimus_id: number;
  name: string;
  item_price: Array<{ item_price: Array<{ unit_id: number; price: number; unit: object }> }>
  variations: Array<{ unit: number; count: number }>
  primary_img: { path_url: string}
}


export const useUserCartStore = defineStore('userCart', {
  state: () => ({
    cart: [] as CartItem[],
    checkoutBtnDisabled: true,
    deliveryCharge: 0,
    selectedReceiveMethod: 1,
    selectedPaymenthMethod: 1
  }),
  getters: {
    countTotalItems: (state) =>
      state.cart.reduce(
        (total, current) => parseInt(total) + parseInt(current.count),
        0
      ),
    groupByStore: (state) => {
      const key = 'store_id';
      return state.cart.reduce(
        (hash, obj) => ({
          ...hash,
          [obj[key]]: (hash[obj[key]] || []).concat(obj),
        }),
        {}
      );
    },
    total: (state) => {
      const purchasesTotal = state.cart.reduce((total, current: { variations: Array<{ count: number, unit: number}>}) => {
        // Iterate over each variation to find the corresponding price
        current.variations?.forEach((variation: { count: number, unit: number}) => {
          // Look through all itemPrice entries to find a matching unit_id
          for (const item of current.item_price) {
            if (item.unit_id === variation.unit) {
              // Accumulate the total
              total += variation.count * item.online_price;
              break; // Exit the loop once you find a match
            }
          }
        });
      
        return total; // Make sure to return total at the end of reduce
      }, 0);

      return purchasesTotal;
    },
    storeIds: (state) => {
      return [...new Set(state.cart.map((v) => v.store_id))];
    }
  },
  actions: {
    addQty(payload: object) {
      const cartIndex = this.cart.findIndex(
        (v) => v.optimus_id === payload.optimus_id
      );
      if (cartIndex > -1) {
       this.cart[cartIndex].count =  this.cart[cartIndex].count + payload.count
       this.cart[cartIndex].variations.push(payload.variations[0])
      } else {
        this.cart.push(payload);
      }
    },
    removeItem(optimusId: number) {
      const cartIndex = this.cart.findIndex(
        (v: { optimus_id: number }) => v.optimus_id === optimusId
      );
      if (cartIndex > -1) {
        Notify.create({
          message:
            'This will delete your item. Are you sure you want to proceed?',
          type: 'negative',
          actions: [
            {
              label: 'Cancel',
              color: 'white',
              handler: () => {
                /* ... */
              },
            },
            {
              label: 'OK',
              color: 'yellow',
              handler: () => {
                this.cart.splice(cartIndex, 1);
              },
            },
          ],
        });
      }
    },
    emptyCart(){
      this.cart = []
    }
  },
  persist: true,
});
