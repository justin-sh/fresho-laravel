<template>
    <BCard title="Filters" class="mb-2 filters">
        <BForm inline>
            <div class="d-flex flex-row">
                <div class="col-2">
                    <label>Code</label>
                    <BFormInput id="code" size="md" v-model="code"></BFormInput>
                </div>
                <div class="col-8 ms-3">
                    <label for="customer" class="justify-content-start">Product Name</label>
                    <BFormInput id="name" size="md" v-model="name"></BFormInput>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col">
                    <label>Category</label>
                    <div class="d-flex">
                        <BFormCheckboxGroup v-model="cat">
                            <BFormCheckbox value="ANGUS BEEF" switch>ANGUS</BFormCheckbox>
                            <BFormCheckbox value="BEEF" switch>B</BFormCheckbox>
                            <BFormCheckbox value="CHICKEN" switch>CK</BFormCheckbox>
                            <BFormCheckbox value="DUCK" switch>D</BFormCheckbox>
                            <BFormCheckbox value="GAME" switch>GAME</BFormCheckbox>
                            <BFormCheckbox value="GOAT" switch>GOAT</BFormCheckbox>
                            <BFormCheckbox value="HOT POT" switch>HOTPOT</BFormCheckbox>
                            <BFormCheckbox value="LAMB" switch>L</BFormCheckbox>
                            <BFormCheckbox value="PORK" switch>P</BFormCheckbox>
                            <BFormCheckbox value="SEAFOOD" switch>SEAFOOD</BFormCheckbox>
                            <BFormCheckbox value="WAGYU" switch>WAGYU</BFormCheckbox>
                        </BFormCheckboxGroup>
                    </div>
                </div>

            </div>
        </BForm>
    </BCard>

    <BCard class="products">
        <template #header>
            <div class="col align-content-center" ref="tableHeaderRefEl">
                <span class="fw-bold fs-4">Products </span>
                <span class="inline fw-light fs-6" v-if="!data_loading">(Total {{ totalRows }})</span>
            </div>
            <BPagination v-model="currentPage" :total-rows="totalRows" :per-page="page_size" limit="7"
                         @update:model-value="goTableHead"></BPagination>
            <BFormRadioGroup v-model="page_size" :options="page_size_options" class="ms-3 align-content-center"
                             value-field="item" text-field="name"/>
        </template>
        <template #footer>
            <BPagination v-model="currentPage" :total-rows="totalRows" :per-page="page_size" limit="7"
                         @update:model-value="goTableHead"></BPagination>
        </template>

        <BTable id="product-table" striped hover
                :busy="data_loading"
                :items="products"
                :fields="fields">
            <template #cell(rowNo)="row">
                {{ row.index + 1 }}
            </template>
            <!--            <template #cell(orderNo)="row">-->
            <!--                <a :href="'https://app.fresho.com/supplier/orders/' + row.item.id" target="_blank">-->
            <!--                    {{ row.value }}-->
            <!--                </a>-->
            <!--            </template>-->
<!--            <template #cell(show_details)="row">-->
<!--                <BButton size="sm" @click="row.toggleDetails" class="mr-2" variant="light">-->
<!--                    {{ row.detailsShowing ? 'Hide' : 'Show' }} Details-->
<!--                </BButton>-->
<!--            </template>-->
<!--            <template #row-details="row">-->
<!--                <BCard>-->
<!--                    <div class="row" v-for="p in row.item.products" :key="p.name">-->
<!--                        <div class="col-2">{{ p.group }}</div>-->
<!--                        <div class="col">{{ p.name }}</div>-->
<!--                        <div class="col-2">{{ p.qty }} {{ p.qtyType }}</div>-->
<!--                        <div class="col-1">{{ p.status }}</div>-->
<!--                    </div>-->
<!--                    <div v-if="!row.item.products">No Products</div>-->
<!--                </BCard>-->
<!--            </template>-->
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
const code = shallowRef('')
const product = ref([])
const cat = shallowRef()

const products = ref([{
    "cat":"BEEF",
    "code":"2166",
    "name":"Beef Chuck Tender Diced ",
    "qty_type":"KG",
}])
let products_backup = [{
    "cat":"BEEF",
    "code":"2166",
    "name":"Beef Chuck Tender Diced ",
    "qty_type":"KG",
}]

const fields_base = [
    {key: 'rowNo', label: '#'},
    {key: 'code', label: 'Code', sortable: true},
    {key: 'name', label: 'Name', sortable: true},
    {key: 'qty_type', label: 'Qyt Type'},
    {key: 'cat', label: 'Category', sortable: true},
]
const fields = shallowRef([])

const data_loading = shallowRef(false)

const currentPage = shallowRef(1)
const totalRows = shallowRef(1)
const page_size = shallowRef(50)
const page_size_options = [
    {item: 50, name: '50'},
    {item: 100, name: '100'},
    {item: 999, name: 'all'}
]

let abortController: AbortController | null = null;

const loading_data = async (page=1) => {

    data_loading.value = true
    if (abortController != null) {
        abortController.abort()
    }

    try {
        abortController = new AbortController()

        const data = (await getProductsWithFilters(
            {code:code.value, name: name.value, cat: cat.value, page, page_size:page_size.value},
            {signal: abortController.signal}
        )).data

        fields.value = [...fields_base]
        totalRows.value = data.meta.total
        products.value = data.data

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

    fields.value = [...fields_base]

    await loading_data(1);
})

watch([code, name, cat, page_size],
    async () => {
        await loading_data()
    })

const tableHeaderRefEl = ref<HTMLElement | null>(null)

const goTableHead = async (page: number) => {
    console.log(page)

    tableHeaderRefEl.value?.scrollIntoView({behavior: 'smooth'})

    await loading_data(page)
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
