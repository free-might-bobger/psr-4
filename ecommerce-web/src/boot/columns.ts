import { CustomerTransactionRow, StoreRow } from './interfaces';

export const storeColumns = [
  {
    name: 'name',
    required: true,
    label: 'Name',
    align: 'left',
    field: (row: StoreRow) => row.name
  },
  
  {
    name: 'mobile',
    required: true,
    label: 'Mobile',
    align: 'left',
    field: (row: StoreRow) => row.mobile
  },
  {
    name: 'Actions',
    label: 'Actions',
    align: 'center',
    field: (row: StoreRow) => row.id
  },
]

export const MyStoreColumns = [
  {
    name: 'name',
    required: true,
    label: 'Name',
    align: 'left' as const,
    field: (row: StoreRow) => row.name
  },
  
  {
    name: 'Actions',
    label: 'Actions',
    align: 'right' as const,
    field: (row: StoreRow) => row.optimus_id
  },
]

export const StoreMenuAccessColumns = [
  {
    name: 'name',
    required: true,
    label: 'Name',
    align: 'left' as const,
    field: (row: StoreRow) => row.user?.name
  },
  {
    name: 'accessRight',
    required: true,
    label: 'Access Right',
    align: 'left' as const,
    field: (row: StoreRow) => row.access_right?.name
  },
  {
    name: 'Menu',
    required: true,
    label: 'Menu',
    align: 'left' as const,
    field: (row: StoreRow) => row.store_menu?.name
  },
  {
    name: 'Store',
    required: true,
    label: 'Store',
    align: 'left' as const,
    field: (row: StoreRow) => row.store?.name
  },
  
  {
    name: 'Actions',
    label: 'Actions',
    align: 'right' as const,
    field: (row: StoreRow) => row.optimus_id
  },
]

export const itemColumns = [
  {
    name: 'name',
    required: true,
    label: 'Name',
    align: 'left' as const,
    field: (row: StoreRow) => row.name
  },
  
  {
    name: 'Actions',
    label: 'Actions',
    align: 'right' as const,
    field: (row: StoreRow) => row.optimus_id
  },
]

export const customerTransactionsColumns = [
  {
    name: 'reference',
    required: true,
    label: 'Reference No.:',
    align: 'left',
    field: (row: CustomerTransactionRow): number => row.reference_id,
  },
  {
    name: 'subtotal',
    required: true,
    label: 'Subtotal: ',
    align: 'left',
    field: (row: CustomerTransactionRow): number => row.total,
  },
  {
    name: 'delivery',
    required: true,
    label: 'Delivery Charge:',
    align: 'left',
    field: (row: CustomerTransactionRow): string => row.delivery_charge,
  },
  {
    name: 'grandTotal',
    required: true,
    label: 'Grand Total:',
    align: 'left',
    field: (row: CustomerTransactionRow): number => row.grand_total,
  },
  {
    name: 'paymentType',
    required: true,
    label: 'Payment Type:',
    align: 'left',
    field: (row: CustomerTransactionRow): string => row.payment_method?.name,
  },
  {
    name: 'receivingMethod',
    required: true,
    label: 'Receiving Method:',
    align: 'left',
    field: (row: CustomerTransactionRow): string => row.receive_method?.name,
  },
  {
    name: 'createdAt',
    required: true,
    label: 'Date:',
    align: 'left',
    field: (row: CustomerTransactionRow): string => row.created_at,
  }
];
