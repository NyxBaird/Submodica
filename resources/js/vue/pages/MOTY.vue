<style scoped lang="scss">
    @import 'resources/sass/variables';
    .nominee {
        position: relative;
        width: calc(100% - 20px);
        background: #1c79d6;
        padding: 5px 10px;
        margin-bottom: 15px;
        border-radius: 5px;
        border: 1px solid $bordersColor;
        cursor: pointer;
    }
</style>

<template>
    <container id="Content">
        <div id="nomineeList">
            <div class="nominee" v-for="nominee in nominees" @click="toggleExpansion(nominee)">
                <font-awesome-icon :icon="'chevron-' + (typeof nominee.active !== 'undefined' && nominee.active ? 'down' : 'right')" /> {{ nominee.name }}

                <WYSIWYG v-if="typeof nominee.active !== 'undefined' && nominee.active"
                         ref="description"
                         name="description"
                         :display-only=true
                         :json-content="nominee.description"
                         placeholder="Loading..." label="" cols=12 />
            </div>
        </div>
    </container>
</template>

<script>
import Container from "../components/Container.vue"
import WYSIWYG from "../components/forms/WYSIWYG.vue"

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome"
import { library } from "@fortawesome/fontawesome-svg-core"

import { faChevronDown, faChevronRight } from "@fortawesome/free-solid-svg-icons"
library.add(faChevronDown, faChevronRight);

export default {
    name: "MOTY",
    data() {
        return {
            user: window.user,
            nominees: []
        };
    },
    components: {
        WYSIWYG,
        Container,
        FontAwesomeIcon
    },
    mounted() {
        this.$axios.post('/api/verifyDiscordServerMembership')
            .then((response) => {
                console.log(response);
            });

        this.$axios.post('/staff/moty/getNominees')
            .then((response) => { this.nominees = response.data.data; });
    },
    methods: {
        toggleExpansion(nominee) {
            if (nominee.active)
                nominee.active = 0;
            else
                nominee.active = 1;
        }
    }
}
</script>
