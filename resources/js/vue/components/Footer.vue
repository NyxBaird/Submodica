<style lang="scss" scoped>
@import "../../../sass/variables";

#Footer {
    position: relative;
    bottom: 0;
    width: calc(100% - 20px);
    text-align: center;
    background-color: #222D4C;
    color: $textColor;
    padding: 5px 10px;
    margin-top: 50px;

    a {
        color: $linkColor;
        cursor: pointer;
    }

    #submodicaFooterLinks {
        font-size: 14px;
    }

    #submodicaAttributions {
        margin-top: 5px;
        font-size: 12px;

        a {
            text-decoration: none;
            color: #fcfce3;
        }
    }

    @include mobile(){
        #submodicaFooterLinks {
            font-size: 9px;
        }
        #submodicaAttributions {
            font-size: 9px;
        }
    }
}

#to {
    font-size: 14px;
    padding: 10px 14px;
}
</style>


<template>
    <div id="Footer">
        <div id="submodicaFooterLinks">
            <a @click="$router.push('/rules')">Rules</a> |
            <a @click="$router.push('/terms')">Terms of Service</a> |
            <a @click="$router.push('/privacy-policy')">Privacy Policy</a> |
            <a @click="this.$refs.contactModal.open()">Contact</a>
        </div>
        <div id="submodicaAttributions">
            Submodica was built and is maintained by <a @click="this.$refs.contactModal.open()">NyxB</a> as a side project. The background art for Submodica was drawn by <a href="https://www.instagram.com/ghosty_leaf/" target="_blank">GhostyLeaf</a>. All mods on Submodica belong to and are maintained by their respective authors.<br />
            Subnautica and Subnautica: Below Zero are trademarks of Unknown Worlds Entertainment and are not affiliated with Submodica.
        </div>
    </div>

    <Modal title="Contact" ref="contactModal">
        <template #default>
            <br />
            You can send a message to the site admin below. Please note that it's usually faster to just message Nyx on discord via the Subnautica Modding Discord server linked in the site header, but this is here if you'd prefer.
            <Form id="contactForm">
                <br />
                <span id="to">
                    <b>To:</b> NyxB (<a href="mailto:admin@submodica.xyz">admin@submodica.xyz</a>)
                </span>
                <br />
                <FormComponent classes="col12" label="Subject">
                    <Field rules="required" name="subject" placeholder="Add a subject here" v-model="email.subject" />
                </FormComponent>

                <FormComponent classes="col12" label="Body">
                    <Field rules="required"
                           name="body"
                           as="textarea"
                           rows="10"
                           placeholder="So what's up?"
                           maxlength="500" v-model="email.body" />
                </FormComponent>
            </Form>
        </template>
        <template #footer>
            <button class="btn" @click="this.$refs.contactModal.close()">Cancel</button>
            <button class="btn btnRed floatRight" @click="contact">Report</button>
        </template>
    </Modal>
</template>

<script>
    import Modal from "./Modal.vue"
    import {Form, Field, ErrorMessage} from 'vee-validate'
    import FormComponent from "./forms/FormComponent.vue"

    export default {
        name: "Footer",
        components: {
            Modal,
            Form,
            Field,
            ErrorMessage,
            FormComponent
        },
        data() {
            return {
                email: {
                    subject: "",
                    body: ""
                }
            }
        },
        methods: {
            contact() {
                this.$axios.post(`/contact`, this.email)
                    .then((response) => {
                        this.$root.alerts.push({
                            type: response.data.success ? "success" : "error",
                            content: response.data.message
                        });

                        if (response.data.success)
                            this.$refs.contactModal.close()
                    });
            }
        },
        mounted() {
            if (window.location.pathname === "/contact")
                this.$refs.contactModal.open();
        }
    }
</script>
