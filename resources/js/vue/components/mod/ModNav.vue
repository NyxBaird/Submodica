<style lang="scss" scoped>
@import "../../../../sass/variables";

#navButtons {
    position: relative;
    bottom: 0;
    width: 100%;

    z-index: 10;

    button.cancel, button.next {
        cursor: pointer;
        font-family: "DaysOne-Regular", sans-serif;
    }
    button.cancel {
        position: relative;
        float: left;
        z-index: 100;
    }
    button.next {
        position: absolute;
        right: 0;
        z-index: 100;
    }

    #bubbles {
        position: absolute;
        width: 100%;
        text-align: center;
        margin-top: 5px;
        z-index: 99;


        .bubble {
            position: relative;
            display: inline-block;
            width: 30px;
            height: 30px;
            line-height: 30px;
            font-weight: bold;

            background-color: #026CD6;
            box-shadow: 0 0 10px 10px #1c83eb inset;

            border: 2px solid $bordersColor;
            border-radius: 50px;

            text-align: center;
            cursor: pointer;

            &:not(:last-child) {
                margin-right: 10%;
            }

            &.complete {
                background-color: #a9bccf;
                color: #555;
                box-shadow: none;
            }

            &.current {
                border-color: $greenBorder;
                background-color: $green;
                box-shadow: none;
            }

            &.future {
                cursor: not-allowed;
            }

            span {
                width: 100%;
                height: 100%;
                position: relative;
                display: block;
                line-height: 30px;

                svg {
                    padding-top: 7px;
                }
            }
        }
    }
}

</style>

<template>
    <div id="navButtons">
        <button type="button" class="cancel btn btnRed" @click="back">{{ currentPageIndex ? 'Back' : 'Cancel' }}</button>

        <div id="bubbles">
            <div v-for="(data, page, index) in pages"
                 class="bubble"
                 :class="[
                    isEditing() && index < currentPageIndex ? 'complete' : '',
                    isEditing() && index === currentPageIndex ? 'current' : '',
                    isEditing() && index > currentPageIndex ? 'future' : ''
                 ]"
                 :title="data.title"
                @click="confirmAndLoad(index, this.rootUri + data.uri)">
                <span v-if="status === 'live' || status === 'preview'">
                    <font-awesome-icon :icon="data.icon" />
                </span>
                <span v-else>
                    {{ index + 1 }}
                </span>
            </div>
        </div>

        <button type="submit" class="next btn">{{ currentPageIndex === Object.keys(this.pages).length - 1 ? 'Save & Finish' : 'Save & Continue' }}</button>
    </div>
</template>

<script>
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
import {library} from "@fortawesome/fontawesome-svg-core"
import {faX, faPenNib, faUsers, faFileArrowUp, faEye} from "@fortawesome/free-solid-svg-icons"

library.add(faX);

import {pages} from "../../../mod.js";

export default {
    name: "ModNav",
    data() {
        return {
            rootUri: `/mods/${this.$route.params.game}/${this.$route.params.mod}`,
            pages: pages,
            lastPage: '',
            nextPage: '',
            currentPageIndex: 0,
        }
    },
    props: {
        page: {
            type: String,
            required: true
        },
        status: {
            type: String,
            required: true
        },
        callback: {
            type: Function,
            required: false
        }
    },
    components: {
        FontAwesomeIcon
    },
    mounted() {
        let pageKeys = Object.keys(this.pages),
            currentIndex = pageKeys.indexOf(this.page);

        this.lastPage = pageKeys[(currentIndex - 1) ? (currentIndex - 1) : 0];
        this.nextPage = pageKeys[(currentIndex + 1) > (pageKeys.length - 1) ? pageKeys.length - 1 : currentIndex + 1];

        this.currentPageIndex = currentIndex;
    },
    methods: {
        back() {
            if (!this.currentPageIndex) {
                this.confirmAndLoad(-1, '/mods/' + this.$route.params.game);
            } else {
                if (this.currentPage === 'preview')
                    this.$router.push(this.rootUri + this.pages[this.lastPage].uri);
                else
                    this.confirmAndLoad(this.rootUri + this.pages[this.lastPage].uri);
            }
        },
        confirmAndLoad(index, uri) {
            console.log(index, this.statusIndex, this.status, this.currentPageIndex);
            if (index <= this.statusIndex && window.confirm("Are you sure you want to leave this page? Leaving will discard any unsaved changes you've made"))
                this.$router.push(uri);
        },
        isEditing() {
            return Object.keys(this.pages).includes(this.status) && this.status !== 'preview'
        },
    },
    computed: {
        currentPage() {
            return Object.keys(this.pages)[this.currentPageIndex];
        },
        statusIndex() {
            if (this.status === 'live')
                return Object.keys(this.pages).indexOf('preview');

            return Object.keys(this.pages).indexOf(this.status);
        }
    }
}
</script>
