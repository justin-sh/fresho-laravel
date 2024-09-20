<template>

    <BCard>
        <template #header>
            <div class="col align-content-center">
                <span class="fw-bold fs-4">Purchase Orders</span>
                <span class="ms-2">{{ pageTitle }}</span>
            </div>
        </template>
        <template #footer>
            <BRow>
                <BCol class="col-auto pe-0">
                    <BButton variant="outline-primary" class="d-inline" @click="addNRow">Add N rows:</BButton>
                </BCol>
                <BCol class="ps-1" sm="1">
                    <BInput type="number" class="d-inline" v-model="nrow"/>
                </BCol>
                <BCol sm="8" class="d-flex justify-content-center">
                    <BButton variant="outline-primary" :loading="processing" @click="savePo">Save</BButton>
                </BCol>
            </BRow>
        </template>

        <BForm>
            <BRow>
                <BCol sm="1">
                    <label for="title">Title</label>
                </BCol>
                <BCol sm="4">
                    <BFormInput id="title" v-model="po.title"></BFormInput>
                </BCol>
            </BRow>
            <BRow class="mt-2">
                <BCol sm="1">
                    <label for="arrive-at">Arrive At</label>
                </BCol>
                <BCol sm="2">
                    <BFormInput id="arrive-at" type="date" v-model="po.arrivalAt"></BFormInput>
                </BCol>
            </BRow>
            <BRow class="mt-2">
                <BCol sm="1">
                    <label for="qty">Qty</label>
                </BCol>
                <BCol sm="2">
                    <BFormInput id="qty" type="number" v-model="po.qty" readonly></BFormInput>
                </BCol>
            </BRow>
            <BRow class="mt-2">
                <BCol sm="1">
                    <label for="status">Status</label>
                </BCol>
                <BCol sm="2">
                    <BFormInput id="status" v-model="po.status" readonly></BFormInput>
                </BCol>
            </BRow>
            <BRow class="mt-2">
                <BCol sm="1">
                    <label for="status">Warehouse</label>
                </BCol>
                <BCol sm="6">
                    <BFormRadioGroup v-model="po.whId" name="wh-radio">
                        <BFormRadio :value="w.id" v-for="w in warehouses">
                            {{ w.name }}
                        </BFormRadio>
                    </BFormRadioGroup>
                </BCol>
            </BRow>
        </BForm>

        <BTable id="po-list" class="mt-2" striped hover :fields="fields" :items="po.details">
            <template #cell(rowNo)="row">
                {{ row.index + 1 }}
            </template>
        </BTable>
    </BCard>
</template>

<script lang="ts" setup>
import {getAllProducts, getWarehousesWithFilters, type PurchaseOrder} from "../api";
import {onMounted, ref, shallowRef, watchEffect} from "vue";
import {useRoute} from "vue-router";
import {formatInTimeZone} from "date-fns-tz";

const localTZ = Intl.DateTimeFormat().resolvedOptions().timeZone

const route = useRoute()

const pageTitle = shallowRef('')
const warehouses = shallowRef([])
const products = shallowRef([])
const nrow = shallowRef('1')

const po = ref<PurchaseOrder>({
    arrivalAt: formatInTimeZone(new Date(), localTZ, "yyyy-MM-dd"),
    qty: 0,
    status: 'INIT',
    details: [{qty: 1}]
});
const processing = shallowRef(false)

const fields = [
    {key: 'rowNo', label: '#'},
    {key: 'cat', label: 'Category'},
    {key: 'name', label: 'Name'},
    {key: 'qty', label: 'Qty'},
    {key: 'location', label: 'Location'},
    {key: 'comment', label: 'Comment'},
]

const savePo = async function () {
    processing.value = true

    try {
        getWarehousesWithFilters();
    } finally {
        processing.value = false
    }
}

const addNRow = function () {
    [...Array(parseInt(nrow.value)).keys()].forEach(function (x) {
        po.value.details.push({qty: 1})
    });
}

watchEffect(function () {
    // update title
    const wh = warehouses.value.find((x) => x.id == po.value.whId)?.code
    po.value.title = `${po.value.arrivalAt?.toString().replace(/-/g, '')} po of ${wh}`
})

onMounted(async function () {

    if ('purchaseOrderNew' == route.name) {
        pageTitle.value = 'New'
    } else {
        pageTitle.value = "update"
    }

    const data = (await getWarehousesWithFilters()).data.data
    warehouses.value = data

    po.value.whId = data.find((x) => x.code = 'HoC')?.id

    products.value = (await getAllProducts()).data.data

})
</script>

<style scoped>
:deep(.card-header) {
    display: flex;
}

.card-header ul {
    margin-bottom: 0;
}
</style>
