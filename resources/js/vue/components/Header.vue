<style lang="scss" scoped>
@import "../../../sass/variables";

#Header {
    $paddingH: 10%;

    width: calc(100% - ($paddingH * 2));
    height: 25px;
    z-index: 100;

    background-color: $headerBG;
    padding: 3px $paddingH;
    font-size: 22px;
    color: $textColor;

    a {
        color: $textColor;
        text-decoration: none;

        &:hover {
            text-decoration: underline;
            cursor: pointer;
        }
    }
    small {
        font-size: 16px;
        margin: 0 5px;
    }
    #authLinks {
        float: right;
    }

    #pageLinks a,
    #authLinks a,
    #pageLinks .linkGroup {
        padding: 0 $headerLinkSpacing 0 $headerLinkSpacing;
    }

    #mobileLogout, #mobileLogin {
        display: none;
    }

    #pageLinks, #modLinks {
        display: inline-block;
    }

    #modLinks {
        position: absolute;
        left: 50%;
        width: 50%;
        transform: translateX(-50%);

        a {
            display: inline-block;
            position: relative;
            width: 50%;
            text-align: center;
        }
    }
}

@include mobile(){
    #Header {
        height: 16px;
        font-size: 14px;

        #pageLinks a, #authLinks a {
            padding: 2px;
        }

        #pageLinks {
            position: absolute;
        }

        small {
            font-size: 15px;
        }
    }
}
</style>

<template>
    <div id="Header">
        <span v-if="!showUserLinks" id="authLinks">
            <a @click="login" title="Log In">
                <font-awesome-icon icon="arrow-right-to-bracket" />
            </a>
        </span>

        <span v-else id="authLinks">
            <a v-if="showUserLinks" href="/users/profile">
                <font-awesome-icon icon="user" />
            </a>
            <a @click="logout" title="Log Out">
                <font-awesome-icon icon="arrow-right-from-bracket" />
            </a>
            <a v-if="user.role.level >= 50" @click="$router.push('/staff')" title="Staff">
                <font-awesome-icon icon="clipboard-user" />
            </a>
        </span>

        <div id="pageLinks">
            <a @click="$router.push('/')" title="Home">
                <font-awesome-icon icon="house" />
            </a>

            <LinkGroup :links="this.links" :donations="notViewingMod">
                <font-awesome-icon icon="bars" />
            </LinkGroup>
        </div>

        <div id="modLinks">
            <a href="/mods/sn1">
                Subnautica Mods
            </a>
            <a href="/mods/sbz">
                Below Zero Mods
            </a>
        </div>

        <span v-if="!showUserLinks" id="mobileLogin">
            <font-awesome-icon :icon="['fas', 'arrow-right-to-bracket']" @click="login" />
        </span>
        <span v-else id="mobileLogout">
            <font-awesome-icon :icon="['fas', 'arrow-right-from-bracket']" @click="logout" />
        </span>
    </div>
</template>

<script>
    import LinkGroup from "../router/LinkGroup.vue";
    import { library } from "@fortawesome/fontawesome-svg-core";

    import { faDiscord, faPatreon } from '@fortawesome/free-brands-svg-icons'
    import {
        faArrowRightFromBracket,
        faArrowRightToBracket,
        faClipboardUser,
        faHouse,
        faUser,
        faBars,
        faCog
    } from "@fortawesome/free-solid-svg-icons";

    library.add(faArrowRightFromBracket);
    library.add(faArrowRightToBracket);
    library.add(faClipboardUser);
    library.add(faDiscord);
    library.add(faPatreon);
    library.add(faHouse);
    library.add(faUser);
    library.add(faBars);
    library.add(faCog);

    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

    export default {
        name: "Header",
        data() {
            return {
                showUserLinks: false,
                user: window.user,
                notViewingMod: true,
                links: [
                    {
                        to: 'https://discord.gg/JMHQBn9rYt',
                        title: 'Discord',
                        icon: faDiscord
                    },
                    {
                        to: 'https://www.patreon.com/Submodica',
                        title: 'Patreon',
                        icon: faPatreon
                    },
                    {
                        to: 'https://ko-fi.com/submodica',
                        title: 'Ko-Fi',
                    },
                    {
                        to: '/patreons',
                        title: 'Supporters',
                    },
                    {
                        to: 'https://github.com/NyxBaird/Submodica',
                        title: 'GitHub'
                    }
                ]
            };
        },
        components: {
            LinkGroup,
            FontAwesomeIcon
        },
        methods: {
            register() {
                this.login(true);
            },

            login(register) {
                if (typeof register === "undefined")
                    register = false;

                let modal = this.$parent.$refs.AuthModal;
                modal.show = true;
            },

            logout() {
                window.location.href = "/logout";
            }
        },
        created() {
            this.showUserLinks = (typeof user.id !== "undefined");
        },
        mounted() {
            this.notViewingMod = !window.location.pathname.match(/\/mods\/(?:sn1|sbz)\/\d*/);
        }
    }
</script>
