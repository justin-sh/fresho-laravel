<template>
    <BCard title="Filters" class="mb-2 filters">
        <BForm inline>
            <div class="d-flex flex-row">
                <div class="col">
                    <label>Warehouse</label>
                    <div class="d-flex">
                        <BFormCheckboxGroup v-model="wh">
                            <BFormCheckbox switch :value="w.id" v-for="w in warehouses">
                                {{ w.name }}
                            </BFormCheckbox>
                        </BFormCheckboxGroup>
                    </div>
                </div>
                <div class="col ms-3">
                    <label for="customer" class="justify-content-start">Product Name</label>
                    <BFormInput id="name" size="md" v-model="name"></BFormInput>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-8">
                    <label>Category</label>
                    <div class="d-flex">
                        <BFormCheckboxGroup v-model="cat">
                            <BFormCheckbox value="BEEF" switch>BEEF</BFormCheckbox>
                            <BFormCheckbox value="LAMB" switch>LAMB</BFormCheckbox>
                            <BFormCheckbox value="PORK" switch>PORK</BFormCheckbox>
                            <BFormCheckbox value="CHICKEN" switch>CHICKEN</BFormCheckbox>
                            <BFormCheckbox value="DUCK" switch>DUCK</BFormCheckbox>
                            <BFormCheckbox value="OTHERS" switch>OTHERS</BFormCheckbox>
                        </BFormCheckboxGroup>
                    </div>
                </div>
                <div class="col-auto">
                    <label>Stock</label>
                    <div class="d-flex">
                        <BFormCheckbox switch v-model="hasStock">
                            Has Stock
                        </BFormCheckbox>
                    </div>
                </div>
            </div>
        </BForm>
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
import {useRouter} from "vue-router";

const router = useRouter()

const name = shallowRef('')
const product = shallowRef('')
const cat = shallowRef()
const wh = shallowRef<string[]>([])
const hasStock = shallowRef(true)

const products = shallowRef([])
let products_backup = []

const fields_base = [
    {key: 'rowNo', label: '#'},
    {key: 'cat', label: 'Category', sortable: true},
    {key: 'name', label: 'Name', sortable: true},
    {key: 'comment', label: 'Comment'},
]
const fields = shallowRef([])

const data_loading = shallowRef(false)

const currentPage = shallowRef(1)
const page_size = shallowRef(50)
const page_size_options = [
    {item: 50, name: '50'},
    {item: 100, name: '100'},
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
            {wh: wh.value, name: name.value, cat: cat.value, hasStock: hasStock.value},
            {signal: abortController.signal}
        )).data.data

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

    fields_base.push(...data.map(function (x) {
        return {key: x.code, sortable: true}
    }))

    fields.value = [...fields_base]

    await loading_data();
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

watch([name, cat, wh, hasStock],
    async ([name_new, status_new, wh_new, hasStock_new],
           [name2, status2, wh_old, hasStock_old]) => {
        // console.log('loading data')
        await loading_data()
        // }
    })

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
