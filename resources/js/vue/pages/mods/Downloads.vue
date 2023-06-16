<style scoped lang="scss">
    @import "../../../../sass/frontend/pages/downloads_and_images.scss";
</style>

<template>
    <div id="downloads">
        <h2>Download Links & Images</h2>
        <hr />
        To add the download links for your mod, first please ensure that the links are located within the GitHub repository you chose for this mod.<br />
        <br />
        <div class="bullet">&#x2022; If the download link is from a GitHub release, please ensure that you navigate to the "Assets" tab of the release and copy the link from there. (Make sure you don't accidentally link the source code instead of the mod!)</div>
        <div class="bullet">&#x2022; If the download link is from a GitHub file, please ensure that you navigate to the files page itself and right click the "Download" button there for its link.</div>
        <br />
        When adding the version for your mod, please ensure that you use the value in the "Version" field of the mod.json file as Submodica validates versions with the same logic as QMod Manager.<br />
        <br />
        <div class="bullet">&#x2022; Valid Version Examples: 1.0, 1.0.0.0, 0.0.1, etc</div>
        <div class="bullet">&#x2022; Invalid Version Example: alpha1, v0.1, etc</div>

        <Form @submit="saveData">
            <FormComponent classes="col12" :label="!typeof downloads[0] === 'undefined' ? 'The main Download Link for your mod' : ''">
                <table>
                    <thead>
                    <tr>
                        <th style="width: 20%"></th>
                        <th style="width: 75%"></th>
                        <th style="width: 5%"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                    <tr v-if="downloads.length">
                        <td colspan="3">
                            Your listed download links:
                        </td>
                    </tr>
                    <tr v-for="(d, i) in downloads" class="attribution">
                        <td v-if="!d.title" colspan="2">
                            {{ d.version }} -
                            <a :href="d.url" target="_blank">{{ d.url }}</a>
                        </td>
                        <td v-else colspan="2">
                            <a :href="d.url" target="_blank">{{ d.title }}</a> ({{ d.version }})
                        </td>
                        <td>
                            <button class="btnRed" type="button" @click="removeDownload(i)">
                                <font-awesome-icon icon="x" />
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3">Add your main download links per version</td>
                    </tr>
                    <tr class="downloadInputs">
                        <td>
                            <Field ref="version"
                                   :rules="'version:' + downloadInputs.version + ',' + downloadInputs.url"
                                   type="text"
                                   name="version"
                                   placeholder="Version"
                                   v-model="downloadInputs.version"
                                   maxlength="20" />
                            &nbsp;
                            <ErrorMessage name="version" class="validationError" />
                        </td>
                        <td>
                            <Field ref="url"
                                   :rules="'downloadUrl:' + this.mod.repo"
                                   type="text"
                                   name="url"
                                   placeholder="Download Link"
                                   v-model="downloadInputs.url"
                                   maxlength="1000" />
                            &nbsp;
                            <ErrorMessage name="url" class="validationError" />
                        </td>
                        <td>
                            <button class="btnGreen" type="button" @click="addDownload(this.downloadInputs.version, this.downloadInputs.url)">
                                <font-awesome-icon icon="plus" />
                            </button>
                            &nbsp;
                        </td>
                    </tr>

                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3">Add any alternative download links specific versions might have</td>
                    </tr>
                    <tr class="altDownloadInputs">
                        <td>
                            <Field as="select" name="altVersion" ref="altVersion" :rules="'version:' + altDownload.version + ',' + altDownload.url" v-model="altDownload.version" :data-val="altDownload.version">
                                <option value=''>-- Alternative Version --</option>
                                <option v-for="d in mainDownloads" :value="d.version">{{ d.version }}</option>
                            </Field>
                        </td>
                        <td colspan="2">
                            <Field :rules="'downloadUrl:' + this.mod.repo"
                                   type="text"
                                   ref="altUrl"
                                   name="altUrl"
                                   placeholder="Alternative Download Link"
                                   v-model="altDownload.url"
                                   maxlength="1000" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <Field :rules="'altTitle:' + altDownload.version + ',' + altDownload.url"
                                   type="text"
                                   ref="altTitle"
                                   name="altTitle"
                                   placeholder="Title this alternative link to distinguish it"
                                   v-model="altDownload.title"
                                   maxlength="1000" />
                        </td>
                        <td>
                            <button class="btnGreen" type="button" @click="addDownload(altDownload.version, altDownload.url, true)">
                                <font-awesome-icon icon="plus" />
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" id="altErrors">
                            <ErrorMessage name="altVersion" class="validationError" />
                            <ErrorMessage name="altUrl" class="validationError" />
                            <ErrorMessage name="altTitle" class="validationError" />
                        </td>
                    </tr>
                    </tbody>
                </table>
            </FormComponent>
            <FormComponent classes="col12" label="The most recent version">
                <select v-model="mod.latest_version" :data-val="mod.latest_version">
                    <option value=''>-- Select the most recent version --</option>
                    <option v-for="d in downloads" :value="d.version">{{ d.version }}</option>
                </select>
            </FormComponent>
            <br />
            <br />
            Add the Icon & Cover images for your mod. The will be displayed on the mod table to help people locate and identify your mod and the title image will be displayed as a banner on your mod page.<br />
            <br />
            <div class="bullet">&#x2022; The icon image should be a square image with a minimum size of 180x180 pixels.</div>
            <div class="bullet">&#x2022; The cover image should be a rectangle image with an approximate size of 1920x1080.</div>
            <div class="bullet"><i>***Please note that image previews on this page do not scale properly, instead they prefer the stretch. This is because of limitations in the way they're displayed for editing purposes. On your actual mod page they'll be displayed to scale properly while maintaining their aspect ratio.</i></div>
            <br />
            <div id="images">
                <input type="checkbox" name="show_cover_title" :checked="mod.show_cover_title?'checked':false" @click="mod.show_cover_title = !mod.show_cover_title" />
                Display the mods cover text?<br />
                <br />

                <div id="coverImage" class="imageGroup">
                    <div class="deleteImage" @click="deleteImage">
                        <font-awesome-icon icon="x" />
                    </div>

                    <label for="coverInput" style="font-size: 30px;"></label>
                    <div id="coverVerticalCenter">
                        <font-awesome-icon icon="plus" /> Cover
                        <input type="file" id="coverInput" @change="imageHandler" />
                    </div>

                    <div v-if="mod.show_cover_title" id="coverText">
                        <div class="title">{{ mod.title }} <small>by {{ mod.creator }}</small></div>
                        <div class="version">{{ mod.version }}</div>
                    </div>

                    <canvas id="cover" height="200" width="1920"></canvas>
                    <input type="hidden" name="coverId" :value="images.cover.id" />
                </div>
                <div id="iconImage" class="imageGroup">
                    <div class="deleteImage" @click="deleteImage">
                        <font-awesome-icon icon="x" />
                    </div>

                    <label for="iconInput" style="font-size: 30px;">
                        <font-awesome-icon icon="plus" /> Icon
                        <input type="file" id="iconInput" @change="imageHandler" />
                    </label>

                    <canvas id="icon" height="180" width="180"></canvas>
                    <input type="hidden" name="iconId" :value="images.icon.id" />
                </div>
                <br />
                <br />
                <div id="galleryTitle">Choose some gallery images for your mod</div>

                <div v-for="(image, index) in images.gallery" class="misc imageGroup" :data-gallery-id="index">
                    <div class="deleteImage" @click="deleteImage">
                        <font-awesome-icon icon="x" />
                    </div>

                    <label :for="'gallery' + index">
                        <input type="file" :id="'gallery' + index" @change="imageHandler" />
                    </label>

                    <div class="galleryVerticalCenter">
                        <font-awesome-icon icon="plus" /> Add Image
                    </div>

                    <div class="galleryText">
                        Gallery Image {{ index + 1 }}
                    </div>

                    <canvas class="galleryIcon" height="52" width="1920"></canvas>
                    <input type="hidden" name="galleryId[]" :value="image.id" />
                </div>
                <br />
            </div>
            <ModNav page="downloads" :status="mod.creation_status" />
            <br />
        </Form>

        <Modal ref="ImageModal"
               padding-horizontal="50"
               padding-vertical="100">
            <template #default>
                <cropper ref="cropper"
                        :src="cropperImage"
                        :stencil-component="this.cropperStencil"
                        :stencil-props="{
                            aspectRatio: cropperRatio
                        }" />
            </template>
            <template #footer>
                <button class="btn btnRed" @click="closeCropper">Cancel</button>
                <button class="btn btnGreen" @click="acceptCrop">Crop</button>
            </template>
        </Modal>

        <Modal ref="savingModal" prevent-manual-close>
            <template #default>
                <span style="color: black;">
                    Processing & uploading your images...
                </span>
                <br />
                <br />
                <div style="opacity: 0.9">
                    This may take a minute- especially if you're uploading many images at once as Submodica has to do a lot in order to process each one, including verifying the content with an AI, converting it to a .webp, and uploading it to its final location. Please allow the uploader at 30~60 seconds <i>per image</i> to work- this pop-up should disappear when the process is complete. That said if you wait a few minutes and still nothing, it might be worth reloading the page and seeing what got uploaded.
                </div>
            </template>
        </Modal>
    </div>
