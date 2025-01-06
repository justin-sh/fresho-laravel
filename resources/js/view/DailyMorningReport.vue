<template>

    <BCard>
        <template #header>
            <div class="row">

                <div class="col-auto">
                    <span class="fw-bold fs-4">Production Plan</span>
                </div>
                <div class="col-auto">
                    <BFormInput id="arrive-at" type="date" v-model="reportDate"/>
                </div>
                <div class="col-auto">
                    <BButton variant="outline-primary"
                             @click="generateReport"
                             :loading="processing"
                             :disabled="processing">
                        Refresh Data
                    </BButton>
                </div>
            </div>
        </template>

        <BRow>
            <BCol class="col-auto">
                <BTableSimple bordered class="fss text-center">
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
                        <BTr class="align-middle">
                            <BTd :rowspan="Object.keys(porkKV).length + 1" class="text-center fw-bolder fsn">Pork
                            </BTd>
                            <BTd class="text-start">Belly</BTd>
                            <BTd>{{ reportData.belly.sum }}</BTd>
                            <BTd>
                                <BFormInput size="sm" type="number" v-model="stockData.belly.sum"></BFormInput>
                            </BTd>
                            <BTd>{{ stockData.belly.sum - reportData.belly.sum }}</BTd>
                        </BTr>

                        <BTr class="align-middle" v-for="(v,k) in porkKV">
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
                        <BTr class="align-middle">
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

                        <BTr class="align-middle" v-for="(v,k) in ckKV">
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
                <BTableSimple bordered class="fss text-center">
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

                        <BTr class="align-middle" v-for="(v,k) in porkSpecial">
                            <BTd class="text-start prd-ex-item-name">{{ k }}</BTd>
                            <BTd>{{ reportData[v]?.sum }}</BTd>
                        </BTr>
                    </BTbody>
                </BTableSimple>
            </BCol>
            <BCol class="col-3">
                <BTableSimple bordered class="fss text-center">
                    <BThead head-variant="dark" class="fsn">
                        <BTr class="align-middle">
                            <BTh colspan="2">CK Special Order</BTh>
                        </BTr>
                    </BThead>
                    <BTbody>
                        <BTr class="align-middle" v-for="(v,k) in ckSpecial">
                            <BTd class="text-start prd-ex-item-name">{{ k }}</BTd>
                            <BTd>{{ reportData[v]?.sum }}</BTd>
                        </BTr>
                    </BTbody>
                </BTableSimple>
            </BCol>
        </BRow>
    </BCard>
</template>

<script lang="ts" setup>
import {dailyReport} from "../api";
import {onMounted, ref, shallowRef, watchEffect} from "vue";
import {useRoute, useRouter} from "vue-router";
import {formatInTimeZone} from "date-fns-tz";


const localTZ = Intl.DateTimeFormat().resolvedOptions().timeZone

const route = useRoute()
const router = useRouter()

const reportDate = shallowRef(formatInTimeZone(new Date(), localTZ, "yyyy-MM-dd"));
const status = shallowRef(['accepted'])

const porkKV = {
    'Belly R/Off': 'bellyROff',
    'Belly B/IN': 'bellyBoneIn',
    'BBQ': 'bbq',
    'Ribs': 'pribs',
}

const porkSpecial = {
    'Meaty Ribs': 'pmeatyribs',
    'EX-Meaty Ribs': 'pexmeatyribs',
    'Cutlet': 'na',
    'Meaty Riblets': 'na',
    'Meaty Leg Bone': 'na',
    'EX-Meaty Leg Bone': 'na',
    'Meaty Neck Bone': 'na',
    'EX-Meaty Neck Bone': 'na',
    'Loin Rind ON': 'na',
    'Shoulder Rind ON': 'na',
    'Middle': 'na',
}

const ckSpecial = {
    '#16 Thigh s/off': 'na',
    '#22 Thigh s/off': 'pexmeatyribs',
    '#28 Thigh s/off': 'na',
    '#16 Thigh s/ON': 'na',
    '#22 Thigh s/ON': 'na',
    '#28 Thigh s/ON': 'na',
    '#16 Br s/ON, Tdr/off': 'na',
    'Butterfly Cut': 'na',
    '#16 Kiev Cut s/ON': 'na',
    '#15 WB s/off': 'na',
    'Butt': 'na',
    'Ribs': 'na',
    'Legette r/ON': 'na',
    'Drumsticks': 'na',
    'Chop s/ON	': 'na',
}

const ckKV = {
    'Breast': 'ckbr',
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
    "ckthon": {
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
    "ckthon": {
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
    }
})
const processing = shallowRef(false)

const generateReport = async function () {

    processing.value = true
    console.log(reportDate.value)

    const rv = (await dailyReport(reportDate.value)).data
    console.log(rv)
    reportData.value = rv.data
    processing.value = false
}


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
