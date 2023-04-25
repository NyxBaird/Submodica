<style lang="scss">
$controlPaddings: 5px;
.commentComposer {
    margin-top: 0;
    margin-bottom: 0;
    width: 100%;

    .wysiwyg {
        width: calc(100% + 2px);
        margin-top: -1;
        padding-bottom: 3px;
    }
}

.wysiwyg {
    background: #CCE2F7;
}

.charsRemaining {
    color: black;
    padding-right: 5px;
}
</style>

<style lang="scss" scoped>
    @import "resources/sass/variables";

    $controlPaddings: 5px;

    .comment:not(.reply) {
        background: $containerBG;

        border: 1px solid #004d99;
        border-radius: 3px;
        margin-bottom: 0;

        &:first-child {
            margin-top: -5px;
        }
        &:last-of-type {
            margin-bottom: 5px;
        }

        $controlBG: rgba(0, 0, 0, 0.2);
        .header {
            width: calc(100% - $controlPaddings * 2);
            background: $containerBG;
            padding: $controlPaddings;

            svg {
                cursor: pointer;
                margin-right: 8px;
            }

            .author {
                $imgSize: 25px;

                a {
                    display: inline-block;
                    margin-top: -10px;

                    img {
                        position: relative;
                        top: 6px;

                        width: $imgSize;
                        height: $imgSize;
                        margin-top: -1px;
                        margin-left: 5px;
                        border-radius: 50px;
                    }

                    .username {
                        display: inline-block;
                        margin-left: 10px;
                    }
                }
            }

            .date {
                float: right;
                margin-right: 5px;

                .shrinkDate {
                    font-size: 12px;
                    font-style: italic;
                }
            }
        }

        .footer {
            padding: 5px;
            background: $containerBG;

            svg {
                cursor: pointer;
                margin-right: 8px;
            }
        }

        .content {
            margin-top: -2px;
            background-color: #CCE2F7;
        }

        .formComponent {
            padding: 0;
            width: calc(100% - 2px);
        }

        .comment {
            margin: 0 0 3px 15px;
            border-left: 5px solid rgba(0, 0, 0, 0.2);
        }
    }
    .comment.reply {
        .footer {
            color: #777;
            font-size: 10px;
            background: none;
            font-style: italic;
            margin-bottom: 5px;
            margin-top: -10px;

            a {
                color: $wysiwygLinkColor;
            }
        }
    }
    .spacer {
        height: 2px;
    }

    .saveChanges {
        width: calc(50% - 20px);
    }
    .cancelChanges {
        width: calc(50% - 20px);
    }
    .editTitle {
        width: calc(50% - 20px) !important;
        margin-bottom: 4px;
    }

    .commentTag {
        font-size: 10px;
        padding: 2px 5px;
        border-radius: 4px;
        display: inline-block;
        margin-bottom: 2px;
        margin-left: 8px;
        vertical-align: text-bottom;
    }
    .commentTag.pinned {
        background: $green;
    }
    .commentTag.locked {
        background: darkred;
    }
</style>

