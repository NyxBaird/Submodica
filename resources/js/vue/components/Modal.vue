<style lang="scss">
    @import "../../../sass/variables";

    $paddingHorizontal: 25px;
    $paddingVertical: 20px;

    .modal {
        position: fixed;
        overflow-y: scroll;

        //Try something like this to vertical center maybe?
        //top: 50%;
        //left: 50%;
        //transform: translate(-50%, -50%);

        top: 0;
        left: 0;
        width: 100%;
        height: 100%;

        z-index: 2000;

        .bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;

            opacity: 0.8;
            background-color: #1C3D6C;
        }
        .modal:hover > .fg {
            overflow-y: scroll;
        }

        .fg {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 60%;
            z-index: 51;

            padding: $paddingVertical $paddingHorizontal;
            vertical-align: middle;
            background-color: #CFECFF;
            border-radius: 10px;

            color: rgb(20, 20, 20, 0.7);

            .title {
                display: inline-grid;
                margin: -5px 0 0 0;
                color: #455CFF;
                font-size: 20px;
            }

            .x {
                cursor: pointer;
                display: inline-grid;

                position: absolute;
                top: 8px;
                right: 10px;

                color: #aaa;
                font-size: 10px;
            }
        }

        a {
            color: #cca404 !important;
            padding: 15px 0;
        }
    }
</style>

<template>
    <div v-if="show" class="modal">
        <div class="bg" @click="!preventManualClose?close():false"></div>
        <div class="fg">
            <div class="header">
                <span class="title">{{ title }}</span>
                <span v-if="!preventManualClose" class="x" @click="close"><font-awesome-icon :icon="['fas', 'x']" /></span>
            </div>
            <div class="body">
                <slot /><!--Render our modals html here-->
            </div>
            <div class="footer">
                <slot name="footer" /><!--Render our modals footer here-->
            </div>
        </div>
    </div>
</template>

<script>

    /* import fontawesome */
    import { library } from "@fortawesome/fontawesome-svg-core"

    import { faX } from "@fortawesome/free-solid-svg-icons"
    library.add(faX);

    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'


    export default {
        name: "Modal",
        props: {
            height: {
                type: String,
                default: "auto",
                required: false
            },
            title: {
                type: String,
                default: "",
                required: false
            },
            preventManualClose: {
                type: Boolean,
                default: false,
                required: false
            }
        },
        data() {
            return {
                show: false,
                padding: 20
            };
        },
        components: {
            FontAwesomeIcon: FontAwesomeIcon
        },
        methods: {
            open() {
                this.show = true;
                document.body.classList.add('noscroll');
            },
            close(overridePreventClose) {
                if (typeof overridePreventClose === "undefined")
                    overridePreventClose = false;

                if (!this.preventManualClose || overridePreventClose) {
                    document.body.classList.remove('noscroll');
                    this.show = false;
                }
            }
        },
        computed: {
            realHeight() {
                return this.$route.params.game === "sn1" ? "Subnautica" : "Below Zero"
            }
        },
    }
</script>
