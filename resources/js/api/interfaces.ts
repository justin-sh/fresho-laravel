import {type GenericAbortSignal} from "axios";

export interface User {
    id: number
    name: string
    password?: string
}

export interface OrderFilter {
    delivery_date: string
    customer: string
    product: string
    status: string[]
    credit: string
}

export interface ProductFilter {
    name?: string
    cat?: string[]
    wh?: string[]
    hasStock: boolean
}

export interface PoDetail {
    prdId?: string
    qty?: number
    location?: string
    comment?: string
}

export interface PurchaseOrder {
    id?: string
    title?: string
    arrivalAt?: string
    qty?: number
    whId?: string
    status?: string
    details?: Array<PoDetail>
}

export interface OptionConfig {
    signal?: GenericAbortSignal
}
