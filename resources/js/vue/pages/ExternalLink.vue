<style scoped lang="scss">
    @import 'resources/sass/variables';

    button {
        font-family: "DaysOne-Regular", sans-serif;
    }
</style>

<template>
    <Container id="Content">
        External Link Detected
        <hr />
        You've clicked a users link to a third party website! Submodica is unable to vouch for the security of such links and does not endorse or accept responsibility for content found on other domains. If you agree to this and would like to continue to the link provided, please click the Continue button.<br />
        <br />
        <div>
            {{ url }}
        </div>
        <button class="btn floatLeft" @click="close">Cancel & Close</button>
        <button class="btn btnRed floatRight" @click="continueNav">I Understand, Continue</button>
        <br />
        <br />
    </Container>
</template>

<script>
import Container from "../components/Container.vue";

export default {
    name: "PrivacyPolicy",
    components: {
        Container
    },
    data() {
        return {
            url: ""
        };
    },
    methods: {
        continueNav() {
            let url = decodeURIComponent(window.location.search.replace("?url=", ""));

            if (url.indexOf('http') !== 0)
                url = 'http://' + url;

            window.location.href = decodeURIComponent(url);
        },
        close() {
            window.close();
        }
    },
    mounted() {
        let url = decodeURIComponent(window.location.search.replace("?url=", ""));

        //Get rid of any trailing slashes
        if (url.slice(-1) === '/')
            url = url.slice(0, -1);

        let pieces = url.split('/'),
            domainIndex = 0; //by default the domain is the first piece in our url

        //If this is true the url contains http:// or https:// and the domain should be piece 2
        if (typeof pieces[1] !== 'undefined' && pieces[1].length === 0)
            domainIndex = 2;

        let domain = pieces[domainIndex];
        domain = domain.replace("www.", "");

        if (domain === 'submodica.xyz' || domain === 'submodica.com' || domain === 'submodica.net')
            this.continueNav();

        this.url = url;
    }
}
</script>
