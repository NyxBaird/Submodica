<style lang="scss" scoped>
@import "../../../../sass/variables";

h2 {
    margin: 0;
    font-family: "DaysOne-Regular", sans-serif;
}

.createMod {
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
    <h2>Your {{ game === 'sn1' ? "Subnautica" : "Below Zero" }} Mods</h2>
    <span class="btn createMod"
          v-if="typeof user.id !== 'undefined' && user.github_id"
          @click="this.$router.push('/mods/' + this.$route.params.game + '/create')">

        <font-awesome-icon icon="plus" />
        Add A Mod
    </span>
    <div>
        <Table :src="'/mods/' + game + '/manage/list'" :columns="tableCols" :sort="tableSort"></Table>
    </div>
</template>

<script>
import Table from "../../components/Table.vue";

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core"
import { faPlus, faPencil, faDownload, faEye, faX, faCopy } from '@fortawesome/free-solid-svg-icons'
library.add(faPlus, faPencil, faX, faCopy);

import {pages} from "../../../mod.js";

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
                    name: "Name",
                    selector: "title",
                    searchable: true,
                    sortable: true,
                    style: {
                        width: "45%"
                    }
                },
                {
                    name: "Status",
                    selector: "creation_status",
                    sortable: false,
                    style: {
                        width: "10%"
                    }
                },
                {
                    name: "",
                    icon: faEye,
                    selector: "views",
                    sortable: true,
                    classes: "center",
                    style: {
                        width: "10%"
                    }
                },
                {
                    name: "",
                    icon: faDownload,
                    selector: "downloads",
                    sortable: true,
                    classes: "center",
                    style: {
                        width: "10%"
                    }
                },
                {
                    name: "Added",
                    selector: "created_at",
                    sortable: true,
                    style: {
                        width: '13%',
                    }
                },
                {
                    name: "",
                    sortable: false,
                    classes: "right",
                    buttons: [
                        {
                            icon: faPencil,
                            classes: "edit btnGreen",
                            title: "Edit",
                            callback(e, row) {
                                let game = window.location.pathname.split("/")[2],
                                    currentPageIndex = 'index';

                                if (!['preview', 'live'].includes(row.creation_status)) {
                                    let curr = Object.keys(pages).indexOf(row.creation_status);
                                    currentPageIndex = Object.keys(pages)[curr];
                                }

                                window.location = `/mods/${game}/${row.id}${pages[currentPageIndex].uri}`;
                            }
                        },
                        {
                            icon: faCopy,
                            classes: "duplicate btnBlue",
                            title: "Duplicate",
                            callback(e, row) {
                                if (window.confirm("Are you sure you want to duplicate this mod?"))
                                    window.location = `/mods/${row.game}/${row.id}/duplicate`;
                            }
                        },
                        {
                            icon: faX,
                            classes: "delete btnRed",
                            title: "Delete",
                            callback(e, row) {
                                let game = window.location.pathname.split("/")[2];
                                if (window.confirm("Are you sure you want to delete this mod? This action cannot be undone."))
                                   window.location = '/mods/' + game + '/' + row.id + '/delete';
                            }
                        }
                    ],
                    style: {
                        width: "12%"
                    }
                }
            ],
        };
    }
}
</script>
