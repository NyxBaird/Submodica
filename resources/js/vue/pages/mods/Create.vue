<style scoped lang="scss">
@import "../../../../sass/variables";

h2 {
    margin: 0;
    font-family: "DaysOne-Regular", sans-serif;
}

form {
    width: 100%;

    .formComponent {
        display: inline-block;
        padding: 10px;
    }
}

.tagList {
    $tagsMargin: 10px;

    width: calc(50% - ($tagsMargin * 2));
    margin: $tagsMargin;
    font-size: 14px;

    @include mobile() {
        width: calc(100% - ($tagsMargin * 2));
    }

    .tag {
        $tagVPadding: 5px;
        $tagHPadding: 10px;

        display: inline-block;
        width: calc(100% - ($tagHPadding * 2));
        background-color: rgb(0, 0, 0, 0.2);
        padding: $tagVPadding $tagHPadding;
        border-radius: 5px;
        margin-bottom: 8px;

        .x {
            float: right;
            cursor: pointer;
            margin-top: 1px;
        }
    }
}

#iAgreeLabel {
    font-size: 14px;
    padding: 10px;
}
</style>

<template>
    <div>
        <h2>
            <span v-if="typeof mod.id !== 'undefined'">
                {{ this.$route.name === "duplicateMod" ? "Duplicate" : "Edit" }}
            </span>
            <span v-else>Add</span>
            A Mod
        </h2>
        <hr />
        <p v-if="this.$route.name !== 'duplicateMod'">
            Please note that you are only allowed to add mods that you have created yourself or that you have direct permission from the original author to post. Users found to be in violation of this rule will be suspended and any offending mods added by them removed.<br />
            <br />
            You will be given the opportunity to add attributions, an image, and download links after you submit the basic information for your mod.
        </p>
        <p v-else>
            You are currently duplicating a mod! Please change any information you'd like to be different on the "cloned" mod and then hit the "Save & Continue" button at the bottom of the page to confirm the cloning process.
        </p>
        <Form @submit="saveModBasics">
            <input v-if="typeof mod.id !== 'undefined'" type="hidden" name="id" :value="mod.id" />

            <FormComponent :classes="'col12'" label="Mod Repository <small>(You'll be able to specify download links later)</small>">
                <Field rules="required" name="repo_id" as="select" v-model="mod.repo_id" :disabled="typeof mod.id !== 'undefined'">
                    <option value="">Select a repository</option>
                    <option v-for="repo in repos"
                            :key="repo.id"
                            :value="repo.id"
                    >
                        {{ repo.name }}
                    </option>
                </Field>

                <ErrorMessage name="repo_id" class="validationError" />
            </FormComponent>

<!--            <FormComponent :classes="'col12'" label="Creator or Team">-->
<!--                <FileTree :repo="RepoName" />-->
<!--            </FormComponent>-->

            <FormComponent :classes="'col12'" label="Creator or Team">
                <Field rules="required"
                       name="creator"
                       type="text"
                       placeholder="The creator or team behind the mod"
                       v-model="mod.creator"
                       maxlength="100" />

                <ErrorMessage name="creator" class="validationError" />
            </FormComponent>

            <FormComponent :classes="'col12'" label="Mod Title">
                <Field rules="required"
                       name="title"
                       type="text"
                       placeholder="Enter the name of the mod"
                       v-model="mod.title"
                       maxlength="100" />

                <ErrorMessage name="title" class="validationError" />
            </FormComponent>

            <FormComponent :classes="'col12'" label="Tagline">
                <Field name="tagline"
                       type="text"
                       placeholder="Enter a brief description of the mod to get users interested"
                       v-model="mod.tagline"
                       maxlength="255" />

                <ErrorMessage name="tagline" class="validationError" />
            </FormComponent>

            <WYSIWYG rules="required" :limit="20000" ref="description" name="description" placeholder="Enter a description of the mod" label="Description" cols=12></WYSIWYG>

            <FormComponent id="searchTags" :classes="'col12'" label="Add Search Tags">
                <v-select placeholder="Add some tags to help users find your mod"
                          @search="tagSearch" :options="tagOptions" v-model="mod.tags"
                          code="id" label="tag"
                          multiple taggable>
                    <template v-slot:no-options="{ search, searching }">
                        <template v-if="searching">
                            No results found for <em>{{ search }}</em>
                        </template>
                        <em v-else style="opacity: 0.5">Waiting for your input...</em>
                    </template>
                </v-select>
            </FormComponent>

            <FormComponent v-if="$route.params.game==='sn1'" :classes="'col12'" label="Subnautica Compatibility">
                <Field rules="required" name="subnautica_compatibility" as="select" v-model="mod.subnautica_compatibility">
                    <option value="legacy">Legacy (Before Subnautica v2.0)</option>
                    <option value="2.0">Current (After Subnautica v2.0)</option>
                </Field>

                <ErrorMessage name="repo_id" class="validationError" />
            </FormComponent>
            <input v-else type="hidden" name="subnautica_compatibility" value="legacy" />

            <Checkbox v-if="typeof mod.id === 'undefined' || (typeof mod.id !== 'undefined' && this.$route.name === 'duplicateMod')"
                rules="terms"
                :checked="false"
                name="terms"
                label="I have read and agree to the <a href='/terms' target='_blank'>Submodica's Terms Of Service</a> and the mod I'm adding conforms to all of <a href='/rules' target='_blank'>Submodica's Rules</a>. By continuing I'm verifying that I have the rights to post this content here and I'm granting Submodica license to host and serve to its users such content and any updates I may add to this listing. I acknowledge that this does not give Submodica any rights to the content itself and that I'm responsible for the maintenance and upkeep of both the code itself and this Submodica listing.<br /><br />Check here to verify you have read and agree to the above" cols=12 />
            <br />

            <ModNav page="index" :status="mod.creation_status" />
            <br />
        </Form>
    </div>
