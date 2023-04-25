<style lang="scss" scoped>
@import "../../../../sass/frontend/table.scss";

    h2 {
        margin: -10px 0;
    }

    button[type="submit"] {
        margin-top: -10px;
        font-family: "DaysOne-Regular", sans-serif;
    }
</style>

<template>
    <container id="Content">
        <a @click="$router.push('/staff')">← Back to Staff Dashboard</a> <br/>
        <br />
        <h2>Manage Modder of The Year</h2>
        <hr />
        <Form @submit="saveNominee">
            <input type="hidden" name="id" v-model="nominee.id" />

            <FormComponent class="col12" label="Modder Name">
                <Field rules="required" placeholder="Name" name="name" v-model="nominee.name" />

                <ErrorMessage name="name" class="validationError" />
            </FormComponent>

            <WYSIWYG rules="required"
                     :limit="1000"
                     ref="description"
                     name="description"
                     placeholder="Add some accomplishments here"
                     label="Briefly describe the nominee and why they're being nominated"
                     cols=12 />

            <button class="btn btnGreen" type="submit">Save Nominee</button>
        </Form>

        <table>
            <thead>
                <tr>
                    <th style="width: 80%">Name</th>
                    <th style="width: 20%">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="person in nominees">
                    <td>{{ person.name }}</td>
                    <td>
                        <button class="btn btnGreen" @click="editNominee(person)">
                            <font-awesome-icon icon="pencil" />
                        </button>
                        <button class="btn btnRed" @click="deleteNominee(person)">
                            <font-awesome-icon icon="trash" />
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </container>
</template>

<script>
import Table from "../../components/Table.vue";
import WYSIWYG from "../../components/forms/WYSIWYG.vue";
import Container from "../../components/Container.vue";
import FormComponent from "../../components/forms/FormComponent.vue";

import { library } from "@fortawesome/fontawesome-svg-core"

import { faPencil, faTrash } from "@fortawesome/free-solid-svg-icons"
library.add(faPencil, faTrash);

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { Form, Field, ErrorMessage } from "vee-validate";

export default {
    name: "ManageUsers",
    components: {
        Form,
        Table,
        Field,
        WYSIWYG,
        Container,
        ErrorMessage,
        FormComponent,
        FontAwesomeIcon
    },
    data() {
        return {
            user: window.user,
            nominee: {
                id: 0,
                name: '',
            },
            nominees: []
        };
    },
    methods: {
        loadNominees() {
            let vue = this;
            this.$axios.post('/staff/moty/getNominees')
                .then((response) => {
                    let data = response.data;

                    vue.$root.alerts.push({
                        type: data.success ? "success" : "error",
                        content: data.message
                    });

                    this.nominees = data.data;
                    console.log("Loaded in nominees", this.nominees);
                });
        },
        saveNominee() {
            this.nominee.description = this.$refs.description.editor.getJSON();

            let vue = this;
            this.$axios.post('/staff/moty/saveNominee', this.nominee)
                .then((response) => {
                    let data = response.data;

                    vue.$root.alerts.push({
                        type: data.success ? "success" : "error",
                        content: data.message
                    });

                    vue.nominee = {
                        id: 0,
                        name: '',
                    };

                    vue.$refs.description.clear();
                });
        },
        editNominee(person) {
            this.nominee = person;

            let content = isJson(person.description) ? JSON.parse(person.description) : person.description;

            this.$refs.description.editor.commands.setContent(content);

            function isJson(str) {
                try {
                    JSON.parse(str);
                } catch (e) {
                    return false;
                }
                return true;
            }
        },
        deleteNominee(person) {
            let vue = this;
            if (window.confirm("Are you sure you want to delete this nominee? This cannot be undone."))
                this.$axios.post('/staff/moty/deleteNominee/' + person.id)
                    .then((response) => {
                        let data = response.data;

                        vue.$root.alerts.push({
                            type: data.success ? "success" : "error",
                            content: data.message
                        });

                        vue.loadNominees();
                    });
        }
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

        this.loadNominees();
    }
}
</script>

