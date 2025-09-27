import {axios} from "../axios";
import {
    type OptionConfig,
    type OrderFilter,
    type ReportParams,
    ProductFilter,
    PurchaseOrder,
    SaleOrder,
    type User,
    FreshoOrderFilter
} from "./interfaces";
import {LocationQueryValue} from "vue-router";

export const getUserInfo = () => axios.get<User>('/auth/user-info')
// @ts-ignore
export const uploadOrdersCsv = (f) => axios.postForm('/api/orders/update-details/', {"orderFile": f})
export const getOrdersWithFilters = (params: OrderFilter, options?: OptionConfig) => axios.get('/api/orders', {params, ...options})


export const searchFreshoOrdersWithFilters = (params: FreshoOrderFilter, options?: OptionConfig) => axios.get('/api/fresho-orders', {params, ...options})
export const syncOrderDetailByOrderNo = (order_id: string, src: string) => axios.get('/api/fresho-orders/sync-detail-by-order-no', {params: {'id':order_id, src}})
export const searchProductsByKey = (s: string, order_id: string) => axios.get('/api/fresho-orders/search_products', {params: {s, order_id}})
export const getProductInfoById = (pid: string, order_id: string) => axios.get('/api/fresho-orders/get-product-info', {params: {pid, order_id}})
export const updateOrder = (order_id:string, order_details:object) => axios.post(`/api/fresho-orders/${order_id}`, order_details)
export const printZt411Label = (order_id:string, size:string) => axios.post(`/api/fresho-orders/${order_id}/label/${size}`)
export const printLabelLargeOne = (order_data:object) => axios.post(`/api/fresho-orders/print-label/large`, order_data)
export const getFreshoProductsWithFilters = (params: ProductFilter, options?: OptionConfig) => axios.get('/api/fresho-products', {params, ...options})


export const initOrders = (delivery_date: string) => axios.get('/api/orders/sync-summary', {params: {delivery_date}})
export const syncOrderDetails = (delivery_date: string) => axios.get('/api/orders/sync-detail', {params: {delivery_date}})
export const deleteOrderDetails = (delivery_date: string) => axios.get('/api/orders/delete-details', {params: {delivery_date}})
export const syncOrderDeliveryProofs = () => axios.get('/api/orders/sync-delivery-proof')
export const getAllProducts = () => axios.get('/api/products/all')
export const getWarehousesWithFilters = (options?: OptionConfig) => axios.get('/api/warehouses', {...options})

export const getPoList = () => axios.get('/api/purchase-orders')
export const getPo = (id: string) => axios.get(`/api/purchase-orders/${id}`)
export const savePo = (params: PurchaseOrder) => axios.post('/api/purchase-orders', params)
export const updatePo = (params: PurchaseOrder) => axios.put(`/api/purchase-orders/${params.id}`, params)
export const approvePo = (params: PurchaseOrder) => axios.put(`/api/purchase-orders/${params.id}/approve`)


export const getSoList = () => axios.get('/api/sale-orders')
export const getSo = (id: string) => axios.get(`/api/sale-orders/${id}`)
export const saveSo = (params: SaleOrder) => axios.post('/api/sale-orders', params)
export const updateSo = (params: SaleOrder) => axios.put(`/api/sale-orders/${params.id}`, params)
export const approveSo = (params: SaleOrder) => axios.put(`/api/sale-orders/${params.id}/approve`)


export const deptReport = (params: ReportParams) => axios.post(`/api/report/dept`, params)
export const dailyReport = (reportDate: string) => axios.get(`/api/report/daily/${reportDate}`)


export const inventory = (d: string) => axios.get('/api/inventory', {params:{d}})
