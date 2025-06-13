import {createRouter, createWebHistory} from "vue-router";

import Home from "@/view/Home.vue";
import Order from "@/view/Order.vue";
import FreshoOrder from "@/view/FreshoOrder.vue";
import FreshoOrderDetail from "@/view/FreshoOrderDetail.vue";
import Product from "@/view/Product.vue";
import PurchaseOrderList from "@/view/PurchaseOrderList.vue";
import PurchaseOrder from "@/view/PurchaseOrder.vue";
import SaleOrderList from "@/view/SaleOrderList.vue";
import SaleOrder from "@/view/SaleOrder.vue";
import DeptReport from "@/view/DeptReport.vue";
import DailyMorningReport from "@/view/DailyMorningReport.vue";
import DriverTrainingS011CRM from "@/view/contract/DriverTrainingS011CRM.vue";
import DriverTrainings from "@/view/contract/index.vue"

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'home',
            component: Order
        },
        {
            path: '/orders',
            name: 'orders',
            component: Order
        },
        {
            path: '/fresho-order',
            name: 'freshoOrder',
            component: FreshoOrder
        },
        {
            path: '/fresho-order/:id',
            name: 'freshoOrderDetail',
            component: FreshoOrderDetail
        },
        {
            path: '/report/dept',
            name: 'deptReport',
            component: DeptReport
        },
        {
            path: '/report/daily-morning',
            name: 'dailyMorningReport',
            component: DailyMorningReport
        },
        {
            path: '/sale-orders',
            name: 'saleOrders',
            component: SaleOrderList
        },
        {
            path: '/sale-orders/new',
            name: 'saleOrderNew',
            component: SaleOrder,
        },
        {
            path: '/sale-orders/:id',
            name: 'saleOrder',
            component: SaleOrder,
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
        {
            path: '/driver-training',
            children:[
                {
                    path: '',
                    name: 'driverTrainings',
                    component: DriverTrainings,
                },
                {
                    path: 's011crm',
                    name: 'driverTrainings.s011crm',
                    component: DriverTrainingS011CRM,
                }
            ]
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
