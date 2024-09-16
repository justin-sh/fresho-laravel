<template>
    <BCard title="Filters" class="mb-2 filters">
        <BForm inline>
            <div class="row">
                <!--                <div class="ml-3  col">-->
                <!--                    <label for="product" class="justify-content-start">Product</label>-->
                <!--                    <BFormInput id="product" v-model="product"></BFormInput>-->
                <!--                </div>-->
                <div class="col">
                    <label>Warehouse</label>
                    <div class="d-flex">
                        <BFormCheckboxGroup v-model="wh">
                            <BFormCheckbox switch :value="w.code" v-for="w in warehouses">
                                {{ w.name }}
                            </BFormCheckbox>
                        </BFormCheckboxGroup>
                    </div>
                </div>
                <div class="ml-3 align-content-center col">
                    <label for="customer" class="justify-content-start">Product Name</label>
                    <BFormInput id="customer" v-model="customer"></BFormInput>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label>Category</label>
                    <div class="d-flex">
                        <BFormCheckboxGroup v-model="status">
                            <BFormCheckbox value="BEEF" switch>BEEF</BFormCheckbox>
                            <BFormCheckbox value="LAMB" switch>LAMB</BFormCheckbox>
                            <BFormCheckbox value="PORK" switch>PORK</BFormCheckbox>
                            <BFormCheckbox value="CHICKEN" switch>CHICKEN</BFormCheckbox>
                            <BFormCheckbox value="DUCK" switch>DUCK</BFormCheckbox>
                            <BFormCheckbox value="OTHERS" switch>OTHERS</BFormCheckbox>
                        </BFormCheckboxGroup>
                    </div>
                </div>
            </div>
        </BForm>

        <!--        <template #footer>-->
        <!--            <div class="row clear">-->
        <!--                <div class="col">-->
        <!--                    <BButton variant="outline-primary" size="sm" :loading="init_loading"-->
        <!--                             @click.stop="initOrder2Server">-->
        <!--                        S1: re-INIT Order-->
        <!--                    </BButton>-->
        <!--                    <BButton variant="outline-primary" class="ms-2" size="sm" :loading="detail_syncing"-->
        <!--                             @click.stop="syncDetails">-->
        <!--                        S2: Sync Detail-->
        <!--                    </BButton>-->
        <!--                    <BButton variant="outline-primary" class="ms-2" size="sm" @click.stop="goDeptRepot">-->
        <!--                        Dept Report-->
        <!--                    </BButton>-->
        <!--                    <BButton variant="outline-primary" class="ms-2" size="sm" :loading="syncing_del_proof"-->
        <!--                             @click.stop="syncDeliveryProofs">-->
        <!--                        Sync Delivery Proof-->
        <!--                    </BButton>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--        </template>-->
    </BCard>

    <BCard class="products">
        <template #header>
            <div class="col align-content-center" ref="tableHeaderRefEl">
                <span class="fw-bold fs-4">Products </span>
                <span class="inline fw-light fs-6" v-if="!data_loading">(Total {{ products_backup.length }})</span>
            </div>

            <BFormRadioGroup v-model="page_size" :options="page_size_options" class="ms-3 align-content-center"
                             value-field="item" text-field="name"/>
        </template>
        <template #footer>
            <BPagination v-model="currentPage" :total-rows="products.length" :per-page="page_size" limit="7"
                         @update:model-value="goTableHead" aria-controls="ordertable"></BPagination>
        </template>

        <BTable id="ordertable" striped hover :current-page="currentPage" :per-page="page_size" :items="products"
                :fields="fields">
            <template #cell(rowNo)="row">
                {{ row.index + 1 }}
            </template>
            <!--            <template #cell(orderNo)="row">-->
            <!--                <a :href="'https://app.fresho.com/supplier/orders/' + row.item.id" target="_blank">-->
            <!--                    {{ row.value }}-->
            <!--                </a>-->
            <!--            </template>-->
            <template #cell(show_details)="row">
                <BButton size="sm" @click="row.toggleDetails" class="mr-2" variant="light">
                    {{ row.detailsShowing ? 'Hide' : 'Show' }} Details
                </BButton>
            </template>
            <template #row-details="row">
                <BCard>
                    <div class="row" v-for="p in row.item.products" :key="p.name">
                        <div class="col-2">{{ p.group }}</div>
                        <div class="col">{{ p.name }}</div>
                        <div class="col-2">{{ p.qty }} {{ p.qtyType }}</div>
                        <div class="col-1">{{ p.status }}</div>
                    </div>
                    <div v-if="!row.item.products">No Products</div>
                </BCard>
            </template>
        </BTable>
    </BCard>
