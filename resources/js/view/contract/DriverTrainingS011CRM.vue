<template>

    <BRow>
        <BCol class="text-center fw-bolder fs-1">S011CRM Training</BCol>
    </BRow>
    <BRow>
        <BCol>
            Make sure follow the below instructions while you drive the UTE S011CRM:
        </BCol>
    </BRow>
    <ol>
        <li>
            Fuel: <span class="fw-bolder">Petrol, #91</span>
        </li>
        <li>
            Turn the <span class="fw-bolder">refrigerator</span> OFF and engine OFF every time once arriving the destination
        </li>
    </ol>


    <BRow>
        <BCol>
            Actions will be applied if failed to follow these instructions. Included but not limited:
        </BCol>
    </BRow>
    <ol>
        <li>Incident Report</li>
        <li>Cost to fix the consequences after the violation</li>
    </ol>



    <BRow class="mt-4">
        <BCol>
            Please sign after reading and understanding all the words:
        </BCol>
    </BRow>
    <BRow>
        <BCol cols="auto"  class="align-middle my-auto">
            Accepted by:
        </BCol>
        <BCol cols="3">
            <BInput></BInput>
        </BCol>
    </BRow>
    <BRow class="mt-2">
        <BCol>
            <vue-esign class="sign-view" ref="esign" :width="400" :height="150"/>
        </BCol>
    </BRow>
    <BRow>
        <BCol cols="2">
            <BButton variant="success">Confirm</BButton>
        </BCol>
        <BCol cols="2">
            <BButton variant="outline-primary" @click="$refs.esign.reset()">Clear</BButton>
        </BCol>
    </BRow>
</template>

<style scoped>
.sign-view {
    border: 2px solid #ccc;
    margin-bottom: 10px;
}
</style>
