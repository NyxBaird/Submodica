<template>
    <container id="Content">
        <a @click="$router.push('/staff')">← Back to Staff Dashboard</a> <br/>
        <br />
        <Table ref="table" src="/users/fetchManageable"
               :columns='tableCols' :sort="tableSort"></Table>
    </container>
</template>

<script>
import Table from "../../components/Table.vue";
import Container from "../../components/Container.vue";

import { library } from "@fortawesome/fontawesome-svg-core"

import { faPencil } from "@fortawesome/free-solid-svg-icons"
library.add(faPencil);

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

export default {
    name: "ManageUsers",
    components: {
        FontAwesomeIcon,
        Container,
        Table
    },
    data() {
        return {
            tableSort: [
                {
                    selector: 'created_at',
                    direction: 'desc'
                }
            ],
            tableCols: [
                {
                    name: "ID",
                    selector: "id",
                    sortable: true,
                    searchable: true,
                    style: {
                        width: "5%"
                    }
                },
                {
                    name: "Name",
                    selector: "name",
                    sortable: true,
                    searchable: true,
                    style: {
                        width: "24%"
                    }
                },
                {
                    name: "Email",
                    selector: "email",
                    sortable: true,
                    searchable: true,
                    style: {
                        width: "24%"
                    }
                },
                {
                    name: "Mods",
                    selector: "mods",
                    sortable: true,
                    style: {
                        width: "10%"
                    }
                },
                {
                    name: "Created",
                    selector: "created_at",
                    sortable: true,
                    classes: "center",
                    style: {
                        width: "12.5%"
                    }
                },
                {
                    name: "Updated",
                    selector: "updated_at",
                    sortable: true,
                    classes: "center",
                    style: {
                        width: "12.5%"
                    }
                },
                {
                    name: "Banned",
                    selector: "banned",
                    sortable: true,
                    classes: "center",
                    style: {
                        width: "6%"
                    }
                },
                {
                    name: "BanToggle",
                    sortable: false,
                    classes: "right",
                    buttons: [
                        {
                            text: "Ban/UnBan?",
                            classes: "edit btnGreen",
                            callback(e, row) {
                                window.location = '/staff/ban/' + row.id;
                            }
                        }
                    ],
                    style: {
                        width: "6%"
                    }
                }
            ]
        };
    },
    mounted() {
        //This is just a soft check to prevent users from visiting accidentally.
        //We'll check authentication on the backend for any data requested or sent
        if (user.role.level < 50) {
            this.$root.alerts.push({
                type: "error",
                content: "You don't have access to that page."
            });
            this.$router.push("/");
        }
    }
}
</script>

