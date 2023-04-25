<style scoped lang="scss">
    @import 'resources/sass/variables';

    h2 {
        margin: 0;
    }
</style>

<template ref="Home">
    <container id="Content">
        <h2>Submodica Site Rules</h2>
        <hr />
        <p> 1. <b>Treat everyone with respect.</b> Submodica aims to be a safe place for everyone, this means that content added to the site that is disrespectful towards anyones race, culture, sexual orientation or gender identity will be removed and appropriate action will be taken against its creator.</p>
        <p> 2. <b>Only post your own content.</b> Submodica has no tolerance for plagiarism or for content uploaded by people other than the original author that do not also have the original authors specific consent to post said content here on Submodica. If you are found to be posting content that is not your own or that you do not have the rights to post, <i>all</i> of your content (whether it's been shown to be plagiarism or not) will be removed from Submodica and your account will be banned.</p>
        <p> 3. <b>Do not post any graphic, obscene, or otherwise generally inappropriate content.</b> Basically, hold your content to the standards of a PG-13 movie. If what you're posting wouldn't be appropriate to share with the general public then it's probably not appropriate to share on Submodica.</p>
        <p> 4. <b>You must be at least 13 years old to be here.</b> Children under the age of 13 aren't allowed to have accounts on websites- this is basic US law. In fact in order to sign in with Submodica at all you first need a Discord account which follows the same rule so in theory this is a non-issue here since the only way to sign in is through Discord but I'm saying it anyway. No one under the age of 13 is allowed on Submodica.</p>
        <p> 5. <b>Listen to & respect Submodica staff.</b> Staff has the authority to kick, ban, or remove content whenever we deem necessary; with or without warning. It is your responsibility to read the rules and follow them! Being on Submodica is not an inherent right, and the staff will have final say in these issues. Be assured that nobody is being banned without reason.</p>
        <br />
        <p>***These rules are subject to change with or without notice. It's your responsibility to read and keep up to date on them.</p>
    </container>
</template>

<script>
import Container from "../components/Container.vue";

export default {
    name: "Rules",
    components: {
        "container": Container
    }
}
</script>
