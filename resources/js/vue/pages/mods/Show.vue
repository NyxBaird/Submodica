<style scoped lang="scss">
    @import "../../../../sass/variables";
</style>

<template>
    <ModView ref="view" />
</template>

<script>
import ModView from "../../components/mod/View.vue"

export default {
    name: "Show",
    components: {
        ModView
    },
    mounted() {
        this.$axios.post(`/track/view/${this.$route.params.mod}`);
    }
}
</script>
