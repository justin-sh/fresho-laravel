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

        <BTableSimple bordered class="fss text-center">
            <BThead head-variant="dark" class="fsn">
                <BTr class="align-middle">
                    <BTh class="prd-cat">Plan</BTh>
                    <BTh class="prd-item-name">Item</BTh>
                    <BTh class="prd-order">Order</BTh>
                    <BTh class="prd-stock">Stock</BTh>
                    <BTh class="prd-produced">Produce</BTh>
                    <BTh>&nbsp;</BTh>
                    <BTd class="text-start prd-ex-item-name">Side: -</BTd>
                    <BTd class="text-start prd-ex-item-v">F/QTR: -</BTd>
                    <BTh>&nbsp;</BTh>
                    <BTh colspan="2">CK Special Order</BTh>
                </BTr>
            </BThead>
            <BTbody>
                <BTr class="align-middle">
                    <BTd rowspan="7" class="text-center fw-bolder fsn">Pork
                    </BTd>
                    <BTd class="text-start">Belly</BTd>
                    <BTd>{{ reportData.belly.sum }}</BTd>
                    <BTd>
                        <BFormInput size="sm" type="number" v-model="stockData.belly.sum"></BFormInput>
                    </BTd>
                    <BTd>{{ reportData.belly.sum - stockData.belly.sum }}</BTd>
                    <BTd rowspan="7"></BTd>
                    <BTd colspan="2" class="text-center fw-bolder fsn">Pork Special Order</BTd>
                    <BTd rowspan="7"></BTd>
                    <BTd class="text-start prd-ex-item-name">#16 Thigh s/off</BTd>
                    <BTd class="prd-ex-item-v"></BTd>
                </BTr>
                <BTr class="align-middle">
                    <BTd class="text-start">Belly R/Off</BTd>
                    <BTd>{{ reportData.bellyROff.sum }}</BTd>
                    <BTd>
                        <BFormInput size="sm" type="number" v-model="stockData.bellyROff.sum"></BFormInput>
                    </BTd>
                    <BTd>{{ reportData.bellyROff.sum - stockData.bellyROff.sum }}</BTd>
                    <BTd class="text-start">Meaty Ribs</BTd>
                    <BTd></BTd>
                    <BTd class="text-start">#22 Thigh s/off</BTd>
                    <BTd></BTd>
                </BTr>
                <BTr class="align-middle">
                    <BTd class="text-start">Belly B/IN</BTd>
                    <BTd>{{ reportData.bellyBoneIn.sum }}</BTd>
                    <BTd>
                        <BFormInput size="sm" type="number" v-model="stockData.bellyBoneIn.sum"></BFormInput>
                    </BTd>
                    <BTd>{{ reportData.bellyBoneIn.sum - stockData.bellyBoneIn.sum }}</BTd>
                    <BTd class="text-start">Cutlet</BTd>
                    <BTd></BTd>
                    <BTd class="text-start">#28 Thigh s/off</BTd>
                    <BTd></BTd>
                </BTr>
                <BTr class="align-middle">
                    <BTd class="text-start">BBQ</BTd>
                    <BTd>{{ reportData.bbq.sum }}</BTd>
                    <BTd>
                        <BFormInput size="sm" type="number" v-model="stockData.bbq.sum"></BFormInput>
                    </BTd>
                    <BTd>{{ reportData.bbq.sum - stockData.bbq.sum }}</BTd>
                    <BTd class="text-start">Meaty Riblets</BTd>
                    <BTd></BTd>
                    <BTd class="text-start">#16 Thigh s/ON</BTd>
                    <BTd></BTd>
                </BTr>
                <BTr class="align-middle">
                    <BTd class="text-start">Leg</BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd class="text-start">Meaty Leg Bone</BTd>
                    <BTd></BTd>
                    <BTd class="text-start">#22 Thigh s/ON</BTd>
                    <BTd></BTd>
                </BTr>
                <BTr class="align-middle">
                    <BTd class="text-start">Loin</BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd class="text-start">Meaty Neck Bone</BTd>
                    <BTd></BTd>
                    <BTd class="text-start">#28 Thigh s/ON</BTd>
                    <BTd></BTd>
                </BTr>
                <BTr class="align-middle">
                    <BTd class="text-start">Ribs</BTd>
                    <BTd>{{ reportData.pribs.sum }}</BTd>
                    <BTd>
                        <BFormInput size="sm" type="number" v-model="stockData.pribs.sum"></BFormInput>
                    </BTd>
                    <BTd>{{ reportData.pribs.sum - stockData.pribs.sum }}</BTd>
                    <BTd class="text-start">EX-Meaty Ribs</BTd>
                    <BTd></BTd>
                    <BTd class="text-start">#16 Br s/ON, Tdr/off</BTd>
                    <BTd>2</BTd>
                </BTr>
                <BTr>
                    <BTd colspan="11"></BTd>
                </BTr>
                <BTr class="align-middle">
                    <BTd rowspan="10" class="align-middle text-center fw-bolder fsn">CK</BTd>
                    <BTd class="text-start">Breast</BTd>
                    <BTd>{{ reportData.ckbr.sum }}</BTd>
                    <BTd>
                        <BFormInput size="sm" type="number" v-model="stockData.ckbr.sum"></BFormInput>
                    </BTd>
                    <BTd>{{ reportData.ckbr.sum - stockData.ckbr.sum }}</BTd>
                    <BTd rowspan="10"></BTd>
                    <BTd class="text-start">EX-Meaty Leg Bone</BTd>
                    <BTd></BTd>
                    <BTd rowspan="10"></BTd>
                    <BTd class="text-start">Butterfly Cut</BTd>
                    <BTd></BTd>
                </BTr>
                <BTr class="align-middle">
                    <BTd class="text-start">ML s/off</BTd>
                    <BTd>{{ reportData.cksoff.sum }}</BTd>
                    <BTd>
                        <BFormInput size="sm" type="number" v-model="stockData.cksoff.sum"></BFormInput>
                    </BTd>
                    <BTd>{{ reportData.cksoff.sum - stockData.cksoff.sum }}</BTd>
                    <BTd class="text-start">EX-Meaty Neck Bone</BTd>
                    <BTd></BTd>
                    <BTd class="text-start">#16 Kiev Cut s/ON</BTd>
                    <BTd></BTd>
                </BTr>
                <BTr class="align-middle">
                    <BTd class="text-start">ML s/ON</BTd>
                    <BTd>{{ reportData.ckson.sum }}</BTd>
                    <BTd>
                        <BFormInput size="sm" type="number" v-model="stockData.ckson.sum"></BFormInput>
                    </BTd>
                    <BTd>{{ reportData.ckson.sum - stockData.ckson.sum }}</BTd>
                    <BTd class="text-start">EX-Meaty Riblets</BTd>
                    <BTd></BTd>
                    <BTd class="text-start">#15 WB s/off</BTd>
                    <BTd></BTd>
                </BTr>
                <BTr class="align-middle">
                    <BTd class="text-start">Thigh s/off</BTd>
                    <BTd>{{ reportData.ckthoff.sum }}</BTd>
                    <BTd>
                        <BFormInput size="sm" type="number" v-model="stockData.ckthoff.sum"></BFormInput>
                    </BTd>
                    <BTd>{{ reportData.ckthoff.sum - stockData.ckthoff.sum }}</BTd>
                    <BTd class="text-start">Loin Rind ON</BTd>
                    <BTd></BTd>
                    <BTd class="text-start">Butt</BTd>
                    <BTd></BTd>
                </BTr>
                <BTr class="align-middle">
                    <BTd class="text-start">Thigh s/ON</BTd>
                    <BTd>{{ reportData.ckthon.sum }}</BTd>
                    <BTd>
                        <BFormInput size="sm" type="number" v-model="stockData.ckthon.sum"></BFormInput>
                    </BTd>
                    <BTd>{{ reportData.ckthon.sum - stockData.ckthon.sum }}</BTd>
                    <BTd class="text-start">Belly B/IN</BTd>
                    <BTd></BTd>
                    <BTd class="text-start">Ribs</BTd>
                    <BTd></BTd>
                </BTr>
                <BTr class="align-middle">
                    <BTd class="text-start">Legette</BTd>
                    <BTd>{{ reportData.cklegette.sum }}</BTd>
                    <BTd>
                        <BFormInput size="sm" type="number" v-model="stockData.cklegette.sum"></BFormInput>
                    </BTd>
                    <BTd>{{ reportData.cklegette.sum - stockData.cklegette.sum }}</BTd>
                    <BTd class="text-start">Shoulder Rind ON</BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                </BTr>
                <BTr class="align-middle">
                    <BTd class="text-start">Bone IN</BTd>
                    <BTd>{{ reportData.ckbi.sum }}</BTd>
                    <BTd>
                        <BFormInput size="sm" type="number" v-model="stockData.ckbi.sum"></BFormInput>
                    </BTd>
                    <BTd>{{ reportData.ckbi.sum - stockData.ckbi.sum }}</BTd>
                    <BTd class="text-start">Middle</BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                </BTr>
                <BTr class="align-middle">
                    <BTd class="text-start">Wings</BTd>
                    <BTd>{{ reportData.ckwings.sum }}</BTd>
                    <BTd>
                        <BFormInput size="sm" type="number" v-model="stockData.ckwings.sum"></BFormInput>
                    </BTd>
                    <BTd>{{ reportData.ckwings.sum - stockData.ckwings.sum }}</BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd class="text-start">Legette r/ON</BTd>
                    <BTd></BTd>
                </BTr>
                <BTr class="align-middle">
                    <BTd class="text-start">Tdr</BTd>
                    <BTd>{{ reportData.cktdr.sum }}</BTd>
                    <BTd>
                        <BFormInput size="sm" type="number" v-model="stockData.cktdr.sum"></BFormInput>
                    </BTd>
                    <BTd>{{ reportData.cktdr.sum - stockData.cktdr.sum }}</BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd class="text-start">Drumsticks</BTd>
                    <BTd></BTd>
                </BTr>
                <BTr class="align-middle">
                    <BTd class="text-start">Mid-Wingettes</BTd>
                    <BTd>{{ reportData.ckwingette.sum }}</BTd>
                    <BTd>
                        <BFormInput size="sm" type="number" v-model="stockData.ckwingette.sum"></BFormInput>
                    </BTd>
                    <BTd>{{ reportData.ckwingette.sum - stockData.ckwingette.sum }}</BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd class="text-start">Chop s/ON</BTd>
                    <BTd></BTd>
                </BTr>
            </BTbody>
        </BTableSimple>
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
    width: 10%;
    min-width: 105px;
}

.prd-ex-item-name {
    width: 15%;
    min-width: 125px;
}

.prd-ex-item-v {
    width: 10%;
    min-width: 100px;
}
</style>
