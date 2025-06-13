<template>
    <BOverlay :show="data_loading" rounded="sm">
    <BCard class="mb-2 filters" v-if="order.id">
        <template #header>
            <div class="col align-content-center" ref="tableHeaderRefEl">
                <span class="fs-4">Order </span>
                <span class="inline fw-bold fs-4">{{ 'F' + route.params.id }}</span>
                <span class="fs-4"> For </span>
                <span class="inline fw-bold fs-4">{{ order.customer }}</span>
            </div>
        </template>

        <template #footer>
            <div class="d-flex clear">
                <div class="col align-content-center">
                    <BButton variant="outline-primary" size="sm" :loading="data_loading" @click.stop="loading_data">
                        S1: Search
                    </BButton>
                </div>
            </div>
        </template>

        <div class="row">
            <div class="col">
                <span>Delivery Method:</span><span></span>
            </div>
            <div class="col">
                <label for="datepicker">Preferred Delivery Date:</label>
                <div>
                    <BFormInput type="date" id="datepicker" class="col-4 d-inline" v-model="order.deliveryDate"
                                :date-format-options="{ year: 'numeric', month: 'short', day: '2-digit', weekday: 'short' }">
                    </BFormInput>
                    <BButton variant="success" size="sm" @click="setToday()" class="ms-2">Today</BButton>
                </div>
            </div>
            <div class="col">
                <label for="datepicker">Boxes:</label>
                <span><BFormInput type="number" class="col-4 d-inline" size="20"/></span>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <span>Picking Instructions:</span><span></span>
            </div>
            <div class="col">
                <label for="datepicker">Preferred Delivery Date:</label>
                <div>
                    <BFormInput type="date" id="datepicker" class="col-4 d-inline" v-model="order.deliveryDate"
                                :date-format-options="{ year: 'numeric', month: 'short', day: '2-digit', weekday: 'short' }">
                    </BFormInput>
                    <BButton variant="success" size="sm" @click="setToday()" class="ms-2">Today</BButton>
                </div>
            </div>
            <div class="col">
                <label for="datepicker">Boxes:</label>
                <span><BFormInput type="number" class="col-4 d-inline" size="20"/></span>
            </div>
        </div>
    </BCard>
</BOverlay>
</template>

<script lang="ts" setup>
import {ref, shallowRef, watch, onMounted} from "vue";
import {CanceledError} from "axios";
import {searchFreshoOrdersWithFilters, initOrders, syncOrderDeliveryProofs, syncOrderDetailByOrderNo} from '../api'

import {format, formatInTimeZone, toDate} from "date-fns-tz";
import {onBeforeRouteLeave, useRouter, useRoute} from "vue-router";

const router = useRouter()
const route = useRoute()

const localTZ = Intl.DateTimeFormat().resolvedOptions().timeZone

const deliveryDate = shallowRef(formatInTimeZone(new Date(), localTZ, "yyyy-MM-dd"))
const customer = shallowRef('')
const product = shallowRef('')
const status = shallowRef(['submitted', 'accepted', 'invoiced'])
const credit = shallowRef('no')
const order_run = ['EDN', 'EDS', 'EE', 'RM1', 'CT', 'S', 'N', 'LE', 'W', 'RM2', 'TTP', 'PU', 'CA', 'EA', '~NR']
const runs = shallowRef([])

const order = ref({'id':''})
let orders_backup = []

const fields = [
    {key: 'orderNo', label: 'Order#', sortable: true},
    {key: 'delivery_date_md', label: 'Date', sortable: true},
    {key: 'customer', label: 'Customer', sortable: true},
    {key: 'state', label: 'State', sortable: true},
    {key: 'show_details', label: 'Action'},
]

const detail_syncing = shallowRef(false)
const current_order_no = shallowRef('')
const syncing_del_proof = shallowRef(false)
const data_loading = shallowRef(false)

const currentPage = shallowRef(1)
const page_size = shallowRef(30)
const page_size_options = [
    {item: 30, name: '30'},
    {item: 50, name: '50'},
    {item: 999, name: 'all'}
]
const sortBy = ref([{key: 'delivery_date_md', order: 'desc'}, {key: 'customer', order: 'asc'}])


let abortController: AbortController | null = null;

