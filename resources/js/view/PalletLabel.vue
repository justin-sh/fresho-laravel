<template>

    <BCard>
        <template #header>
            <div class="col align-content-center">
                <span class="fw-bold fs-4">Pallet Label</span>
            </div>
        </template>
        <template #footer>
            <BRow>
                <BCol class="d-flex justify-content-center">
                    <BButton variant="outline-primary" :loading="processing" @click="generate">Print</BButton>
                    <BButton variant="outline-primary" class="ms-5" :loading="processing" @click="clearClick">Clean
                    </BButton>
                </BCol>
            </BRow>
        </template>

        <BForm>
            <BRow>
                <BCol sm="2">
                    <label for="palletInfo">Products Information</label>
                </BCol>
                <BCol>
                    <BFormTextarea id="palletInfo" rows="6" v-model="palletInfo"/>
                </BCol>
            </BRow>
            <BRow class="mt-2">
                <BCol sm="2">
                    <label for="palletInfo">Delivery Date</label>
                </BCol>
                <BCol>
                    <BFormInput type="date" id="datepicker" class="col-4 d-inline" v-model="deliveryDate"
                                :date-format-options="{ year: 'numeric', month: 'short', day: '2-digit', weekday: 'short' }">
                    </BFormInput>
                </BCol>
            </BRow>
        </BForm>
    </BCard>
    <div id="print_labels" class="flex text-center">
        <div class="text-center page mx-auto" v-for="p in pallets">
            <h1 class="fs-1">House of Carnivore Pty Ltd</h1>
            <h2 class="mt-5">{{ p.name }} {{ p.qty }} cartons</h2>

            <div class="text-start mt-5 page-left fs-4">
                <span>PALLET Number #:</span><span class="fw-bold">{{ p.palletNo }}</span>
            </div>

            <div class="text-start page-left fs-4">
                Delivery Date: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="fw-bold">{{ p.deliveryDate }}</span>
            </div>


            <div class="text-start mt-5 page-left fs-4">
                Address： &nbsp;<span class="fw-bold">28 Tolley St, Wingfield SA 5013</span>
            </div>
            <div class="text-start page-left fs-4">
                Phone No.: <span class="fw-bold">+61 410 334 213</span>
            </div>


            <div class="text-center mt-5 fs-4 fw-bold">
                Pallet {{ p.palletIdx + 1 }}/ {{ p.palletTotal }}
            </div>

        </div>
    </div>
</template>

<script lang="ts" setup>
import {onMounted, reactive, ref, shallowRef} from "vue";
import {formatInTimeZone} from "date-fns-tz";

const localTZ = Intl.DateTimeFormat().resolvedOptions().timeZone

const palletInfo = ref<string>();
const deliveryDate = shallowRef(formatInTimeZone(new Date(), localTZ, "yyyy-MM-dd"))
const pallets = reactive<Array<{
    name: string,
    qty: number,
    palletNo: string,
    deliveryDate: string,
    palletIdx: number,
    palletTotal: number
}>>([]);
const processing = shallowRef(false)


const generate = async function () {
    processing.value = true

    const t = palletInfo.value.split('\n');

    t.forEach((v, idx) => {
        console.log(idx, v)
        const p = v.split('\t')
        console.log(p)
        pallets.push({
            name: p[4],
            qty:p[1],
            palletNo: p[0],
            deliveryDate: deliveryDate.value,
            palletIdx: idx,
            palletTotal: t.length
        })
    })

    processing.value = false

}

const clearClick = function () {
    palletInfo.value = ''
}

onMounted(function (){
})
</script>

<style scoped>
:deep(.card-header) {
    display: flex;
}

.card-header ul {
    margin-bottom: 0;
}

.page{
    width: 18cm;
    margin-top: 300px;
}

.page-left{
    margin-left: 2cm;
}

@media print {
    .card{
        display: none;
    }

    .page{
        page-break-after:always;
    }
}
</style>
