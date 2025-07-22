<template>
    <BCard title="Filters" class="mb-2 filters">
        <BForm inline>
            <div class="row">
                <div class="col">
                    <label for="datepicker">Delivery date</label>
                    <div>
                        <BFormInput type="date" id="datepicker" class="col-4 d-inline" v-model="deliveryDate"
                                    :date-format-options="{ year: 'numeric', month: 'short', day: '2-digit', weekday: 'short' }">
                        </BFormInput>
                        <BButton variant="success" size="sm" @click="setToday()" class="ms-2">Today</BButton>
                    </div>
                </div>
                <div class="col-2">
                    <label>Code</label>
                    <BFormInput id="code" size="md" v-model="code"></BFormInput>
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
                </div>
            </div>
        </template>
    </BCard>

    <BCard class="orders">
        <table class="table">
            <template v-for="(d, key) in orders">
                <tr class="border" v-for="(x, idx) in d">
                    <td v-if="idx === 0" :rowspan="d.length">
                        {{ key }}
                    </td>
                    <td>
                        {{ x.qty + x.qty_type }}
                    </td>
                    <td>
                        {{ x.customer_notes ? 'C:' + x.customer_notes : '' }}
                        {{ x.supplier_notes ? 'S:' + x.supplier_notes : '' }}
                    </td>
                </tr>
            </template>
        </table>
    </BCard>
</template>

<script lang="ts" setup>
import {ref, shallowRef, watch} from "vue";
import {inventory} from '../api'

import {formatInTimeZone} from "date-fns-tz";
import {onBeforeRouteLeave, useRoute, useRouter} from "vue-router";

const localTZ = Intl.DateTimeFormat().resolvedOptions().timeZone

const router = useRouter()
const route = useRoute()

const defaultDate = route.query.d ?? formatInTimeZone(new Date(), localTZ, "yyyy-MM-dd")
const defaultCustomer = route.query.c ?? ''
const defaultStatus = route.query.s ?? 'invoiced'
const defaultRun = route.query.r ?? 'EDN'

const deliveryDate = shallowRef(defaultDate)
const code = shallowRef('')
const customer = shallowRef(defaultCustomer)
const status = shallowRef(defaultStatus)
const order_run = ['EDN', 'EDS', 'EE', 'RM1', 'CT', 'S', 'N', 'LE', 'W', 'RM2', 'TTP', 'PU', 'CA', 'EA', '~NR', 'ALL']
const runs = shallowRef(defaultRun)

const orders = shallowRef([])

const data_loading = shallowRef(false)


const loading_data = async () => {

    data_loading.value = true
    orders.value = []
    try {
        orders.value = (await inventory(
            deliveryDate.value
            // customer: customer.value,
            // status: status.value,
            // run: runs.value
        )).data.data
        console.log(orders.value)
    } catch (e) {
        console.error(e)
    } finally {
        data_loading.value = false
    }
}
const searchFreshoOrder = async () => {
    await loading_data()
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
        await router.push({
            'name': 'inventory',
            query: {d: deliveryDate.value, c: customer.value, s: status.value, r: runs.value}
        })
        await searchFreshoOrder()
    }, {immediate: true})

const tableHeaderRefEl = ref<HTMLElement | null>(null)
const goTableHead = (page: number) => {
    // console.log(page)
    tableHeaderRefEl.value?.scrollIntoView({behavior: 'smooth'})
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
