<template>
    <div class="accountPanel" :class="classes">
        <div class="accountDetails" v-if="typeof data.username !== 'undefined'">
            <div class="circle">
                <img :src="data.avatar" :alt="accountType + ' Profile Image'" height="80" width="80" />
            </div>
            <div>
                Connected with {{ accountType }} as <span v-html="data.username"></span> <small v-if="accountType === 'Github'">(<a href="#" @click="disconnect">Disconnect</a>?)</small>
            </div>
        </div>
        <div class="accountConnect" v-else>
            <button :class="'btn btn' + accountType" @click="$root.$refs.Frontend.authenticate(account, true)">
                Connect {{ accountType }} <faIcon :icon="['fab', account]" />
            </button>
        </div>
    </div>
</template>

<script>
    import { library } from "@fortawesome/fontawesome-svg-core"

    import { faGithub } from '@fortawesome/free-brands-svg-icons'
    import { faDiscord } from '@fortawesome/free-brands-svg-icons'
    library.add(faDiscord);
    library.add(faGithub);

    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

    const Types = [
        "github",
        "discord"
    ];

    export default {
        name: "AccountPanel",
        components: {
            "faIcon": FontAwesomeIcon
        },
        data(){
            return {
                imgSize: 50,
                accountType: ""
            }
        },
        props: {
            account: {
                type: String,
                validator(value) {
                    return Types.includes(value);
                }
            },
            data: {
                type: Object,
                default() {
                    return {
                        "username": "",
                        "email": "",
                        "avatar": ""
                    };
                }
            },
            img: String,
            classes: String
        },
        methods: {
            disconnect() {
                this.$axios.post("/auth/disconnect", {service: this.account})
                    .then((response) => {
                        this.$root.alerts.push({
                            type: response.data.success ? "success" : "error",
                            content: response.data.message
                        });

                        delete this.data.username;
                    });
            }
        },
        mounted() {
            this.accountType = this.account[0].toUpperCase() + this.account.slice(1);
        }
    }
</script>

<style lang="scss" scoped>
    @import "../../../../../sass/frontend/accountPanel.scss";
</style>
