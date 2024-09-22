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
                    <BButton variant="outline-primary" :loading="processing" @click="save">Save</BButton>
                    <BButton v-if="'purchaseOrderNew' !== route.name" variant="outline-primary" :loading="processing"
                             @click="approvePo" class="ms-2">
                        Approve
                    </BButton>
                </BCol>
            </BRow>
        </template>

        <BForm>
            <BRow>
                <BCol sm="1">
                    <label for="title">Title</label>
                </BCol>
                <BCol sm="4">
                    <BFormInput id="title" v-model="po.title" :state="po.title?.length>0"/>
                </BCol>
            </BRow>
            <BRow class="mt-2">
                <BCol sm="1">
                    <label for="arrive-at">Arrive At</label>
                </BCol>
                <BCol sm="2">
                    <BFormInput id="arrive-at" type="date" v-model="po.arrivalAt" :state="po.arrivalAt!==''"/>
                </BCol>
            </BRow>
            <BRow class="mt-2">
                <BCol sm="1">
                    <label for="qty">Qty</label>
                </BCol>
                <BCol sm="2">
                    {{ po.qty }}
                </BCol>
            </BRow>
            <BRow class="mt-2">
                <BCol sm="1">
                    <label for="status">Status</label>
                </BCol>
                <BCol sm="2">
                    {{ po.state }}
                </BCol>
            </BRow>
            <BRow class="mt-2">
                <BCol sm="1">
                    <label for="status">Warehouse</label>
                </BCol>
                <BCol sm="6">
                    <BFormRadioGroup v-model="po.wh.id" name="wh-radio">
                        <BFormRadio :value="w.id" v-for="w in warehouses">
                            {{ w.name }}
                        </BFormRadio>
                    </BFormRadioGroup>
                </BCol>
            </BRow>
        </BForm>

        <!--        <BTable id="po-list" class="mt-2" striped hover :fields="fields" :items="po.details">-->
        <!--            <template #cell(rowNo)="row">-->
        <!--                {{ row.index + 1 }}-->
        <!--            </template>-->
        <!--        </BTable>-->

        <table class="w-100 mt-4">
            <thead>
            <tr>
                <th v-for="f in fields" :key="f.key">{{ f.label }}</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(d, idx) in po.details">
                <td>{{ idx + 1 }}</td>
                <td class="col-1">{{ d.cat }}</td>
                <td class="col-4 px-2">

                    <v-select v-model="po.details[idx].prdId"
                              :options="products_list"
                              label="name"
                              @option:selected="(o)=>po.details[idx].cat=o.cat"
                              :reduce="prd => prd.id"/>
                </td>
                <td class="col-2 ms-2">
                    <BFormInput type="number" required="required"
                                :state="po.details[idx].qty>0"
                                v-model="po.details[idx].qty"
                                :min="0"
                                @change="updateQty"
                    />
                </td>
                <td class="col-2 px-2">
                    <BFormInput v-model="po.details[idx].location"/>
                </td>
                <td>
                    <BFormInput v-model="po.details[idx].comment"/>
                </td>
            </tr>
            </tbody>
        </table>
        <BFormDatalist id="products-list" :options="products_list"/>
    </BCard>
</template>

<script lang="ts" setup>
import {getAllProducts, getPo, getWarehousesWithFilters, type PurchaseOrder, savePo, updatePo} from "../api";
import {onMounted, ref, shallowRef, watchEffect} from "vue";
import {useRoute, useRouter} from "vue-router";
import {formatInTimeZone} from "date-fns-tz";


const localTZ = Intl.DateTimeFormat().resolvedOptions().timeZone

const route = useRoute()
const router = useRouter()

const pageTitle = shallowRef('')
const isNew = shallowRef(false)
const warehouses = shallowRef([])
let products = {}
const products_list = shallowRef([])
const nrow = shallowRef('1')

const po = ref<PurchaseOrder>({
    arrivalAt: formatInTimeZone(new Date(), localTZ, "yyyy-MM-dd"),
    qty: 0,
    state: 'INIT',
    wh: {},
    details: [{qty: 0}]
});
const processing = shallowRef(false)

const fields = [
    {key: 'rowNo', label: '#'},
    {key: 'cat', label: 'Category'},
    {key: 'name', label: 'Product'},
    {key: 'qty', label: 'Qty'},
    {key: 'location', label: 'Location(04L1F1)'},
    {key: 'comment', label: 'Comment'},
]

const save = async function () {
    processing.value = true

    try {
        if (isNew.value) {
            const rv = (await savePo(po.value)).data

            if (rv.ok) {
                await router.push({'name': 'purchaseOrder', params: {id: rv.data.id}})
            }
        } else {
            (await updatePo(po.value)).data
        }
    } finally {
        processing.value = false
    }
}

const approvePo = async function () {
    processing.value = true

    try {
        // getWarehousesWithFilters();
        // savePo(po.value)
    } finally {
        processing.value = false
    }
}

const addNRow = function () {
    [...Array(parseInt(nrow.value)).keys()].forEach(function (x) {
        po.value.details.push({qty: 1})
    });
}

const updateQty = function () {
    po.value.qty = 0;
    po.value.details.forEach(function (x) {
        if (x.qty && x.prdId) {
            po.value.qty += parseInt(x.qty)
        }
    })
}

watchEffect(function () {
    // update title
    // const wh = warehouses.value.find((x) => x.id == po.value.whId)?.code
    if (pageTitle.value == 'new') {
        const wh = po.value.wh.code || ''
        po.value.title = `${po.value.arrivalAt?.toString().replace(/-/g, '')} po of ${wh}`
    }
})

onMounted(async function () {

    const data = (await getWarehousesWithFilters()).data.data
    warehouses.value = data

    if ('purchaseOrderNew' == route.name) {
        pageTitle.value = 'New'
        isNew.value = true
        po.value.whId = data.find((x) => x.code = 'HoC')?.id
    } else {
        pageTitle.value = "Update"
        isNew.value = false

        po.value.id = route.params.id
    }

    const prds = (await getAllProducts()).data.data
    prds.forEach(function (x) {
        products[x.id] = x
    })

    products_list.value = prds

    const po2 = (await getPo(po.value.id)).data.data
    po.value = po2


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
