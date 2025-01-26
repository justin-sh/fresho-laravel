<template>

    <BCard>
        <template #header>
            <div class="row">

                <div class="col-auto">
                    <span class="fw-bold fs-4">Production Plan</span>
                </div>
                <div class="col-auto">
                    <BFormInput id="arrive-at" type="date" v-model="reportDate" />
                </div>
                <div class="col-auto">
                    <BButton variant="outline-primary" @click="generateReport" :loading="processing"
                        :disabled="processing">
                        Refresh Data
                    </BButton>
                </div>
            </div>
        </template>

        <BRow>
            <BCol class="col-auto">
                <BTableSimple hover bordered class="fss text-center">
                    <BThead head-variant="dark" class="fsn">
                        <BTr class="align-middle">
                            <BTh class="prd-cat">Plan</BTh>
                            <BTh class="prd-item-name">Item</BTh>
                            <BTh class="prd-order">Order</BTh>
                            <BTh class="prd-stock">Stock</BTh>
                            <BTh class="prd-produced">Produce</BTh>
                        </BTr>
                    </BThead>

                    <BTbody>
                        <BTr class="align-middle" id="order-Belly" @click="showDetail('belly', 'Belly')">
                            <BTd :rowspan="Object.keys(porkKV).length + 1" class="text-center fw-bolder fsn">Pork
                            </BTd>
                            <BTd class="text-start">Belly</BTd>
                            <BTd>{{ reportData.belly.sum }}</BTd>
                            <BTd>
                                <BFormInput size="sm" type="number" v-model="stockData.belly.sum"></BFormInput>
                            </BTd>
                            <BTd>{{ stockData.belly.sum - reportData.belly.sum }}</BTd>
                        </BTr>

                        <BTr class="align-middle" v-for="(v,k) in porkKV" @click="showDetail(v, k)">
                            <BTd class="text-start">{{ k }}</BTd>
                            <BTd>{{ reportData[v].sum }}</BTd>
                            <BTd>
                                <BFormInput size="sm" type="number" v-model="stockData[v].sum"></BFormInput>
                            </BTd>
                            <BTd>{{ stockData[v].sum - reportData[v].sum }}</BTd>
                        </BTr>

                        <BTr>
                            <BTd colspan="11"></BTd>
                        </BTr>
                        <BTr class="align-middle" @click="showDetail('ckbr', 'Breast')">
                            <BTd :rowspan="Object.keys(ckKV).length + 1" class="align-middle text-center fw-bolder fsn">
                                CK
                            </BTd>
                            <BTd class="text-start">Breast</BTd>
                            <BTd>{{ reportData.ckbr.sum }}</BTd>
                            <BTd>
                                <BFormInput size="sm" type="number" v-model="stockData.ckbr.sum"></BFormInput>
                            </BTd>
                            <BTd>{{ stockData.ckbr.sum - reportData.ckbr.sum }}</BTd>
                        </BTr>

                        <BTr class="align-middle" v-for="(v,k) in ckKV" @click="showDetail(v, k)">
                            <BTd class="text-start">{{ k }}</BTd>
                            <BTd>{{ reportData[v].sum }}</BTd>
                            <BTd>
                                <BFormInput size="sm" type="number" v-model="stockData[v].sum"></BFormInput>
                            </BTd>
                            <BTd>{{ stockData[v].sum - reportData[v].sum }}</BTd>
                        </BTr>
                    </BTbody>
                </BTableSimple>
            </BCol>
            <BCol class="col-3">
                <BTableSimple hover bordered class="fss text-center">
                    <BThead head-variant="dark" class="fsn">
                        <BTr class="align-middle">
                            <BTh class="text-start col-7">Side: -</BTh>
                            <BTh class="text-start">F/QTR: -</BTh>
                        </BTr>
                    </BThead>
                    <BTbody>
                        <BTr>
                            <BTd colspan="2" class="text-center fw-bolder fsn prd-ex-item-name">Pork Special Order</BTd>
                        </BTr>

                        <BTr class="align-middle" v-for="(v,k) in porkSpecial" @click="showDetail(v, k)">
                            <BTd class="text-start prd-ex-item-name">{{ k }}</BTd>
                            <BTd>{{ reportData[v]?.sum === 0 ? '' : reportData[v]?.sum }}</BTd>
                        </BTr>
                    </BTbody>
                </BTableSimple>
            </BCol>
            <BCol class="col-3">
                <BTableSimple hover bordered class="fss text-center">
                    <BThead head-variant="dark" class="fsn">
                        <BTr class="align-middle">
                            <BTh colspan="2">CK Special Order</BTh>
                        </BTr>
                    </BThead>
                    <BTbody>
                        <BTr class="align-middle" v-for="(v,k) in ckSpecial" @click="showDetail(v, k)">
                            <BTd class="text-start prd-ex-item-name">{{ k }}</BTd>
                            <BTd>{{ reportData[v]?.sum === 0 ? '' : reportData[v]?.sum }}</BTd>
                        </BTr>
                    </BTbody>
                </BTableSimple>
            </BCol>
        </BRow>
        <BModal id="popover-m" v-model="modalShow" scrollable :title="modalTitle" ok-only size="xl">
            <BTableSimple bordered striped hover>
                <BThead>
                    <BTr>
                        <BTh>Customer</BTh>
                        <BTh>Product</BTh>
                        <BTh>Qty</BTh>
                        <BTh>Supplier Notes</BTh>
                        <BTh>Customer Notes</BTh>
                    </BTr>
                </BThead>
                <BTbody>
                    <BTr v-for="d in modelData">
                        <BTd>
                            {{ d.customer.substring(0, 20) }}
                        </BTd>
                        <BTd>
                            {{ ('(' + d.prd_code + ') ' + d.prd_name).substring(0, 40) }}
                        </BTd>
                        <BTd>
                            {{ d.qty }}
                        </BTd>
                        <BTd>
                            {{ d.supplier_notes }}
                        </BTd>
                        <BTd>
                            {{ d.customer_notes }}
                        </BTd>
                    </BTr>
                </BTbody>
            </BTableSimple>
        </BModal>
        <BTableSimple striped hover small caption-top bordered v-if="Object.keys(reportData.others).length>0">
            <caption>
                Details - Others
                <BButton @click="showAll = !showAll" variant="primary">
                    Show {{ showAll ? 'Available' : 'All' }}
                </BButton>
            </caption>
            <BThead>
                <BTr>
                    <!--                    <BTh>Customer</BTh>-->
                    <BTh style="width:50%">Product</BTh>
                    <BTh>Qty</BTh>
                    <BTh style="width:10%" class="text-center">Actions</BTh>
                </BTr>
            </BThead>
            <BTbody>
                <template v-for="(v, k) in reportData.others">
                    <BTr v-if="showAll || !hPrds.includes(k)" @click="showDetailByKV(k, v)">
                        <BTd>
                            {{ k }}
                        </BTd>
                        <BTd>
                            {{ v.sum }}
                        </BTd>
                        <BTd class="text-center">
                            <template v-if="hPrds.includes(k)">
                                <BButton @click.stop="toggleProduct(k)" variant="outline-info">
                                    Show
                                </BButton>
                            </template>
                            <template v-else>
                                <BButton @click.stop="toggleProduct(k)" variant="outline-danger">
                                    Hide
                                </BButton>
                            </template>
                        </BTd>
                    </BTr>

                </template>
            </BTbody>
        </BTableSimple>
    </BCard>
