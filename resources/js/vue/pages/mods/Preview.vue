<style scoped lang="scss">
@import "../../../../sass/variables";

#startPreview {
    width: calc(100% + 40px);
    background: rgb(0, 0, 0, 0.5);
    text-align: center;
    padding: 3px 0;
    margin: -21px 0 20px -20px;
    border-bottom: 1px solid darkred;
    border-top-left-radius: $corners;
    border-top-right-radius: $corners;
}
#endPreview {
    width: calc(100% + 40px);
    background: rgb(0, 0, 0, 0.5);
    text-align: center;
    padding: 3px 0;
    margin: -16px 0 10px -20px;
    border-top: 1px solid darkred;
    border-bottom: 1px solid darkred;
}

#enableDonations {
    float: left;
    display: inline-block;
    width: auto;
}

</style>

<template>
    <div id="startPreview">Mod Page Preview Below</div>

    <ModView :preview=true></ModView>

    <div id="endPreview">End of Preview</div>

    <Form @submit="saveData">
        <FormComponent class="col12" label="Add a donation link (either Ko-Fi or Patreon)">

            <Field name="donation_link"
                   rules="donationUrl"
                   type="text"
                   placeholder="Donation URL"
                   v-model="mod.donation_link"
                   maxlength="1000" />

            <ErrorMessage name="donation_link" class="validationError" />
        </FormComponent>
        <br />
        <br />
        <input type="checkbox" name="show_donation_link" v-model="mod.show_donation_link" :checked="mod.show_donation_link?'checked':''" />  Display your donation link on this mod?<br />
        <br />
        <br />

        Would you like to publish this mod?<br />
        <br />
        <input type="radio" name="publish" value=false :checked="mod.creation_status !== 'live'" /> No <small>(this mod will show under your private mod directory as complete but will not display to other users in the global Submodica mod directory)</small><br />
        <br />
        <input id="publish" type="radio" name="publish" value=true :checked="mod.creation_status === 'live'" /> Yes <small>(this mod will appear in the global Submodica mod directory to other users)</small>
        <br />
        <br />
        <ModNav page="preview" :status="mod.creation_status"  />
        <br />
    </Form>
</template>

<script>
import ModView from "../../components/mod/View.vue";
import ModNav from "../../components/mod/ModNav.vue";

import { Form, ErrorMessage, Field } from 'vee-validate'
import FormComponent from "../../components/forms/FormComponent.vue";

export default {
    name: "Preview",
    data() {
        return {
            user: window.user,
            mod: {
                creation_status: 'preview',
                donation_link: '',
            },
        };
    },
    components: {
        Form,
        Field,
        ModNav,
        ModView,
        ErrorMessage,
        FormComponent
    },
    methods: {
        saveData() {
            let publish = document.querySelector("#publish").checked;

            this.$axios.post(this.$route.path, {publish: publish, mod: this.mod})
                .then((response) => {
                    this.$root.alerts.push({
                        type: response.data.success ? "success" : "error",
                        content: response.data.message
                    });

                    if (publish)
                        this.$router.push(`/mods/${this.$route.params.game}/${this.$route.params.mod}`);
                    else
                        this.$router.push({ name: 'manageMods' });
                });
        }
    },
    mounted() {
        this.$axios.post(`/mods/${this.$route.params.mod}/fetch`)
            .then((response) => {
                if (response.data.success) {
                    this.mod = response.data.data.mod;

                    if (!this.mod.donation_link)
                        this.mod.donation_link = this.user.donation_link;
                } else
                    this.$router.push({ name: "404" });
            });
    }
}
</script>
