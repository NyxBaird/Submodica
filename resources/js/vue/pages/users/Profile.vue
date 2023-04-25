<style lang="scss" scoped>
@import "../../../../sass/variables";

h2 {
    margin: 0;
}

#profileImage {
    $imgH: 100px;

    display: inline-block;
    height: $imgH;
    width: 25%;

    img {
        height: $imgH;
    }
}

#connectedAccounts {
    width: 100%;

    .accountPanel {
        display: inline-block;
        width: 50%;

        &:first-of-type {
            margin-right: -1px;
            border-right: 1px solid $textColor;
        }
    }
}


</style>

<template>
    <div>
        <container id="Content">
            <h2>User Profile</h2>
            <hr />
            <Form @submit="saveProfile">
                <div id="connectedAccounts">
                    <AccountPanel account="discord" :data="discordData" />
                    <AccountPanel account="github" :data="githubData" />
                </div>
                <br />
                <FormComponent class='col6 mCol12' label="Update your publicly displayed username">

                    <Field name="username"
                           type="text"
                           placeholder="Your Username"
                           v-model="user.name"
                           maxlength="20" />



                    <ErrorMessage name="creator" class="validationError" />
                </FormComponent>


                <FormComponent class="col12" label="Add a donation link (either Ko-Fi or Patreon)">

                    <Field name="donation_link"
                           rules="donationUrl"
                           type="text"
                           placeholder="Donation URL"
                           v-model="user.donation_link"
                           maxlength="1000" />

                    <ErrorMessage name="donation_link" class="validationError" />
                </FormComponent>

                <button class="btn btnGreen updateUsername" type="submit">Update</button>
            </Form>
        </container>
    </div>
</template>

<script>
    import Container from "../../components/Container.vue";
    import AccountPanel from "../../pages/users/Profile/AccountPanel.vue";
    import FormComponent from "../../components/forms/FormComponent.vue";

    import {Form, Field, ErrorMessage, defineRule} from 'vee-validate';

    export default {
        name: "Profile",
        components: {
            Form,
            Field,
            Container,
            AccountPanel,
            ErrorMessage,
            FormComponent
        },
        methods: {
            saveProfile(values) {
                this.$axios.post("/users/profile/update", this.user)
                    .then((response) => {
                        response = response.data;

                        if (response.message.length)
                            this.$root.alerts.push({
                                type: response.success ? "success" : "error",
                                content: response.message
                            });
                    });
            }
        },
        data(){
            return {
                user: window.user,
                discordData: {},
                githubData: {},
            };
        },
        mounted() {
            this.$axios.post("/users/getConnectionInfo")
                .then((response) => {
                    response = response.data;

                    if (response.message.length)
                        this.$root.alerts.push({
                            type: response.success ? "success" : "error",
                            content: response.message
                        });

                    if (response.data.discord)
                        this.discordData = response.data.discord;

                    if (response.data.github)
                        this.githubData = response.data.github;
                });
        }
    }
</script>
