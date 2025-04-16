<template>

    <BCard>
        <template #header>
            <div class="col align-content-center">
                <span class="fw-bold fs-4">Dept Report / Pick Slip</span>
            </div>
        </template>
        <template #footer>
            <BRow>
                <BCol sm="12" class="d-flex justify-content-center">
                    <BButton variant="outline-primary" :loading="processing" :disabled="processing" @click="generateReport">Generate Report
                    </BButton>
                </BCol>
            </BRow>
        </template>

        <BForm>
            <BRow class="mt-2">
                <BCol sm="1">
                    <label for="arrive-at">Date</label>
                </BCol>
                <BCol sm="4" md="3" class="col-6">
                    <BFormInput id="arrive-at" type="date" v-model="reportDate"/>
                </BCol>
            </BRow>
            <BRow class="mt-2">
                <BCol sm="1">
                    <label for="qty">Runs</label>
                </BCol>
                <BCol sm="11">
                    <BFormCheckboxGroup v-model="orderRuns">
                        <template v-for="(x) in orderRunLoop">
                            <BFormCheckbox :value="x" switch>
                                {{ x }}
                            </BFormCheckbox>
                        </template>
                    </BFormCheckboxGroup>
                </BCol>
            </BRow>
            <BRow class="mt-2">
                <BCol sm="1">
                    <label for="qty">Status</label>
                </BCol>
                <BCol sm="11">
                    <BFormCheckboxGroup v-model="status">
                        <BFormCheckbox value="in_progress" switch>Process</BFormCheckbox>
                        <BFormCheckbox value="submitted" switch>Submitted</BFormCheckbox>
                        <BFormCheckbox value="accepted" switch>Accepted</BFormCheckbox>
                        <BFormCheckbox value="invoiced" switch>Invoiced</BFormCheckbox>
                        <BFormCheckbox value="paid" switch>Paid</BFormCheckbox>
                        <BFormCheckbox value="cancelled" switch>Cancelled</BFormCheckbox>
                    </BFormCheckboxGroup>
                </BCol>
            </BRow>
            <BRow class="mt-2">
                <BCol sm="1">
                    <label for="qty">P.Groups</label>
                </BCol>
                <BCol sm="11">
                    <BFormCheckboxGroup v-model="prdGroups">
                        <BFormCheckbox value="Band Saw" switch>Band Saw</BFormCheckbox>
                        <BFormCheckbox value="Boning" switch>Boning</BFormCheckbox>
                        <BFormCheckbox value="Frozen Products" switch>Frozen</BFormCheckbox>
                        <BFormCheckbox value="Hot Pot" switch>Hot Pot</BFormCheckbox>
                        <BFormCheckbox value="Mince" switch>Mince</BFormCheckbox>
                        <BFormCheckbox value="Slicer" switch>Slicer</BFormCheckbox>
                        <BFormCheckbox value="Slicing Beef" switch>Slicing Beef</BFormCheckbox>
                        <BFormCheckbox value="Slicing Chicken" switch>Slicing Chicken</BFormCheckbox>
                    </BFormCheckboxGroup>
                </BCol>
            </BRow>
            <BRow class="mt-2">
                <BCol sm="1">
                    <label for="qty">P.Status</label>
                </BCol>
                <BCol sm="11">
                    <BFormCheckboxGroup v-model="prdStatus">
                        <BFormCheckbox value="to_pick" switch>To Pick</BFormCheckbox>
                        <BFormCheckbox value="supplied" switch>Supplied</BFormCheckbox>
                        <BFormCheckbox value="na" switch>N/A</BFormCheckbox>
                        <BFormCheckbox value="backorder" switch>Back Order</BFormCheckbox>
                        <BFormCheckbox value="subnstituted" switch>Substituted</BFormCheckbox>
                    </BFormCheckboxGroup>
                </BCol>
            </BRow>
            <BRow class="mt-2">
                <BCol sm="1">
                    <label for="qty">R.Type</label>
                </BCol>
                <BCol sm="11">
                    <BFormRadioGroup v-model="reportType">
                        <BFormRadio value="dept-report" switch>Product Totals By Customer</BFormRadio>
                        <!-- <BFormRadio value="operational-product-totals" switch>Product Totals</BFormRadio> -->
                        <BFormRadio value="picking-slip" switch>Picking Slip</BFormRadio>
                        <!--                        <BFormRadio value="sticker" switch>Product Stickers</BFormRadio>-->
                    </BFormRadioGroup>
                </BCol>
            </BRow>
        </BForm>

    </BCard>
</template>

<script lang="ts" setup>
import {deptReport, type ReportParams} from "../api";
import {onMounted, ref, shallowRef, toRaw, watchEffect} from "vue";
import {useRoute, useRouter} from "vue-router";
import {formatInTimeZone} from "date-fns-tz";
import {parse, isTuesday, isThursday} from "date-fns"


const localTZ = Intl.DateTimeFormat().resolvedOptions().timeZone

const route = useRoute()
const router = useRouter()

const reportDate = shallowRef(formatInTimeZone(new Date(), localTZ, "yyyy-MM-dd"));

const status = shallowRef(['accepted', 'submitted'])
const orderRuns = ref(['EDN', 'EDS', 'EE', 'RM1', 'S', 'CT', 'N', 'LE', 'W', 'RM2', 'TTP', 'PU', 'CA', 'EA']);
const orderRunLoop = ref(['EDN', 'EDS', 'EE', 'RM1', 'S', 'CT', 'N', 'LE', 'W', 'RM2', 'TTP', 'PU', 'CA', 'EA'])
const prdGroups = shallowRef(['Band Saw', 'Boning', 'Frozen Products', 'Hot Pot', 'Slicing Beef', 'Slicing Chicken'])
const prdStatus = shallowRef(['to_pick', 'supplied'])
const reportType = shallowRef('dept-report');

const processing = shallowRef(false)

const generateReport = async function () {

    processing.value = true

    const oRuns = toRaw(orderRuns.value)
    const runs = toRaw(orderRunLoop.value).filter(x=>oRuns.includes(x))

    const rptParams: ReportParams = {
        reportDate: reportDate.value,
        orderRuns: runs,
        orderStatus: status.value,
        prdGroups: prdGroups.value,
        prdStatus: prdStatus.value,
        reportType: reportType.value
    }

    try{
        (await deptReport(rptParams)).data
    }finally {
        processing.value = false
    }
}



watchEffect(() => {

    const curDate = parse(reportDate.value, 'yyyy-MM-dd', new Date())
    const tueOrThur = isTuesday(curDate) || isThursday(curDate);
    if(tueOrThur){
        orderRunLoop.value[4] = 'CT'
        orderRunLoop.value[5] = 'S'
    }else{
        orderRunLoop.value[4] = 'S'
        orderRunLoop.value[5] = 'CT'
    }

});


onMounted(async function () {

})
</script>

<style scoped>
:deep(.card-header) {
    display: flex;
}

.card-header ul {
    margin-bottom: 0;
}

.run {
    min-width: 40px;
    text-align: center;
    padding: 5px;
    display: inline-block;
    background-color: #b6d4fe;
    border-radius: 15%;
    cursor: grab;
}

.drop-space {
    display: inline-block;
    padding: 5px;
    min-width: 15px;
    border-radius: 15%;
    text-align: center;
}

.drop-space.dragover {
    background-color: #0937ff;
}
</style>
