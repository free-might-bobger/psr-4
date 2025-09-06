

export default [
    {
      path: '/dashboard',
      meta: {
        needAuth: true
      },
      component: () => import('layouts/DashboardLayout.vue'),
      children: [
        {
          path: 'profile',
          children: [
            { path: '', component: () => import('src/pages/dashboard/ProfilePage.vue') },
          ]
        },
        {
          path: 'customer-transactions',
          children: [
            { path: '', component: () => import('src/pages/dashboard/customer-transactions/IndexPage.vue') },
            { path: ':id', component: () => import('src/pages/dashboard/customer-transactions/ShowPage.vue') },
          ]
        },
        {
          path: 'my-stores',
          children: [
            { path: '', component: () => import('src/pages/dashboard/my-stores/IndexPage.vue') },
            { path: ':id', component: () => import('src/pages/dashboard/my-stores/EditPage.vue') },
            { path: ':id/items', component: () => import('src/pages/dashboard/my-stores/items/IndexPage.vue') },
            { path: ':id/items/:itemId', component: () => import('src/pages/dashboard/my-stores/items/EditPage.vue') },
            { path: ':id/items/:itemId/item-prices', component: () => import('src/pages/dashboard/my-stores/items/ItemPrice.vue') }
          ]
        },
        {
          path: 'store-menu-access',
          children: [
            { path: '', component: () => import('src/pages/dashboard/store-menu-access/IndexPage.vue') },
            { path: ':id', component: () => import('src/pages/dashboard/store-menu-access/ShowPage.vue') }, 
            { path: ':storeId/create/:userId', component: () => import('src/pages/dashboard/store-menu-access/CreatePage.vue') }
          ]
        }
      ]
    }
  ]