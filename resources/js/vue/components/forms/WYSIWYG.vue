<style lang="scss">
    @use 'sass:math';
    @import "../../../../sass/variables";

    $paddingW: 10px;
    $paddingH: 15px;

    $controlsWidth: math.div(100, 17);

    .wysiwyg {
        width: 100%;

        background-color: rgba(255, 255, 255, $formOpacity);
        border: 1px solid $formBordersColor;
        border-radius: 5px;

        .controls {
            $controlBorder: #888;

            width: 100%;
            font-size: 2px;
            margin-bottom: 2px;
            color: black;

            tr {
                text-align: center;


                td {
                    width: $controlsWidth * 1%;
                    cursor: pointer;
                    padding: 0;
                    font-size: 18px;

                    select, input[type="color"] {
                        background-color: transparent;
                        cursor: pointer;
                        border: none;
                    }

                    &.quote {
                        font-size: 10px;
                    }

                    &.sectionEnd {
                        border-right: 1px solid $controlBorder;
                    }

                    &:first-child {
                        border-top-left-radius: 4px;
                    }

                    &:last-child {
                        border-top-right-radius: 4px;
                    }

                    &:hover {
                        background-color: rgba(0, 0, 0, 0.1);
                    }

                    &.active {
                        background-color: rgba(0, 0, 0, 0.2);
                    }
                }
            }
        }

        .content {
            $borderWidth: 1px;

            width: calc(100% - ($paddingW * 2) - $borderWidth);

            color: black;
            cursor: text;

            .ProseMirror {
                padding: $paddingH $paddingW;
                width: 100%;
                //height: 100%;

                h1, h2, h3, h4, h5, h6 {
                    font-weight: 100;
                    margin-top: 10px;
                    margin-bottom: 10px;
                }
                h1 {
                    font-size: 4em;
                }
                h2 {
                    font-size: 3em;
                }
                h3 {
                    font-size: 2em;
                }
                h4 {
                    font-size: 1em;
                }

                .code {
                    background-color: rgba(0, 0, 0, 0.1);
                    border-radius: 5px;
                    padding: 5px;
                }

                blockquote {
                    border-left: 5px solid rgba(0, 0, 0, 0.1);
                }

                hr {
                    background-color: rgb(0, 0, 0, 0.2);
                }

                a {
                    color: $wysiwygLinkColor;
                    text-decoration: underline;
                    cursor: pointer;
                }

                [data-youtube-video] {
                    text-align: center;
                }

                li > p {
                    margin-top: 10px;
                    margin-bottom: 10px;
                }

                p.is-editor-empty:first-child::before {
                    content: attr(data-placeholder);
                    float: left;
                    color: grey;
                    pointer-events: none;
                    height: 0;
                }

                &[contenteditable='false'] {
                    cursor: default;
                }
            }

            [contenteditable]:focus {
                outline: $borderWidth solid $formBordersColor;
                border-bottom-left-radius: 5px;
                border-bottom-right-radius: 5px;
            }

            p {
                text-indent: 10px;
            }
        }
    }

    .charsRemaining {
        margin-top: 3px;
        color: $textColor;

        span {
            color: darkred;
        }
    }
</style>

