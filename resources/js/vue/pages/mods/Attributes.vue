<style scoped lang="scss">
@import "../../../../sass/variables";
@import 'vue-select/dist/vue-select.css';

h2 {
    margin: 0;
    font-family: "DaysOne-Regular", sans-serif;
}

form {
    width: 100%;

    table {
        color: $textColor;
        width: 100%;

        margin-top: -10px;

        td button {
            width: 50%;
        }

        tr td:last-of-type {
            button {
                width: 100%;
                border-radius: 5px;
            }
        }
    }
}
</style>

<template>
    <div>
        <h2>Dependencies & Attributions</h2>
        <hr />
        <p>

        </p>
        <Form @submit="saveModAttrs">
            First select your mods dependencies. When a user clicks to download your mod, they will be prompted to download these mods first.

            <FormComponent :classes="'col12'" label="Search Submodica for a mod">
                <v-select placeholder="Start typing to search for a mod" @search="modSearch" :options="options" v-model="requiredMods" multiple>
                    <template v-slot:no-options="{ search, searching }">
                        <template v-if="searching">
                            No results found for <em>{{ search }}</em>
                        </template>
                        <em v-else style="opacity: 0.5">Waiting for your input...</em>
                    </template>
                </v-select>
            </FormComponent>
            <br />
            <br />
            If the mod you're trying to add doesn't appear in the list above, it's likely not been added to Submodica by it's creator yet. In that case you can go ahead and create a custom attribution for it below. Please remember to link to the actual mod description page and not just the download itself so as to properly credit the creator!<br />
            <br />
            <FormComponent :classes="'col12'" :label="!this.customAttributions.length ? 'Add custom attributes for mods not on Submodica' : ''">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 35%"></th>
                            <th style="width: 60%"></th>
                            <th style="width: 5%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="attr in customAttributions" class="attribution">
                            <td colspan="2">
                                <input type="hidden" v-model="attr.name" />
                                <input type="hidden" v-model="attr.url" />
                                {{ attr.name }} -
                                <a :href="attr.url" target="_blank">{{ attr.url }}</a>
                            </td>
                            <td>
                                <button class="btnRed" type="button" @click="removeCustomAttribution">
                                    <font-awesome-icon icon="x" />
                                </button>
                            </td>
                        </tr>
                        <tr class="newAttribution">
                            <td>
                                &nbsp;
                                <ErrorMessage name="customAttributionName" class="validationError" />

                                <Field ref="customAttributionName"
                                       type="text"
                                       name="customAttributionName"
                                       placeholder="Mod Name"
                                       v-model="newAttribution.name"
                                       maxlength="100" />
                            </td>
                            <td>
                                &nbsp;
                                <ErrorMessage name="customAttributionUrl" class="validationError" />

                                <Field ref="customAttributionUrl"
                                       rules="attrUrl"
                                       type="text"
                                       name="customAttributionUrl"
                                       placeholder="Creators Description Page Url"
                                       v-model="newAttribution.url"
                                       maxlength="100" />
                            </td>
                            <td>
                                &nbsp;
                                <button class="btnGreen" type="button" @click="addCustomAttribution">
                                    <font-awesome-icon icon="plus" />
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </FormComponent>
            <br />
            If you were unable to add a required mod to either list above, please contact Nyx so we can figure it out!<br />
            <br />
            <br />
            Go ahead and attribute anyone who deserves credit here! Note that your dependencies will be shown separately at the bottom of your attributions.
            <FormComponent :classes="'col12'" label="Is there anything else you'd like to add? Say what you need to here.">
                <Field ref="additionalInfo"
                       as="textarea"
                       name="additionalInfo"
                       placeholder="Additional Info"
                       v-model="additionalInfo"
                       maxlength="240" />

                <ErrorMessage name="additionalInfo" class="validationError" />
            </FormComponent>
            <br />
            <br />
            <ModNav page="attributions" :status="mod.creation_status" />
            <br />
        </Form>
    </div>
