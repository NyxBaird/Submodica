<style lang="scss" scoped>
    @import "../../../sass/variables";

    $hoverColor: #495780;

    .linkGroup {
        position: relative;
        display: inline-block;
        cursor: pointer;
        top: -3px;
        height: 100%;
        padding-bottom: 6px !important;

        .title {
            position: relative;
            top: 3px;
        }

        ul {
            position: absolute;
            display: none;

            margin: 0;
            top: 31px;
            left: 0;
            width: 150px;
            padding: 0;
            border-bottom-left-radius: 3px;
            border-bottom-right-radius: 3px;

            li {
                display: block;
                padding: 8px 20px;
                text-align: center;

                svg, #kofiLink {
                    float: left;
                }

                #kofiLink {
                    position: relative;
                    display: inline-block;
                    height: 40px;
                    width: 40px;

                    top: -7px;
                    left: -7px;

                    background-image: url("https://storage.ko-fi.com/cdn/brandasset/kofi_s_logo_nolabel.png");
                    background-size: cover;
                }

                a {
                    color: $textColor;
                    text-decoration: none;
                    pointer-events: none;
                }

                &:hover {
                    background: $headerBG;
                }
            }
        }

        &:hover {
            text-decoration: underline;
            background: $hoverColor;

            ul {
                display: block;
                background: $hoverColor;
            }
        }

        @include mobile(){
            ul {
                top: 21px;
            }
        }
    }
</style>

<template>
    <div class="linkGroup">
        <div class="title"><slot /></div>
        <ul>
            <li v-for="link in links" @click="openLink(link.to)">
                <a>
<!--                    <span v-if="link.title==='Ko-Fi'" id="kofiLink">&nbsp;</span>-->
<!--                    <font-awesome-icon v-else :icon="link.icon" />-->
                    {{ link.title }}
                </a>
            </li>
        </ul>
    </div>
</template>

<script>
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

export default {
    name: "LinkGroup",
    props: {
        links: Array,
        donations: {
            type: Boolean,
            required: false,
            default: false
        }
    },
    components: {
        FontAwesomeIcon
    },
    methods: {
        openLink(link) {
            if (link.indexOf('http') === -1)
                this.$router.push(link);
            else
                window.open(link, '_blank');

        }
    }
}
</script>