<template>
    <div :class="'comment ' + (isReply ? 'reply' : '') + (minimized ? ' minimized' : '')">
        <div v-if="!isReply" class="header">
            <span v-if="comment.title && !beingEdited(comment)">
                <font-awesome-icon
                    :title="minimized ? 'Expand this comment' : 'Minimize this comment'"
                    :icon="minimized ? 'plus' : 'minus'"
                    @click="toggleExpand" />

                <span v-if="user.id === mod.user_id">
                    <font-awesome-icon
                        title="Pin this comment"
                        icon="thumbtack"
                        @click="pin" />

                    <font-awesome-icon
                        title="Lock this comment"
                        :icon="comment.locked ? 'lock' : 'unlock-alt'"
                        @click="lock" />
                </span>

                {{ comment.title }}

                <span v-if="comment.pinned" class="commentTag pinned">Pinned!</span>
                <span v-if="comment.locked" class="commentTag locked">Locked!</span>
            </span>

            <FormComponent v-else-if="comment.title && beingEdited(comment)" classes="editTitle" label="">
                <Field :rules="'commentTitle:' + commentHasContent()"
                       name="title"
                       type="text"
                       ref="commentTitle"
                       placeholder="Comment title"
                       v-model="comment.title"
                       maxlength="70" />

                <ErrorMessage name="title" class="validationError" />
            </FormComponent>
            &nbsp;
            <span class="date">
                <span :class="comment.title ? 'shrinkDate' : ''">
                    <span v-if="comment.title"> posted </span>{{ comment.created_at }} (UTC) by
                </span>

                <span class="author">
                    <a :href="'/users/profile/' + comment.author.id">
                        <img :src="comment.author.avatar" alt="avatar">
                        <div class="username">{{comment.author.name}}</div>
                    </a>
                </span>
            </span>
        </div>

        <div v-if="!minimized" class="content">
            <WYSIWYG :name="`comment[${comment.id}]`"
                     ref="comment"
                     :display-only="!beingEdited(comment)"
                     :show-controls="beingEdited(comment)"
                     :json-content="comment.comment"
                     placeholder="Loading..."
                     :excludeControls="[
                        'fontsize',
                        'justify',
                        'youtube',
                        'links'
                     ]"
                     label="" cols=12 />

            <div v-if="isReply && !beingEdited(comment)" class="footer">
                Response by

                <a :href="'/users/profile/' + comment.author.id">{{comment.author.name}}</a>

                on {{comment.created_at}} (UTC)

                <span v-if="!locked">&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;</span>

                <a v-if="(user.id && user.discord_id) && !locked"
                    @click="toggleReplying">Reply</a>
                &nbsp;
                <a v-if="user.id !== comment.author.id"
                   @click="toggleReporting">Report</a>
                &nbsp;
                <a v-if="user.id === comment.author.id && !locked"
                   @click="edit(comment)">Edit</a>
                &nbsp;
                <a v-if="user.id === comment.author.id && !locked"
                   @click="deleteComment(comment.id)">Delete</a>
            </div>

            <div class="comments">
                <Comment v-for="reply in comments" :comment="reply" :locked=comment.locked :key="reply.id" :is-reply=true />
            </div>
        </div>

        <div v-if="user.id && !isReply && !beingEdited(comment) && !minimized" class="footer">
            <font-awesome-icon
                title="Reply to this comment"
                v-if="(user.id && user.discord_id) && !comment.locked"
                style="{'background-color': (beingEdited(comment) ? 'rgba(0, 0, 0, 0,5)' : 'none')}"
                icon="reply"
                @click="toggleReplying" />

            <font-awesome-icon
                title="Report this comment"
                v-if="user.id !== comment.author.id"
                icon="flag"
                @click="toggleReporting" />

            <font-awesome-icon
                title="Edit this comment"
                v-if="user.id === comment.author.id && !comment.locked"
                icon="pencil"
                @click="edit(comment)" />

            <font-awesome-icon
                title="Delete this comment"
                v-if="user.id === comment.author.id && !comment.locked"
                icon="trash"
                @click="deleteComment(comment.id)" />
        </div>
        <div v-else-if="beingEdited(comment)" class="footer">
            <button @click="saveChanges(comment)" class="saveChanges btn btnGreen">Save changes!</button>
            <button @click="edit(comment)" class="cancelChanges btn btnRed">Cancel</button>
        </div>

        <Form v-if="replying" class="commentComposer" @submit="saveReply">
            <WYSIWYG ref="reply"
                     name="comment"
                     :limit=1000
                     label="" cols=12
                     :excludeControls="[
                        'fontsize',
                        'justify',
                        'youtube',
                        'links'
                     ]"
                     :placeholder="'Respond to ' + comment.author.name" />

            <button type="submit" class="submitReply btn btnGreen col12">Reply!</button>
        </Form>
    </div>
<!--    <div class="spacer">&nbsp;</div>-->

    <Modal title="Report this comment" ref="reportModal">
        <template #default>
            <Form id="reportForm">
                <br />
                <FormComponent classes="col12" label="Why are you reporting this comment?">
                    <Field rules="required" name="reason" as="select" v-model="report.reason">
                        <option value="" selected="selected">-- Select a reason --</option>
                        <option value="inappropriate">Contains inappropriate or offensive content</option>
                        <option value="other">Other (please explain below)</option>
                    </Field>
                </FormComponent>
                <br />
                <br />
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
            <button class="btn btnRed floatRight" @click="reportComment">Report</button>
        </template>
    </Modal>
</template>

<script>
import Modal from "../Modal.vue";
import WYSIWYG from "../forms/WYSIWYG.vue"
import FormComponent from "../forms/FormComponent.vue"
import {Form, Field, ErrorMessage, defineRule, } from "vee-validate"

import { library } from "@fortawesome/fontawesome-svg-core"
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faFlag, faReply, faPencil, faTrash, faThumbtack, faLock, faUnlockAlt, faPlus, faMinus } from "@fortawesome/free-solid-svg-icons"

library.add(faFlag);
library.add(faPlus);
library.add(faLock);
library.add(faReply);
library.add(faTrash);
library.add(faMinus);
library.add(faPencil);
library.add(faThumbtack);
library.add(faUnlockAlt);

defineRule('commentTitle', (value, [writingComment]) => {
    writingComment = writingComment === 'true';

    if (writingComment && !value)
        return 'Comment title is required';

    return true;
});

