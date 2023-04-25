<style lang="scss" scoped>
.label {
    font-size: 14px;
    padding: 1px 6px;
}
</style>

<template>
    <div class="formComponent" :class="classes">
        <div class="label" :style="labelStyle" v-html="label" />
        <slot />
    </div>
</template>

<script>
    export default {
        name: "FormComponent",
        props: {
            label: String,
            labelStyle: {
                type: Object,
                required: false,
                default: () => ({})
            },
            classes: {
                type: String,
                required: false,
                default: ""
            }
        }
    }
</script>
