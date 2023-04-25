<template>
    <div ref="Frontend" id="Frontend">
        <Header />

        <router-view v-slot="{ Component, route }">
            <transition :name="route.meta.transition || 'fade'">
                <component :is="Component" />
            </transition>
        </router-view>

        <Modal ref="AuthModal" height="135px">
            <div id="authMethods">
                <h3 class="authTitle">Authenticate With</h3>
                <br />
                <button class="btn btnDiscord" @click="authenticate('discord')">
                    Discord <faIcon :icon="['fab', 'discord']" />
                </button>
                <br />
                <span class="or">-- OR --</span>
                <br />
                <button class="btn btnGithub" @click="authenticate('github')">
                    Github <faIcon :icon="['fab', 'github']" />
                </button>
                <br />
                <br />
                <br />
                <span id="emailDisclaimer">
                    *** In order to comply with certain laws, Submodica requires that you provide an up-to-date email address on your account as a primary form of contact. To this end you may be asked to provide access to your email address on the Discord or Github account you log in with so that we can keep our records up to date. Your email will never be shared with any third parties and you will not receive marketing or promotional material from Submodica.
                </span>
            </div>
        </Modal>

        <Modal ref="CookiesModal" :prevent-manual-close=true height="135px">
            <div id="cookieAgreement">
                Submodica (along with basically every other website out there) uses a few basic cookies for both security and ease of use.<br />
                <br />
                If you choose to proceed the following cookies may be saved on your device;<br />
                <br />
                <span id="cookieTypes">
                    &nbsp;*&nbsp;An XSRF token (used to verify that you're not a malicious actor)<br />
                    &nbsp;*&nbsp;A session token (used to verify essential data like your logged in user)<br />
                    &nbsp;*&nbsp;A login redirect url (used to send you back to the page you were on after you log in)<br />
                </span>
                <br />
                This is all pretty standard stuff. If more are added in the future this list will be updated and you will be asked to re-verify your consent.<br />
                <br />
                <button class="btn btnGreen col12" @click="consentToCookies">I consent to Submodica storing the cookies outlined above on my device</button>
            </div>
        </Modal>

        <Footer />
    </div>
</template>

<script>
    import {latestConsent} from "../globals";

    import { useCookies } from "vue3-cookies";

    import Header from "./components/Header.vue";
    import Footer from "./components/Footer.vue";
    import Modal from "./components/Modal.vue";

    /* import fontawesome */
    import { library } from "@fortawesome/fontawesome-svg-core"

    import { faGithub } from '@fortawesome/free-brands-svg-icons'
    import { faDiscord } from '@fortawesome/free-brands-svg-icons'
    library.add(faDiscord);
    library.add(faGithub);

    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

    export default {
        name: "Frontend",
        components: {
            Header,
            Footer,
            Modal,
            "faIcon": FontAwesomeIcon
        },
        methods: {
            authenticate(method, forExisting) {
                if (typeof forExisting === "undefined")
                    forExisting = false;

                this.cookies.set("login_redirect", window.location.href, "10min");
                this.$axios.post("/auth/service", {method: method, forExisting: forExisting})
                    .then((response) => {
                        window.location.href = response.data.url;
                    });
            },
            consentToCookies() {
                this.cookies.set("cookie_agreement", latestConsent, "1y");
                this.$refs.CookiesModal.close(true);
            }
        },
        setup() {
            const { cookies } = useCookies();
            return { cookies };
        },
        mounted() {
            if (this.cookies.get("cookie_agreement") !== latestConsent)
                this.$refs.CookiesModal.open();

            if (this.cookies.get("login_redirect")) {
                let redirect = this.cookies.get("login_redirect");
                this.cookies.remove("login_redirect");

                window.location = redirect;
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import '../../sass/variables';

    #Frontend {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    #authMethods {
        width: 100%;
        height: 100%;
        text-align: center;

        margin-top: 10px;

        h3 {
            font-family: "DaysOne-Regular", sans-serif;
            margin: -5px 0 10px 0;
            font-weight: 100;
        }

        .or {
            font-size: 10px;
        }

        .btn {
            width: 250px;
            margin: 10px;
        }

        #emailDisclaimer {
            font-size: 11px;
            font-style: italic;
            opacity: 0.8;
        }
    }

    #cookieTypes {
        font-style: italic;
        font-size: 12px;
        opacity: 0.9;
    }
</style>
