<template>
    <div>
        <container id="Content" :margin-top="60">
            <router-view />
        </container>
    </div>
</template>

<script>
    import Container from "../../components/Container.vue";

    export default {
        name: "Sn1Mods",
        components: {
            'container': Container,
        }
    }
</script>

<style scoped lang="scss"></style>