</template>
<script>
    import { shallowRef } from "vue"
    import { Cropper, CircleStencil, RectangleStencil } from 'vue-advanced-cropper'
    import '/node_modules/vue-advanced-cropper/dist/style.css'
    import '/node_modules/vue-advanced-cropper/dist/theme.compact.css'

    const circleStencil = shallowRef(CircleStencil);
    const rectangleStencil = shallowRef(RectangleStencil);

    import {Form, Field, ErrorMessage, defineRule} from 'vee-validate'
    import FormComponent from "../../components/forms/FormComponent.vue"
    import Modal from "../../components/Modal.vue";
    import ModNav from "../../components/mod/ModNav.vue";

    //Font Awesome
    import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
    import {library} from "@fortawesome/fontawesome-svg-core"
    import {faPlus, faX} from "@fortawesome/free-solid-svg-icons"

    library.add(faX);
    library.add(faPlus);

    defineRule('altTitle', (value, [version, url]) => {
        if (!value.length && url !== '')
            return 'You must provide an alternative title to distinguish this link.';

        return true;
    });

    defineRule('version', (value, [version, url]) => {
        if (!version.length) {
            if (!url.length)
                return true;
            else
                return "The Version field is required";
        }

        const originalRegex = /^(((\d+)\.?){0,3}\d+)$/;
        const semverRegex = /^(0|[1-9]\d*)\.(0|[1-9]\d*)\.(0|[1-9]\d*)(?:-([0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*))?(?:\+([0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*))?$/;

        if (originalRegex.test(version) || semverRegex.test(version)) {
            return true;
        } else {
            return "Invalid Version Format";
        }
    });


    //This is verified on the server end too
    defineRule('downloadUrl', (value, [repo]) => {
        // WIP We started saving the mods actual repo url in an update so we can implement that here

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

        if (domain !== 'github.com' || pieces[domainIndex + 1] + "/" + pieces[domainIndex + 2] !== repo)
            return 'Your mod download link must be from the GitHub Repository for your mod.';

        let dotPieces = value.split('.'),
            extension = dotPieces[dotPieces.length - 1];

        if (extension === "gz")
            extension = dotPieces[dotPieces.length - 2] + "." + extension;

        let validExtensions = [
            "7z",
            "zip",
            "rar",
            "tar.gz",
        ];

        if (!validExtensions.includes(extension))
            return 'Your mod download link must be a valid archive file.';

        return true;
    });

    export default {
        name: "EditModDownloads",
        data() {
            return {
                user: window.user,
                mod: {
                    id: this.$route.params.mod,
                    creation_status: 'downloads',
                    show_cover_title: true
                },
                downloadInputs: {
                    version: '',
                    url: ''
                },
                downloads: [],
                altDownload: {
                    version: '',
                    url: '',
                    title: ''
                },
                maxGallerySize: 10,
                images: {
                    gallery: [],
                    cover: {
                        id: 0,
                        source: '',
                        aspectRatio: 3 / 1,
                    },
                    icon: {
                        id: 0,
                        source: '',
                        aspectRatio: 1 / 1,
                        stencil: circleStencil
                    }
                },
                cropperSourceGroup: null,
                cropperDestination: null,
                cropperImage: '',
                cropperRatio: 16 / 9,
                cropperStencil: rectangleStencil,
                cropperHeight: 500
            };
        },
        components: {
            Form,
            Field,
            Modal,
            ModNav,
            Cropper,
            ErrorMessage,
            FormComponent,
            FontAwesomeIcon,
        },
        methods: {
            async saveData() {
                let cover = document.getElementById('cover'),
                    icon = document.getElementById('icon'),
                    data = {};

                if (!this.images.cover.id && !isCanvasBlank(cover))
                    data.cover = this.images.cover.source;

                if (!this.images.icon.id && !isCanvasBlank(icon))
                    data.icon = this.images.icon.source;

                data.gallery = this.images.gallery.filter(image => image.source !== '').map(image => image.source);

                let errors = false;
                if (!this.downloads.length) {
                    this.$root.alerts.push({
                        type: 'error',
                        content: 'Please enter at least one download link to continue.'
                    });
                    errors = true;
                }

                if (!this.mod.latest_version && this.downloads.length > 1) {
                    this.$root.alerts.push({
                        type: 'error',
                        content: 'Please specify one of your download links as the Current Version for users.'
                    });
                    errors = true;
                } else if (!this.mod.latest_version && this.downloads.length === 1) {
                    this.mod.latest_version = this.downloads[0].version;
                }

                if (typeof data.icon === 'undefined' && !this.images.icon.id) {
                    this.$root.alerts.push({
                        type: 'error',
                        content: 'A Icon Image is required to continue.'
                    });
                    errors = true;
                }

                if (typeof data.cover === 'undefined' && !this.images.cover.id) {
                    this.$root.alerts.push({
                        type: 'error',
                        content: 'A Cover Image is required to continue.'
                    });
                    errors = true;
                }

                if (errors)
                    return;

                //Save our data
                let vue = this;
                this.$refs.savingModal.open();
                this.$axios.post(`/mods/${this.$route.params.game}/${this.$route.params.mod}/edit/saveDownloads`, {
                    downloads: this.downloads,
                    currentVersion: this.mod.latest_version,
                    show_cover_title: this.mod.show_cover_title
                }).then(async function(response) {
                    vue.$root.alerts.push({
                        type: response.data.success ? "success" : "error",
                        content: response.data.message
                    });

                    //Save our images
                    for (let d in data) {
                        if (d === "gallery") {
                            for (let g in data[d])
                                await saveImage(data[d][g], d);
                        } else
                            await saveImage(data[d], d);
                    }

                    vue.$refs.savingModal.close(true);
                    vue.$router.push(`/mods/${vue.$route.params.game}/${vue.mod.id}/edit/preview`);

                    async function saveImage(image, type) {
                        await vue.$axios.post(`/mods/${vue.$route.params.game}/${vue.$route.params.mod}/edit/saveImage`, {
                            type: type,
                            image: image
                        }).then((response) => {
                            if (!response.data.success) {
                                vue.$root.alerts.push({
                                    type: response.data.success ? "success" : "error",
                                    content: response.data.message
                                });
                            }

                            return response.data.success;
                        });
                    }
                });

                function isCanvasBlank(canvas) {
                    return !canvas.getContext('2d')
                        .getImageData(0, 0, canvas.width, canvas.height).data
                        .some(channel => channel !== 0);
                }
            },
            fetchData() {
                this.$axios.post('/mods/' + this.$route.params.mod + '/fetch').then((response) => {
                    this.mod = response.data.data.mod;
                    console.log(this.mod)
                });

                // WIP we started saving the repo url on the mod itself in an update. Implement that here in the future
                this.$axios.post(`/mods/${this.$route.params.mod}/fetchRepo`).then((response) => {
                    this.mod.repo = response.data.data.full_name;
                });
                this.$axios.post(`/mods/${this.$route.params.mod}/fetchDownloads`).then((response) => {
                    this.downloads = response.data.data;
                });
                this.$axios.post(`/mods/${this.$route.params.mod}/fetchImages`).then((response) => {
                    let images = response.data.data;
                    for (let i in images) {
                        if (images[i].type !== "gallery")
                            this.images[images[i].type] = images[i];
                        else
                            this.images.gallery.push(images[i]);
                    }

                    if (this.images.icon.source !== '') {
                        let icon = document.getElementById('icon'),
                            iconCtx = icon.getContext('2d'),
                            iconImg = new Image();

                        iconImg.src = this.images.icon.source;
                        iconImg.onload = () => {
                            iconCtx.drawImage(iconImg, 0, 0, 180, 180);
                        }
                        document.getElementById("iconImage").classList.add("active");
                    }

                    if (this.images.cover.source !== '') {
                        let cover = document.getElementById('cover'),
                            coverCtx = cover.getContext('2d'),
                            coverImg = new Image();

                        coverImg.src = this.images.cover.source;
                        // iconImg.crossOrigin = "Anonymous";
                        coverImg.onload = () => {
                            coverCtx.drawImage(coverImg, 0, 0, 1920, 200);
                        }
                        document.getElementById("coverImage").classList.add("active");
                    }

                    if (this.images.gallery.length) {
                        this.$nextTick().then(() => {
                            document.querySelectorAll(".imageGroup.misc").forEach((group, i) => {
                                let canvas = group.querySelector(".galleryIcon"),
                                    ctx = canvas.getContext('2d'),
                                    img = new Image();

                                img.src = this.images.gallery[group.dataset.galleryId].source;
                                img.onload = () => {
                                    ctx.drawImage(img, 0, 0, 1920, 52);

                                    let width = img.width,
                                        height = img.height,
                                        startCoords = {
                                            x: 0,
                                            y: 0
                                        };

                                    if (ctx.canvas.height < height) {
                                        height = ctx.canvas.height;
                                        startCoords.y = (img.height - ctx.canvas.height) / 2;
                                    }
                                    if (ctx.canvas.width < width) {
                                        width = ctx.canvas.width;
                                        startCoords.x = (img.width - ctx.canvas.width) / 2;
                                    }

                                    ctx.drawImage(img,
                                        startCoords.x, startCoords.y, width, height,
                                        0, 0, ctx.canvas.width, ctx.canvas.height);
                                }

                                group.classList.add("active");
                            });

                            if (document.querySelectorAll(".imageGroup.misc").length < this.maxGallerySize)
                                this.perpetuateGallery();
                        });
                    } else {
                        this.perpetuateGallery();
                    }
                });
            },
            addDownload(version, url, isAlt) {
                let title = '',
                    fail = false;

                if (typeof isAlt === "undefined")
                    isAlt = false;
                else
                    title = this.altDownload.title;

                let $version = isAlt ? this.$refs.altVersion : this.$refs.version,
                    $url = isAlt ? this.$refs.altUrl : this.$refs.url;

                if (!version.length) {
                    $version.setErrors(['The version field is required']);
                    fail = true;
                }

                if (!url.length) {
                    $url.setErrors(['The download link field is required']);
                    fail = true
                }

                if (isAlt && !this.altDownload.title.length) {
                    this.$refs.altTitle.setErrors(['The title field is required']);
                    fail = true;
                }

                if (fail)
                    return;

                this.$refs.url.validate().then((result) => {
                    if (result.valid) {
                        this.$refs.version.validate().then((result) => {
                            if (result.valid) {
                                this.downloads.push({
                                    version: version,
                                    url: url,
                                    title: title
                                });

                                if (isAlt)
                                    this.altDownload = {
                                        version: '',
                                        url: '',
                                        title: ''
                                    };
                                else
                                    this.download = {
                                        version: '',
                                        url: ''
                                    };

                                console.log("download links: ", this.downloads);
                            }
                        });
                    }
                });
            },
            removeDownload(index) {
                this.downloads.splice(index, 1);
            },
            deleteImage($event) {
                let group = $event.target.closest('.imageGroup'),
                    imageId = group.querySelector('input[type="hidden"]').value,
                    canvas = group.querySelector('canvas'),
                    fileInput = group.querySelector('input[type="file"]');

                if (imageId !== 0) {
                    this.$axios.post(`/mods/${this.$route.params.mod}/deleteImage/${imageId}`).then((response) => {
                        this.$root.alerts.push({
                            type: response.data.success ? "success" : "error",
                            content: response.data.message
                        });

                        if (response.data.success) {
                            let ctx = canvas.getContext('2d');
                            ctx.clearRect(0, 0, canvas.width, canvas.height);
                            group.classList.remove("active");
                        }

                        //Atm because of the complexity of this page reloading is the best way to resync all our image data
                        window.location.reload();
                    });
                } else {
                    let ctx = canvas.getContext('2d');
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    group.classList.remove("active");
                    fileInput.value = "";

                    //Atm because of the complexity of this page reloading is the best way to resync all our image data
                    window.location.reload();
                }


            },
            imageHandler(e) {
                let file = e.target.files[0],
                    displayCanvas = e.target.parentElement.parentElement.querySelector('canvas'),
                    size = file.size / 1000000; //In MB

                this.cropperDestination = displayCanvas;
                this.cropperSourceGroup = e.target.closest('.imageGroup');

                if (size > 10) {
                    alert("The image you have selected is too large. Please select an image that is less than 10MB in size.");
                    return;
                }

                //Load our image into our cropper
                let reader = new FileReader();
                reader.onload = (data) => {
                    this.cropperImage = data.target.result

                    if (!displayCanvas.classList.contains('galleryIcon')) {
                        if (typeof displayCanvas.id !== "undefined" && typeof this.images[displayCanvas.id] !== "undefined") {
                            this.cropperRatio = this.images[displayCanvas.id].aspectRatio;

                            if (typeof this.images[displayCanvas.id].stencil !== 'undefined')
                                this.cropperStencil = this.images[displayCanvas.id].stencil;
                            else
                                this.cropperStencil = RectangleStencil;
                        }
                    } else {
                        this.cropperRatio = 16 / 9;
                        this.cropperStencil = RectangleStencil;
                    }

                    this.$refs.ImageModal.open();

                    //This is the only way to get the cropper ref
                    this.$nextTick(() => {
                        this.$refs.cropper.reset();
                    });
                };
                reader.readAsDataURL(file);
            },
            closeCropper() {
                this.cropperDestination = null;
                this.$refs.ImageModal.close();
            },
            acceptCrop() {
                if (!this.cropperDestination) {
                    this.$root.alerts.push({
                        type: 'error',
                        content: 'The cropper encountered a destination error! Please alert Nyx, refresh the page, then try again.'
                    });
                    return;
                }

                let source = this.$refs.cropper.getResult().canvas,
                    destCanvas = this.cropperDestination,
                    imgGroup = destCanvas.closest('.imageGroup'),
                    destination = destCanvas.getContext('2d'),
                    $vue = this;

                // let data = source.toDataUrl();
                let sourceData = source.toDataURL();

                let destinationImage = new Image;
                destinationImage.onload = function() {
                    let width = source.width,
                        height = source.height,
                        startCoords = {
                            x: 0,
                            y: 0
                        };

                    if (destination.canvas.height < height) {
                        height = destination.canvas.height;
                        startCoords.y = (source.height - destination.canvas.height) / 2;
                    }
                    if (destination.canvas.width < width) {
                        width = destination.canvas.width;
                        startCoords.x = (source.width - destination.canvas.width) / 2;
                    }

                    //If it's a gallery image...
                    if (imgGroup.classList.contains('misc')) {
                        destination.drawImage(destinationImage,
                            startCoords.x, startCoords.y, width, height,
                            0, 0, destination.canvas.width, destination.canvas.height);

                        //Add the image data to our gallery and perpetuate our gallery
                        $vue.images.gallery[imgGroup.dataset.galleryId].source = sourceData;

                        if (document.querySelectorAll(".imageGroup.misc").length < $vue.maxGallerySize)
                            $vue.perpetuateGallery();

                        //If it's the cover or icon images...
                    } else {
                        destination.drawImage(destinationImage,
                            0, 0, source.width, source.height,
                            0, 0, destination.canvas.width, destination.canvas.height);

                        if (imgGroup.id === 'coverImage') {
                            $vue.images.cover.source = sourceData;
                        } else if (imgGroup.id === 'iconImage') {
                            $vue.images.icon.source = sourceData;
                        }
                    }
                };
                destinationImage.src = sourceData;

                this.cropperSourceGroup.classList.add('active');
                this.closeCropper();
            },
            perpetuateGallery() {
                this.images.gallery.push({
                    id: 0,
                    source: ''
                });
            },
        },
        mounted() {
            if (parseInt(this.mod.id) === 208)
                this.maxGallerySize = 23;

            this.fetchData();
        },
        computed: {
            mainDownloads() {
                return this.downloads.filter((download) => {
                    return !download.title;
                });
            },
        }
    }
</script>
