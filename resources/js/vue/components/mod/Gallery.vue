<style lang="scss" scoped>
@import "../../../../sass/variables";

::-webkit-scrollbar {
    height: 10px;
}

@include mobile() {
    ::-webkit-scrollbar {
        height: 25px;
    }
}

#gallery {
    $height: 180px;
    height: $height;

    display: flex;
    background: rgb(0, 0, 0, 0.4);
    width: calc(100% + 40px);
    margin-top: -52px;
    margin-left: -20px;
    overflow-x: auto;
    overflow-y: hidden;

    .image {
        $padding: 20px;

        position: relative;
        display: inline-block;
        height: 100%;

        img {
            max-height: calc(100% - #{$padding * 2});
            max-width: 1920px;
            height:auto;
            width:auto;

            padding-top: $padding;
            padding-left: $padding;
        }

        .hoverCover {
            position: absolute;
            top: $padding;
            left: $padding;
            width: calc(100% - $padding);
            height: calc(100% - #{$padding * 2});
            background: rgb(0, 0, 0, 0.4);
            color: white;
            text-align: center;
            vertical-align: middle;
            display: none;

            $fontSize: 20px;
            font-size: $fontSize;

            svg {
                padding-top: calc(($height / 2) - $padding - $fontSize);
            }
        }
        &:hover {
            .hoverCover {
                cursor: pointer;
                display: initial;
            }
        }

        &:last-of-type {
            margin-right: $padding;
        }
    }
}

#galleryPlaceholder {
    $height: 52px;

    background: rgb(0, 0, 0, 0.4);
    width: calc(100% + 40px);
    height: $height;
    line-height: $height;
    margin-top: -52px;
    margin-left: -20px;
    font-size: 20px;
    text-align: center;
    vertical-align: middle;
}

#bigGallery {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 2000;

    background: rgb(0, 0, 0, 0.7);

    #bigGalleryImages {
        position: relative;
        width: 100%;
        height: 100%;

        img {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            max-height: 80%;
            max-width: 80%;
            height: auto;
            width: auto;
        }
    }
    #bigGalleryControls {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        pointer-events: none;

        .close {
            position: absolute;
            margin-top: 25px;
            padding: 15px;
            font-size: 25px;
            right: 0;
            cursor: pointer;
            pointer-events: auto;
        }

        .last, .next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            height: auto;
            width: 10%;
            pointer-events: auto;

            svg {
                height: auto;
                width: auto;
                fill: white;
            }
        }
        .next {
            right: 0;
        }
    }
}
</style>

<template>
    <div id="galleryPlaceholder" v-if="!galleryImgs.length">No Gallery Images...</div>
    <div id="gallery" v-if="galleryImgs.length">
        <div class="image" v-for="(image, index) in galleryImgs">
            <img :src="image.source" :alt="'Image #' +  index" />

            <div class="hoverCover" @click="openGalleryTo(index)">
                <font-awesome-icon icon="magnifying-glass" />
            </div>
        </div>
    </div>
    <div id="bigGallery" v-if="selectedImage.id > 0">
        <div id="bigGalleryImages" @click="closeGallery">
            <div class="image">
                <img :src="selectedImage.source" alt='Image Missing' />
            </div>
        </div>
        <div id="bigGalleryControls">
            <div class="close" @click="closeGallery">
                <font-awesome-icon icon="x" />
            </div>
            <div class="last"
                 v-if="selectedImageIndex"
                 @click="bigGalleryLast">
                <font-awesome-icon icon="chevron-left" />
            </div>
            <div class="next"
                 v-if="selectedImageIndex < galleryImgs.length - 1"
                 @click="bigGalleryNext">
                <font-awesome-icon icon="chevron-right" />
            </div>
        </div>
    </div>
</template>

<script>
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome'
import {library} from "@fortawesome/fontawesome-svg-core"
import {faX, faChevronLeft, faChevronRight, faMagnifyingGlass} from "@fortawesome/free-solid-svg-icons"


library.add(faX);
library.add(faChevronLeft);
library.add(faChevronRight);
library.add(faMagnifyingGlass);

export default {
    name: "Gallery",
    data() {
        return {
            galleryImgs: [],
            selectedImageIndex: 0,
            selectedImage: {
                source: "",
                index: 0
            },
        }
    },
    props: {
        mod: {
            type: Object,
            required: true
        },
        images: {
            type: Array,
            required: false
        }
    },
    components: {
        FontAwesomeIcon
    },
    mounted() {
        if (this.images)
            this.galleryImgs = this.images;
        else
            this.galleryImgs = this.mod.images;
    },
    methods: {
        openGalleryTo(index) {
            console.log("Opening gallery to index: ", index);
            this.selectedImageIndex = index;
            this.selectedImage = this.galleryImgs[this.selectedImageIndex];
        },
        bigGalleryNext() {
            this.selectedImageIndex += 1;
            if (this.selectedImageIndex >= this.galleryImgs.length)
                this.selectedImageIndex = this.galleryImgs.length - 1;

            this.selectedImage = this.galleryImgs[this.selectedImageIndex];
        },
        bigGalleryLast() {
            this.selectedImageIndex -= 1;
            if (this.selectedImageIndex < 0)
                this.selectedImageIndex = 0;

            this.selectedImage = this.galleryImgs[this.selectedImageIndex];
        },
        closeGallery() {
            this.selectedImage = {
                source: "",
                index: 0
            };
        }
    }
}
</script>
