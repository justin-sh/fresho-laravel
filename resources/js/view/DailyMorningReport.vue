<template>

    <BCard class="">
        <template #header>
            <div class="row">
                <div class="col-auto">
                    <span class="fw-bold fs-4">Production Plan</span>
                </div>
                <div class="col-auto">
                    <BFormInput id="arrive-at" type="date" v-model="reportDate"/>
                </div>
                <div class="col-auto">
                    <BButton variant="outline-primary" @click="generateReport" :loading="processing"
                             :disabled="processing">
                        Refresh Data
                    </BButton>
                </div>
            </div>
        </template>

        <BRow class="mt-2">
            <BCol class="col-auto">
                <BTableSimple hover bordered class="fss text-center">
                    <BThead head-variant="dark" class="fsn">
                        <BTr class="align-middle">
                            <BTh class="prd-cat">Plan</BTh>
                            <BTh class="prd-item-name">Item</BTh>
                            <BTh class="prd-order">Order</BTh>
                            <BTh class="prd-stock">Stock</BTh>
                            <BTh class="prd-produced">Produce</BTh>
                            <BTh class="prd-produced">Status</BTh>
                        </BTr>
                    </BThead>

                    <BTbody>
                        <BTr class="align-middle" id="order-Belly">
                            <BTd :rowspan="Object.keys(porkKV).length + 1" class="text-center fw-bolder fsn">Pork
                            </BTd>
                            <BTd class="text-start" @click="showDetail('belly_fresh', 'Belly')">Belly Fresh Boning</BTd>
                            <BTd @click="showDetail('belly_fresh', 'Belly')">{{ reportData.belly_fresh.sum }}</BTd>
                            <BTd>
                                <BFormInput size="sm" type="number" v-model="stockData['belly_fresh']"></BFormInput>
                            </BTd>
                            <BTd @click="showDetail('belly_fresh', 'Belly')">{{ sumPorkPotion('belly') }}</BTd>
                            <BTd @click="showDetail('belly_fresh', 'Belly')"
                                 :class="{'text-danger': (parseFloat(stockData['belly_fresh']||'0') + sumPorkPotion('belly') - reportData.belly_fresh.sum) < 0}">
                                {{
                                    get2Decimal(parseFloat(stockData['belly_fresh'] || '0') + sumPorkPotion('belly') - reportData.belly_fresh.sum)
                                }}
                            </BTd>
                        </BTr>

                        <BTr class="align-middle" v-for="(v,k) in porkKV">
                            <BTd class="text-start" @click="showDetail(v, k)">{{ k }}</BTd>
                            <BTd @click="showDetail(v, k)">{{ reportData[v].sum }}</BTd>
                            <BTd>
                                <BFormInput size="sm" type="number" v-model="stockData[v]"></BFormInput>
                            </BTd>
                            <BTd @click="showDetail(v, k)">
                                {{ v == 'belly' ? 0 : sumPorkPotion(v) }}
                            </BTd>
                            <BTd @click="showDetail(v, k)"
                                 :class="{'text-danger': (parseFloat(stockData[v]||'0') - reportData[v].sum) < 0}">
                                {{
                                    get2Decimal(parseFloat(stockData[v] || '0') + (v == 'belly' ? 0 : sumPorkPotion(v)) - reportData[v].sum)
                                }}
                            </BTd>
                        </BTr>

                        <BTr>
                            <BTd colspan="11"></BTd>
                        </BTr>
                        <BTr class="align-middle">
                            <BTd :rowspan="Object.keys(ckKV).length + 1" class="align-middle text-center fw-bolder fsn">
                                CK
                            </BTd>
                            <BTd class="text-start" @click="showDetail('ckbr', 'Breast')">Breast</BTd>
                            <BTd @click="showDetail('ckbr', 'Breast')">{{ reportData.ckbr.sum }}</BTd>
                            <BTd>
                                <BFormInput size="sm" type="number" v-model="stockData.ckbr"></BFormInput>
                            </BTd>
                            <BTd @click="showDetail('ckbr', 'Breast')">{{ sumCkPotion('ckbr') }}</BTd>
                            <BTd @click="showDetail('ckbr', 'Breast')"
                                 :class="{'text-danger': (parseFloat(stockData.ckbr||'0') + sumCkPotion('ckbr') - reportData.ckbr.sum) < 0}">
                                {{
                                    get2Decimal(parseFloat(stockData.ckbr || '0') + sumCkPotion('ckbr') - reportData.ckbr.sum)
                                }}
                            </BTd>
                        </BTr>

                        <BTr class="align-middle" v-for="(v,k) in ckKV">
                            <BTd class="text-start" @click="showDetail(v, k)">{{ k }}</BTd>
                            <BTd @click="showDetail(v, k)">{{ reportData[v].sum }}</BTd>
                            <BTd>
                                <BFormInput size="sm" type="number" v-model="stockData[v]"></BFormInput>
                            </BTd>
                            <BTd @click="showDetail(v, k)">{{ sumCkPotion(v) }}</BTd>
                            <BTd @click="showDetail(v, k)"
                                 :class="{'text-danger': (parseFloat(stockData[v]||'0') + sumCkPotion(v) - reportData[v].sum) < 0}">
                                {{ get2Decimal(parseFloat(stockData[v] || '0') + sumCkPotion(v) - reportData[v].sum) }}
                            </BTd>
                        </BTr>
                    </BTbody>
                </BTableSimple>
            </BCol>
            <BCol class="col-3">
                <BTableSimple hover bordered class="fss text-center">
                    <BThead head-variant="dark" class="fsn">
                        <BTr class="align-middle">
                            <BTh class="text-start col-7">Side:
                                <BFormInput size="sm" type="number" v-model="porkBoning.side"></BFormInput>
                            </BTh>
                            <BTh class="text-start">F/QTR:
                                <BFormInput size="sm" type="number" v-model="porkBoning.shoulder"></BFormInput>
                            </BTh>
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

                        <BTr>
                            <BTd colspan="2" class="text-center fw-bolder fsn prd-ex-item-name">Lamb</BTd>
                        </BTr>
                        <BTr class="align-middle" v-for="(v,k) in lambSpecial" @click="showDetail(v, k)">
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
                            <BTh>Whole CK(Kg)
                                <BFormInput size="sm" type="text" v-model="wholeChickWeight" class="mt-2"></BFormInput>
                                <BInputGroup prepend="Sum:" append="Kg" class="m-0 mt-1">
                                    <BFormInput size="sm" type="number" class="bg-light"
                                                v-model="ckBoning.w"></BFormInput>
                                </BInputGroup>
                            </BTh>
                            <BTh>Supreme(ctns)
                                <BInputGroup prepend="ML s/off:">
                                    <BFormInput size="sm" type="number" v-model="ckBoning.ssoff"></BFormInput>
                                </BInputGroup>
                                <BInputGroup prepend="ML s/on:">
                                    <BFormInput size="sm" type="number" v-model="ckBoning.sson"></BFormInput>
                                </BInputGroup>
                                <BInputGroup prepend="Th s/off:">
                                    <BFormInput size="sm" type="number" v-model="ckBoning.sthoff"></BFormInput>
                                </BInputGroup>
                            </BTh>
                        </BTr>
                    </BThead>
                    <BTbody>
                        <BTr>
                            <BTd colspan="2">CK Special Order</BTd>
                        </BTr>
                        <BTr class="align-middle" v-for="(v,k) in ckSpecial" @click="showDetail(v, k)">
                            <BTd class="text-start prd-ex-item-name">{{ k }}</BTd>
                            <BTd>{{ reportData[v]?.sum === 0 ? '' : reportData[v]?.sum }}</BTd>
                        </BTr>
                    </BTbody>
                </BTableSimple>
            </BCol>
        </BRow>

        <BRow class="mt-2">
            <BTableSimple bordered hover style="table-layout: fixed;">
                <colgroup>
                    <col style="width:200px">
                    <col style="width:250px">
                    <col>
                </colgroup>
                <BTbody>
                    <BTr @click="showDetail('c15br', 'Size 15 Breast')">
                        <BTd rowspan="8">
                            Whole Chicken Size 15
                        </BTd>
                        <BTd>
                            Breast
                        </BTd>
                        <BTd>
                            {{ reportData?.c15br?.sum ?? 0 }}
                            (
                            <template v-for="v in reportData?.c15br?.details">
                                {{v.qty + '+ '}}
                            </template>
                            )
                        </BTd>
                    </BTr>
                    <BTr @click="showDetail('c15bron', 'Size 15 Breast S/ON')">
                        <BTd>
                            Breast S/On
                        </BTd>
                        <BTd>
                            {{ reportData?.c15bron?.sum ?? 0 }}
                            (
                            <template v-for="v in reportData?.c15bron?.details">
                                {{v.qty + '+ '}}
                            </template>
                            )
                        </BTd>
                    </BTr>
                    <BTr @click="showDetail('c15kiev', 'Size 15 Kiev')">
                        <BTd>
                            Breast Fillets Kiev Cut S/On
                        </BTd>
                        <BTd>
                            {{ reportData?.c15kiev?.sum ?? 0 }}
                            (
                            <template v-for="v in reportData?.c15kiev?.details">
                                {{v.qty + '+ '}}
                            </template>
                            )
                        </BTd>
                    </BTr>
                    <BTr @click="showDetail('c15barrel', 'Size 15 Barrel')">
                        <BTd>
                            Barrel Skin On
                        </BTd>
                        <BTd>
                            {{ reportData?.c15barrel?.sum ?? 0 }}
                            (
                            <template v-for="v in reportData?.c15barrel?.details">
                                {{v.qty + '+ '}}
                            </template>
                            )
                        </BTd>
                    </BTr>
                    <BTr @click="showDetail('c15thoff', 'Size 15 Thigh S/OFF')">
                        <BTd>
                            Thigh Skin Off
                        </BTd>
                        <BTd>
                            {{ reportData?.c15thoff?.sum ?? 0 }}
                            (
                            <template v-for="v in reportData?.c15thoff?.details">
                                {{v.qty + '+ '}}
                            </template>
                            )
                        </BTd>
                    </BTr>
                    <BTr @click="showDetail('c15soff', 'Size 15 ML Fillet S/OFF')">
                        <BTd>
                            Maryland Fillet Skin Off
                        </BTd>
                        <BTd>
                            {{ reportData?.c15soff?.sum ?? 0 }}
                            (
                            <template v-for="v in reportData?.c15soff?.details">
                                {{v.qty + '+ '}}
                            </template>
                            )
                        </BTd>
                    </BTr>
                    <BTr @click="showDetail('c15son', 'Size 15 ML Fillet S/ON')">
                        <BTd>
                            Maryland Fillet Skin ON
                        </BTd>
                        <BTd>
                            {{ reportData?.c15son?.sum ?? 0 }}
                            (
                            <template v-for="v in reportData?.c15son?.details">
                                {{v.qty + '+ '}}
                            </template>
                            )
                        </BTd>
                    </BTr>
                    <BTr @click="showDetail('c15chopon', 'Size 15 CHOP S/ON')">
                        <BTd>
                            CHOP Skin ON
                        </BTd>
                        <BTd>
                            {{ reportData?.c15chopon?.sum ?? 0 }}
                            (
                            <template v-for="v in reportData?.c15chopon?.details">
                                {{v.qty + '+ '}}
                            </template>
                            )
                        </BTd>
                    </BTr>
                    <BTr @click="showDetail('c20br', 'Size 20 Breast')">
                        <BTd rowspan="6">
                            Whole Chicken Size 20
                        </BTd>
                        <BTd>
                            Breast
                        </BTd>
                        <BTd>
                            {{ reportData?.c20br?.sum ?? 0 }}
                            (
                            <template v-for="v in reportData?.c20br?.details">
                                {{v.qty + '+ '}}
                            </template>
                            )
                        </BTd>
                    </BTr>
                    <BTr @click="showDetail('c20kiev', 'Size 20 Kiev')">
                        <BTd>
                            Breast Fillets Kiev Cut S/On
                        </BTd>
                        <BTd>
                            {{ reportData?.c20kiev?.sum ?? 0 }}
                            (
                            <template v-for="v in reportData?.c20kiev?.details">
                                {{v.qty + '+ '}}
                            </template>
                            )
                        </BTd>
                    </BTr>
                    <BTr @click="showDetail('c20barrel', 'Size 20 Barrel')">
                        <BTd>
                            Chicken Barrel Skin On
                        </BTd>
                        <BTd>
                            {{ reportData?.c20barrel?.sum ?? 0 }}
                            (
                            <template v-for="v in reportData?.c20barrel?.details">
                                {{v.qty + '+ '}}
                            </template>
                            )
                        </BTd>
                    </BTr>
                    <BTr @click="showDetail('c20thoff', 'Size 20 Thigh S/OFF')">
                        <BTd>
                            Thigh Skin Off
                        </BTd>
                        <BTd>
                            {{ reportData?.c20thoff?.sum ?? 0 }}
                            (
                            <template v-for="v in reportData?.c20thoff?.details">
                                {{v.qty + '+ '}}
                            </template>
                            )
                        </BTd>
                    </BTr>
                    <BTr @click="showDetail('c20soff', 'Size 20 ML Fillet S/OFF')">
                        <BTd>
                            Maryland Fillet Skin Off
                        </BTd>
                        <BTd>
                            {{ reportData?.c20soff?.sum ?? 0 }}
                            (
                            <template v-for="v in reportData?.c20soff?.details">
                                {{v.qty + '+ '}}
                            </template>
                            )
                        </BTd>
                    </BTr>
                    <BTr @click="showDetail('c20son', 'Size 20 ML Fillet S/ON')">
                        <BTd>
                            Maryland Fillet Skin ON
                        </BTd>
                        <BTd>
                            {{ reportData?.c20son?.sum ?? 0 }}
                            (
                            <template v-for="v in reportData?.c20son?.details">
                                {{v.qty + '+ '}}
                            </template>
                            )
                        </BTd>
                    </BTr>
                </BTbody>
            </BTableSimple>
        </BRow>

        <BRow>
            <BCol>
                Pork Meat Ratio:
                <span class="d-block ps-5">
                    1 side = <b>{{ porkRatio.side.bbq }}</b>kg BBQ Shoulder + <b>{{ porkRatio.side.shoulderTrim }}</b>kg Shoulder Trim
                    + <b>{{ porkRatio.side.belly }}</b>kg Belly + <b>{{ porkRatio.side.ploin }}</b>kg Loin
                    + <b>{{ porkRatio.side.bbqleg }}</b>kg BBQ Leg
                    + <b>{{ porkRatio.side.plegmeat }}</b>kg Pork Leg Meat
                    + <b>{{ porkRatio.side.pribs }}</b>kg Ribs + <b>{{ porkRatio.side.pneck }}</b>kg Neck
                </span>
                <span class="d-block ps-5">
                    1 Pork Shoulder B/IN = <b>{{ porkRatio.shoulder.bbq }}</b>kg BBQ Shoulder
                    + <b>{{ porkRatio.shoulder.shoulderTrim }}</b>kg Shoulder Trim
                    + <b>{{ porkRatio.side.pneck }}</b>kg Neck
                </span>
            </BCol>
        </BRow>
        <BRow class="mt-1">
            <BCol>
                Chicken Meat Ratio:
                <span class="ps-2">
                    Whole CK: Breast <b>31% (26.35% + 4.65%,  Tdr = BR * 15% )</b> + ML s/off <b>21%</b> + Wings <b>11%</b>
                    Mid-Wingettes: 40% from Wings, ML s/off = Thigh:65% + Legette 35%
                    <span class="d-block ps-5">Supreme: ML s/off <b>66%</b>, ML s/on <b>76%</b></span>
                </span>
            </BCol>
        </BRow>

        <BModal id="popover-m" v-model="modalShow" scrollable :title="modalTitle" ok-only size="xl">
            <BTableSimple bordered striped hover class="fs-6">
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
                            {{ (d.prd_name).substring(0, 40) }}
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
import {BButton} from "bootstrap-vue-next";