</template>

<script lang="ts" setup>
import {dailyReport} from "../api";
import {onMounted, ref, shallowRef, watchEffect} from "vue";
import {useRoute, useRouter} from "vue-router";
import {formatInTimeZone} from "date-fns-tz";
import { BButton } from "bootstrap-vue-next";


const localTZ = Intl.DateTimeFormat().resolvedOptions().timeZone

const route = useRoute()
const router = useRouter()

const reportDate = shallowRef(formatInTimeZone(new Date(), localTZ, "yyyy-MM-dd"));
const status = shallowRef(['accepted'])
const showAll = shallowRef(false)

const porkKV = {
    'Belly R/Off': 'bellyROff',
    'Belly B/IN': 'bellyBoneIn',
    'BBQ': 'bbq',
    'Ribs': 'pribs',
    'Neck': 'pneck',
}

const porkSpecial = {
    'Meaty Ribs': 'pmeatyribs',
    'EX-Meaty Ribs': 'pexmeatyribs',
    'Cutlet': 'pcutlets',
    'Meaty Riblets': 'pmeatyriblet',
    'Meaty Leg Bone': 'pmeatylegbone',
    'EX-Meaty Leg Bone': 'pexmeatylegbone',
    'Meaty Neck Bone': 'pmeatyneckbone',
    'EX-Meaty Neck Bone': 'pexmeatyneckbone',
    'Loin Rind ON': 'ploinrindon',
    'Shoulder Rind ON': 'pshrindon',
    'Middle': 'pmiddle',
}

