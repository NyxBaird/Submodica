<style scoped lang="scss">
@import "../../../../sass/variables";

$pageMargin: 20px;
$imageOverlayOpacity: 0.25;

#cover {
    position: relative;
    top: 0;
    left: 0;
    width: calc(100% + 40px);
    height: 200px;

    margin-top: -$pageMargin;
    margin-left: -$pageMargin;

    border-bottom: 2px solid $textColor;

    background-size: cover;
    background-repeat: no-repeat;
    background-position: center center;

    &.roundTop {
        border-top-left-radius: $corners;
        border-top-right-radius: $corners;
    }

    #coverText {
        position: absolute;
        bottom: 0;
        left: 260px;

        z-index: 50;

        margin: 0 0 15px 15px;

        font-family: "DaysOne-Regular", sans-serif;
        text-shadow: 0 0 11px rgba(0, 56, 113, 1);

        .title {
            font-size: 4vw;
            line-height: 3.65vw;
            margin-bottom: -5px;

            small {
                font-size: 22px;
                font-family: "Roboto", sans-serif;
            }
        }

        .tinyData {
            font-size: 14px;
            font-family: "Roboto", sans-serif;
            background: rgba(0, 0, 0, $imageOverlayOpacity);
            padding: 2px 4px;
            border-radius: 5px;
        }
    }
}

#icon {
    position: relative;
    width: 180px;
    height: 180px;
    border-radius: 90px;
    border: 1px solid $textColor;
    margin-top: -130px;
    margin-left: 50px;
    z-index: 100;
    background-color: $textColor;
    background-position: center; /* Center the image */
    background-repeat: no-repeat; /* Do not repeat the image */
    background-size: cover;
}

#modPanels {
    position: relative;
    margin-top: -16px;
    margin-left: -$pageMargin;
    width: calc(100% + $pageMargin * 2);

    ol {
        padding: 0;
        list-style-type: none;

        .panel {
            $background: #026CD6;

            border-top: 1px solid $bordersColor;

            &:last-of-type:not(.active) > .title {
                border-bottom-left-radius: $corners;
                border-bottom-right-radius: $corners;
            }

            .title {
                padding: 5px 10px;
                background-color: $background;
                box-shadow: 0 0 10px 10px #1c83eb inset;

                font-size: 20px;
                font-family: "DaysOne-Regular", sans-serif;
                cursor: pointer;
            }

            .content {
                padding: $pageMargin;
                background: $background;

                &.description {
                    padding: 0;
                }
            }

            @include mobile() {
                .content {
                    font-size: 8px;
                }
            }

            &:last-of-type:not(.preview) {
                margin-bottom: -36px;

                .content {
                    $borderRadius: 18px;
                    border-bottom-left-radius: $borderRadius;
                    border-bottom-right-radius: $borderRadius;
                }
            }
        }
    }
}
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

input[type='radio'] {
    cursor: pointer;
}

#actionBtns {
    position: absolute;
    right: 0;
    padding: 10px 10px;
    background: rgb(0, 0, 0, $imageOverlayOpacity);
    border-top-right-radius: 17px;
    border-bottom-left-radius: 17px;
    z-index: 55;

    svg {
        cursor: pointer;
    }

    a {
        color: $textColor;
    }
}

#reportForm {
    color: black;
}

#customDependencies {
    margin-top: 5px;
    font-size: 13px;
}

#kofiLink {
    position: relative;
    display: inline-block;
    height: 35px;
    width: 35px;

    top: -7px;
    left: 3px;
    margin-bottom: -20px;
    margin-right: -2px;

    background-image: url("https://storage.ko-fi.com/cdn/brandasset/kofi_s_logo_nolabel.png");
    background-size: cover;
}

</style>

