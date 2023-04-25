<style lang="scss" scoped>
    @import "resources/sass/variables";

    $paddingHorizontal: 20px;
    $paddingVertical: 20px;
    $borderRadius: 20px;
    $screenPadding: 20px;
    $innerBorderThickness: 2px;

    .container {
        position: relative;
        left: calc(10% - ($screenPadding / 2));
        width: 80%;

        .fg {
            position: relative;
            width: calc(100% - $screenPadding);
            z-index: 6;
            background-color: $containerBG;

            padding: $paddingVertical $paddingHorizontal;

            border-radius: $borderRadius;
            border: $innerBorderThickness solid $bordersColor;
            box-shadow: 0 0 10px 10px #2491FF inset, 0 0 10px 10px #026CD6;

            color: $textColor;
        }
    }

</style>

<template>
    <div class="container" v-bind:style="{ 'margin-top': this.setMarginTop + 'px' }">
<!--        <div class="bg"></div>-->
        <div class="fg">
            <slot />
        </div>
    </div>
</template>

<script>
    export default {
        name: "Container",
        data() {
            return {
                //Set our default margin top
                setMarginTop: 60
            };
        },
        props: {
            marginTop: {
                type: Number,
                required: false
            }
        },
        created() {
            if (typeof this.marginTop !== "undefined")
                this.setMarginTop = this.marginTop;
        }
    }
</script>
