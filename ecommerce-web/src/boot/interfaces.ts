export interface ResultInterface {
  data: {
    data: Array<object>
  }
}


export interface GetQuery {
  with?: string
  columns?: string
  filters?: string
  page?: number
  type?: string
  orderBy?: string
  latitude?: number
  longitude?: number
  radius?: number
  storeIds?: Array<number>
  limit?: number
  whereHas?: string
  listingApi?: string
}
export interface GetParams {
  message?: string
  entity: string
  query: GetQuery
}

export interface Pagination {
  page: number
  rowsPerPage: number
  rowsNumber: number
  from: number
  to: number
  lastPage: number
}

export interface CommonState {
  entityQuery: GetParams
  pagination: Pagination
  result: Array<object>
  currentPage: number
  lastPage: number
  lat: number
  lng: number
  mobile: string
}

export interface CreateData {
  entity: string
  data: object
}

export interface User {
  email: string|null
  password: string|null
  images: Array<object>
  enrollees: Array<object>
}

interface UserMenu {
  id: number 
  name: string 
  icon: string 
  path: string
}

export interface ProfileState {
  name: string|null
  mobile: number|string
  token: string
  optimus_id: number
  userMenu: Array<UserMenu>
}

export interface Menu {
  id: number
  icon: string | undefined
  name: string|undefined
  all_children: Array<Menu>
  path: string | undefined
}

export interface UserState {
  profile: ProfileState
  user: ProfileState
}

export interface UnitInterface{
  value: number
  label: string
  name?: string
  id?: number

}

export interface SizeInterface{
  value: number
  label: string
  name?: string
  id?: number

}

export interface CategoryInterface{
  value: number
  label: string
  name?: string
  id?: number

}

interface PriceInterface {
  id: number;
  color_id?: number; // Optional
  size_id?: number;  // Optional
  original_price: number;
  selling_price: number;
  online_price: number;
  qty?: number; // Optional
  category?: { value: number; label: string; name?: string; id?: number } | null; // Optional
  unit?: { value: number; label: string } | null; // Optional
}

export interface ItemInterface {
  name: string;
  description: string;
  images?: string[]; // Assuming it's an array of strings
  unit?: UnitInterface
  category?: CategoryInterface|null
  item_price?: PriceInterface[]
}

export interface ItemOrder {
  item_id: number
  unit_id: number
}

export interface CustomerOrder {
  store_id: string
  items: ItemOrder[]
}

export interface LoginInterface {
  mobile: string
  password: string
}

export interface ShowInterface {
  message?: string
  entity: string
  optimus_id: number
  query?: GetQuery
}

export interface DeleteInterface {
  label: string
  entity: string
  optimus_id: number
  query?: GetQuery
}

export interface UpdateInterface {
  message?: string
  entity: string
  optimus_id: number|string|string[]
  data: object
  query?: GetQuery
}

export interface StoreInterface {
  id: number
  name: string
  latitude: number
  longitude: number
  distance: number
  optimus_id: number
}
interface OrderInterface {
  id: string
  store_id: string
  item_name: string
  store: StoreInterface
  online_price: string
  qty: number
  subtotal: number
}

export interface TransactionResultInterface {
  orders: Array<OrderInterface>
  created_at: string
}

export interface GroupStoreItemInterface{
  id: number
  variations: Array<object>
  count: number 
  unit_id: number
}
export interface GroupStoreItemsInterface {
  items: Array<GroupStoreItemInterface>
}

export interface CustomerTransactionRow {
  reference_id: number
  grand_total: number
  total: number
  payment_method: { name: string }
  receive_method: { name: string }
  delivery_charge: string
  optimus_id: number,
  created_at: string
}

export interface StoreRow {
  id: number;
  name: string;
  mobile: string;
  optimus_id: number;
}

export interface AccessRightInterface {
  id: number,
  label: string,
  value: number
}

export interface StoreMenuInterface {
  id: number,
  label: string,
  value: number
}

export interface StoreMenuAccessInterface {
  id: number;
  store: StoreInterface;
  access_right: AccessRightInterface|null;
  store_menu: StoreMenuInterface|null;
  optimus_id: number;
}

export interface ErrorValidationInterface{
  message: string
  status: boolean
}

