import axiosFactory, {AxiosError} from 'axios'

import type {AxiosInstance} from 'axios'

import { setupCache } from 'axios-cache-interceptor/dev';

// @ts-ignore
import {stringify} from 'qs'

const instance: AxiosInstance = axiosFactory.create({
    // baseURL: import.meta.env.VITE_API_HOST,
    withCredentials: true,
    paramsSerializer: params => stringify(params, {arrayFormat: 'brackets', skipNulls: true})
})

export const axios = setupCache(instance, {debug:console.log});

axios.interceptors.response.use((resp) => resp, (error) => {
    if(error instanceof AxiosError){
        if(error.response?.status === 401){
            window.location.href = '/login'
            return
        }
    }
    console.log(error)
})