<template>
    <fComponent :classes="'col' + cols + (mCols ? ' mCol' + mCols : '')" :label="label">
        <Field :rules="validateWYSIWYG" type="hidden" :name="name" :value="json" />
        <div class="wysiwyg">
            <table v-if="!displayOnly && showControls" class="controls">
                <tr>
                    <td title="Paragraph"
                        :class="{ 'active': !editor.isActive('codeBlock') }"
                        @click="editor.chain().focus().toggleCodeBlock().run()">

                        <fa-icon icon="paragraph" />
                    </td>
                    <td title="Code"
                        class="sectionEnd"
                        :class="{ 'active': editor.isActive('codeBlock') }"
                        @click="editor.chain().focus().toggleCodeBlock().run()">

                        <fa-icon icon="code" />
                    </td>

                    <td v-if="!excludeControls.includes('fontsize')" title="Font Size">
                        <select name="fontSize" @change="changeHeading">
                            <option value="0">Normal</option>
                            <option value="1">Header 1</option>
                            <option value="2">Header 2</option>
                            <option value="3">Header 3</option>
                            <option value="4">Header 4</option>
                            <option value="5">Header 5</option>
                            <option value="6">Header 6</option>
                        </select>
                    </td>

                    <td class="sectionEnd"
                        title="Color">

                        <input type="color"
                               @input="editor.chain().focus().setColor($event.target.value).run()"
                               value="#000000"/>
                    </td>

                    <td v-if="!excludeControls.includes('justify')"
                        title="Left Adjust"
                        :class="{ 'active': editor.isActive({ textAlign: 'left' }) }"
                        @click="editor.chain().focus().setTextAlign('left').run()">

                        <fa-icon icon="fa-solid fa-align-left" />
                    </td>
                    <td v-if="!excludeControls.includes('justify')"
                        title="Center Adjust"
                        :class="{ 'active': editor.isActive({ textAlign: 'center' }) }"
                        @click="editor.chain().focus().setTextAlign('center').run()">

                        <fa-icon icon="fa-solid fa-align-center" />
                    </td>
                    <td v-if="!excludeControls.includes('justify')"
                        title="Right Adjust"
                        class="sectionEnd"
                        :class="{ 'active': editor.isActive({ textAlign: 'right' }) }"
                        @click="editor.chain().focus().setTextAlign('right').run()">

                        <fa-icon icon="fa-solid fa-align-right" />
                    </td>

                    <td title="Bold"
                        :class="{ 'active': editor.isActive('bold') }"
                        @click="editor.chain().focus().toggleBold().run()">

                        <fa-icon icon="bold" />
                    </td>

                    <td title="Italicize"
                        :class="{ 'active': editor.isActive('italic') }"
                        @click="editor.chain().focus().toggleItalic().run()">

                        <fa-icon icon="italic" />
                    </td>

                    <td title="Underline"
                        :class="{ 'active': editor.isActive('underline') }"
                        @click="editor.chain().focus().toggleUnderline().run()">

                        <fa-icon icon="underline" />
                    </td>

                    <td title="Strikethrough"
                        class="sectionEnd"
                        :class="{ 'active': editor.isActive('strike') }"
                        @click="editor.chain().focus().toggleStrike().run()">

                        <fa-icon icon="strikethrough" />
                    </td>

                    <td title="Youtube"
                        v-if="!excludeControls.includes('youtube')"
                        @click="addVideo">

                        <fa-icon :icon="['fab', 'youtube']" />
                    </td>

                    <td title="Link"
                        v-if="!excludeControls.includes('links')"
                        @click="setLink">

                        <fa-icon icon="link" />
                    </td>

                    <td title="Unlink"
                        v-if="!excludeControls.includes('links')"
                        @click="editor.commands.unsetLink()">

                        <fa-icon icon="link-slash" />
                    </td>

                    <td title="Quote"
                        class="quote"
                        :class="{ 'active': editor.isActive('quote') }"
                        @click="editor.chain().focus().toggleBlockquote().run()">

                        <fa-icon icon="fa-solid fa-quote-left" />&nbsp;
                        <fa-icon icon="fa-solid fa-quote-right" />
                    </td>

                    <td title="List"
                        :class="{ 'active': editor.isActive('orderedList') }"
                        @click="editor.chain().focus().toggleOrderedList().run()">

                        <fa-icon icon="fa-solid fa-list-ol" />
                    </td>

                    <td title="Unordered List"
                        :class="{ 'active': editor.isActive('bulletList') }"
                        @click="editor.chain().focus().toggleBulletList().run()">

                        <fa-icon icon="fa-solid fa-list-ul" />
                    </td>
                </tr>
            </table>
            <editor-content class="content" :editor="editor" v-model="slot" />
            <div v-if="showCharsRemaining && !displayOnly" class="charsRemaining floatRight">
                Characters Remaining: <span>{{ limit - editor.storage.characterCount.characters() }}</span>
            </div>
        </div>
        <ErrorMessage :name="name" class="validationError" />
    </fComponent>
</template>

