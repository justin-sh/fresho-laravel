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
                        <div class="row note-fz" v-if="p.customer_notes.length>0 || p.supplier_notes.length > 0">
<!--                            <div class="col-2"></div>-->
<!--                            <div class="col">-->
                                <span v-if="p.customer_notes" class="fw-bold text-danger">C: {{ p.customer_notes }} &nbsp;</span>
                                <span v-if="p.supplier_notes" class="fw-bold text-success">S: {{ p.supplier_notes }} <font-awesome-icon icon="fa-solid fa-pencil"/></span>
<!--                            </div>-->
                        </div>
                        <template v-else>
                            <span><font-awesome-icon icon="fa-solid fa-pencil"/></span>
                        </template>
                    </td>
                    <td class="align-middle" style="border-left: none;">{{ p.group }}</td>
                    <td class="align-middle">
                        <select v-model="p.status">
                            <option value="to_pick">To Pick</option>
                            <option value="supplied">Supplied</option>
                            <option value="n/a">Not available</option>
                            <option value="backorder">Back order</option>
                            <option value="partially_picked">Partially picked</option>
                            <option value="substituted">Substituted</option>
                        </select>
                    </td>
                    <td class="align-middle position-relative">
                        <input type="number" v-model="p.qty" placeholder="0" class="text-end pe-0" style="width: 75px;"/>
                        <!-- textarea class="position-absolute top-50 start-0" style="z-index:99;"></textarea-->
                    </td>
                    <td class="align-middle">
                        <template v-if="(order.products[p.product_id]['product_item_ids'].length??0) > 1">

                            <select :id="'qtyType-' + p.id " v-model="p.qtyTypeId" @change="qtyTypeChanged(p, $event)">
                                <option value="" disabled>Select</option>
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
                    <td class="align-middle">$
                        <input type="number" v-model="p.price" class="d-inline text-end pe-0" style="width: 75px;"/>
                    </td>
                    <td class="text-end pe-1 align-middle">${{ parseFloat(bigDecimal.multiply(p.qty, p.price)).toFixed(2) }}</td>
                </tr>

                <tr>
                    <td colspan="2" class="position-relative">
                        <div>
                            <div class="position-absolute d-inline mt-1 ps-1 text-body-tertiary">
                                <font-awesome-icon icon="fa-solid fa-magnifying-glass"/>
                            </div>
                            <input type="text" v-model="s" name="search" @keyup="searchProducts"
                                   class="w-100 rounded ps-4 border-dark-subtle"
                                   placeholder="Start typing to find a product">
                        </div>
                        <div class="list-group prd-list position-absolute w-100 pe-1" v-if="prdRv.length > 0">
                            <div class="list-group-item list-group-item-action" v-for="prd in prdRv" key="prd.id"
                                 @click="getProductById(prd.id)">
                                <span>{{ prd.name }}</span>
                                <span v-if="prd.is_pantry_item" class="float-end"><font-awesome-icon icon="fa-solid fa-star"/></span>
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
import {getProductInfoById, searchProductsByKey, syncOrderDetailByOrderNo, updateOrder} from '../api'

import {format, formatInTimeZone, toDate} from "date-fns-tz";
import {onBeforeRouteLeave, useRoute, useRouter} from "vue-router";
import {v4 as uuidv4} from 'uuid';

const router = useRouter()
const route = useRoute()

const localTZ = Intl.DateTimeFormat().resolvedOptions().timeZone

const deliveryDate = shallowRef(formatInTimeZone(new Date(), localTZ, "yyyy-MM-dd"))
const customer = shallowRef('')
const product = shallowRef('')
const status = shallowRef(['submitted', 'accepted', 'invoiced'])
const credit = shallowRef('no')
const s = ref('');
const prdRv = ref([])

const order = ref({'id': ''})

const data_loading = shallowRef(false)

let abortController: AbortController | null = null;

const saveNClose = async () => {
    console.log(order.value)

    const params = {
        'numberOfBoxes':order.value.numberOfBoxes,
        'deliveryDate':order.value.deliveryDate,
        'additionalNotes':order.value.additionalNotes,
        'details':order.value.product_orders,
    }

    await updateOrder(order.value.orderNo, params);
}

const qtyTypeChanged = function (p, evt) {
    const prdId = p.product_id
    const qtyTypeId = p.qtyTypeId
    const piid = evt.srcElement.selectedOptions[0].dataset['piid']
    const pitem = order.value.product_items[piid]

    p.price = (order.value.prices[pitem['price_id']]['price'] / 100).toFixed(2)
    p.group = pitem['product_group']
    console.log(p)
    console.log(order.value)
    console.log(p.qtyTypeId)
    // console.log(`prdId    =${prdId}`)
    // console.log(`qtyTypeId=${qtyTypeId}`)
    // console.log(`pii      d=${piid}`)
    // console.log(`pitem    d=${pitem}`)
}

const loadDetailForOne = async () => {
    data_loading.value = true
    order.value = (await syncOrderDetailByOrderNo(route.params.id, 'OrderDetailPage')).data.data
    data_loading.value = false
}

const searchProducts = async () => {
    if (s.value.trim().length < 3) {
        prdRv.value = []
        return;
    }
    prdRv.value = (await searchProductsByKey(s.value, order.value.id)).data.search_products;
    console.log(prdRv.value)
}

const getProductById = async (pid) => {
    s.value = ''
    prdRv.value = []
    const prd = (await getProductInfoById(pid, order.value.id)).data;

    const productsInfo = {'price_ids': [], 'product_item_ids': []}
    prd.prices.forEach(e => {
        const eid = e.id
        productsInfo['price_ids'].push(eid)
        if (!(eid in order.value.prices)) {
            delete e['id']
            order.value.prices[eid] = e
        }
    })

    prd.product_items.forEach(e => {
        const eid = e.id
        productsInfo['product_item_ids'].push(eid)
        if (!(eid in order.value.product_items)) {
            const eid = e.id
            delete e['id']
            order.value.product_items[eid] = e
        }
    })

    productsInfo['name'] = prd.products[0]['name']
    order.value.products[pid] = productsInfo

    prd.quantity_types.forEach(e => {
        if (!(e.id in order.value.quantity_types)) {
            const eid = e.id
            delete e['id']
            order.value.quantity_types[eid] = e
        }
    })

    const curPrd = {
        'id': uuidv4(),
        'name': prd.products[0].name,
        'group': prd.product_items.length == 1 ? prd.product_items[0].product_group : '',
        'customer_notes': '',
        'price': prd.prices.length == 1 ? (prd.prices[0].price / 100).toFixed(2) : 0,
        'product_id': pid,
        'qty': '',
        'qtyType': prd.quantity_types.length == 1 ? prd.quantity_types[0].name : '',
        'qtyTypeId': prd.quantity_types.length == 1 ? prd.quantity_types[0].id : '',
        'status': 'supplied',
        'supplier_notes': ''
    }
    order.value.product_orders.push(curPrd)

}

onBeforeRouteLeave((to, before) => {
})

onMounted(() => {
    loadDetailForOne()
})

// watch([deliveryDate, customer, product, status, runs],
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

.list-group-item:hover {
    font-weight: bold;
    background-color: rgb(239, 239, 239);
}

.note-fz{
    font-size: 0.8rem;
}
</style>