export default {
    name: "Comments",
    components: {
        Form,
        Field,
        Modal,
        WYSIWYG,
        ErrorMessage,
        FormComponent,
        FontAwesomeIcon
    },
    data() {
        return {
            user: window.user,
            mod: this.$parent.$parent.mod,
            replying: false,
            comments: [],
            reply: '',
            report: {
                reason: '',
                details: ''
            },
            json: '',
            minimized: false
        };
    },
    props: {
        comment: {
            type: Object,
            required: true
        },
        isReply: {
            type: Boolean,
            default: false
        },
        locked: {
            type: Boolean,
            default: false
        }
    },
    methods: {
        saveReply() {
            this.$axios.post(`/mods/${this.$route.params.mod}/comment/${this.comment.id}`, {comment: this.$refs.reply.editor.getJSON()})
                .then((response) => {
                    this.$root.alerts.push({
                        type: response.data.success ? "success" : "error",
                        content: response.data.message
                    });

                    if (response.data.success) {
                        this.fetchReplies();
                        this.$refs.reply.editor.commands.clearContent(true);
                        this.replying = false;
                    }
                });
        },
        fetchReplies() {
            this.$axios.post(`/mods/${this.$route.params.mod}/fetchComments/${this.comment.id}`)
                .then((response) => {
                    this.comments = response.data.data;
                });
        },
        deleteComment(id) {
            console.log(id)
            if (window.confirm("Are you sure you want to delete this comment?"))
                this.$axios.delete(`/mods/${this.$route.params.mod}/comment/${id}`)
                    .then((response) => {
                        this.$root.alerts.push({
                            type: response.data.success ? "success" : "error",
                            content: response.data.message
                        });

                        if (response.data.success) {
                            this.$parent.comments = this.$parent.comments.filter((comment) => comment.id !== this.comment.id);
                        }
                    });
        },
        edit(comment) {
            if (comment.editting) {
                this.$refs.comment.editor.setEditable(false);
                this.$refs.comment.resetContent(JSON.stringify(this.json));
                comment.editting = false;
            } else {
                this.json = this.$refs.comment.editor.getJSON();
                this.replying = false;
                this.closeMainComment();

                this.$refs.comment.editor.setEditable(true);
                comment.editting = true;
            }
        },
        beingEdited(comment) {
            return typeof comment.editting !== 'undefined' && comment.editting;
        },
        commentHasContent() {
            return typeof this.$refs.comment !== 'undefined' ? !this.$refs.comment.editor.isEmpty : false;
        },
        toggleReplying() {
            if (!user.id || (user.id && !user.discord_id))
                return this.$root.alerts.push({
                    type: "error",
                    content: "Please log in with an account linked to Discord to reply to a comment!"
                });

            this.closeMainComment();

            this.replying = !this.replying;
        },
        toggleReporting() {
            if (!user.id)
                return this.$root.alerts.push({
                    type: "error",
                    content: "Please log in to report a comment!"
                });

            this.$refs.reportModal.open();
        },
        reportComment() {
            this.$refs.reportModal.close();

            this.$axios.post(`/mods/${this.$route.params.mod}/comment/${this.comment.id}/report`, this.report)
                .then((response) => {
                    this.$root.alerts.push({
                        type: response.data.success ? "success" : "error",
                        content: response.data.message
                    });
                });
        },
        closeMainComment() {
            let $n = this.$parent;
            while (!hasPostingNewComment($n)) {
                $n = $n.$parent;
            }

            $n.postingNewComment = false;

            function hasPostingNewComment($node) {
                return typeof $node.postingNewComment !== 'undefined';
            }
        },
        saveChanges(comment) {
            let data = {
                id: comment.id,
                title: comment.title,
                comment: this.$refs.comment.editor.getJSON()
            };
            this.$axios.post(`/mods/${this.$route.params.mod}/comment/${this.comment.id}`, data)
                .then((response) => {
                    this.$root.alerts.push({
                        type: response.data.success ? "success" : "error",
                        content: response.data.message
                    });

                    if (response.data.success) {
                        this.comment.content = this.json = this.$refs.comment.editor.getJSON();
                        this.comment.editting = false;
                        this.$refs.comment.editor.setEditable(false);
                    }
                });
        },
        lock() {
            this.$axios.post(`/mods/${this.$route.params.mod}/comment/${this.comment.id}/toggleLock`)
                .then((response) => {
                    this.$root.alerts.push({
                        type: response.data.success ? "success" : "error",
                        content: response.data.message
                    });

                    if (response.data.success)
                        this.comment.locked = response.data.data.locked;
                });
        },
        pin() {
            this.$axios.post(`/mods/${this.$route.params.mod}/comment/${this.comment.id}/togglePin`)
                .then((response) => {
                    this.$root.alerts.push({
                        type: response.data.success ? "success" : "error",
                        content: response.data.message
                    });

                    if (response.data.success)
                        this.comment.pinned = response.data.data.pinned;
                });
        },
        toggleExpand() {
            this.minimized = !this.minimized;
        }
    },
    mounted() {
        this.fetchReplies();
console.log(this.comment)
        setTimeout(() => {
            this.mod = this.$parent.$parent.mod;
        }, 100);
    }
}
</script>
