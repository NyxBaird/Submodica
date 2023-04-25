<style lang="scss" scoped>
@import '/resources/sass/variables';

.page {
    margin: 10px;
}
</style>

<template>
    <container id="Content">
        Hi {{ user.name }}! Welcome to the Staff Dashboard
        <div v-for="page in pages" class="page">
            <a v-if="user.role.level >= page.level" @click="this.$router.push('/staff' + page.path)">{{ page.name }}</a>
        </div>
    </container>
</template>

<script>
import {roles} from "../../../globals";
import Container from "../../components/Container.vue";

export default {
    name: "Index",
    components: {
        Container,
    },
    data() {
        return {
            user: window.user,
            pages: [
                {name: 'Manage Modder of The Year', path: '/moty', level: roles.mod},
                {name: 'Manage Mods', path: '/mods', level: roles.mod},
                {name: 'Manage Images', path: '/images', level: roles.mod},
                {name: 'Manage Users', path: '/users', level: roles.admin},
            ]
        };
    },
    mounted() {
        //This is just a soft check to prevent users from visiting accidentally.
        //We'll check authentication on the backend for any data requested or sent
        if (user.role.level < roles.mod) {
            this.$root.alerts.push({
                type: "error",
                content: "You don't have access to that page."
            });
            this.$router.push("/");
        }
    }
}
</script>

