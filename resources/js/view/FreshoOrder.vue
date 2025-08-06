<template>
    <BCard title="Filters" class="mb-2 filters">
        <BForm inline>
            <div class="row">
                <div class="col-5">
                    <label for="datepicker">Delivery date</label>
                    <div>
                        <BFormInput type="date" id="datepicker" class="col-4 d-inline" v-model="deliveryDate"
                                    :date-format-options="{ year: 'numeric', month: 'short', day: '2-digit', weekday: 'short' }">
                        </BFormInput>
                        <BButton variant="success" size="sm" @click="setToday()" class="ms-2">Today</BButton>
                    </div>
                </div>
                <div class="ml-3 align-content-center col">
                    <label for="customer" class="justify-content-start">Customer</label>
                    <BFormInput id="customer" v-model="customer"></BFormInput>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label>State</label>
                    <div class="d-flex">
                        <BFormRadioGroup v-model="status">
                            <BFormRadio value="in_progress">Process</BFormRadio>
                            <BFormRadio value="submitted">Submitted</BFormRadio>
                            <BFormRadio value="accepted">Accepted</BFormRadio>
                            <BFormRadio value="invoiced">Invoiced</BFormRadio>
                            <BFormRadio value="paid">Paid</BFormRadio>
                            <BFormRadio value="cancelled">Cancelled</BFormRadio>
                            <BFormRadio value="all" switch>All</BFormRadio>
                        </BFormRadioGroup>
                        <!-- <BFormCheckbox v-model="credit" value="yes" uncheckedValue="no" switch>Credit -->
                        <!-- </BFormCheckbox> -->
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label>Run</label>
                    <div class="d-flex">
                        <BFormRadioGroup v-model="runs" class="run-group">
                            <BFormRadio :value="r" :key="r" v-for="r in order_run">{{
                                    (r + "").substring(0, 5)
                                }}
                            </BFormRadio>
                        </BFormRadioGroup>
                    </div>
                </div>
            </div>
        </BForm>

        <template #footer>
            <div class="d-flex clear">
                <div class="col align-content-center">
                    <BButton variant="outline-primary" size="sm" :loading="data_loading"
                             @click.stop="searchFreshoOrder">
                        S1: Search
                    </BButton>
                    <!-- <BButton variant="outline-primary" class="ms-2" size="sm" :loading="detail_syncing"
                             @click.stop="syncDetails">
                        S2: Sync Detail
                    </BButton>
                    <BButton variant="outline-primary" class="ms-2" size="sm" :loading="syncing_del_proof"
                             @click.stop="syncDeliveryProofs">
                        Sync Delivery Proof
                    </BButton>
                    -->
                </div>
                <BFormRadioGroup v-model="page_size" :options="page_size_options" class="ms-3 align-content-center"
                                 value-field="item" text-field="name"/>
            </div>
        </template>
    </BCard>

    <BCard class="orders">
        <template #header>
            <div class="col align-content-center" ref="tableHeaderRefEl">
                <span class="fw-bold fs-4">Orders </span>
                <span class="inline fw-light fs-6" v-if="!data_loading">(Total {{ orders_backup.length }})</span>
            </div>

            <BPagination v-model="currentPage" :total-rows="orders.length" :per-page="page_size" limit="7"
                         @update:model-value="goTableHead" aria-controls="ordertable"></BPagination>
        </template>
        <template #footer>
            <BPagination v-model="currentPage" :total-rows="orders.length" :per-page="page_size" limit="7"
                         @update:model-value="goTableHead" aria-controls="ordertable"></BPagination>
        </template>

        <BTable id="ordertable" striped hover
                :multisort="true"
                :current-page="currentPage"
                :sort-by="sortBy"
                :per-page="page_size"
                :items="orders"
                :fields="fields">
            <template #cell(orderNo)="row">
                <a :href="'https://app.fresho.com/supplier/orders/' + row.item.id" target="_blank">
                    {{ row.value }}
                </a>
            </template>
            <template #cell(show_details)="row">
                <!-- row.toggleDetails -->
                <BButton size="sm" @click="loadDetailForOne(row)" class="mr-2" variant="light" :loading="detail_syncing && current_order_no === row.item.orderNo">
                    {{ row.detailsShowing ? 'Hide' : 'Show' }}
                </BButton>
                <BButton size="sm" @click="printLabel(row.item)" class="mr-2 ms-2" variant="light">
                    Label
                    <form action="/orders/label" method="post" target="_blank" :id="row.item.id">
                        <input type="hidden" name="data" value="">
                        <input type="hidden" name="_token" value="">
                    </form>
                </BButton>
                <BButton size="sm" :href="'/fresho-order/' + row.item.id" class="mr-2 ms-2" variant="light">
                    {{ row.item.isLocked?'View':'Edit' }}
                </BButton>
            </template>
            <template #row-details="row">
                <BCard>
                    <template v-for="p in row.item.product_orders" :key="p.name">
                        <div class="row">
                            <div class="col-2">{{ p.group }}</div>
                            <div class="col">{{ p.name }}</div>
                            <div class="col-2">{{ p.qty }} {{ p.qtyType }}</div>
                            <div class="col-2">{{ p.status }}</div>
                        </div>
                        <div class="row" v-if="p.customer_notes.length>0 || p.supplier_notes.length > 0">
                            <div class="col-2"></div>
                            <div class="col">
                                <span v-if="p.customer_notes" class="fw-bold text-danger">C: {{ p.customer_notes }} &nbsp;</span>
                                <span v-if="p.supplier_notes" class="fw-bold text-success">S: {{ p.supplier_notes }} </span>
                            </div>
                        </div>
                    </template>
                    <div v-if="!row.item.product_orders">No Products</div>
                </BCard>
            </template>
        </BTable>
    </BCard>
