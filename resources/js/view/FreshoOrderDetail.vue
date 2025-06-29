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
                    <span class="bg-success badge">{{ order.state }}</span>
                </div>
            </template>

            <template #footer>
                <div class="d-flex clear justify-content-end">
                    <div>
                        <BButton variant="outline-primary" class="mx-2" size="sm" :loading="data_loading"
                                 @click.stop="saveNClose">
                            Save & Close
                        </BButton>
                        <BButton variant="outline-primary" class="mx-2" size="sm" :loading="data_loading"
                                 @click.stop="saveNClose">
                            Save & Print Picking Slip
                        </BButton>
                        <BButton variant="outline-primary" class="mx-2" size="sm" :loading="data_loading"
                                 @click.stop="saveNClose">
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
                    <th colspan="2" class="w-50">Product</th>
                    <th>Status</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Price (Ex TAX)</th>
                    <th>Total</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="p in order.product_orders">
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
                    <td>
                        <template v-if="(order.products[p.product_id]['product_item_ids'].length??0) > 1">

                            <select :id="'qtyType-' + p.id " v-model="p.qtyTypeId" @change="qtyTypeChanged(p, $event)">
                                <option :value="order.product_items[piid]['quantity_type_id']" :data-piid="piid"
                                        v-for="piid in order.products[p.product_id]['product_item_ids']">
                                    {{ order.quantity_types[order.product_items[piid]['quantity_type_id']].name }}
                                </option>
                            </select>

                        </template>
                        <template v-else>
                            {{ p.qtyType }}
                        </template>
                    </td>
                    <td>$
                        <input type="number" v-model="p.price" class="d-inline text-end pe-0" style="width: 75px;"/>
                    </td>
                    <td class="text-end pe-0">${{ parseFloat(bigDecimal.multiply(p.qty, p.price)).toFixed(2) }}</td>
                </tr>

                <tr>
                    <td colspan="2" class="position-relative">
                        <div>
                            <div class="position-absolute d-inline mt-1 ps-1 text-body-tertiary">
                                <font-awesome-icon icon="fa-solid fa-magnifying-glass" />
                            </div>
                            <input type="text" v-model="s" name="search" @keyup="searchProducts"
                                   class="w-100 rounded ps-4 border-dark-subtle"
                                   placeholder="Start typing to find a product">
                        </div>
                        <div class="list-group prd-list position-absolute w-100 pe-1" v-if="prdRv.length > 0">
                            <div href="#" class="list-group-item list-group-item-action" v-for="prd in prdRv" key="prd.id">
                                {{ prd.name }}
                            </div>
                        </div>
                    </td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </BCard>
    </BOverlay>
</template>

<script lang="ts" setup>
import {onMounted, ref, shallowRef} from "vue";
import bigDecimal from "js-big-decimal";
import {searchProductsByKey, syncOrderDetailByOrderNo} from '../api'

import {format, formatInTimeZone, toDate} from "date-fns-tz";
import {onBeforeRouteLeave, useRoute, useRouter} from "vue-router";
import {values} from "pusher-js/types/src/core/utils/collections";

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
const s = ref('');
const prdRv = ref([])

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

const saveNClose = async () => {
    console.log(order.value)
}

const qtyTypeChanged = function (p, evt) {
    const prdId = p.product_id
    const qtyTypeId = p.qtyTypeId
    const piid = evt.srcElement.selectedOptions[0].dataset['piid']
    const pitem = order.value.product_items[piid]

    p.price = order.value.prices[pitem['price_id']]['price'] / 100
    p.group = pitem['product_group']
    console.log(p)
    console.log(p.qtyTypeId)
    // console.log(`prdId    =${prdId}`)
    // console.log(`qtyTypeId=${qtyTypeId}`)
    // console.log(`pii      d=${piid}`)
    // console.log(`pitem    d=${pitem}`)
}

const loadDetailForOne = async () => {
    data_loading.value = true
    order.value = (await syncOrderDetailByOrderNo(route.params.id, 'OrderDetailPage')).data.data
    // console.log(order.value)
    data_loading.value = false
}

const searchProducts = async () => {
    console.log('----seach products---')
    console.log('-------' + s.value)
    if (s.value.trim().length < 3) {
        prdRv.value = []
        return;
    }
    prdRv.value = (await searchProductsByKey(s.value, order.value.id)).data.search_products;
    console.log(prdRv.value)
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

// watch([deliveryDate, customer, product, status, credit, runs],
//     async ([deliveryDate_new, customer_new, product_new, status_new, credit_new, runs_new],
//            [deliveryDate_old, customer_old, product_old, status_old, credit_old, runs_old]) => {
//
//         // console.log(`deliveryDate ${deliveryDate_old}=>${deliveryDate_new}`)
//
//         runs_old = runs_old || []
//         if (runs_new.toString() !== runs_old.toString()) {
//             const _s = new Date().getTime()
//             let x = runs_new.length === 0 ? orders_backup : orders_backup.filter((o) => runs.value.includes(o.run))
//             // console.log("filter data in js:" + (new Date().getTime() - _s))
//             orders.value = x
//             // setTimeout(() => {
//             //     console.log("update page:" + (new Date().getTime() - _s))
//             // }, 0);
//         } else {
//             // console.log('loading data')
//             // await loading_data()
//         }
//     }, {immediate: true})


const printLabel = async function (row) {
    // console.log(row)

    const f = document.forms[row.id];
    f.querySelector('input[name="_token"]').value = getCsrfToken();
    const prds = [];
    row.product_orders.forEach(p => {

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

.prd-list {
    padding-right: 0.9rem;
    margin-top: -0.1rem;
    height: 140px;
    overflow-y: scroll;
}

.list-group-item:hover{
    //color: lightgreen;
    //color: #009A44;
    font-weight: bold;
    background-color: rgb(239, 239, 239);
}
</style>
