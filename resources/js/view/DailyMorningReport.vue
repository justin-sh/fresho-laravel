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
                    <BTd>1</BTd>
                    <BTd></BTd>
                    <BTd>1000</BTd>
                    <BTd rowspan="7"></BTd>
                    <BTd colspan="2" class="text-center fw-bolder fsn">Pork Special Order</BTd>
                    <BTd rowspan="7"></BTd>
                    <BTd class="text-start prd-ex-item-name">#16 Thigh s/off</BTd>
                    <BTd class="prd-ex-item-v"></BTd>
                </BTr>
                <BTr>
                    <BTd class="text-start">Belly R/Off</BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd class="text-start">Meaty Ribs</BTd>
                    <BTd></BTd>
                    <BTd class="text-start">#22 Thigh s/off</BTd>
                    <BTd></BTd>
                </BTr>
                <BTr>
                    <BTd class="text-start">Belly B/IN</BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd class="text-start">Cutlet</BTd>
                    <BTd></BTd>
                    <BTd class="text-start">#28 Thigh s/off</BTd>
                    <BTd></BTd>
                </BTr>
                <BTr>
                    <BTd class="text-start">BBQ</BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd class="text-start">Meaty Riblets</BTd>
                    <BTd></BTd>
                    <BTd class="text-start">#16 Thigh s/ON</BTd>
                    <BTd></BTd>
                </BTr>
                <BTr>
                    <BTd class="text-start">Leg</BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd class="text-start">Meaty Leg Bone</BTd>
                    <BTd></BTd>
                    <BTd class="text-start">#22 Thigh s/ON</BTd>
                    <BTd></BTd>
                </BTr>
                <BTr>
                    <BTd class="text-start">Loin</BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd class="text-start">Meaty Neck Bone</BTd>
                    <BTd></BTd>
                    <BTd class="text-start">#28 Thigh s/ON</BTd>
                    <BTd></BTd>
                </BTr>
                <BTr>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd>1</BTd>
                    <BTd class="text-start">EX-Meaty Ribs</BTd>
                    <BTd></BTd>
                    <BTd class="text-start">#16 Br s/ON, Tdr/off</BTd>
                    <BTd>2</BTd>
                </BTr>
                <BTr>
                    <BTd colspan="11"></BTd>
                </BTr>
                <BTr>
                    <BTd rowspan="10" class="align-middle text-center fw-bolder fsn">CK</BTd>
                    <BTd class="text-start">Breast</BTd>
                    <BTd>1</BTd>
                    <BTd>2</BTd>
                    <BTd>6</BTd>
                    <BTd rowspan="10"></BTd>
                    <BTd class="text-start">EX-Meaty Leg Bone</BTd>
                    <BTd></BTd>
                    <BTd rowspan="10"></BTd>
                    <BTd class="text-start">Butterfly Cut</BTd>
                    <BTd></BTd>
                </BTr>
                <BTr>
                    <BTd class="text-start">ML s/off</BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd class="text-start">EX-Meaty Neck Bone</BTd>
                    <BTd></BTd>
                    <BTd class="text-start">#16 Kiev Cut s/ON</BTd>
                    <BTd></BTd>
                </BTr>
                <BTr>
                    <BTd class="text-start">ML s/ON</BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd class="text-start">EX-Meaty Riblets</BTd>
                    <BTd></BTd>
                    <BTd class="text-start">#15 WB s/off</BTd>
                    <BTd></BTd>
                </BTr>
                <BTr>
                    <BTd class="text-start">Thigh s/off</BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd class="text-start">Loin Rind ON</BTd>
                    <BTd></BTd>
                    <BTd class="text-start">Butt</BTd>
                    <BTd></BTd>
                </BTr>
                <BTr>
                    <BTd class="text-start">Thigh s/ON</BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd class="text-start">Belly B/IN</BTd>
                    <BTd></BTd>
                    <BTd class="text-start">Ribs</BTd>
                    <BTd></BTd>
                </BTr>
                <BTr>
                    <BTd class="text-start">Legette</BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd class="text-start">Shoulder Rind ON</BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                </BTr>
                <BTr>
                    <BTd class="text-start">Bone IN</BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd class="text-start">Middle</BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                </BTr>
                <BTr>
                    <BTd class="text-start">Wings</BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd class="text-start">Legette r/ON</BTd>
                    <BTd></BTd>
                </BTr>
                <BTr>
                    <BTd class="text-start">Tdr</BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd class="text-start">Drumsticks</BTd>
                    <BTd></BTd>
                </BTr>
                <BTr>
                    <BTd class="text-start">Mid-Wingettes</BTd>
                    <BTd></BTd>
                    <BTd></BTd>
                    <BTd></BTd>
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
import {dailyReport, deptReport, type ReportParams} from "../api";
import {onMounted, shallowRef, watchEffect} from "vue";
import {useRoute, useRouter} from "vue-router";
import {formatInTimeZone} from "date-fns-tz";


const localTZ = Intl.DateTimeFormat().resolvedOptions().timeZone

const route = useRoute()
const router = useRouter()

const reportDate = shallowRef(formatInTimeZone(new Date(), localTZ, "yyyy-MM-dd"));
const status = shallowRef(['accepted'])
const order_run = shallowRef(['ED', 'EE', 'RM1', 'CT', 'S', 'N', 'LE', 'W', 'RM2', 'TTP', 'PU', 'CA', 'EA'])
const prdGroups = shallowRef(['Band Saw', 'Boning', 'Frozen Products', 'Hot Pot', 'Slicing Beef', 'Slicing Chicken'])
const prdStatus = shallowRef(['topicked', 'supplied'])
const reportType = shallowRef('dept-report');

const processing = shallowRef(false)

const generateReport = async function () {

    processing.value = true
    console.log(reportDate.value)

    const rv = (await dailyReport(reportDate.value)).data
    console.log(rv)
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
    width: 15%;
}

.prd-stock {
    width: 5%;
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
