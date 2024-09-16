import {axios} from "../axios";
import {type OptionConfig, type OrderFilter, type User} from "./interfaces";

export const getUserInfo = () => axios.get<User>('/auth/user-info')
// @ts-ignore
export const uploadOrdersCsv = (f) => axios.postForm('/api/orders/update-details/', {"orderFile": f})
export const getOrdersWithFilters = (params: OrderFilter, options?: OptionConfig) => axios.get('/api/orders', {params, ...options})
export const initOrders = (delivery_date: string) => axios.get('/api/orders/sync-summary', {params: {delivery_date}})
export const syncOrderDetails = (delivery_date: string) => axios.get('/api/orders/sync-detail', {params: {delivery_date}})
export const syncOrderDeliveryProofs = () => axios.get('/api/orders/sync-delivery-proof')
export const getProductsWithFilters = (options?: OptionConfig) => axios.get('/api/products', {...options})
export const getWarehousesWithFilters = (options?: OptionConfig) => axios.get('/api/warehouses', {...options})