</template>
<script>
    import ModNav from "../../components/mod/ModNav.vue"

    import { defineRule, Form, Field, ErrorMessage } from 'vee-validate'
    import FormComponent from "../../components/forms/FormComponent.vue"
    import vSelect from 'vue-select'

    //Font Awesome
    import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
    import {library} from "@fortawesome/fontawesome-svg-core"
    import {faPlus, faX} from "@fortawesome/free-solid-svg-icons"

    library.add(faX);
    library.add(faPlus);

    //Temp rule to prevent console error. This is overwritten in mounted()
    defineRule('attrUrl', value => {
        return "The validator has not loaded in properly. Please refresh the page or contact Nyx for help."
    });

    export default {
        name: "EditModAttributes",
        data() {
            return {
                user: window.user,
                mod: {
                    id: this.$route.params.mod,
                    creation_status: ''
                },
                repos: [],
                requiredMods: [],
                customAttributions: [],
                newAttribution: {
                    name: '',
                    url: ''
                },
                options: [],
                additionalInfo: ''
            };
        },
        components: {
            Form,
            vSelect,
            FormComponent,
            Field,
            ModNav,
            ErrorMessage,
            FontAwesomeIcon
        },
        methods: {
            back() {
                if (window.confirm("Are you sure you want to leave this page? Leaving will discard any unsaved changes you've made"))
                    this.$router.push(`/mods/${this.$route.params.game}/${this.mod.id}/edit`);
            },
            saveModAttrs(values) {
                let nextUrl = `/mods/${this.$route.params.game}/${this.mod.id}/edit/downloads`;

                //If there's un-added custom attribution info, make sure the user really wants to continue without saving it
                if (this.newAttribution.name.length || this.newAttribution.url.length)
                    if (!window.confirm("You've entered custom attribution data but not added it to the list (try pressing the plus button to the right side of it). Are you sure you want to continue?"))
                        return;

                //Continue without attributions
                if (!this.customAttributions.length || !this.requiredMods.length)
                    this.$router.push(nextUrl);

                this.$axios.post(`/mods/${this.$route.params.game}/${this.$route.params.mod}/edit/saveAttributes`, {
                    local: this.requiredMods,
                    custom: this.customAttributions,
                    additionalInfo: this.additionalInfo
                }).then((response) => {
                    this.$root.alerts.push({
                        type: "success",
                        content: "Mod attributions were saved successfully!"
                    });

                    this.$router.push(nextUrl);
                });
            },
            modSearch(search, loading) {
                if(search.length) {
                    loading(true);

                    this.$axios.post(`/mods/${this.$route.params.game}/search`, {q: search}).then((response) => {
                        this.options = response.data.data;
                        loading(false);
                    });
                }
            },
            addCustomAttribution() {
                if (!this.newAttribution.name.length)
                    this.$refs.customAttributionName.setErrors(['This field is required']);

                if (!this.newAttribution.url.length)
                    this.$refs.customAttributionUrl.setErrors(['This field is required']);

                if (!this.newAttribution.name.length || !this.newAttribution.url.length)
                    return;

                this.$refs.customAttributionUrl.validate().then((result) => {
                    if (result.valid) {
                        this.customAttributions.push(this.newAttribution);
                        this.newAttribution = {
                            name: '',
                            url: ''
                        };
                    }
                });
            },
            removeCustomAttribution(index) {
                this.customAttributions.splice(index, 1);
            },
            loadAttributions() {
                this.$axios.post(`/mods/${this.$route.params.game}/${this.$route.params.mod}/edit/attributions/fetch`).then((response) => {
                    let attributions = response.data.data.attributions;

                    for (let i in attributions) {
                        if (attributions[i].attributed_mod)
                            this.requiredMods.push({
                                code: attributions[i].local_attribution_id,
                                label: attributions[i].attributed_mod.title
                            });
                        else
                            this.customAttributions.push({
                                name: attributions[i].name,
                                url: attributions[i].url
                            });

                    }

                    this.additionalInfo = response.data.data.additionalInfo;
                });
            }
        },
        mounted() {
            defineRule('attrUrl', value => {
                if (!value.length)
                    return true;

                //Get rid of any trailing slashes
                if (value.slice(-1) === '/')
                    value = value.slice(0, -1);

                let pieces = value.split('/'),
                    domainIndex = 0; //by default the domain is the first piece in our url

                //If this is true the url contains http:// or https:// and the domain should be piece 2
                if (typeof pieces[1] !== 'undefined' && pieces[1].length === 0)
                    domainIndex = 2;

                let domain = pieces[domainIndex];
                domain = domain.replace("www.", "");

                console.log("Analyzing URL from " + domain + " (" + value + ")");

                //Thunderstore has this weird case on download urls that we want to catch
                if (value.startsWith('ror2mm://'))
                    return "This is a mod manager download. Please link the mod's description page instead.";

                if (['subnautica.thunderstore.io', 'belowzero.thunderstore.io'].indexOf(domain) > -1) {
                    //Thunderstore stuff
                    if (pieces[pieces.length - 1].match(/^(\d*\.?){2,3}/)[0].length)
                        return "This is a mod download. Please link the mod's description page instead.";
                } else if (domain === 'nexusmods.com') {

                    //Nexus stuff
                    let nexusPieces = pieces[pieces.length - 1].split("?");
                    if (!/^-?\d+$/.test(nexusPieces[0]) || ['subnautica', 'subnauticabelowzero'].indexOf(pieces[domainIndex + 1]) === -1)
                        return "This is not a valid Nexus Mods mod description page.";
                    else
                        pieces[pieces.length - 1] = nexusPieces[0];

                    this.newAttribution.url = pieces.join('/');
                } else if (domain === 'github.com') {
                    //Github stuff
                } else {
                    return 'For security reasons Submodica currently only supports attributions from known sources. Right now that means Thunderstore, Nexus Mods, and Github.';
                }

                return true;
            });

            this.$axios.post('/mods/' + this.$route.params.mod + '/fetch').then((response) => {
                this.mod = response.data.data.mod;
            });

            this.loadAttributions();
        }
    }
</script>
