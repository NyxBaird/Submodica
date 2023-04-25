<style lang="scss" scoped>
@import "../../../sass/variables";

.alert {
    position: relative;
    top: 0;
    right: 0;
    width: 200px;
    display: block;
    margin: 18px;
    padding: 8px 10px 11px 10px;
    border-radius: 0;
    color: #555;
    font-size: 12px;

    &.success {
        background-color: #98EAC2;
    }

    &.error {
        background-color: #FCBAB3;
    }

    .timer {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 5px;
        width: 100%;
        background-color: #000;
        opacity: 0.5;
    }
}
</style>

<template>
    <div class="alert" :class="type">
        <slot>{{ content }}</slot>

        <div v-if="timer" class="timer" ref="timer" />
    </div>
</template>

<script>
import { gsap } from "gsap";

const Types = [
    "success",
    "error",
    "info"
];

export default {
    name: "Alert",
    data() {
        return { timeShown: 100, content: "" };
    },
    props: {
        type: {
            type: String,
            validator(value) {
                return Types.includes(value);
            }
        },
        timer: {
            type: Boolean,
            required: false,
            default: true
        },
        seconds: {
            type: Number,
            required: false,
            default: 5
        }
    },
    mounted() {
        if (this.timer) {
            setTimeout(this.destroy, (this.seconds * 1000));
            gsap.to(this.$refs.timer, {width: 0, duration: this.seconds});
        }

        if (this.$slots.default()[0].children.length)
            this.content = this.$slots.default()[0].children;
    },
    methods: {
        destroy(){
            this.$el.remove();
        }
    }
}
</script>
