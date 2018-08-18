<template>
    <section id="wrapper">
        <div class="hero d-flex" :style="{ 'background-image' : 'url(\'' + getBackground + '\')' }">
            <div class="login-box card row justify-content-center align-self-center">
                <div class="card-body">
                    <form class="form-horizontal form-material" id="passwordform" @submit.prevent="submit"
                          @keydown="passwordForm.errors.clear($event.target.name)">
                        <h3 class="box-title m-b-20">{{ trans('passwords.reset_password') }}</h3>
                        <div class="form-group ">
                            <input type="text" name="email" class="form-control" :placeholder="trans('auth.email')"
                                   v-model="passwordForm.email" autocapitalize="none">
                            <show-error :form-name="passwordForm" prop-name="email"></show-error>
                        </div>
                        <div class="form-group text-center m-t-20">
                            <button-spinner
                                    :btn-text="trans('passwords.reset_password')"
                                    :class="'btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light'"
                                    :is-loading="isLoading"
                                    :disabled="isLoading">
                            </button-spinner>
                        </div>
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
                    </form>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    import buttonSpinner from '../../components/ButtonSpinner';

    export default {
        data() {
            return {
                passwordForm: new Form({
                    email: ''
                }),
                isLoading: false
            }
        },
        components: {
            buttonSpinner
        },
        mounted() {
            if (!helper.featureAvailable('reset_password')) {
                helper.featureNotAvailableMsg();
                return this.$router.push('/home');
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
        methods: {
            submit() {
                this.isLoading = true;
                this.passwordForm.post('/api/auth/password')
                    .then(response => {
                        toastr.success(response.message);
                        this.$router.push('/login');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                        this.isLoading = false;
                    });
            },
            getConfig(config) {
                return helper.getConfig(config);
            }
        }
    }
</script>