const loading_data = async () => {

    // data_loading.value = true
    // orders.value = []
    // orders_backup = orders.value
    // if (abortController != null) {
    //     abortController.abort()
    // }

    // try {
    //     abortController = new AbortController()

    //     const data = (await searchFreshoOrdersWithFilters({
    //             delivery_date: deliveryDate.value,
    //             delivery_date2: deliveryDate.value,
    //             customer: customer.value,
    //             product: product.value,
    //             status: status.value,
    //             credit: credit.value,
    //         },
    //         {signal: abortController.signal}
    //     )).data.data


    //     orders.value = data.map(function (x) {
    //         x.detailsShowing = false
    //         x.delivery_date_md = formatInTimeZone(new Date(x.deliveryDate), localTZ, "yyyy-MM-dd")
    //         x.delivery_at_hm = x.at ? formatInTimeZone(new Date(x.at), localTZ, "HH:mm") : ''
    //         return x
    //     })

    //     orders_backup = orders.value

    // } catch (e) {
    //     if (!(e instanceof CanceledError)) {
    //         console.error(e)
    //     }
    // } finally {
    //     abortController = null
    //     data_loading.value = false
    // }
}

const loadDetailForOne = async () => {
    data_loading.value = true
    order.value = (await syncOrderDetailByOrderNo(route.params.id)).data.data
    console.log(order.value)
    data_loading.value = false
}

const setToday = function () {
    deliveryDate.value = formatInTimeZone(new Date(), localTZ, "yyyy-MM-dd")
}


onBeforeRouteLeave((to, before) => {
    // if (to.name == 'dept-report') {
    //     to.meta.orders = orders.value

    //     const weedDay = toDate(deliveryDate.value).getDay()
    //     if ([2, 4].includes(weedDay)) {
    //         to.meta.ordered_run = ['ED', 'EE', 'RM1', 'CT', 'S', 'N', 'LE', 'RM2', 'W', 'PU', 'CA', 'EA', '~NR']
    //     } else {
    //         to.meta.ordered_run = ['ED', 'EE', 'RM1', 'S', 'CT', 'N', 'LE', 'RM2', 'W', 'PU', 'CA', 'EA', '~NR']
    //     }
    // }
})

onMounted(()=>{
    loadDetailForOne()
})

watch([deliveryDate, customer, product, status, credit, runs],
    async ([deliveryDate_new, customer_new, product_new, status_new, credit_new, runs_new],
           [deliveryDate_old, customer_old, product_old, status_old, credit_old, runs_old]) => {

        // console.log(`deliveryDate ${deliveryDate_old}=>${deliveryDate_new}`)

        runs_old = runs_old || []
        if (runs_new.toString() !== runs_old.toString()) {
            const _s = new Date().getTime()
            let x = runs_new.length === 0 ? orders_backup : orders_backup.filter((o) => runs.value.includes(o.run))
            // console.log("filter data in js:" + (new Date().getTime() - _s))
            orders.value = x
            // setTimeout(() => {
            //     console.log("update page:" + (new Date().getTime() - _s))
            // }, 0);
        } else {
            // console.log('loading data')
            await loading_data()
        }
    }, {immediate: true})


const printLabel = async function (row){
    // console.log(row)

    const f = document.forms[row.id];
    f.querySelector('input[name="_token"]').value = getCsrfToken();
    const prds = [];
    row.products.forEach(p=>{

        if(!['backorder','n/a'].includes(p.status)) {
            var x = toDate(row.deliveryDate);
            if (p.group?.includes('Frozen') || p.group?.includes('Hot')) {
                x.setDate(x.getDate() + 365)
            } else {
                x.setDate(x.getDate() + 7)
            }

            prds.push({
                'cus': row.customer,
                'prd': p.name,
                'qty': p.qty + " " + p.qtyType,
                'pd': row.deliveryDate,
                'bbd': format(x, 'yyyy-MM-dd'),
                'orderNo': 'F' + row.orderNo,
                'run': row.run,
            });
        }
    })

    f.querySelector('input[name="data"]').value = JSON.stringify(prds);

    f.submit();
    // await printZt411Label(row)
}

const getCsrfToken = ()=>{
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

</script>
<style scoped>
tbody tr {
    cursor: pointer;
}

#datepicker, #datepicker2 {
    width: 40%;
}

:deep(.card-header) {
    display: flex;
}

.card-header ul {
    margin-bottom: 0;
}

.orders :deep(.card-footer) {
    display: flex;
    justify-content: center;
}

.card-footer ul {
    margin-bottom: 0;
}
</style>
