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
                        ~
                        <BFormInput type="date" id="datepicker2" class="col-1 d-inline" v-model="deliveryDate2"
                                    :date-format-options="{ year: 'numeric', month: 'short', day: '2-digit', weekday: 'short' }">
                        </BFormInput>
                        <BButton variant="success" size="sm" @click="setToday()" class="ms-2">Today</BButton>
                    </div>
                </div>
                <div class="ml-3 align-content-center col">
                    <label for="customer" class="justify-content-start">Customer</label>
                    <BFormInput id="customer" v-model="customer"></BFormInput>
                </div>
                <div class="ml-3  col">
                    <label for="product" class="justify-content-start">Product</label>
                    <BFormInput id="product" v-model="product"></BFormInput>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label>State</label>
                    <div class="d-flex">
                        <BFormCheckboxGroup v-model="status">
                            <BFormCheckbox value="in_progress" switch>Process</BFormCheckbox>
                            <BFormCheckbox value="submitted" switch>Submitted</BFormCheckbox>
                            <BFormCheckbox value="accepted" switch>Accepted</BFormCheckbox>
                            <BFormCheckbox value="invoiced" switch>Invoiced</BFormCheckbox>
                            <BFormCheckbox value="paid" switch>Paid</BFormCheckbox>
                            <BFormCheckbox value="cancelled" switch>Cancelled</BFormCheckbox>
                        </BFormCheckboxGroup>
                        <BFormCheckbox v-model="credit" value="yes" uncheckedValue="no" switch>Credit
                        </BFormCheckbox>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label>Run</label>
                    <div class="d-flex">
                        <BFormCheckbox-group v-model="runs" class="run-group">
                            <BFormCheckbox :value="r" :key="r" checked v-for="r in order_run" switch>{{
                                    (r + "").substring(0, 5)
                                }}
                            </BFormCheckbox>
                        </BFormCheckbox-group>
                    </div>
                </div>
            </div>
        </BForm>

        <template #footer>
            <div class="row clear">
                <div class="col">
                    <BButton variant="outline-primary" size="sm" :loading="init_loading"
                             @click.stop="initOrder2Server">
                        S1: re-INIT Order
                    </BButton>
                    <BButton variant="outline-primary" class="ms-2" size="sm" :loading="detail_syncing"
                             @click.stop="syncDetails">
                        S2: Sync Detail
                    </BButton>
                    <BButton variant="outline-primary" class="ms-2" size="sm" @click.stop="goDeptRepot">
                        Dept Report
                    </BButton>
                    <BButton variant="outline-primary" class="ms-2" size="sm" :loading="syncing_del_proof"
                             @click.stop="syncDeliveryProofs">
                        Sync Delivery Proof
                    </BButton>
                </div>
            </div>
        </template>
    </BCard>

    <BCard class="orders">
        <template #header>
            <div class="col align-content-center" ref="tableHeaderRefEl">
                <span class="fw-bold fs-4">Orders </span>
                <span class="inline fw-light fs-6" v-if="!data_loading">(Total {{ orders.length }})</span>
            </div>

            <BFormRadioGroup v-model="page_size" :options="page_size_options" class="ms-3 align-content-center"
                             value-field="item" text-field="name"/>
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
                <BButton size="sm" @click="row.toggleDetails" class="mr-2" variant="light">
                    {{ row.detailsShowing ? 'Hide' : 'Show' }}
                </BButton>
                <BButton size="sm" @click="printLabel(row.item)" class="mr-2 ms-2" variant="light">
                    Label
                    <form action="/orders/label" method="post" target="_blank" :id="row.item.id">
                        <input type="hidden" name="data" value="">
                        <input type="hidden" name="_token" value="">
                    </form>
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
                    <div v-if="!row.item.products">No Products</div>
                </BCard>
            </template>
        </BTable>
    </BCard>
</template>

<script lang="ts" setup>
import {ref, shallowRef, watch} from "vue";
import {CanceledError} from "axios";
import {getOrdersWithFilters, initOrders, syncOrderDeliveryProofs, syncOrderDetails} from '../api'

import {format, formatInTimeZone, toDate} from "date-fns-tz";
import {onBeforeRouteLeave, useRouter} from "vue-router";

const router = useRouter()

const localTZ = Intl.DateTimeFormat().resolvedOptions().timeZone

