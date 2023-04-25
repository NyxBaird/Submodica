<style lang="scss" scoped>
@import "../../../../sass/variables";

h2 {
    display: inline-block;
    margin: 0;
    font-family: "DaysOne-Regular", sans-serif;
}
#actionBtns {
    display: inline-block;
    margin-top: 30px;
    float: right;
}
@media (pointer:none), (pointer:coarse), screen and ( max-width: 1250px ) {
    h2 {
        text-align: center;
        width: 100%;
    }
    #actionBtns {
        position: relative;
        width: 100%;
        display: inline-block;
        text-align: center;
        float: none;
        //margin: 0;
        margin: 45px 0 0 0;

        .btn {
            position: relative;
            display: inline-block;
            border-radius: 3px;
            float: none;
            width: 25%;
        }
    }
}

.btn {
    float: right;
    cursor: pointer;
    color: $textColor;
    margin-top: -30px;

    &.createMod {
        margin-left: 0;
        margin-right: 0;
        border-radius: 0;
    }
    &.editMods {
        margin-right: 10px;
        margin-left: 0;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }
    &.viewFavoriteMods {
        margin-left: 10px;
        margin-right: 0;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
}

#coreMods {
    display: flex;
    position: relative;
    text-align: center;
    width: 100%;

    &.twoPointOh > .btn {
        $margin: 15px;

        margin: 0 $margin;
        position: relative;
        display: inline-block;
        width: 100%;
        text-align: center;
        padding: 5px 0;
        color: $textColor;
    }

    &:not(.twoPointOh) > .btn {
        $margin: 15px;

        margin: 0 $margin;
        position: relative;
        display: inline-block;
        width: 50%;
        text-align: center;
        padding: 5px 0;
        color: $textColor;
    }
}

.coreTitle {
    text-align: center;
}

</style>

<template>
    <h2>Showing all {{ gameTitle }} Mods</h2>
    <div v-if="typeof user.id !== 'undefined' && user.github_id" id="actionBtns">

        <div class="btn editMods"
              @click="this.$router.push('/mods/' + this.$route.params.game + '/manage')">

            <font-awesome-icon icon="pencil" />
            &nbsp;Manage My Mods
        </div>
        <div class="btn createMod"
              @click="this.$router.push('/mods/' + this.$route.params.game + '/create')">

            <font-awesome-icon icon="plus" />
            &nbsp;Add A Mod
        </div>
        <div class="btn viewFavoriteMods"
              @click="this.$router.push('/mods/' + this.$route.params.game + '/favorites')">

            <font-awesome-icon icon="star" />
            &nbsp;My Favorites
        </div>
    </div>

    <div>
        <hr />
        <h3 class="coreTitle">These are Core Mods that are required by just about every other mod to function</h3>
        <div v-if="searchPrefix === 'legacy' || $route.params.game === 'sbz'" id="coreMods">
            <a class="btn btnBlue" :href="$route.params.game==='sn1' ? '/mods/sn1/86' : '/mods/sbz/88'" target="_blank">QMod Manager</a>
            <a class="btn btnBlue" :href="$route.params.game==='sn1' ? '/mods/sn1/87' : '/mods/sbz/89'" target="_blank">SML Helper</a>
        </div>
        <div v-else id="coreMods" class="twoPointOh">
            <a class="btn btnBlue" href="/mods/sn1/141" target="_blank">BepInEx Subnautica Pack</a>
        </div>
        <br />
    </div>
    <hr />
    <div ref="mods">
        <Table ref="table" :src="'/mods/' + game + '/list'" :columns='tableCols' :sort="tableSort">
            <template v-slot:searchPrefix v-if="game === 'sn1'">
                <select name="searchPrefix" v-model="searchPrefix" @change="this.$refs.table.fetchData">
                    <option value="legacy">Legacy Mods</option>
                    <option value="2.0">v2.0+ Mods</option>
                </select>
            </template>
        </Table>
    </div>
</template>

<script>
import Table from "../../components/Table.vue";

import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core"
import { faPlus, faPencil, faDownload, faX, faEye, faStar } from '@fortawesome/free-solid-svg-icons'
library.add(faPlus, faPencil, faDownload, faStar);

export default {
    name: "Index",
    components: {
        'Table': Table,
        FontAwesomeIcon
    },
    data() {
        return {
            game: this.$route.params.game,
            searchPrefix: '2.0',
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
                    name: "",
                    icon: faEye,
                    selector: "views",
                    title: "Views",
                    sortable: true,
                    classes: "center",
                    style: {
                        width: "5%"
                    }
                },
                {
                    name: "",
                    icon: faDownload,
                    selector: "downloads",
                    title: "Downloads",
                    sortable: true,
                    classes: "center",
                    style: {
                        width: "5%"
                    }
                },
                {
                    name: "",
                    icon: faStar,
                    selector: "likes",
                    title: "Likes",
                    sortable: true,
                    classes: "center",
                    style: {
                        width: "5%"
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
    },
    computed: {
        gameTitle() {
            return this.$route.params.game === "sn1"
                ? (this.searchPrefix === "legacy"
                    ? "Legacy Subnautica"
                    : "Subnautica 2.0+")
                : "Below Zero"
        }
    },
    watch:{
        $route (to, from){
            //This is a workaround for the table not updating when the route changes from one game to another
            if (from.path.includes("/mods/") && (to.path.match(/\//g) || []).length === 2) {
                this.game = this.$route.params.game;
                this.$refs.table.fetchData(to.path + "/list");
            }
        }
    }
}
</script>
