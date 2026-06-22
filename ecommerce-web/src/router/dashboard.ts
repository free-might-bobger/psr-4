

export default [
    {
      path: '/dashboard',
      meta: {
        requiresAuth: true
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
          path: 'users',
          children: [
            { path: '', component: () => import('src/pages/dashboard/users/IndexPage.vue') },
            { path: ':id', component: () => import('src/pages/dashboard/users/EditPage.vue') },
          ]
        },
        {
          path: 'deliveries',
          children: [
            { path: '', component: () => import('src/pages/dashboard/deliveries/IndexPage.vue') },
            { path: ':id', component: () => import('src/pages/dashboard/deliveries/ShowPage.vue') },
          ]
        },
        {
          path: 'my-transactions',
          children: [
            { path: '', component: () => import('src/pages/dashboard/my-transactions/IndexPage.vue') },
            { path: ':id', component: () => import('src/pages/dashboard/my-transactions/ShowPage.vue') },
          ]
        },
        
        {
          path: 'store-menu-access',
          children: [
            { path: '', component: () => import('src/pages/dashboard/store-menu-access/IndexPage.vue') },
            { path: ':id', component: () => import('src/pages/dashboard/store-menu-access/ShowPage.vue') }, 
            { path: ':storeId/create/:userId', component: () => import('src/pages/dashboard/store-menu-access/CreatePage.vue') }
          ]
        },
        {
          path: 'my-transactions',
          children: [
            { path: '', component: () => import('src/pages/dashboard/my-transactions/IndexPage.vue') },
            { path: ':id', component: () => import('src/pages/dashboard/my-transactions/ShowPage.vue') },
          ]
        },
        {
          path: 'all-transactions',
          children: [
            { path: '', component: () => import('src/pages/dashboard/all-transactions/IndexPage.vue') },
            { path: ':id', component: () => import('src/pages/dashboard/all-transactions/ShowPage.vue') },
          ]
        },
        {
          path: 'my-stores',
          children: [
            { path: '', component: () => import('src/pages/dashboard/my-stores/IndexPage.vue') },
            { path: ':id', component: () => import('src/pages/dashboard/my-stores/EditPage.vue') },
            { path: ':id/store-users', component: () => import('src/pages/dashboard/my-stores/store-users/IndexPage.vue') },
            { path: ':id/store-users/invite-users', component: () => import('src/pages/dashboard/my-stores/store-users/InvitePage.vue') },
            { path: ':id/store-users/menus', component: () => import('src/pages/dashboard/my-stores/store-users/MenusPage.vue') },
            { path: ':id/items', component: () => import('src/pages/dashboard/my-stores/items/IndexPage.vue') },
            { path: ':id/items/:itemId', component: () => import('src/pages/dashboard/my-stores/items/EditPage.vue') },
            { path: ':id/items/:itemId/item-prices', component: () => import('src/pages/dashboard/my-stores/items/ItemPrice.vue') },
            { path: ':id/transactions', component: () => import('src/pages/dashboard/my-stores/transactions/IndexPage.vue') },
            { path: ':id/transactions/:transactionId', component: () => import('src/pages/dashboard/my-stores/transactions/ShowPage.vue') },
          ]
        },
        {
          path: 'shared-store-access',
          children: [
            { path: '', component: () => import('src/pages/dashboard/shared-store-access/IndexPage.vue') },
            { path: ':id', component: () => import('src/pages/dashboard/shared-store-access/EditPage.vue') },
            { path: ':id/users', component: () => import('src/pages/dashboard/shared-store-access/users/IndexPage.vue') },
            { path: ':id/users/invite-users', component: () => import('src/pages/dashboard/shared-store-access/users/InvitePage.vue') },
            { path: ':id/items', component: () => import('src/pages/dashboard/shared-store-access/items/IndexPage.vue') },
            { path: ':id/items/:itemId', component: () => import('src/pages/dashboard/shared-store-access/items/EditPage.vue') },
            { path: ':id/items/:itemId/item-prices', component: () => import('src/pages/dashboard/shared-store-access/items/ItemPrice.vue') },
            { path: ':id/transactions', component: () => import('src/pages/dashboard/shared-store-access/transactions/IndexPage.vue') },
            { path: ':id/transactions/:transactionId', component: () => import('src/pages/dashboard/shared-store-access/transactions/ShowPage.vue') },
          ]
        },
        {
          path: 'all-stores',
          children: [
            { path: '', component: () => import('src/pages/dashboard/all-stores/IndexPage.vue') },
            { path: ':id', component: () => import('src/pages/dashboard/all-stores/EditPage.vue') },
            { path: ':id/items', component: () => import('src/pages/dashboard/all-stores/items/IndexPage.vue') },
            { path: ':id/items/:itemId', component: () => import('src/pages/dashboard/all-stores/items/EditPage.vue') },
            { path: ':id/items/:itemId/item-prices', component: () => import('src/pages/dashboard/all-stores/items/PricePage.vue') },
          ]
        }
      ]
    }
  ]