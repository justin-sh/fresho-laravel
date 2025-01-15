<template>
    <BTableSimple bordered>
        <BThead>
            <BTr>
                <BTh class="col">#</BTh>
                <BTh class="col-3">Docs</BTh>
                <BTh class="col" v-for="d in drivers">
                    {{ d }}
                </BTh>
            </BTr>
        </BThead>
        <BTbody>
            <BTr v-for="(doc, idx) in docs">
                <BTd class="col"> {{ idx + 1 }}</BTd>
                <BTd class="col-3">
                    <RouterLink :to="{name: 'driverTrainings.s011crm'}">{{ doc.name }}</RouterLink>
                </BTd>
                <BTh class="col" v-for="d in drivers">
                    <BFormCheckbox disabled :value="d" v-model="doc.eSigned[d]"></BFormCheckbox>
                </BTh>
            </BTr>
        </BTbody>
    </BTableSimple>
</template>

<script lang="ts" setup>
import {ref} from "vue";

const drivers = ref(['Saul', 'Chris', 'Rahman', 'Zou Peng', 'Kelvin', 'Wayne', 'Leo', 'Chen Wen', 'Jing Chao', 'Xie Yujie', 'Jiang Yuan', 'Tim']);

const docs = ref([{
    'name': 'S011CRM Training',
    'pathName': 'driverTrainings.s011crm',
    'eSigned': {'Saul': 'Saul'}
},])

</script>
