<template>

    <BRow>
        <BCol class="text-center fw-bolder fs-1">S011CRM Training</BCol>
    </BRow>
    <ol>
        <li>
            112132132
            <ol type="a">
                <li>fdad</li>
                <li>fdaddfdasf</li>
            </ol>
        </li>
        <li>12</li>
    </ol>

    <BRow>
        <BCol>
            <vue-esign class="sign-view" ref="esign" :width="800" :height="300"/>
        </BCol>
    </BRow>
    <BRow>
        <BCol cols="2">
            <BButton variant="success">Confirm</BButton>
        </BCol>
        <BCol cols="2">
            <BButton variant="outline-primary" @click="$refs.esign.reset()">Re-Sign</BButton>
        </BCol>
    </BRow>
</template>

<style scoped>
.sign-view {
    border: 2px solid #ccc;
    margin-bottom: 10px;
}
</style>
