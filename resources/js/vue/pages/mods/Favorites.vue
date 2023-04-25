<style lang="scss" scoped>
@import "../../../../sass/variables";

h2 {
    margin: 0;
    font-family: "DaysOne-Regular", sans-serif;
}

.back {
    float: right;
    cursor: pointer;
    color: $textColor;
    margin-top: -30px;
}

.duplicate {
    border-radius: 0;
}
</style>

<template>
    <h2>Your favorite {{ game === 'sn1' ? "Subnautica" : "Below Zero" }} Mods</h2>
    <span class="btn back" @click="this.$router.push('/mods/' + this.$route.params.game)">

        <font-awesome-icon icon="arrow-left" />
        Back to all mods
    </span>
    <div>
        <Table :src="'/mods/' + game + '/favorites/list'" :columns="tableCols" :sort="tableSort"></Table>
    </div>
</template>

<script>
import Table from "../../components/Table.vue";

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome"
import { library } from "@fortawesome/fontawesome-svg-core"
import { faPlus, faPencil, faX, faCopy, faArrowLeft } from '@fortawesome/free-solid-svg-icons'

library.add(faPlus, faPencil, faX, faCopy, faArrowLeft);

export default {
    name: "Index",
    components: {
        'Table': Table,
        FontAwesomeIcon
    },
    data() {
        return {
            game: this.$route.params.game,
            user: window.user,
            tableSort: [
                {
                    selector: 'created_at',
                    direction: 'desc'
                }
            ],
            tableCols: [
                {
                    name: '',
                    image: true,
                    selector: 'profile_image',
                    link: {
                        href: "/mods/:game/:id",
                        target: "_blank"
                    },
                },
                {
                    name: "Name",
                    link: {
                        href: "/mods/:game/:id",
                        target: "_blank"
                    },
                    subtext: {
                        selector: 'tagline'
                    },
                    selector: "title",
                    sortable: true,
                    searchable: true,
                    style: {
                        width: "35%"
                    }
                },
                {
                    name: "Publisher",
                    selector: "creator",
                    sortable: true,
                    searchable: true,
                    style: {
                        width: "15%"
                    }
                },
                {
                    name: "Version",
                    selector: "latest_version",
                    sortable: false,
                    style: {
                        width: "10%"
                    }
                },
                {
                    name: "Added",
                    selector: "created_at",
                    sortable: true,
                    style: {
                        width: '12%',
                    }
                },
                {
                    name: "Updated",
                    selector: "updated_at",
                    sortable: true,
                    style: {
                        width: '12%',
                    }
                }
            ]
        };
    }
}
</script>
