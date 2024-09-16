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
    wh: string[]
}

export interface OptionConfig {
    signal?: GenericAbortSignal
}
