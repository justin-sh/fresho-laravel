<template>

    <BCard>
        <template #header>
            <div class="col align-content-center">
                <span class="fw-bold fs-4">Purchase Orders</span>
                <span class="ms-2">{{ pageTitle }}</span>
            </div>
        </template>
        <template #footer>
        </template>

        <BForm>
            <BRow>
                <BCol sm="1">
                    <label for="title">Title</label>
                </BCol>
                <BCol sm="4">
                    <BFormInput id="title"></BFormInput>
                </BCol>
            </BRow>
            <BRow class="mt-2">
                <BCol sm="1">
                    <label for="arrive-at">Arrive At</label>
                </BCol>
                <BCol sm="2">
                    <BFormInput id="arrive-at" type="date"></BFormInput>
                </BCol>
            </BRow>
            <BRow class="mt-2">
                <BCol sm="1">
                    <label for="qty">Init Qty</label>
                </BCol>
                <BCol sm="2">
                    <BFormInput id="qty" type="number"></BFormInput>
                </BCol>
            </BRow>
            <BRow class="mt-2">
                <BCol sm="1">
                    <label for="status">Status</label>
                </BCol>
                <BCol sm="2">
                    <BFormInput id="status"></BFormInput>
                </BCol>
            </BRow>
        </BForm>

        <BTable id="po-list" striped hover>

        </BTable>
    </BCard>
</template>

<script lang="ts" setup>
import {getPoList, type PurchaseOrder} from "../api";
import {onMounted, ref, toRaw, shallowRef} from "vue";
import {useRoute} from "vue-router";

const route = useRoute()

const pageTitle = shallowRef('')
// const data = shallowRef([])
const po = ref<PurchaseOrder>({});

console.log(po.value)
console.log(po.value.id)

const load_data = async function () {
    // data.value = (await getPoList()).data.data
}

let x;

const x1 = shallowRef([1,2]);

onMounted(function (){

    // const {value} = x1.value

    // console.log(value)

    x = x1.value.slice()

    console.log(x)
    x1.value.push(3);
    console.log('x='+x)
    console.log(x)
    console.log('x='+x)

    if('purchaseOrderNew' == route.name){
        pageTitle.value = 'New'
    }else{
        pageTitle.value = "update"
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