const ckSpecial = {
    '#16 Thigh s/off': 'ckthoff16',
    '#22 Thigh s/off': 'ckthoff22',
    '#28 Thigh s/off': 'ckthoff28',
    '#16 Thigh s/ON': 'ckthon16',
    '#22 Thigh s/ON': 'na',
    '#28 Thigh s/ON': 'na',
    'Special Br s/ON': 'ckbron',
    'Butterfly Cut': 'ckbrbf',
    '#15 WB s/off': 'ckwboff15',
    'Butt': 'ckbutt',
    'Ribs': 'ckrib',
    'Legette r/ON': 'cklegetteon',
    'Drumsticks': 'ckdrumstick',
    'Chop s/ON	': 'ckchopon',
}

const ckKV = {
    'ML s/off': 'cksoff',
    'ML s/ON': 'ckson',
    'Thigh s/off': 'ckthoff',
    'Thigh s/ON': 'ckthon',
    'Legette': 'cklegette',
    'Bone IN': 'ckbi',
    'Wings': 'ckwings',
    'Tdr': 'cktdr',
    'Mid-Wingettes': 'ckwingette',
}

const reportData = shallowRef({
    "belly": {
        "sum": 0,
        "details": []
    },
    "bellyROff": {
        "sum": 0,
        "details": []
    },
    "bellyBoneIn": {
        "sum": 0,
        "details": []
    },
    "bbq": {
        "sum": 0,
        "details": []
    },
    "pribs": {
        "sum": 0,
        "details": []
    },
    "pneck": {
        "sum": 0,
        "details": []
    },
    "pmeatyribs": {
        "sum": 0,
        "details": []
    },
    "pexmeatyribs": {
        "sum": 0,
        "details": []
    },
    "pfrzribs": {
        "sum": 0,
        "details": []
    },
    "ckbr": {
        "sum": 0,
        "details": []
    },
    "cksoff": {
        "sum": 0,
        "details": []
    },
    "ckson": {
        "sum": 0,
        "details": []
    },
    "ckbi": {
        "sum": 0,
        "details": []
    },
    "ckthoff": {
        "sum": 0,
        "details": []
    },
    "ckthoff16": {
        "sum": 0,
        "details": []
    },
    "ckthoff22": {
        "sum": 0,
        "details": []
    },
    "ckthoff28": {
        "sum": 0,
        "details": []
    },
    "ckthon": {
        "sum": 0,
        "details": []
    },
    "ckthon16": {
        "sum": 0,
        "details": []
    },
    "cklegette": {
        "sum": 0,
        "details": []
    },
    "ckwings": {
        "sum": 0,
        "details": []
    },
    "ckwingette": {
        "sum": 0,
        "details": []
    },
    "cktdr": {
        "sum": 0,
        "details": []
    },
    "ckrib": {
        "sum": 0,
        "details": []
    },
    "ckbutt": {
        "sum": 0,
        "details": []
    },
    "others": {
    }
})