<template>
    <div id="modView">
        <div id="cover"
             :class="[!preview ? 'roundTop' : '']"
             :style="{'background-image': `url(${this.images.cover})`}">

            <div id="actionBtns" v-if="!preview">
                <span :title="mod.isFavorite ? 'Remove from favorites' : 'Favorite this mod'">
                    <font-awesome-icon :icon="['fas', 'star']" v-if="mod.isFavorite" @click="toggleFavorite" />
                    <font-awesome-icon :icon="['far', 'star']" v-if="!mod.isFavorite" @click="toggleFavorite" />
                </span>

                <span title="Report this mod">
                    &nbsp;<font-awesome-icon icon="flag" @click="checkReport" />
                </span>

                <span v-if="typeof user.id !== 'undefined' && (user.id === mod.user_id || user.role.level >= 90)">
                    <span title="Edit mod">
                        &nbsp;<font-awesome-icon icon="cog" @click="this.$router.push(`/mods/${this.$route.params.game}/${this.$route.params.mod}/edit`)" />
                    </span>
                    <span title="Duplicate mod">
                        &nbsp;<font-awesome-icon icon="copy" @click="duplicate" />
                    </span>
                </span>

                <a :href="mod.repo_url" target="_blank" title="Visit this mods git repository" @click="visitGithub">
                    &nbsp;<font-awesome-icon :icon="['fab', 'github']" />
                </a>

                <a v-if="mod.donation_link && mod.show_donation_link" :href="mod.donation_link" target="_blank" title="Donate to the creator of this mod!">
                    <span v-if="mod.donation_link.indexOf('patreon.com') > -1">
                        &nbsp;<font-awesome-icon :icon="['fab', 'patreon']" />
                    </span>
                    <span v-else>
                        <a id="kofiLink">&nbsp;</a>
                    </span>
                </a>
            </div>

            <div id="coverText">
                <div class="title" v-if="mod.show_cover_title">{{ mod.title }} <small>by {{ mod.creator }}</small></div>
                <span class="tinyData">
                    <span v-if="showMaintainer"> <!--temp workaround for nautilus shouldn't show maintainer-->
                        Maintained By: {{ mod.maintainer }}
                    </span>
                    <span v-if="mod.latest_version">
                        <span v-if="showMaintainer">|</span>
                        Latest: {{ mod.latest_version }}
                    </span>
                </span>
            </div>
        </div>

        <div id="icon" :style="{'background-image': `url(${this.images.icon})`}"></div>

        <Gallery :mod="mod" :images="images.gallery"></Gallery>

        <div id="modPanels" :class="[!preview ? 'roundBottom' : '']">
            <ol>
                <li class="panel">
                    <div class="title" @click="show.downloads = !show.downloads">
                        <font-awesome-icon :icon="'chevron-' + (show.downloads ? 'down' : 'right')" />
                        Download Links
                    </div>
                    <div v-if="show.downloads" class="content">
                        <Downloads :dependencies="mod.attributions" :mod="mod"></Downloads>
                    </div>
                </li>
                <li class="panel" id="descriptionPanel">
                    <div class="title" @click="show.description = !show.description">
                        <font-awesome-icon :icon="'chevron-' + (show.description ? 'down' : 'right')" />
                        Description
                    </div>
                    <div v-if="show.description" class="content description">
                        <WYSIWYG ref="description"
                                 name="description"
                                 :display-only=true
                                 :json-content="mod.description"
                                 placeholder="Loading..." label="" cols=12 />
                    </div>
                </li>
                <li :class="'panel' + (preview ? ' preview':'')">
                    <div class="title" @click="show.attributions = !show.attributions">
                        <font-awesome-icon :icon="'chevron-' + (show.attributions ? 'down' : 'right')" />
                        Attributions
                    </div>
                    <div v-if="show.attributions" class="content">
                        <div v-if="mod.misc_attributions" id="miscAttrs">
                            {{ mod.misc_attributions }}
                        </div>
                        <br />
                        <div v-if="typeof mod.attributions !== 'undefined' && mod.attributions.length > 0" id="attrs">
                            This mod depends on these other great mods to work!<br />
                            <span id="customDependencies" v-for="dependency in mod.attributions">
                                <span v-if="!dependency.attributed_mod">
                                    &nbsp;&nbsp;&nbsp;&nbsp;• <a :href="dependency.url" target="_blank">{{ dependency.name }}</a><br />
                                </span>
                                <span v-else>
                                    &nbsp;&nbsp;&nbsp;&nbsp;• <a :href="`/mods/${dependency.attributed_mod.game}/${dependency.attributed_mod.id}`" target="_blank">{{ dependency.attributed_mod.title }}</a><br />
                                </span>
                            </span>
                        </div>
                    </div>
                </li>
                <li v-if="!preview" :class="'panel ' + (show.comments ? 'active' : '')" id="commentsPanel">
                    <div class="title" @click="show.comments = !show.comments">
                        <font-awesome-icon :icon="'chevron-' + (show.comments ? 'down' : 'right')" />
                        Comments
                    </div>
                    <div v-if="show.comments" class="content">
                        <Comments :mod="mod" />
                    </div>
                </li>
            </ol>
        </div>
    </div>

    <Modal title="Report this mod" ref="reportModal">
        <template #default>
            <Form id="reportForm">
                <br />
                <FormComponent classes="col12" label="Why are you reporting this mod?">
                    <Field rules="required" name="reason" as="select" v-model="report.reason">
                        <option value="" selected="selected">-- Select a reason --</option>
                        <option value="inappropriate">Contains inappropriate or offensive content</option>
                        <option value="permissions">Mod entry creator does not have permission to post this content</option>
                        <option value="other">Other (please explain below)</option>
                    </Field>
                </FormComponent>

                <FormComponent classes="col12" label="Briefly explain">
                    <Field rules="required"
                           name="details"
                           as="textarea"
                           rows="10"
                           placeholder="Provide details here..."
                           maxlength="500" v-model="report.details" />
                </FormComponent>
            </Form>
        </template>
        <template #footer>
            <button class="btn" @click="this.$refs.reportModal.close()">Cancel</button>
            <button class="btn btnRed floatRight" @click="reportMod">Report</button>
        </template>
    </Modal>

    <Modal title="Unpublished Mod" ref="unpublishedModal">
        <template #default>
            <br />
            This mod is currently unpublished and not accessible. If you believe this to be in error, please contact the mod author for help.
        </template>
    </Modal>
