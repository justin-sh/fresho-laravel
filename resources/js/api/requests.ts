import {axios} from "../axios";
import {type OptionConfig, type OrderFilter, type ReportParams, ProductFilter, PurchaseOrder, SaleOrder, type User} from "./interfaces";

export const getUserInfo = () => axios.get<User>('/auth/user-info')
// @ts-ignore
export const uploadOrdersCsv = (f) => axios.postForm('/api/orders/update-details/', {"orderFile": f})
export const getOrdersWithFilters = (params: OrderFilter, options?: OptionConfig) => axios.get('/api/orders', {params, ...options})
export const searchFreshoOrdersWithFilters = (params: OrderFilter, options?: OptionConfig) => axios.get('/api/fresho-orders', {params, ...options})
export const syncOrderDetailByOrderNo = (order_no: string, src: string) => axios.get('/api/fresho-orders/sync-detail-by-order-no', {params: {order_no, src}})
export const initOrders = (delivery_date: string) => axios.get('/api/orders/sync-summary', {params: {delivery_date}})
export const syncOrderDetails = (delivery_date: string) => axios.get('/api/orders/sync-detail', {params: {delivery_date}})
export const syncOrderDeliveryProofs = () => axios.get('/api/orders/sync-delivery-proof')
export const getProductsWithFilters = (params: ProductFilter, options?: OptionConfig) => axios.get('/api/products', {params, ...options})
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
