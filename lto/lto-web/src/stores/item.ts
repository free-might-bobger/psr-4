
import { defineStore } from 'pinia'

interface ItemState {
    name: string,
    barcode: string,
    sku: string,
    description: string,
    unit: string,
    item_price: {
        price: number
    }
  }

interface ItemObjectState {
    items: Array<ItemState>,
    item: ItemState,
    searchString: string,
    selectedCategory: string | null
}

const itemFields = {
    name: '',
    barcode: '',
    sku: '',
    description: '',
    item_price: {
      price: 0
    },
    unit: ''
}
export const useItemStore = defineStore('items', {
    state: () : ItemObjectState => ({
        selectedCategory: null,
        items: [],
        item: itemFields,
        searchString: ''
    }),
    actions: {
      setSearchString(payload: string){
        this.searchString = payload
      },
      // setItems(payload){
      //   this.items = payload
      // },
      // setEmptyItem(){
      //   this.item = itemFields
      // },
      // setItem(payload){
      //   this.item = payload
      // },
      // setShowForm(payload){
      //   this.showForm = payload
      // },
      // setPrice(payload){
      //   this.item.item_price.price = payload
      // }
      
    }
})
