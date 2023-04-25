<style lang="scss">
$controlPaddings: 5px;
.comment {
    margin-top: 15px;

    .wysiwyg {
        border-radius: 0;
        border: none;

        [contenteditable]:focus {
            outline: none;
        }

        .ProseMirror {
            padding-top: 0;
            padding-bottom: 0;
        }
    }
}
.wysiwyg {
    width: calc(100% - 2px);
}

#newComment .col12 {
    padding-left: 0;
    padding-right: 0;
    margin-left: 0;
    margin-right: 0;
    width: 100%;
}
</style>

<style lang="scss" scoped>
    @import "resources/sass/variables";

    $controlPaddings: 5px;

    #commentsPane {
        $padding: 10px;

        width: calc(100% + $padding * 2);
        padding: 0 $padding;
        margin: -20px;
        margin-top: -10px;
        margin-bottom: -15px;
        color: $textColor;

        #commentsPlaceholder {
            $height: 52px;

            background: rgb(0, 0, 0, 0.4);
            height: $height;
            line-height: $height;

            margin-top: -20px;
            margin-left: -$padding;
            margin-bottom: 5px;

            width: calc(100% + $padding * 2);

            font-size: 20px;
            text-align: center;
            vertical-align: middle;
        }

        .submitComment {
            margin-top: -5px;
            padding: 5px 0;
        }

        .newCommentBtn:not(:last-of-type) {
            margin: 0 0 20px 0;
        }
        .newCommentBtn {
            text-align: center;
        }
    }
</style>

<template>
    <div id="commentsPane">
        <div v-if="!Object.keys(comments).length" id="commentsPlaceholder">
            This mod has no comments yet!
        </div>

        <div v-if="!postingNewComment" @click="postingNewComment = true" class="btn btnBlue newCommentBtn">
            Add a comment
        </div>

        <Form v-else id="newComment" @submit="saveComment">
            <div v-if="!user.id">
                <p>You must be logged in and have a verified Discord account to comment. <a @click="login">Login</a></p>
                <p></p>
            </div>
            <div v-else-if="!user.discord_id">
                <p>You must have a connected Discord account to comment. <a href="/users/profile">Click to Connect Your Discord</a></p>
            </div>
            <div v-else>
                <FormComponent :classes="'col12'" label="Add a brief title">
                    <Field :rules="'commentTitle:' + writingComment()"
                           name="title"
                           type="text"
                           ref="commentTitle"
                           placeholder="Comment title"
                           v-model="comment.title"
                           maxlength="70" />

                    <ErrorMessage name="title" class="validationError" />
                </FormComponent>

                <WYSIWYG ref="comment"
                         name="comment"
                         :limit=1000
                         label="" cols=12
                         :excludeControls="[
                            'fontsize',
                            'justify',
                            'youtube',
                            'links'
                         ]"
                         placeholder="Tell the mod author what you think!" />

                <button type="submit" class="submitComment btn btnGreen col12">Post Comment!</button>
            </div>
        </Form>

        <div v-if="Object.keys(comments).length">
            <Comment v-for="comment in comments" :comment="comment" />
        </div>
    </div>
</template>

<script>
import WYSIWYG from "../forms/WYSIWYG.vue"
import FormComponent from "../forms/FormComponent.vue"

import {Form, Field, ErrorMessage, defineRule} from "vee-validate"

import { library } from "@fortawesome/fontawesome-svg-core"
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faFlag, faReply, faThumbsUp } from "@fortawesome/free-solid-svg-icons"

import Comment from "./Comment.vue";

library.add(faFlag);
library.add(faReply);
library.add(faThumbsUp);

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
        Comment,
        WYSIWYG,
        ErrorMessage,
        FormComponent,
        FontAwesomeIcon
    },
    data() {
        return {
            user: window.user,
            postingNewComment: false,
            comments: [],
            comment: {
                id: 0,
                comment: ""
            }
        };
    },
    props: {
        mod: {
            type: Object,
            required: true
        }
    },
    methods: {
        saveComment() {
            if (!this.writingComment()) {
                this.$root.alerts.push({
                    type: "error",
                    content: "You haven't entered anything to save!"
                });
                return;
            }

            this.$refs.commentTitle.validate().then((result) => {
                if (result.valid) {
                    this.comment.comment = this.$refs.comment.editor.getJSON();
                    this.$axios.post(`/mods/${this.$route.params.mod}/comment`, this.comment)
                        .then((response) => {
                            this.$root.alerts.push({
                                type: response.data.success ? "success" : "error",
                                content: response.data.message
                            });

                            if (response.data.success) {
                                this.fetchComments();
                                this.$refs.comment.editor.commands.clearContent(true);
                                this.comment.title = "";
                                this.postingNewComment = false;
                            }
                        });
                }
            });
        },
        fetchComments() {
            this.comments = [];
            this.$axios.post(`/mods/${this.$route.params.mod}/fetchComments`)
                .then((response) => {
                    this.comments = response.data.data;
                });
        },
        login() {
            this.$root.$refs.Frontend.$refs.AuthModal.open();
        },
        writingComment() {
            return typeof this.$refs.comment !== 'undefined' && this.$refs.comment ? !this.$refs.comment.editor.isEmpty : false;
        }
    },
    mounted() {
        this.fetchComments();
        console.log(this.comment)
    },
}
</script>
