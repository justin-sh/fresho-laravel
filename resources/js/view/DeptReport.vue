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
                    <BButton variant="outline-primary" :loading="processing" @click="generateReport">Generate Report</BButton>
                </BCol>
            </BRow>
        </template>

        <BForm>
            <BRow class="mt-2">
                <BCol sm="1">
                    <label for="arrive-at">Date</label>
                </BCol>
                <BCol sm="2">
                    <BFormInput id="arrive-at" type="date" v-model="reportDate"/>
                </BCol>
            </BRow>
            <BRow class="mt-2">
                <BCol sm="1">
                    <label for="qty">Runs</label>
                </BCol>
                <BCol sm="10">
                    <template v-for="(x, idx) in order_run">
                        <div :id="x" class="run" draggable="true" @dragstart="startDrag($event, idx)">
                            {{ x }}
                        </div>
                        <div class="drop-space"
                             @drop="onDrop($event, idx)"
                             @dragover.prevent
                             @dragleave="onDragLeave($event)"
                             @dragenter.prevent="onDragEnter($event)">
                            &nbsp;
                        </div>
                    </template>
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
                        <BFormCheckbox value="bandsaw" switch>Band Saw</BFormCheckbox>
                        <BFormCheckbox value="boning" switch>Boning</BFormCheckbox>
                        <BFormCheckbox value="frozen" switch>Frozen</BFormCheckbox>
                        <BFormCheckbox value="hotpot" switch>Hot Pot</BFormCheckbox>
                        <BFormCheckbox value="mince" switch>Mince</BFormCheckbox>
                        <BFormCheckbox value="slicer" switch>Slicer</BFormCheckbox>
                        <BFormCheckbox value="slicing_beef" switch>Slicing Beef</BFormCheckbox>
                        <BFormCheckbox value="slicing_pork" switch>Slicing Pork</BFormCheckbox>
                    </BFormCheckboxGroup>
                </BCol>
            </BRow>
            <BRow class="mt-2">
                <BCol sm="1">
                    <label for="qty">P.Status</label>
                </BCol>
                <BCol sm="11">
                    <BFormCheckboxGroup v-model="prdStatus">
                        <BFormCheckbox value="topicked" switch>To Pick</BFormCheckbox>
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
                        <BFormRadio value="PRD_TOTAL_CUS" switch>Product Totals By Customer</BFormRadio>
                        <!-- <BFormRadio value="operational-product-totals" switch>Product Totals</BFormRadio> -->
                        <BFormRadio value="picking-slip" switch>Picking Slip</BFormRadio>
                        <BFormRadio value="STICKER" switch>Product Stickers</BFormRadio>
                    </BFormRadioGroup>
                </BCol>
            </BRow>
        </BForm>

    </BCard>
</template>

<script lang="ts" setup>
import {type ReportParams, deptReport} from "../api";
import {computed, onMounted, ref, shallowRef, watch, watchEffect} from "vue";
import {useRoute, useRouter} from "vue-router";
import {formatInTimeZone} from "date-fns-tz";


const localTZ = Intl.DateTimeFormat().resolvedOptions().timeZone

const route = useRoute()
const router = useRouter()

const reportDate = shallowRef(formatInTimeZone(new Date(), localTZ, "yyyy-MM-dd"));
const status = shallowRef(['accepted'])
const order_run = shallowRef(['ED', 'EE', 'RM1', 'CT', 'S', 'N', 'LE', 'W', 'RM2', 'TTP', 'PU', 'CA', 'EA'])
const prdGroups = shallowRef(['bandsaw', 'boning', 'frozen', 'hotpot', 'slicing_beef', 'slicing_pork'])
const prdStatus = shallowRef(['topicked', 'supplied'])
const reportType = shallowRef('operational-product-totals-by-customer');

const processing = shallowRef(false)

const generateReport = async function () {

    processing.value = true
    const rptParams: ReportParams = {
        reportDate: reportDate.value,
        orderRuns: order_run.value,
        orderStatus: status.value,
        prdGroups: prdGroups.value,
        prdStatus: prdStatus.value,
        reportType: reportType.value
    }
    console.log(rptParams)

    const rv = (await deptReport(rptParams)).data
    console.log(rv)
    processing.value = false

    // processing.value = true
    //
    // try {
    //     if (isNew.value) {
    //         const rv = (await saveSo(so.value)).data
    //
    //         if (rv.ok) {
    //             await router.push({'name': 'purchaseOrder', params: {id: rv.data.id}})
    //         }
    //     } else {
    //         const rv = (await updateSo(so.value)).data
    //     }
    // } finally {
    //     processing.value = false
    // }
}



const startDrag = function (evt, idx) {
    evt.dataTransfer.dropEffect = 'move'
    evt.dataTransfer.effectAllowed = 'move'
    evt.dataTransfer.setData('idx', idx)
}

const onDrop = function (evt, toIdx) {
    if (evt.target.classList.contains("drop-space")) {
        evt.target.classList.remove("dragover");
    }

    const fromIdx = evt.dataTransfer.getData('idx')
    if (fromIdx === toIdx) return;

    const fromEl = order_run.value.at(fromIdx)
    console.log(`move item:${fromEl} from ${fromIdx} to ${toIdx}`)
    const newRun = order_run.value.toSpliced(toIdx + 1, 0, fromEl)
    console.log(newRun)
    const delIdx = fromIdx > toIdx ? fromIdx + 1 : fromIdx;
    order_run.value = newRun.toSpliced(delIdx, 1)
}

const onDragEnter = function (evt){
    evt.target.classList.add("dragover");
}

const onDragLeave = function (evt){
    if (evt.target.classList.contains("drop-space")) {
        evt.target.classList.remove("dragover");
    }
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
