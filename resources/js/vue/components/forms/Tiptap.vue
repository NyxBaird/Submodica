<template>

</template>

<script>


export default {
    components: {
        EditorContent,
    },

    data() {
        return {
            editor: null,
        }
    },

    mounted() {
        this.editor = new Editor({
            content: '<p>I’m running Tiptap with Vue.js. 🎉</p>',
            extensions: [
                StarterKit,
            ],
        })
    },

    beforeUnmount() {
        this.editor.destroy()
    },
}
</script>
