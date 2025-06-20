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
                <div>
                    <span class="bg-success text-white p-1 rounded">{{ order.state }}</span>
                </div>
            </template>

            <template #footer>
                <div class="d-flex clear justify-content-end">
                    <div>
                        <BButton variant="outline-primary" class="mx-2" size="sm" :loading="data_loading" @click.stop="loading_data">
                            Save & Close
                        </BButton>
                        <BButton variant="outline-primary" class="mx-2" size="sm" :loading="data_loading" @click.stop="loading_data">
                            Save & Print Picking Slip
                        </BButton>
                        <BButton variant="outline-primary" class="mx-2" size="sm" :loading="data_loading" @click.stop="loading_data">
                            Invoice
                        </BButton>
                    </div>
                </div>
            </template>

            <div class="row">
                <div class="col-4">
                    <label for="datepicker">Delivery Method:</label>
                    <BFormInput v-model="order.deliveryMethod" disabled class="w-50"/>
                </div>
                <div class="col-4">
                    <label for="datepicker">Preferred Delivery Date:</label>
                    <div>
                        <BFormInput type="date" id="datepicker" class="col-4 d-inline" v-model="order.deliveryDate"
                                    :date-format-options="{ year: 'numeric', month: 'short', day: '2-digit', weekday: 'short' }">
                        </BFormInput>
                    </div>
                </div>
                <div class="col-2">
                    <label for="datepicker">Boxes:</label>
                    <BFormInput type="number" class="w-50" v-model="order.numberOfBoxes"/>
                </div>
                <div class="col-2">
                    <label for="datepicker">Delivery Run:</label>
                    <BFormInput v-model="order.run" disabled class="w-50"/>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col">
                    <label for="deliveryInstructions">Delivery Instructions:</label>
                    <div>
                        <BFormTextarea id="deliveryInstructions" class="col-4 d-inline" disabled
                                       v-model="order.deliveryInstructions">
                        </BFormTextarea>
                    </div>
                </div>
                <div class="col">
                    <label for="pickingInstructions">Picking Instructions:</label>
                    <div>
                        <BFormTextarea id="pickingInstructions" class="col-4 d-inline" disabled
                                       v-model="order.pickingInstructions">
                        </BFormTextarea>
                    </div>
                </div>
                <div class="col">
                    <label for="additionalNotes">Additional Notes:</label>
                    <div>
                        <BFormTextarea id="additionalNotes" class="col-4 d-inline" v-model="order.additionalNotes">
                        </BFormTextarea>
                    </div>
                </div>
            </div>

            <table class="table table-bordered mt-4">
                <thead class="table-secondary">
                <tr>
                    <th colspan="2">Product</th>
                    <th>Status</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Price (Ex TAX)</th>
                    <th>Total</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="p in order.products">
                    <td style="border-right: none;">
                        {{ p.name }}
                    </td>
                    <td style="border-left: none;">{{ p.group }}</td>
                    <td>
                        <select v-model="p.status">
                            <option value="to_pick">To Pick</option>
                            <option value="supplied">Supplied</option>
                            <option value="n/a">Not available</option>
                            <option value="backorder">Back order</option>
                            <option value="partially_picked">Partially picked</option>
                            <option value="substituted">Substituted</option>
                        </select>
                    </td>
                    <td>
                        <input type="number" v-model="p.qty" class="text-end pe-0" style="width: 75px;"/>
                    </td>
                    <td>{{ p.qtyType }}</td>
                    <td>$
                        <input type="number" v-model="p.price" class="d-inline text-end pe-0" style="width: 75px;"/>
                    </td>
                    <td class="text-end pe-0">${{ parseFloat(bigDecimal.multiply(p.qty, p.price)).toFixed(2) }}</td>
                </tr>

                <tr>
                    <td colspan="2">
                        <input type="text" name="search" class="w-100 rounded p-1 border-dark-subtle" placeholder="Start typing to find a product">
                    </td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </BCard>
    </BOverlay>
</template>

<script lang="ts" setup>
import {onMounted, ref, shallowRef, watch} from "vue";
import bigDecimal from "js-big-decimal";
import {syncOrderDetailByOrderNo} from '../api'

import {format, formatInTimeZone, toDate} from "date-fns-tz";
import {onBeforeRouteLeave, useRoute, useRouter} from "vue-router";

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

const order = ref({'id': ''})
let orders_backup = []

const fields = [
    {key: 'orderNo', label: 'Order#', sortable: true},
    {key: 'delivery_date_md', label: 'Date', sortable: true},
    {key: 'customer', label: 'Customer', sortable: true},
    {key: 'state', label: 'State', sortable: true},
    {key: 'show_details', label: 'Action'},
]

const data_loading = shallowRef(false)


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
    order.value = (await syncOrderDetailByOrderNo(route.params.id, 'OrderDetailPage')).data.data
    console.log(order.value)
    data_loading.value = false
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

onMounted(() => {
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


const printLabel = async function (row) {
    // console.log(row)

    const f = document.forms[row.id];
    f.querySelector('input[name="_token"]').value = getCsrfToken();
    const prds = [];
    row.products.forEach(p => {

        if (!['backorder', 'n/a'].includes(p.status)) {
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

const getCsrfToken = () => {
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