<script>
    import FormComponent from "./FormComponent.vue";

    import { Editor, EditorContent } from '@tiptap/vue-3'
    import Document from '@tiptap/extension-document'
    import Paragraph from '@tiptap/extension-paragraph'
    import Text from '@tiptap/extension-text'
    import TextStyle from '@tiptap/extension-text-style'
    import Bold from '@tiptap/extension-bold'
    import Italic from '@tiptap/extension-italic'
    import Underline from '@tiptap/extension-underline'
    import CodeBlock from '@tiptap/extension-code-block'
    import Strike from '@tiptap/extension-strike'
    import Blockquote from '@tiptap/extension-blockquote'
    import Link from '@tiptap/extension-link'
    import { Color } from '@tiptap/extension-color'
    import ListItem from '@tiptap/extension-list-item'
    import OrderedList from '@tiptap/extension-ordered-list'
    import BulletList from '@tiptap/extension-bullet-list'
    import Placeholder from '@tiptap/extension-placeholder'
    import Typography from '@tiptap/extension-typography'
    import Youtube from '@tiptap/extension-youtube'
    import TextAlign from '@tiptap/extension-text-align'
    import Heading from '@tiptap/extension-heading'
    import HorizontalRule from '@tiptap/extension-horizontal-rule'
    import History from '@tiptap/extension-history'
    import GapCursor from '@tiptap/extension-gapcursor'
    import HardBreak from '@tiptap/extension-hard-break'
    import CharacterCount from '@tiptap/extension-character-count'
    import {Code} from "@tiptap/extension-code"

    import { Field, ErrorMessage } from 'vee-validate'
    import { defineRule } from 'vee-validate'

    import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
    import { library } from "@fortawesome/fontawesome-svg-core"
    import { faYoutube } from '@fortawesome/free-brands-svg-icons'
    import {
        faParagraph, faCode,
        faBold, faItalic, faUnderline, faStrikethrough,
        faLink, faLinkSlash, faListOl, faListUl,
        faQuoteLeft, faQuoteRight,
        faAlignLeft, faAlignCenter, faAlignRight
    } from "@fortawesome/free-solid-svg-icons"

    //WIP add collaboration/collab cursor

    //Add all our icons
    library.add(faParagraph);
    library.add(faCode);
    library.add(faLink);
    library.add(faLinkSlash);
    library.add(faBold);
    library.add(faItalic);
    library.add(faUnderline);
    library.add(faStrikethrough);
    library.add(faQuoteLeft);
    library.add(faQuoteRight);
    library.add(faListOl);
    library.add(faListUl);
    library.add(faAlignLeft);
    library.add(faAlignCenter);
    library.add(faAlignRight);
    library.add(faYoutube);

    defineRule('wysiwyg', value => {
        if (!value || !value.length)
            return 'This field is required';

        return true;
    });

    export default {
        name: "WYSIWYG",
        components: {
            "fComponent": FormComponent,
            "faIcon": FontAwesomeIcon,
            EditorContent,
            Field,
            ErrorMessage
        },
        computed: {
            json() {
                return this.editor.getJSON()
            }
        },
        data(){
            return {
                editor: new Editor({
                    extensions: [
                        Document,
                        Placeholder.configure({
                            placeholder: this.placeholder ? this.placeholder : 'Draft up a description of your mod here!'
                        }),
                        Text,
                        Paragraph,
                        Bold,
                        Italic,
                        Underline,
                        CodeBlock.configure({
                            HTMLAttributes: {
                                class: 'code'
                            }
                        }),
                        Code.configure({
                            HTMLAttributes: {
                                class: 'code'
                            }
                        }),
                        Strike,
                        Blockquote,
                        Link.configure({
                            // autolink: !this.excludeControls.includes('links'),
                            autolink: false,
                            HTMLAttributes: {
                                target: '_blank'
                            }
                        }),
                        TextStyle,
                        Color.configure({

                        }),
                        OrderedList,
                        ListItem,
                        BulletList,
                        Typography,
                        Youtube,
                        TextAlign.configure({
                            types: ['paragraph', 'heading']
                        }),
                        Heading,
                        HorizontalRule,
                        History,
                        GapCursor,
                        HardBreak,
                        CharacterCount.configure({
                            limit: this.limit
                        })
                    ],
                }),
                slot: "",
                mCols: 0,
                exclude: []
            }
        },
        methods: {
            addVideo() {
                const url = prompt('Enter YouTube URL')

                this.editor.commands.setYoutubeVideo({
                    src: url,
                    width: Math.max(320, parseInt(this.width, 10)) || 640,
                    height: Math.max(180, parseInt(this.height, 10)) || 480,
                })
            },
            changeHeading($event) {
                if ($event.target.value === '0') {
                    this.editor.chain().focus().setParagraph().run()
                } else {
                    this.editor.chain().focus().toggleHeading({level: Number($event.target.value)}).run()
                }
            },
            validateWYSIWYG() {
                if (this.rules.split("|").indexOf("required") > -1 && this.editor.storage.characterCount.characters() === 0)
                    return 'This field is required';

                return true;
            },
            setLink() {
                const url = prompt('Enter URL');
                this.editor.commands.setLink({
                    href: "/link/external?url=" + encodeURIComponent(url),
                    target: '_blank'
                });
            },
            clear() {
                this.editor.commands.clearContent(true);
            },
            resetContent(content) {
                if (typeof content === 'undefined')
                    content = false;

                if (!content)
                    this.editor.commands.setContent(JSON.parse(this.jsonContent));
                else
                    this.editor.commands.setContent(JSON.parse(content));
            }
        },
        props: {
            name: String,
            label: {
                type: String,
                required: false,
                default: ""
            },
            placeholder: {
                type: String,
                required: false,
                default: ""
            },
            rules: {
                type: String,
                default: ""
            },
            limit: {
                type: Number,
                default: 20000
            },
            showCharsRemaining: {
                type: Boolean,
                default: true
            },
            cols: {
                required: false,
                default: 6,
                validator(value) {
                    return !isNaN(value);
                }
            },
            mobileCols: {
                required: false,
                default: 0,
                validator(value) {
                    return !isNaN(value);
                }
            },
            displayOnly: {
                type: Boolean,
                required: false,
                default: false,
            },
            jsonContent: {
                type: String,
                required: false,
                default: ''
            },
            showControls: {
                type: Boolean,
                required: false,
                default: true
            },
            excludeControls: {
                type: Array,
                required: false,
                default: () => []
            }
        },
        mounted() {
            if (typeof this.$slots.default !== 'undefined')
                this.slot = this.$slots.default()[0].children;

            this.mCols = this.mobileCols ? this.mobileCols : this.cols;

            if (this.displayOnly)
                this.editor.setEditable(false);

            if (this.jsonContent.length)
                this.editor.commands.setContent(JSON.parse(this.jsonContent));

            this.exclude = this.excludeControls;
        },
        beforeUnmount() {
            this.editor.destroy();
        },
    }
</script>