</template>

<script>
//Libraries
import { defineRule, Form, Field, ErrorMessage } from 'vee-validate'
import vSelect from 'vue-select'

//Custom code
import WYSIWYG from "../../components/forms/WYSIWYG.vue"
import Checkbox from "../../components/forms/Checkbox.vue"
import FormComponent from "../../components/forms/FormComponent.vue"
import ModNav from "../../components/mod/ModNav.vue";
import FileTree from "../../components/forms/FileTree.vue"

//Font Awesome
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
import {library} from "@fortawesome/fontawesome-svg-core"
import {faPlus, faX} from "@fortawesome/free-solid-svg-icons"

library.add(faX);
library.add(faPlus);

//Define a custom validation rule for our terms checkbox
defineRule('terms', value => {
    if (typeof value == "undefined" || !value || (isNaN(value) && !value.length))
        return 'You must agree to the Terms Of Service and the Rules to add a mod';

    return true;
});

export default {
    name: "Sn1Mods",
    data() {
        return {
            user: window.user,
            maxTagLength: 40,
            mod: {
                repo_id: '',
                creator: "",
                title: "",
                tags: [],
                description: "",
                creation_status: "index",
                subnautica_compatibility: "legacy"
            },
            repos: [],
            tagInput: "",
            tagOptions: [],
        };
    },
    components: {
        Checkbox,
        WYSIWYG,
        Form,
        Field,
        ModNav,
        ErrorMessage,
        FontAwesomeIcon,
        FormComponent,
        vSelect
    },
    methods: {
        tagSearch(search, loading) {
            if(search.length) {
                loading(true);

                this.$axios.post(`/tags/search`, {q: search}).then((response) => {
                    this.tagOptions = response.data.data;
                    loading(false);
                });
            }
        },
        saveModBasics(values) {
            let tagInput = document.querySelector('#searchTags .vs__search');
            if (tagInput.value.length) {
                if (!window.confirm("You have entered search tag data without actually adding it as a tag (try clicking the entry that pops up below the search tag input type there). Are you sure you'd like to continue without adding your tag, \"" + tagInput.value + "\"?")) {
                    return;
                }
            }

            this.mod.repo_id = values.repo_id;
            this.mod.creator = values.creator;
            this.mod.title = values.title;
            this.mod.description = this.$refs.description.editor.getJSON();
            this.mod.terms = values.terms;

            this.$axios.post(this.$route.path, this.mod)
                .then((response) => {
                    let mod = response.data.data;

                    this.$root.alerts.push({
                        type: response.data.success ? "success" : "error",
                        content: response.data.message
                    });

                    if (response.data.success)
                        this.$router.push({
                            name: "editModAttrs",
                            params: {
                                game: this.$route.params.game,
                                mod: mod.id
                            }
                        });
                });
        }
    },
    mounted() {
        this.$axios.get('/api/mods/fetchRepos').then((response) => {
            this.repos = response.data.data;
        });

        if (this.$route.params.mod) {
            this.$axios.post('/mods/' + this.$route.params.mod + '/fetch').then((response) => {
                this.mod = response.data.data.mod;
                this.mod.tags = response.data.data.tags;
                this.$refs.description.editor.commands.setContent(JSON.parse(this.mod.description));
            });
        }
    },
    computed: {
        RepoName() {
            return typeof this.repos.find(repo => repo.id === this.mod.repo_id) === 'undefined'
                ? ''
                : this.repos.find(repo => repo.id === this.mod.repo_id).name;
        }
    }
}
</script>
