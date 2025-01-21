<template>
    <div :class="'col s' + size">
        <slot></slot>
    </div>
</template>

<script>
export default {
    name: 'GridVue',
    props: ['size']
}
</script>

<style></style>