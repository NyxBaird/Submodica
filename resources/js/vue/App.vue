<style lang="scss" scoped>
    @import "../../sass/app.scss";
</style>

<template ref="App">
    <div id="alerts">
        <Alert v-for="alert in alerts"
               :type="alert.type"
               :timer="typeof alert.timer!=='undefined' ? alert.timer : true"
               :seconds="typeof alert.seconds!=='undefined' ? alert.seconds : 5">

            {{alert.content}}
        </Alert>
    </div>

    <Frontend ref="Frontend" />
</template>

<script>
    import Frontend from "../vue/Frontend.vue"
    import Alert from "../vue/components/Alert.vue";

    export default {
        name: "App",
        components: {Alert, Frontend},
        data() {
            return { alerts: [] };
        },
        created() {
            let data = {};
            if (window.location.search && isJson(decodeURIComponent(window.location.search.substring(1).replace(/\+/g, ' '))))
                data = JSON.parse(decodeURIComponent(window.location.search.substring(1).replace(/\+/g, ' ')));

            if (typeof data.route !== "undefined")
                this.$router.push(data.route);

            if (typeof data.success !== "undefined" && typeof data.message !== "undefined") {
                this.alerts.push({
                    type: data.success ? "success" : "error",
                    content: data.message
                });
            }

            function isJson(str) {
                try {
                    JSON.parse(str);
                } catch (e) {
                    return false;
                }
                return true;
            }
        },
        mounted() {
            let vue = this;
            this.$axios.interceptors.response.use(function (response) {
                return response;
            }, function (error) {

                if (window.development)
                    console.log(error);

                //WIP definitely more to do here
                switch (error.response.data.message) {
                    case 'CSRF token mismatch.':
                        // push new alert to $root.alerts array

                        vue.alerts.push({
                            type: "error",
                            content: "Your session has expired. Please refresh the page to re-authenticate and continue. The page will be refreshed for you in 3 seconds."
                        });

                        // setTimeout(function(){
                            location.reload();
                        // }, 3000);
                        break;
                    case "Unauthenticated.":
                        vue.alerts.push({
                            type: "error",
                            content: "Please log in!"
                        });
                        break;
                    default:
                        break;
                }

                return Promise.reject(error);
            });
        }
    };
</script>
