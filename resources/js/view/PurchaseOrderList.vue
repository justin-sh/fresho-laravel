<template>

    <BCard>
        <template #header>
            <div class="col align-content-center" ref="tableHeaderRefEl">
                <span class="fw-bold fs-4">Purchase Orders </span>
            </div>

            <BFormRadioGroup v-model="page_size" :options="page_size_options" class="ms-3 align-content-center"
                             value-field="item" text-field="name"/>
        </template>
        <template #footer>
            <BPagination v-model="currentPage" :total-rows="data.length" :per-page="page_size" limit="7"
                         @update:model-value="goTableHead" aria-controls="po-list"></BPagination>
        </template>

        <BTable id="po-list" striped hover :busy="loading"
                :sort-by="[{key: 'arrivalAt', order: 'desc'},{key: 'title', order: 'asc'}]"
                :current-page="currentPage"
                :per-page="page_size"
                :items="data"
                :fields="fields"
                @rowClicked="gotoDetail">
            <template #cell(rowNo)="row">
                {{ row.index + 1 }}
            </template>
        </BTable>
    </BCard>
</template>

<script lang="ts" setup>
import {getPoList} from "../api";
import {onMounted, ref, shallowRef} from "vue";
import {TableItem} from "bootstrap-vue-next";
import {useRouter} from "vue-router";

const router = useRouter()

const loading = ref(false)
const currentPage = shallowRef(1)
const page_size = shallowRef(50)
const page_size_options = [
    {item: 50, name: '50'},
    {item: 100, name: '100'},
    {item: 999, name: 'all'}
]

const fields = [
    {key: 'rowNo', label: '#'},
    {key: 'title', label: 'Title', sortable: true},
    {key: 'qty', label: 'Qty'},
    {key: 'state', label: 'State'},
    {key: 'arrivalAt', label: 'Arrival At', sortable: true},
    {key: 'wh.code', label: 'Warehouse', sortable: true},
]

const data = shallowRef([])

const load_data = async function () {
    loading.value = true
    data.value = (await getPoList()).data.data
    loading.value = false
}

const tableHeaderRefEl = ref<HTMLElement | null>(null)

const goTableHead = (page: number) => {
    tableHeaderRefEl.value?.scrollIntoView({behavior: 'smooth'})
}

const gotoDetail = (item: TableItem, index: number, event: MouseEvent) => {
    router.push({"name": 'purchaseOrder', "params": {"id": item.id}})
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

:deep(tr) {
    cursor: pointer;
}
</style>
