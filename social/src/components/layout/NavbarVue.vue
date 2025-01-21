<template>
    <nav :class="color || 'black'">
        <div class="nav-wrapper container">
            <router-link :to="url || '/'" class="brand-logo">{{ logo || 'Site' }}</router-link>
            <!-- <router-link :to="url || '/'" class="brand-logo">{{ $store.getters.getName }}</router-link> -->
            <ul id="nav-mobile" class="right hide-on-med-and-down">
                <slot></slot>
            </ul>
        </div>
    </nav>
</template>

<script>
export default {
    name: 'NavbarVue',
    props: ['logo', 'url', 'color'],
    data() {
        return {

        }
    },
    // created(){
    //     this.$store.commit('setNome', 'Outro nome')
    // }
}
</script>