</template>

<script>
import Modal from "../Modal.vue"
import ModNav from "./ModNav.vue"
import Gallery from "./Gallery.vue"
import Downloads from "./Downloads.vue"
import Comments from "./Comments.vue"

import {Form, Field, ErrorMessage} from 'vee-validate'
import FormComponent from "../forms/FormComponent.vue"
import WYSIWYG from "../forms/WYSIWYG.vue"

import {library} from "@fortawesome/fontawesome-svg-core"
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
import { faGithub, faPatreon } from "@fortawesome/free-brands-svg-icons"
import { faStar as starOpen } from "@fortawesome/free-regular-svg-icons"
import {faChevronRight, faChevronDown, faFlag, faCopy, faStar as starClosed} from "@fortawesome/free-solid-svg-icons"

library.add(faChevronRight);
library.add(faChevronDown);
library.add(faGithub);
library.add(faPatreon);
library.add(faFlag);
library.add(faCopy);
library.add(starOpen);
library.add(starClosed);

export default {
    name: "View",
    data() {
        return {
            user: {
                name: ""
            },
            mod: {
                creation_status: '',
                isFavorite: false
            },
            images: {
                cover: '',
                icon: '',
                gallery: []
            },
            show: {
                attributions: true,
                description: false,
                downloads: false,
                comments: true
            },
            editor: null,
            report: {
                reason: '',
                details: ''
            },
            showMaintainer: (this.$route.params.mod!=246 && this.$route.params.mod!=247)
        };
    },
    props: {
        preview: {
            type: Boolean,
            default: false
        }
    },
    methods: {
        hasAttributions() {
            return (typeof this.mod.attributions !== 'undefined' && this.mod.attributions.length > 0) || this.mod.misc_attributions;
        },
        checkReport() {
            if (typeof this.user.id === 'undefined')
                this.$root.alerts.push({
                    type: "error",
                    content:"Please log in to report a mod!"
                });
            else
                this.$refs.reportModal.open();
        },
        reportMod() {
            this.$axios.post(`/mods/${this.$route.params.mod}/report`, this.report)
                .then((response) => {
                    this.$root.alerts.push({
                        type: response.data.success ? "success" : "error",
                        content: response.data.message
                    });

                    if (response.data.success)
                        this.$refs.reportModal.close()
                });
        },
        toggleFavorite() {
            if (typeof this.user.id === 'undefined') {
                window.alert("Please log in to favorite a mod!");
                return;
            }

            this.$axios.post(`/mods/${this.$route.params.mod}/favorite`)
                .then((response) => {
                    this.$root.alerts.push({
                        type: response.data.success ? "success" : "error",
                        content: response.data.message
                    });

                    this.mod.isFavorite = response.data.data.favorite;
                });
        },
        visitGithub() {
            if (this.mod.repo_url === null)
                this.$root.alerts.push({
                    type: "error",
                    content: "Due to an update this mods GitHub repo will not be visitable until the mod author resubmits the mod from the mods preview page. If you're seeing this after the official launch of Submodica please inform Nyx.",
                    seconds: 10
                });
        },
        duplicate() {
            if (window.confirm("Are you sure you want to duplicate this mod?"))
                this.$router.push(`/mods/${this.$route.params.game}/${this.$route.params.mod}/duplicate`);
        }
    },
    components: {
        Form,
        Modal,
        Field,
        ModNav,
        Gallery,
        WYSIWYG,
        Comments,
        Downloads,
        ErrorMessage,
        FormComponent,
        FontAwesomeIcon
    },
    mounted() {
        this.user = user;
        this.$axios.post(`/mods/${this.$route.params.mod}/fetchWithAll`)
            .then((response) => {
                if (response.data.success) {
                    //If the mod isn't live, stop us right here.
                    if (response.data.data.creation_status !== 'live' && this.$route.name !== "editModPreview") {
                        let timeout = 3,
                            vue = this;

                        this.$root.alerts.push({
                            type: "error",
                            content: "This mod is not published yet! Sending you back...",
                            seconds: timeout
                        });

                        if (window.history.length > 1)
                            window.setTimeout(function () {
                                vue.$router.go(-1);
                            }, timeout * 1000);
                        else
                            this.$refs.unpublishedModal.open();

                        return;
                    }

                    this.mod = response.data.data;

                    for (let i = 0; i < this.mod.images.length; i++)
                        if (this.mod.images[i].type === 'cover')
                            this.images.cover = this.mod.images[i].source;
                        else if (this.mod.images[i].type === 'icon')
                            this.images.icon = this.mod.images[i].source;
                        else
                            this.images.gallery.push(this.mod.images[i]);

                    this.show.description = true;

                    if (!this.hasAttributions())
                        this.show.attributions = false;
                } else
                    this.$router.push({ name: "404" });
            });

    }
}
</script>
