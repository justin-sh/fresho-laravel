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
                    <BButton variant="outline-primary" :loading="processing" @click="generate">Generate</BButton>
                    <BButton variant="outline-primary" :loading="processing" @click="generate">Print</BButton>
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
        </BForm>
    </BCard>
    <div id="print_labels">
        <div class="text-center">
            <h1>Pork Belly Rindless Rivalea 48 cartons</h1>

            <div>
                <span>PALLET Number #:</span><span>HOC-0010</span>
            </div>
            <div>
                Delivery Date:         10/13/2025
            </div>


            <div>
            Address：  28 Tolley St, Wingfield SA 5013
            Phone No.: +61 410 334 213
            </div>


            <div>
            Pallet  04/ 04
            </div>

        </div>
    </div>
</template>

<script lang="ts" setup>
import {ref, shallowRef} from "vue";

const localTZ = Intl.DateTimeFormat().resolvedOptions().timeZone

const palletInfo = ref<string>();
const processing = shallowRef(false)


const generate = async function () {
    processing.value = true

    const t = palletInfo.value.split('\n');

    t.forEach((v,idx)=>{
        console.log(idx, v)
    })


    try {
    } finally {
        processing.value = false
    }
}
</script>

<style scoped>
:deep(.card-header) {
    display: flex;
}

.card-header ul {
    margin-bottom: 0;
}
</style>
