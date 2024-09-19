import {createRouter, createWebHistory} from "vue-router";

import Home from "@/view/Home.vue";
import Order from "@/view/Order.vue";
import Product from "@/view/Product.vue";
import PurchaseOrderList from "@/view/PurchaseOrderList.vue";
import PurchaseOrder from "@/view/PurchaseOrder.vue";

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'home',
            component: Home
        },
        {
            path: '/orders',
            name: 'orders',
            component: Order
        },
        {
            path: '/report/dept',
            name: 'deptReport',
            component: Home
        },
        {
            path: '/sales-orders',
            name: 'salesOrders',
            component: Home
        },
        {
            path: '/purchase-orders',
            name: 'purchaseOrders',
            component: PurchaseOrderList,
        },
        {
            path: '/purchase-orders/new',
            name: 'purchaseOrderNew',
            component: PurchaseOrder,
        },
        {
            path: '/purchase-orders/:id',
            name: 'purchaseOrder',
            component: PurchaseOrder,
        },
        {
            path: '/products',
            name: 'products',
            component: Product
        },
        // {
        //     path: '/about',
        //     name: 'about',
        //     // route level code-splitting
        //     // this generates a separate chunk (About.[hash].js) for this route
        //     // which is lazy-loaded when the route is visited.
        //     component: () => import('../views/AboutView.vue')
        // }
    ],
})

export default router
