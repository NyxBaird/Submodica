<style scoped lang="scss">
    @import "../../../../sass/variables";

    input[type="checkbox"] {
        $size: 15px;
        width: $size;
        height: $size;
        cursor: pointer;
        float: left;
    }

    .validationError {
        display: block;
    }
</style>

<template>
    <fComponent
        :classes="'col' + cols + (mCols ? ' mCol' + mCols : '')"
        :label="label"
        :label-style="{
            display: 'inline-block',
            fontSize: '14px',
        }">

        <Field :rules="rules"
               :name="name"
               type="checkbox"
               v-model="checked"
               :value="true" />
        <ErrorMessage :name="name" class="validationError" />
    </fComponent>
</template>

<script>
    import { Field, ErrorMessage } from 'vee-validate';
    import FormComponent from "./FormComponent.vue";

    export default {
        name: "Checkbox",
        components: {
            "fComponent": FormComponent,
            ErrorMessage,
            Field,
        },
        data(){
            return {
                mCols: 0,
            }
        },
        props: {
            name: String,
            rules: {
                type: String,
                default: ""
            },
            label: {
                type: String,
                required: false,
                default: ""
            },
            cols: {
                required: false,
                default: 6,
                validator(value) {
                    return !isNaN(value);
                }
            },
            mobileCols: {
                required: false,
                default: 0,
                validator(value) {
                    return !isNaN(value);
                }
            },
            checked: {
                type: Boolean,
                required: false,
                default: false
            }
        },
        mounted() {
            this.mCols = this.mobileCols ? this.mobileCols : this.cols;
        }
    }
</script>
