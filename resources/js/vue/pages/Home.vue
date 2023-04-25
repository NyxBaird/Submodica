<style scoped lang="scss">
    @import 'resources/sass/variables';

    #Title {
        position: relative;
        top: 1vw;
        width: 100%;

        font-family: "DaysOne-Regular", sans-serif;
        font-size: 10vw;
        color: #C7E5F6;
        text-align: center;

        -webkit-text-stroke: 2px black;
        text-shadow: 0 0 10px white;

        span {
            color: #FFCC42;
        }

        @include mobile() {
            margin-top: 5px;
        }
    }

    h2 {
        font-family: "DaysOne-Regular", sans-serif;
        margin: 0;
    }

    #modLinkBtns {
        position: relative;
        display: inline-block;
        left: 50%;
        transform: translateX(-50%);
        margin-top: 40px;


        .btn {
            display: inline-block;
            padding: 10px 30px;
            font-weight: bold;
            font-size: 2vw;
            border-radius: 0.5vw;

            &.bz {
                $color: #383c8a;
                background-color: #c4e2ff;
                color: $color;
                border: 1px solid $color;
            }
        }
    }

    #News {
        margin-top: 40px !important; // WIP implement this without !important because using !important is stupid
        p {
            text-indent: 25px;
            line-height: 24px;
        }
    }
</style>

<template ref="Home">
    <div>
        <div id="Title">
            SUB<span>MOD</span>ICA
        </div>

        <div id="modLinkBtns">
            <div class="btn btnRed" @click="$router.push('/mods/sn1')">View Subnautica Mods</div>
            <div class="btn bz" @click="$router.push('/mods/sbz')">View Subnautica: Below Zero Mods</div>
        </div>

        <container id="News">
            <h2>News</h2>
            <hr />
            <p>
                Submodica is live! Please join the Subnautica Modding Discord for updates on future Submodica features and to discuss modding in general. <a href="https://discord.com/invite/JMHQBn9rYt">https://discord.com/invite/JMHQBn9rYt</a>
            </p>
            <p>
                There's still lots to do and I'll release a proper road map here soon. If you find any bugs, please report them <a href="/contact">here</a>.
            </p>
        </container>
    </div>
</template>

<script>
import Container from "../components/Container.vue";
import Modal from "../components/Modal.vue";

export default {
    name: "Home",
    data() {
        return {
            trendingMods: []
        };
    },
    components: {
        "authModal": Modal,
        "container": Container
    },
    mounted() {
        document.querySelector('title').textContent = "Home | Submodica";

        this.$axios.post('/mods/getTrending')
            .then((response) => {
                this.trendingMods = response.data.data;
                console.log("fetched!", response.data.data);
            });
    }
}
</script>
