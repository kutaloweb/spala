<template>
    <section id="wrapper" class="error-page">
        <div class="error-box">
            <div class="error-body text-center">
                <h2>{{ trans('general.maintenance') }}</h2>
                <p class="text-muted m-t-30 m-b-30">{{ getConfig('maintenance_mode_message') }}</p>
                <router-link to="/" class="btn btn-info btn-rounded waves-effect waves-light m-b-40">
                    {{ trans('general.back') }}
                </router-link>
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        mounted() {
            if (!helper.getConfig('maintenance_mode') || !helper.isAuth() || helper.hasRole('admin')) {
                this.$router.push('/home');
            }
            this.$store.dispatch('resetAuthUserDetail');
        },
        methods: {
            getConfig(config) {
                return helper.getConfig(config);
            }
        }
    }
</script>
