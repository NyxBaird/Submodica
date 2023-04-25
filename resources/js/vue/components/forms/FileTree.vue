<template>
    <div class="FileTree">
        File Tree Component (WIP)
    </div>
</template>

<script>
export default {
    name: "FileTree",
    props: {
        repo: {
            type: String,
            required: true
        }
    },
    methods: {
        loadTree() {
            this.$axios.post('/api/mods/fetchRepoDirectories', {repo: this.repo})
                .then((response) => {
                    console.log("fetched!", response.data.data);
                });
        }
    },
    watch: {
        repo: function() {
            this.loadTree();
        }
    },
}
</script>

<style lang="scss" scoped>
    @import "../../../../sass/variables";

    .FileTree {
        padding: $formComponentPadding;
        border-radius: $formBorderRadius;
        background: white;
    }
</style>
