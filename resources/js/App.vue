<template>
    <header>
        <div>
            <BNavbar toggleable="md" type="dark" variant="info">
                <BNavbarBrand>Fresho Ext</BNavbarBrand>

                <BNavbarToggle target="nav-collapse"></BNavbarToggle>

                <BCollapse id="nav-collapse" is-nav>
                    <BNavbarNav>
                        <BNavItem :to="{name:'home'}">
                            Home
                        </BNavItem>
                        <BNavItem :to="{name:'orders'}">
                            Orders
                        </BNavItem>
                        <BNavItem :to="{name:'saleOrders'}">
                            Sale Orders
                        </BNavItem>
                        <BNavItem :to="{name:'purchaseOrders'}">
                            Purchase Orders
                        </BNavItem>
                        <BNavItem :to="{name:'products'}">
                            Products
                        </BNavItem>
                    </BNavbarNav>

                    <BNavbarNav>
                        <BNavItemDropdown>
                            <template #button-content>
                                New +
                            </template>
                            <BDropdownItem :to="{name:'saleOrderNew'}">
                                new Sale Order
                            </BDropdownItem>
                            <BDropdownItem :to="{name:'purchaseOrderNew'}">
                                new Purchase Order
                            </BDropdownItem>
                            <BDropdownItem :to="{name:'purchaseOrderNew'}">
                                new Product
                            </BDropdownItem>
                        </BNavItemDropdown>
                    </BNavbarNav>

                    <BNavbarNav class="ml-auto">
                        <BNavItemDropdown right>
                            <template #button-content>
                                <em>User</em>
                            </template>
                            <BDropdownItem href="#">Profile</BDropdownItem>
                            <BDropdownItem href="#">Sign Out</BDropdownItem>
                        </BNavItemDropdown>
                    </BNavbarNav>
                </BCollapse>
            </BNavbar>
        </div>
    </header>

    <div class="container">
        <RouterView/>
    </div>
    <footer>
        <div class="d-flex bg-info bg-gradient mt-2 p-2 justify-content-center">Fresho Extension @Justin</div>
    </footer>
</template>

<script setup>

import {getWarehousesWithFilters} from "@/api/index";
import {onMounted} from "vue";
console.log('app.vue 00')
onMounted(async function () {
    console.log('app.vue 11')
    const wh = (await getWarehousesWithFilters()).data.data

    console.log(wh)
})

</script>
