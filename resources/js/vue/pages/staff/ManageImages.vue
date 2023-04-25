<style lang="scss" scoped>
    @import "resources/sass/variables";

    $margin: 10px;

    .image {
        background-color: rgb(0, 0, 0, 0.3);
        display: inline-block;
        width: calc(50% - ($margin * 2));
        min-height: 200px;
        height: auto;
        background-size: contain;
        background-position: center;
        background-repeat: no-repeat;
        margin: $margin;

        div {
            display: inline-block;
        }

        .details {
            text-shadow: 1px 1px 1px #000;
        }
    }

    .btn {
        margin: 0;
        background-color: #026CD6;
    }

    .controls {
        margin: 5px 0;
    }
</style>

<template>
    <container id="Content">
        <a @click="$router.push('/staff')">← Back to Staff Dashboard</a> <br/>
        <br />
        <div v-for="image in images"
             class="image"
             :style="{'background-image': 'url(https://submodica.b-cdn.net/mods/' + image.name + ')'}">

            <div v-if="image.exists" class="details">
                <b>User:</b> {{ image.exists.mod.user.name }} (#{{ image.exists.mod.user.id }})<br/>
                <b>Mod:</b> {{ image.exists.mod.id }}<br/>
                <b>Created:</b> {{ image.created_at }}<br/>
            </div>
            <div v-else class="details">
                <b>Not in database (likely from a development server)</b>
            </div>

            <div class="controls floatRight">
                <a v-if="image.exists"
                   :href="`/mods/${image.exists.mod.game}/${image.exists.mod.id}`"
                   target="_blank"
                   class="btn">

                    Mod
                </a>
                <a v-if="image.exists"
                   @click="findUser(image.exists.mod.user.id)"
                   target="_blank"
                   class="btn">

                    User
                </a>
                <a v-if="image.exists"
                   @click="deleteImage($event, image.exists)"
                   target="_blank"
                   class="btn">

                    Delete
                </a>
            </div>
        </div>
    </container>
</template>

<script>
import Table from "../../components/Table.vue";
import Container from "../../components/Container.vue";

import { library } from "@fortawesome/fontawesome-svg-core"

import { faPencil } from "@fortawesome/free-solid-svg-icons"
library.add(faPencil);

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import {roles} from "../../../globals";

export default {
    name: "ManageImages",
    components: {
        FontAwesomeIcon,
        Container,
        Table
    },
    data() {
        return {
            images: [
                {
                    name: "1-icon1668064281.webp"
                }
            ]
        };
    },
    methods: {
        deleteImage($event, image) {
            let img = $event.currentTarget.closest('.image');

            //wip pop up confirm
            this.$axios.post(`/staff/deleteImage/${image.id}`)
                .then((response) => {
                    this.$root.alerts.push({
                        type: response.data.success ? "success" : "error",
                        content: response.data.message
                    });

                    if (response.data.success)
                        img.remove();
                });
        },
        findUser(id) {
            window.tableSearch = {"users.id": id};
            this.$router.push("/staff/users");
        }
    },
    mounted() {
        if (user.role.level < roles.admin) {
            this.$root.alerts.push({
                type: "error",
                content: "You don't have access to that page."
            });
            this.$router.push("/");
        }

        this.$axios.post('/staff/fetchAllImages', {repo: this.repo})
            .then((response) => {
                console.log(response.data.data);
                this.images = response.data.data;
            });
    }
}
</script>

