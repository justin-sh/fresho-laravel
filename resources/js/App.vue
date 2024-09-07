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


