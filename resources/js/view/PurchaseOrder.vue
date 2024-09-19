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
            <BRow>
                <BCol sm="1">
                    <label for="arrive-at">Arrive At</label>
                </BCol>
                <BCol sm="4">
                    <BFormInput id="arrive-at"></BFormInput>
                </BCol>
            </BRow>
        </BForm>

        <BTable id="po-list" striped hover>

        </BTable>
    </BCard>
</template>

<script lang="ts" setup>
import {getPoList} from "../api";
import {onMounted, ref, shallowRef} from "vue";
import {useRoute} from "vue-router";

const route = useRoute()

const pageTitle = shallowRef('')
const data = shallowRef([])

const load_data = async function () {
    data.value = (await getPoList()).data.data
}

onMounted(function (){
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
