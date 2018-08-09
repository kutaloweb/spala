<template>
    <section id="wrapper" class="error-page">
        <div class="error-box">
            <div class="error-body text-center">
                <h1 class="text-muted m-t-30 m-b-30"><i class="fas fa-wrench"></i></h1>
                <h2 class="text-muted m-t-30 m-b-30 text-uppercase">{{ getConfig('maintenance_mode_message') }}</h2>
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        mounted() {
            if (!helper.getConfig('maintenance_mode')) {
                this.$router.push('/');
            }
        },
        methods: {
            getConfig(config) {
                return helper.getConfig(config);
            }
        }
    }
</script>