</template>

<script lang="ts" setup>
import {onMounted, ref, shallowRef, watch} from "vue";
import {CanceledError} from "axios";
import {getProductsWithFilters, getWarehousesWithFilters} from '../api'

import {formatInTimeZone, toDate} from "date-fns-tz";
import {onBeforeRouteLeave, useRouter} from "vue-router";

const router = useRouter()

const localTZ = Intl.DateTimeFormat().resolvedOptions().timeZone

const deliveryDate = shallowRef(formatInTimeZone(new Date(), localTZ, "yyyy-MM-dd"))
const customer = shallowRef('')
const product = shallowRef('')
const status = shallowRef(['BEEF', 'LAMB', 'PORK', 'CHICKEN', 'DUCK', 'OTHERS'])
const wh = shallowRef([])
const runs = shallowRef([])

const products = shallowRef([])
let products_backup = []

const fields = [
    {key: 'rowNo', label: '#'},
    {key: 'cat', label: 'Category', sortable: true},
    {key: 'code', label: 'Code', sortable: true},
    {key: 'name', label: 'Name', sortable: true},
    {key: 'comment', label: 'Comment'},
    // {key: 'delivery_at_hm', label: 'At', sortable: true},
    // {key: 'proof', label: 'Proof', sortable: true},
    // {key: 'show_details', label: 'Action'},
]

const data_loading = shallowRef(false)

const currentPage = shallowRef(1)
const page_size = shallowRef(50)
const page_size_options = [
    {item: 30, name: '30'},
    {item: 50, name: '50'},
    {item: 999, name: 'all'}
]

const warehouses = shallowRef([])

let abortController: AbortController | null = null;

const loading_data = async () => {

    data_loading.value = true
    products.value = []
    products_backup = products.value
    if (abortController != null) {
        abortController.abort()
    }

    try {
        abortController = new AbortController()

        const data = (await getProductsWithFilters(
            {signal: abortController.signal}
        )).data.data


        // products.value = data.map(function (x) {
        //     x.detailsShowing = false
        //     x.delivery_date_md = formatInTimeZone(new Date(x.deliveryDate), localTZ, "MM-dd")
        //     x.delivery_at_hm = x.at ? formatInTimeZone(new Date(x.at), localTZ, "HH:mm") : ''
        //     return x
        // })

        products_backup = products.value = data

    } catch (e) {
        if (!(e instanceof CanceledError)) {
            console.error(e)
        }
    } finally {
        abortController = null
        data_loading.value = false
    }
}

onMounted(async () => {
    const data = (await getWarehousesWithFilters()).data.data
    warehouses.value = data

    wh.value = data.map(x=>x.code)
})

// onBeforeRouteLeave((to, before) => {
//     if (to.name == 'dept-report') {
//         to.meta.orders = orders.value
//
//         const weedDay = toDate(deliveryDate.value).getDay()
//         if ([2, 4].includes(weedDay)) {
//             to.meta.ordered_run = ['ED', 'EE', 'RM1', 'CT', 'S', 'N', 'LE', 'RM2', 'W', 'PU', 'CA', 'EA', '~NR']
//         } else {
//             to.meta.ordered_run = ['ED', 'EE', 'RM1', 'S', 'CT', 'N', 'LE', 'RM2', 'W', 'PU', 'CA', 'EA', '~NR']
//         }
//     }
// })

watch([deliveryDate, customer, product, status, runs],
    async ([deliveryDate_new, customer_new, product_new, status_new, runs_new],
           [deliveryDate2, customer2, product2, status2, runs_old]) => {
        runs_old = runs_old || []
        if (runs_new.toString() !== runs_old.toString()) {
            const _s = new Date().getTime()
            let x = runs_new.length === 0 ? products_backup : products_backup.filter((o) => runs.value.includes(o.run))
            console.log("filter data in js:" + (new Date().getTime() - _s))
            products.value = x
            setTimeout(() => {
                console.log("update page:" + (new Date().getTime() - _s))
            }, 0);
        } else {
            console.log('loading data')
            await loading_data()
        }
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

.text-right {
    text-align: right;
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
