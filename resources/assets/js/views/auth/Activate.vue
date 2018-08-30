<template>
    <section id="wrapper">
        <div class="hero d-flex" :style="{ 'background-image' : 'url(\'' + getBackground + '\')' }">
            <div class="login-box card row justify-content-center align-self-center">
                <div class="card-body">
                    <h3 class="box-title m-b-20 text-center">{{ trans('auth.account_activation') }}</h3>
                    <h4 v-text="message" class="text-center m-t-20 m-b-20 alert alert-success" v-if="status"></h4>
                    <h4 v-text="message" class="text-center m-t-20 m-b-20 alert alert-danger" v-else></h4>
                    <div class="form-group m-b-0">
                        <div class="col-sm-12 text-center">
                            <p>
                                {{ trans('auth.back_to_login?') }}
                                <router-link to="/login" class="text-info m-l-5">
                                    <b>{{ trans('auth.sign_in') }}</b>
                                </router-link>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    export default {
        data() {
            return {
                token: this.$route.params.token,
                message: i18n.general.processing,
                status: true
            }
        },
        computed: {
            getBackground() {
                if (helper.getConfig('background')) {
                    return '/' + helper.getConfig('background');
                }

                return '/uploads/config/background/background.jpg'
            }
        },
        mounted() {
            if (!helper.featureAvailable('registration') || !helper.featureAvailable('email_verification')) {
                helper.featureNotAvailableMsg();

                return this.$router.push('/home');
            }
            helper.showSpinner();
            axios.get('/api/auth/activate/' + this.token)
                .then(response => response.data)
                .then(response => {
                    this.message = response.message;
                    helper.hideSpinner();
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                    this.status = false;
                    helper.hideSpinner();
            });
        }
    }
</script>