const deliveryDate = shallowRef(formatInTimeZone(new Date(), localTZ, "yyyy-MM-dd"))
const deliveryDate2 = shallowRef(formatInTimeZone(new Date(), localTZ, "yyyy-MM-dd"))
const customer = shallowRef('')
const product = shallowRef('')
const status = shallowRef(['submitted', 'accepted', 'invoiced', 'paid'])
const credit = shallowRef('no')
const order_run = ['EDN', 'EDS', 'EE', 'RM1', 'CT', 'S', 'N', 'LE', 'W', 'RM2', 'TTP', 'PU', 'CA', 'EA', '~NR']
const runs = shallowRef([])

const orders = shallowRef([])
let orders_backup = []

const fields = [
    {key: 'orderNo', label: 'Order#', sortable: true},
    {key: 'delivery_date_md', label: 'Date', sortable: true},
    {key: 'customer', label: 'Customer', sortable: true},
    {key: 'run', label: 'Run', sortable: true},
    {key: 'state', label: 'State', sortable: true},
    {key: 'by', label: 'By', sortable: true},
    {key: 'delivery_at_hm', label: 'At', sortable: true},
    {key: 'proof', label: 'Proof', sortable: true},
    {key: 'show_details', label: 'Action'},
]

const init_loading = shallowRef(false)
const detail_syncing = shallowRef(false)
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

    data_loading.value = true
    orders.value = []
    orders_backup = orders.value
    if (abortController != null) {
        abortController.abort()
    }

    try {
        abortController = new AbortController()

        const data = (await getOrdersWithFilters({
                delivery_date: deliveryDate.value,
                delivery_date2: deliveryDate2.value,
                customer: customer.value,
                product: product.value,
                status: status.value,
                credit: credit.value,
            },
            {signal: abortController.signal}
        )).data.data


        orders.value = data.map(function (x) {
            x.detailsShowing = false
            x.delivery_date_md = formatInTimeZone(new Date(x.deliveryDate), localTZ, "yyyy-MM-dd")
            x.delivery_at_hm = x.at ? formatInTimeZone(new Date(x.at), localTZ, "HH:mm") : ''
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
const initOrder2Server = async () => {
    init_loading.value = true
    await initOrders(deliveryDate.value)
    init_loading.value = false
    await loading_data()
}
const syncDetails = async () => {
    detail_syncing.value = true
    await syncOrderDetails(deliveryDate.value)
    detail_syncing.value = false
    await loading_data()
}
const syncDeliveryProofs = async () => {
    syncing_del_proof.value = true
    await syncOrderDeliveryProofs()
    syncing_del_proof.value = false
    await loading_data()
}

const setToday = function () {
    deliveryDate.value = formatInTimeZone(new Date(), localTZ, "yyyy-MM-dd")
    deliveryDate2.value = formatInTimeZone(new Date(), localTZ, "yyyy-MM-dd")
}

const goDeptRepot = () => {
    router.push({name: 'dept-report'})
}

onBeforeRouteLeave((to, before) => {
    if (to.name == 'dept-report') {
        to.meta.orders = orders.value

        const weedDay = toDate(deliveryDate.value).getDay()
        if ([2, 4].includes(weedDay)) {
            to.meta.ordered_run = ['ED', 'EE', 'RM1', 'CT', 'S', 'N', 'LE', 'RM2', 'W', 'PU', 'CA', 'EA', '~NR']
        } else {
            to.meta.ordered_run = ['ED', 'EE', 'RM1', 'S', 'CT', 'N', 'LE', 'RM2', 'W', 'PU', 'CA', 'EA', '~NR']
        }
    }
})

watch([deliveryDate, deliveryDate2, customer, product, status, credit, runs],
    async ([deliveryDate_new, deliveryDate2_new, customer_new, product_new, status_new, credit_new, runs_new],
           [deliveryDate_old, deliveryDate2_old, customer_old, product_old, status_old, credit_old, runs_old]) => {

        // console.log(`deliveryDate ${deliveryDate_old}=>${deliveryDate_new}`)
        // console.log(`deliveryDate2 ${deliveryDate2_old}=>${deliveryDate2_new}`)

        if (deliveryDate_old && deliveryDate_old != deliveryDate_new && (deliveryDate?.value > deliveryDate2?.value)) {
            deliveryDate2.value = deliveryDate.value
        } else if (deliveryDate2_old && deliveryDate2_old != deliveryDate2_new && (deliveryDate2_new < deliveryDate_new)) {
            deliveryDate.value = deliveryDate2.value
        }
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