const zero = ref(0)
const stockData = ref({
    "belly": {
        "sum": 0,
        "details": []
    },
    "bellyROff": {
        "sum": 0,
        "details": []
    },
    "bellyBoneIn": {
        "sum": 0,
        "details": []
    },
    "bbq": {
        "sum": 0,
        "details": []
    },
    "pribs": {
        "sum": 0,
        "details": []
    },
    "pneck": {
        "sum": 0,
        "details": []
    },
    "pmeatyribs": {
        "sum": 0,
        "details": []
    },
    "pexmeatyribs": {
        "sum": 0,
        "details": []
    },
    "pfrzribs": {
        "sum": 0,
        "details": []
    },
    "ckbr": {
        "sum": 0,
        "details": []
    },
    "cksoff": {
        "sum": 0,
        "details": []
    },
    "ckson": {
        "sum": 0,
        "details": []
    },
    "ckbi": {
        "sum": 0,
        "details": []
    },
    "ckthoff": {
        "sum": 0,
        "details": []
    },
    "ckthoff16": {
        "sum": 0,
        "details": []
    },
    "ckthoff22": {
        "sum": 0,
        "details": []
    },
    "ckthoff28": {
        "sum": 0,
        "details": []
    },
    "ckthon": {
        "sum": 0,
        "details": []
    },
    "ckthon16": {
        "sum": 0,
        "details": []
    },
    "cklegette": {
        "sum": 0,
        "details": []
    },
    "ckwings": {
        "sum": 0,
        "details": []
    },
    "ckwingette": {
        "sum": 0,
        "details": []
    },
    "cktdr": {
        "sum": 0,
        "details": []
    },
    "ckrib": {
        "sum": 0,
        "details": []
    },
    "ckbutt": {
        "sum": 0,
        "details": []
    }
})
const processing = shallowRef(false)

const generateReport = async function () {

    processing.value = true
    // console.log(reportDate.value)

    const rv = (await dailyReport(reportDate.value)).data
    // console.log(rv)
    reportData.value = rv.data
    processing.value = false
}

const modalTitle = ref('')
const modalShow = ref(false)
const modelData = ref([])
const showDetail = function (id, name) {

    modalTitle.value = name + ' Detail'

    try {
        modelData.value = reportData.value[id]['details']
    } catch (e) {
        console.log("No Details for '" + name + "'")
        console.log(e)
    }

    modalShow.value = !modalShow.value
}
const showDetailByKV = function (name, data) {

    modalTitle.value = name + ' Detail'

    try {
        modelData.value = data['details']
    } catch (e) {
        console.log("No Details for '" + name + "'")
        console.log(e)
    }

    modalShow.value = !modalShow.value
}

const toggleProduct = function(product: string){
    // const hPrds = getHidenProducts()
    console.log(product)

    // if(new Set(hPrds.value).has(product)){
    //     return
    // }

    const idx = hPrds.value.indexOf(product)
    if (idx === -1) {
        hPrds.value.push(product)
    }else{
        hPrds.value.splice(idx, 1)
    }

    localStorage.setItem('dmrHidenPrds', JSON.stringify(hPrds.value))
}

const getHidenProducts = function(){
    return JSON.parse(localStorage.getItem('dmrHidenPrds') ?? '[]')
}

const hPrds = ref([])

watchEffect(() => {
    // so.value.id = route.params.id ?? ''
    //
    // if (isNew.value) {
    //     pageTitle.value = 'New'
    //
    //     so.value = {
    //         pickupAt: formatInTimeZone(new Date(), localTZ, "yyyy-MM-dd"),
    //     }
    // } else if (isView.value) {
    //     pageTitle.value = "View"
    // } else {
    //     pageTitle.value = "Update"
    // }
});


onMounted(async function () {
    hPrds.value = getHidenProducts()
})
</script>

<style scoped>
/*
:deep(.card-header) {
    display: flex;
}

.card-header ul {
    margin-bottom: 0;
}
*/

.fss {
    font-size: 0.75rem;
}

.fsn {
    font-size: 1rem;
}

tbody tr{
    cursor: pointer;
}

table td {
    padding-top: 0.15rem;
    padding-bottom: 0.15rem;
}

.prd-cat {
    width: 5%;
    min-width: 55px;
}

.prd-item-name {
    width: 10%;
    min-width: 105px;
}

.prd-order {
    width: 10%;
}

.prd-stock {
    width: 10%;
    min-width: 55px;
}

.prd-produced {
    width: 8%;
    min-width: 105px;
}

.prd-ex-item-name {
    width: 55%;
    min-width: 125px;
    line-height: 31px;
}

</style>
