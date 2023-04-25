<style lang="scss">
$controlPaddings: 5px;
.comment {

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
</style>

<style lang="scss" scoped>
    @import "resources/sass/variables";

    $controlPaddings: 5px;

    #commentsPane {
        $padding: 10px;

        width: calc(100% + $padding * 2);
        padding: 0 $padding;
        margin-left: -20px;
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

        .comment {
            border: 1px solid rgb(0, 0, 0, 0.2);
            border-radius: 3px;
            margin-bottom: 5px;

            &:first-child {
                margin-top: -10px;
            }

            $controlBG: rgba(0, 0, 0, 0.2);
            .header {
                width: calc(100% - $controlPaddings * 2);
                background: $controlBG;
                padding: $controlPaddings;

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
                }
            }

            .footer {
                padding: 5px;
                background: $controlBG;

                svg {
                    cursor: pointer;
                    margin-right: 8px;
                }
            }

            .content {
                margin-top: -2px;
                background-color: white;
            }

            .formComponent {
                padding: 0;
                width: calc(100% - 2px);
            }
        }
    }
</style>

<template>
    <div>
        <div class="header">
            <span class="author">
                <a :href="'/users/profile/' + comment.author.id">
                    <img :src="comment.author.avatar" alt="avatar">
                    <div class="username">{{comment.author.name}}</div>
                </a>
            </span>
            <span class="date">
                {{ comment.created_at }}
            </span>
        </div>
        <div class="content">
            <WYSIWYG :name="`comment[${comment.id}]`"
                     :display-only=true
                     :show-controls=false
                     :json-content="comment.comment"
                     placeholder="Loading..." label="" cols=12 />

            <div class="replies">
                asdfasdfasdfasdf
            </div>
        </div>
        <div v-if="user.id" class="footer">
            <font-awesome-icon icon="reply" />
            <font-awesome-icon icon="flag" />
        </div>

        <Form @submit="saveComment">
            <div v-if="!user.id">
                <p>You must be logged in and have a verified Discord account to comment. <a @click="login">Login</a></p>
            </div>
            <div v-else-if="!user.discord_id">
                <p>You must have a connected Discord account to comment. <a href="/users/profile">Click to Connect Your Discord</a></p>
            </div>
            <div v-else>
                <WYSIWYG ref="comment"
                         name="comment"
                         :limit=1000
                         label="" cols=12
                         :excludeControls="[
                            'fontsize',
                            'justify',
                            'youtube'
                         ]"
                         placeholder="Tell the mod author what you think!" />

                <button type="submit" class="submitComment btn btnGreen col12">Post Comment!</button>
            </div>
        </Form>
    </div>
</template>

<script>
import WYSIWYG from "../forms/WYSIWYG.vue"
import { Form, Field, ErrorMessage } from "vee-validate"

import { library } from "@fortawesome/fontawesome-svg-core"
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faFlag, faReply, faThumbsUp } from "@fortawesome/free-solid-svg-icons"

library.add(faFlag);
library.add(faReply);
library.add(faThumbsUp);

export default {
    name: "Comments",
    components: {
        Form,
        Field,
        WYSIWYG,
        ErrorMessage,
        FontAwesomeIcon
    },
    data() {
        return {
            user: window.user,
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
            this.comment.comment = this.$refs.comment.editor.getJSON()
            this.$axios.post(`/mods/${this.$route.params.mod}/comment`, this.comment)
                .then((response) => {
                    console.log("Comment saved!", response.data);
                });
        },
        login() {
            console.log("logging in ", this.$root.$refs.Frontend.$refs.AuthModal.open())
        }
    },
    mounted() {
        this.$axios.post(`/mods/${this.$route.params.mod}/fetchComments`)
            .then((response) => {
                this.comments = response.data.data;
                console.log("fetched!", this.comments);
            });
    }
}
</script>