const localTZ = Intl.DateTimeFormat().resolvedOptions().timeZone

const route = useRoute()
const router = useRouter()

const reportDate = shallowRef(formatInTimeZone(new Date(), localTZ, "yyyy-MM-dd"));
const status = shallowRef(['accepted'])
const showAll = shallowRef(false)

const wholeChickWeight = shallowRef('0')

const porkBoning = ref({"side": 0, "shoulder": 0})
const porkRatio = {
    side: {bbq: 2.7, shoulderTrim: 1.7, belly: 6, bbqleg: 6, plegmeat: 1.3, ploinrindoff: 2.3, pneck: 1.85, pribs: 1.2},
    shoulder: {bbq: 2.7, shoulderTrim: 1.7, belly: 0, bbqleg: 0, plegmeat: 0, pneck: 1.85, pribs: 0}
}

const ckBoning = ref({"w": 0, "s": 0, "ssoff": 0, "sson": 0, "sthoff": 0})
const ckRatio = {
    w: {ckbr: 0.31 - 0.0465, cksoff: 0.21, ckwings: 0.11, cktdr: 0.0465},
    s: {ckbr: 0, cksoff: 0.66, ckwings: 0, cktdr: 0},
    ssoff: {ckbr: 0, cksoff: 0.66, ckwings: 0, cktdr: 0},
    sson: {ckbr: 0, ckson: 0.76, ckwings: 0, cktdr: 0},
    sthoff: {ckbr: 0, ckthoff: 0.66 * 0.66, cklegette: 0.66 * 0.34, cktdr: 0}
}

