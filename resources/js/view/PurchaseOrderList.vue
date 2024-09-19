<template>

    <BCard>
        <template #header>
            <div class="col align-content-center" ref="tableHeaderRefEl">
                <span class="fw-bold fs-4">Purchase Orders </span>
<!--                <span class="inline fw-light fs-6" v-if="!data_loading">(Total {{ data.length }})</span>-->
            </div>

            <BFormRadioGroup v-model="page_size" :options="page_size_options" class="ms-3 align-content-center"
                             value-field="item" text-field="name"/>
        </template>
        <template #footer>
            <BPagination v-model="currentPage" :total-rows="data.length" :per-page="page_size" limit="7"
                         @update:model-value="goTableHead" aria-controls="po-list"></BPagination>
        </template>

        <BTable id="po-list" striped hover>

        </BTable>
    </BCard>
</template>

<script lang="ts" setup>
import {getPoList} from "../api";
import {onMounted, ref, shallowRef} from "vue";

const currentPage = shallowRef(1)
const page_size = shallowRef(50)
const page_size_options = [
    {item: 50, name: '50'},
    {item: 100, name: '100'},
    {item: 999, name: 'all'}
]

const data = shallowRef([])

const load_data = async function () {
    data.value = (await getPoList()).data.data
}

const tableHeaderRefEl = ref<HTMLElement | null>(null)

const goTableHead = (page: number) => {
    tableHeaderRefEl.value?.scrollIntoView({behavior: 'smooth'})
}

onMounted(load_data)
</script>

<style scoped>
:deep(.card-header) {
    display: flex;
}

.card-header ul {
    margin-bottom: 0;
}
</style>
