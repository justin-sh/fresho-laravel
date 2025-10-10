<template>

    <BCard>
        <template #header>
            <div class="col align-content-center">
                <span class="fw-bold fs-4">Sale Orders</span>
                <span class="ms-2">{{ pageTitle }}</span>
            </div>
        </template>
        <template #footer>
            <BRow v-if="!isView">
                <BCol class="col-auto pe-0">
                    <BButton variant="outline-primary" class="d-inline" @click="addNRow">Add N rows:</BButton>
                </BCol>
                <BCol class="ps-1" sm="1">
                    <BInput type="number" class="d-inline" v-model="nrow"/>
                </BCol>
                <BCol sm="8" class="d-flex justify-content-center">
                    <BButton variant="outline-primary" :loading="processing" @click="save">Save</BButton>
                    <BButton v-if="!isNew" variant="outline-primary" :loading="processing"
                             @click="approve" class="ms-2">
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
                    <BFormInput id="title" v-model="so.title" :disabled="isView"/>
                </BCol>
            </BRow>
            <BRow class="mt-2">
                <BCol sm="1">
                    <label for="arrive-at">Arrive At</label>
                </BCol>
                <BCol sm="2">
                    <BFormInput id="arrive-at" type="date" v-model="so.pickupAt" :disabled="isView"/>
                </BCol>
            </BRow>
            <BRow class="mt-2">
                <BCol sm="1">
                    <label for="qty">Qty</label>
                </BCol>
                <BCol sm="2">
                    {{ so.qty }}
                </BCol>
            </BRow>
            <BRow class="mt-2">
                <BCol sm="1">
                    <label for="status">Status</label>
                </BCol>
                <BCol sm="2">
                    {{ so.state }}
                </BCol>
            </BRow>
            <BRow class="mt-2">
                <BCol sm="1">
                    <label for="status">Warehouse</label>
                </BCol>
                <BCol sm="6">
                    <BFormRadioGroup v-model="so.whId" name="wh-radio">
                        <BFormRadio :value="w.id" v-for="w in warehouses" :disabled="isView">
                            {{ w.name }}
                        </BFormRadio>
                    </BFormRadioGroup>
                </BCol>
            </BRow>
        </BForm>

        <table class="w-100 mt-4">
            <thead>
            <tr>
                <th v-for="f in fields" :key="f.key">{{ f.label }}</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(d, idx) in so.details">
                <td>{{ idx + 1 }}</td>
                <td class="col-1">{{ d.cat }}</td>
                <td class="col-4 px-2">

                    <v-select v-model="so.details[idx].prdId"
                              :options="products_list"
                              label="name"
                              :disabled="isView"
                              @option:selected="(o)=>so.details[idx].cat=o.cat"
                              :reduce="prd => prd.id"/>
                </td>
                <td class="col-1 ms-2">
                    <BFormInput type="number" required="required"
                                v-model="so.details[idx].qty"
                                :min="0"
                                :disabled="isView"
                                @change="updateQty"
                    />
                </td>
                <td class="col-2 px-2">
                    <BFormInput v-model="so.details[idx].location" :disabled="isView"/>
                </td>
                <td>
                    <BFormInput v-model="so.details[idx].comment" :disabled="isView"/>
                </td>
            </tr>
            </tbody>
        </table>
        <BFormDatalist id="products-list" :options="products_list"/>
    </BCard>
</template>

<script lang="ts" setup>
import {
    approvePo, approveSo,
    getAllProducts,
    getPo,
    getWarehousesWithFilters,
    type PurchaseOrder,
    savePo,
    saveSo,
    updatePo, updateSo
} from "../api";
import {computed, onMounted, ref, shallowRef, watch, watchEffect} from "vue";
import {useRoute, useRouter} from "vue-router";
import {formatInTimeZone} from "date-fns-tz";


const localTZ = Intl.DateTimeFormat().resolvedOptions().timeZone

const route = useRoute()
const router = useRouter()

const pageTitle = shallowRef('')
const warehouses = shallowRef([])
let products = {}
const products_list = shallowRef([])
const nrow = shallowRef('1')

const so = ref<PurchaseOrder>({});
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
            const rv = (await saveSo(so.value)).data

            if (rv.ok) {
                await router.push({'name': 'purchaseOrder', params: {id: rv.data.id}})
            }
        } else {
            const rv = (await updateSo(so.value)).data
        }
    } finally {
        processing.value = false
    }
}

const approve = async function () {
    processing.value = true
    try {
        const params = {id: so.value.id};
        const rv = (await approveSo(params)).data
        if (rv.ok == true) {
            so.value.state = 'APPROVE';
            pageTitle.value = "View"
        }
    } finally {
        processing.value = false
    }
}

const addNRow = function () {
    [...Array(parseInt(nrow.value)).keys()].forEach(function (x) {
        so.value.details.push({qty: 1})
    });
}

const updateQty = function () {
    so.value.qty = 0;
    so.value.details.forEach(function (x) {
        if (x.qty && x.prdId) {
            so.value.qty += parseInt(x.qty)
        }
    })
}

const isView = computed(function () {
    return !isNew.value && so.value?.state === 'APPROVE';
});

const isNew = computed(function () {
    return !so.value.id;
});

watchEffect(() => {
    so.value.id = route.params.id ?? ''

    if (isNew.value) {
        pageTitle.value = 'New'

        so.value = {
            title: formatInTimeZone(new Date(), localTZ, "yyyyMMdd") + ' so',
            pickupAt: formatInTimeZone(new Date(), localTZ, "yyyy-MM-dd"),
            qty: 0,
            state: 'INIT',
            whId: warehouses.value.find((x) => x.code === 'HoC')?.id,
            details: [{qty: 0}]
        }
    } else if (isView.value) {
        pageTitle.value = "View"
    } else {
        pageTitle.value = "Update"
    }
});

watch(() => so.value.pickupAt, (newArrivalAt, oldArrivalAt) => {

    if (!newArrivalAt) return;

    const ymd = so.value.pickupAt.toString().replace(/-/g, '')
    if (oldArrivalAt) {
        so.value.title = so.value.title.replace(oldArrivalAt.replace(/-/g, ''), ymd)
    }
});

onMounted(async function () {

    warehouses.value = (await getWarehousesWithFilters()).data.data

    const prds = (await getAllProducts()).data.data
    prds.forEach(function (x) {
        products[x.id] = x
    })

    products_list.value = prds

    if (so.value.id) {
        so.value = (await getPo(so.value.id)).data.data
    }

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