const porkKV = {
    'Belly': 'belly',
    'Belly R/Off': 'bellyROff',
    'Loin Rind OFF': 'ploinrindoff',
    'Loin Rind ON': 'ploinrindon',
    'BBQ': 'bbq',
    'BBQ(Leg)': 'bbqleg',
    'Ribs': 'pribs',
    'Leg B/L': 'plegmeat',
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
    'Belly B/IN': 'bellyBoneIn',
    'Shoulder Rind ON': 'pshrindon',
    'Middle': 'pmiddle',
    'Fat': 'pfat',
}

const ckSpecial = {
    '#16 Thigh s/off': 'ckthoff16',
    '#22 Thigh s/off': 'ckthoff22',
    '#28 Thigh s/off': 'ckthoff28',
    '#16 Thigh s/ON': 'ckthon16',
    '#22 Thigh s/ON': 'na',
    '#28 Thigh s/ON': 'na',
    'Br Kiev s/ON': 'ckbron',
    'Barrel s/On': 'ckbron2',
    'Butterfly Cut': 'ckbrbf',
    '#15 WB s/off': 'ckwboff15',
    'Butt': 'ckbutt',
    'Ribs': 'ckrib',
    'Legette r/ON': 'cklegetteon',
    'Drumsticks': 'ckdrumstick',
    'Chop s/ON	': 'ckchopon',
}

