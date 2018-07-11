<template>
    <footer class="footer d-print-none">
        {{ getConfig('footer_credit') }}
    </footer>
</template>

<script>
    import {EventBus} from '../event-bus'

    export default {
        created() {
            EventBus.$on("config::set", () => {
                this.$forceUpdate();
            });
        },
        methods: {
            getConfig(config) {
                return helper.getConfig(config);
            }
        }
    }
</script>