</template>

<script lang="ts" setup>
import {ref, shallowRef, watch} from "vue";
import {CanceledError} from "axios";
import {searchFreshoOrdersWithFilters, syncOrderDetailByOrderNo} from '../api'

import {format, formatInTimeZone, toDate} from "date-fns-tz";
import {onBeforeRouteLeave, useRoute, useRouter} from "vue-router";

const localTZ = Intl.DateTimeFormat().resolvedOptions().timeZone

const router = useRouter()
const route = useRoute()

const  defaultDate = route.query.d??formatInTimeZone(new Date(), localTZ, "yyyy-MM-dd")
const  defaultCustomer = route.query.c??''
const  defaultStatus = route.query.s??'accepted'
const  defaultRun = route.query.r??'EDN'

const deliveryDate = shallowRef(defaultDate)
const customer = shallowRef(defaultCustomer)
const status = shallowRef( defaultStatus)
const order_run = ['EDN', 'EDS', 'EE', 'RM1', 'CT', 'S', 'N', 'LE', 'W', 'RM2', 'TTP', 'PU', 'CA', 'EA', '~NR', 'ALL']
const runs = shallowRef(defaultRun)

const orders = shallowRef([])
let orders_backup = []

const fields = [
    {key: 'orderNo', label: 'Order#', sortable: true},
    // {key: 'delivery_date_md', label: 'Date', sortable: true},
    {key: 'customer', label: 'Customer', sortable: true},
    // {key: 'run', label: 'Run', sortable: true},
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
const sortBy = ref([{key: 'customer', order: 'asc'}])


let abortController: AbortController | null = null;

const loading_data = async () => {

    console.log('loading.....')
    data_loading.value = true
    orders.value = []
    orders_backup = orders.value
    if (abortController != null) {
        abortController.abort()
    }

    try {
        abortController = new AbortController()

        const data = (await searchFreshoOrdersWithFilters({
                delivery_date: deliveryDate.value,
                customer: customer.value,
                status: status.value,
                run: runs.value
            },
            {signal: abortController.signal}
        )).data


        orders.value = data.map(function (x) {
            x.detailsShowing = false
            x.product_orders = []
            return x
        })

        orders_backup = orders.value

    } catch (e) {
        if (!(e instanceof CanceledError)) {
            console.error(e)
        }
    } finally {
        abortController = null
        data_loading.value = false
    }
}
const searchFreshoOrder = async () => {
    await loading_data()
}

const loadDetailForOne = async (row) => {
    if(!row.detailsShowing && row.item.product_orders.length == 0){
        detail_syncing.value = true
        current_order_no.value = row.item.orderNo
        const orderWithDetail = (await syncOrderDetailByOrderNo(row.item.id, 'OrderPage')).data.data
        row.item.product_orders = orderWithDetail.product_orders
        row.item.run = orderWithDetail.run
        detail_syncing.value = false
    }
    row.toggleDetails()
}

const goDetail = async(row)=>{

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

watch([deliveryDate, customer, status, runs],
    async () => {
        await router.push({'name': 'freshoOrder', query: {d: deliveryDate.value, c:customer.value,s:status.value,r:runs.value}})
        await searchFreshoOrder()
    }, {immediate: true})

const tableHeaderRefEl = ref<HTMLElement | null>(null)
const goTableHead = (page: number) => {
    // console.log(page)
    tableHeaderRefEl.value?.scrollIntoView({behavior: 'smooth'})
}

const printLabel = async function (row){
    // console.log(row)

    const f = document.forms[row.id];
    f.querySelector('input[name="_token"]').value = getCsrfToken();
    const prds = [];
    row.product_orders.forEach(p=>{

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
