import {defineStore} from "pinia";

const key = 'hoc.warehouse'

export const useWarehouseStore = defineStore('warehouse', () => {
    function set(value?: any) {
        localStorage.setItem(key, JSON.stringify(value))
    }

    function get() {
        return JSON.parse(localStorage.getItem(key))
    }

    return {get, set}
})
