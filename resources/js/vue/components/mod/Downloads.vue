<style lang="scss" scoped>
    .oldLinks {
        font-size: 13px;
    }
    .altTitle {
        margin-top: 5px;
    }

    #old {
        margin-top: -10px;
    }

    .oldLink {
        margin-bottom: 15px;
    }
</style>

<template>
    <div>
        <div id="current">
            <a v-if="typeof links[mod.latest_version].main !== 'undefined'"
               href="#" @click="confirmDownload(links[mod.latest_version].main)">

                Current Version ({{ mod.latest_version }})
            </a>
            <span v-else>Current Version ({{ mod.latest_version }}):</span>

            <div v-if="typeof links[mod.latest_version].alts !== 'undefined' && typeof links[mod.latest_version].main !== 'undefined'" class='altTitle'>
                Alternative Downloads for {{ mod.latest_version }}:
            </div>
            <div v-if="typeof links[mod.latest_version].alts !== 'undefined'" v-for="link in links[mod.latest_version].alts">
                &nbsp;&nbsp;&nbsp;&nbsp;- <a href="#" @click="confirmDownload(link)">{{ link.title }}</a>
            </div>
        </div>
        <h2 v-if="Object.keys(this.links).length > 1">
            <br />
            Older Versions
        </h2>
        <div id="old">
            <div v-for="(link, version) in links" class="oldLink">
                <div v-if="version !== mod.latest_version">
                    <a href="#" @click="confirmDownload(link.main)">Version {{ version }}</a>

                    <div v-if="typeof link.alts !== 'undefined'" class='altTitle'>
                        Alternative Downloads for {{ version }}:
                    </div>
                    <div v-if="typeof link.alts !== 'undefined'" v-for="alt in link.alts">
                        &nbsp;&nbsp;&nbsp;&nbsp;- <a href="#" @click="confirmDownload(alt)">{{ alt.title }}</a>
                    </div>
                </div>
            </div>
        </div>

        <Modal title="Dependencies" ref="dependenciesModal">
            <template #default>
                <br />
                This mod depends on these other mods to work! Please install them first.
                <br />
                <br />
                <div id="dependencies">
                    <span class="customDependencies">
                        <span v-if="!mod.attributions.length">
                            &nbsp;&nbsp;&nbsp;&nbsp;• None<br />
                        </span>
                    </span>

                    <span class="customDependencies" v-for="dependency in mod.attributions">
                        <span v-if="!dependency.attributed_mod">
                            &nbsp;&nbsp;&nbsp;&nbsp;• <a :href="dependency.url" target="_blank">{{ dependency.name }}</a><br />
                        </span>
                        <span v-else>
                            &nbsp;&nbsp;&nbsp;&nbsp;• <a :href="`/mods/${dependency.attributed_mod.game}/${dependency.attributed_mod.id}`" target="_blank">{{ dependency.attributed_mod.title }}</a><br />
                        </span>
                    </span>
                </div>
                <br/>
            </template>
            <template #footer>
                <button class="btn" @click="openAllDependencies">Open all dependencies in New Tabs</button>
                <button class="btn btnRed floatRight" @click="returnDownload">Continue to my download</button>
            </template>
        </Modal>
    </div>
</template>

<script>
import Modal from "../Modal.vue"

export default {
    name: "Downloads",
    components: {
        Modal
    },
    data() {
        return {
            downloading: {},
            processingDownload: false,
        };
    },
    props: {
        mod: {
            type: Object,
            required: true
        },
        dependencies: {
            type: Array,
            required: true
        }
    },
    methods: {
        confirmDownload(download) {
            console.log("setting download to ", download, 'from', this.links)

            this.downloading = download;
            this.$refs.dependenciesModal.open();
        },
        async returnDownload() {
            if (!this.processingDownload) {
                this.processingDownload = true;
                this.$axios.post(`/track/download/${this.downloading.id}`)
                    .then((response) => {
                        this.$root.alerts.push({
                            type: response.data.success ? "success" : "error",
                            content: response.data.message
                        });

                        //Make the user wait before they can this.downloading the file
                        let vue = this;
                        setTimeout(function () {
                            vue.processingDownload = false;
                        }, 3000);

                        window.open(this.downloading.url, '_parent');
                        this.$refs.dependenciesModal.close();
                    });
            } else {
                this.$root.alerts.push({
                    type: "error",
                    content: "Your download request is already being processed."
                });
            }
        },
        openAllDependencies() {
            this.dependencies.forEach(dependency => {
                if (dependency.attributed_mod)
                    window.open(`/mods/${dependency.attributed_mod.game}/${dependency.attributed_mod.id}`, '_blank');
                else
                    window.open(dependency.url, '_blank');
            });
        },
    },
    computed: {
        links() {
            let links = this.mod.download_links,
                finalLinks = {};

            for (let l in links) {
                if (typeof finalLinks[links[l].version] === "undefined")
                    finalLinks[links[l].version] = {};

                if (links[l].title) {
                    if (typeof finalLinks[links[l].version].alts === "undefined")
                        finalLinks[links[l].version].alts = [];

                    finalLinks[links[l].version].alts.push(links[l]);
                } else
                    finalLinks[links[l].version].main = links[l];
            }

            return finalLinks;
        }
    }
}
</script>