const lambSpecial = {
    'Shoulder': 'lshoulder',
    'Leg': 'lleg',
    'Rump': 'lrump',
}

const ckKV = {
    'ML s/off': 'cksoff',
    'ML s/ON': 'ckson',
    'Thigh s/off': 'ckthoff',
    'Thigh s/ON': 'ckthon',
    'Legette': 'cklegette',
    // 'Bone IN': 'ckbi',
    'Wings': 'ckwings',
    'Tdr': 'cktdr',
    'Mid-Wingettes': 'ckwingette',
}

const reportData = ref({
    "belly": {
        "sum": 0,
        "details": []
    },
    "belly_fresh": {
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
    "ploinrindoff": {
        "sum": 0,
        "details": []
    },
    "ploinrindon": {
        "sum": 0,
        "details": []
    },
    "bbq": {
        "sum": 0,
        "details": []
    },
    "bbqleg": {
        "sum": 0,
        "details": []
    },
    "pribs": {
        "sum": 0,
        "details": []
    },
    "plegmeat": {
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
    "others": {}
})

const zero = ref(0)
const stockData = ref({
    "belly_fresh": 0,
    "belly": 0,
    "bellyROff": 0,
    "bellyBoneIn": 0,
    "ploinrindoff": 0,
    "ploinrindon": 0,
    "bbq": 0,
    "bbqleg": 0,
    "plegmeat": 0,
    "pribs": 0,
    "pneck": 0,
    "ckbr": 0,
    "cksoff": 0,
    "ckson": 0,
    "ckthoff": 0,
    "ckthon": 0,
    "cklegette": 0,
    "ckwings": 0,
    "ckwingette": 0,
    "cktdr": 0
})
const processing = shallowRef(false)

const generateReport = async function () {

    processing.value = true
    // console.log(reportDate.value)

    const rv = (await dailyReport(reportDate.value)).data
    // console.log(rv)
    reportData.value = rv.data
    stockData.value = rv.dailyStock
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

const toggleProduct = function (product: string) {
    // const hPrds = getHidenProducts()
    console.log(product)

    // if(new Set(hPrds.value).has(product)){
    //     return
    // }

    const idx = hPrds.value.indexOf(product)
    if (idx === -1) {
        hPrds.value.push(product)
    } else {
        hPrds.value.splice(idx, 1)
    }

    localStorage.setItem('dmrHidenPrds', JSON.stringify(hPrds.value))
}

const getHidenProducts = function () {
    return JSON.parse(localStorage.getItem('dmrHidenPrds') ?? '[]')
}

const get2Decimal = function (num) {
    return Math.round((num + Number.EPSILON) * 100) / 100
}

const sumPorkPotion = function (portion) {
    const sideRitio = porkRatio['side'][portion] || 0
    const shRitio = porkRatio['shoulder'][portion] || 0
    const ssd = sideRitio * parseInt(porkBoning.value.side.toString().trim() || '0')
    const ssh = shRitio * parseInt(porkBoning.value.shoulder.toString().trim() || '0')
    return get2Decimal(ssd + ssh)
}

const sumCkPotion = function (portion) {
    const wholeChickenRitio = ckRatio['w'][portion] || 0
    const supremeForMLsoffRatio = ckRatio['ssoff'][portion] || 0
    const supremeForMLsonRatio = ckRatio['sson'][portion] || 0
    const supremeForThsoffRatio = ckRatio['sthoff'][portion] || 0
    const wholeCkWeight = wholeChickenRitio * parseInt(ckBoning.value.w.toString().trim() || '0')
    const supremeMLOffWeight = supremeForMLsoffRatio * 12 * parseInt(ckBoning.value.ssoff.toString().trim() || '0')
    const supremeMLOnWeight = supremeForMLsonRatio * 12 * parseInt(ckBoning.value.sson.toString().trim() || '0')
    const supremeThOffWeight = supremeForThsoffRatio * 12 * parseInt(ckBoning.value.sthoff.toString().trim() || '0')
    return get2Decimal(wholeCkWeight + supremeMLOffWeight + supremeMLOnWeight + supremeThOffWeight)
}

const hPrds = ref([])

watchEffect(() => {
    ckBoning.value.w = wholeChickWeight.value.split(/[\s+,]+/)
        .filter(Boolean)
        .map(Number)
        .reduce((total, n) => total + n, 0)
});

onMounted(async function () {
    hPrds.value = getHidenProducts()
})
</script>

<style scoped>

.fss {
    font-size: 0.75rem;
}

.fsn {
    font-size: 1rem;
}

tbody tr {
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
    width: 4%;
    min-width: 50px;
}

.prd-ex-item-name {
    width: 55%;
    min-width: 125px;
    line-height: 31px;
}

</style>
