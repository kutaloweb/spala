<template>
    <section id="wrapper">
        <div class="hero d-flex" :style="{ 'background-image' : 'url(\'' + getBackground + '\')' }">
            <div class="login-box card row justify-content-center align-self-center">
                <div class="card-body">
                    <h3 class="box-title m-b-20 text-center">{{ trans('passwords.reset_password') }}</h3>
                    <div v-if="!showMessage">
                        <form class="form-horizontal form-material" id="resetform" @submit.prevent="submit"
                              @keydown="resetForm.errors.clear($event.target.name)">
                            <div class="form-group ">
                                <input type="text" name="email" class="form-control" :placeholder="trans('auth.email')"
                                       v-model="resetForm.email" autocapitalize="none">
                                <show-error :form-name="resetForm" prop-name="email"></show-error>
                            </div>
                            <div class="form-group">
                                <input type="password" name="password" class="form-control"
                                       :placeholder="trans('auth.password')" v-model="resetForm.password">
                                <show-error :form-name="resetForm" prop-name="password"></show-error>
                            </div>
                            <div class="form-group">
                                <input type="password" name="password" class="form-control"
                                       :placeholder="trans('auth.confirm_password')"
                                       v-model="resetForm.password_confirmation">
                                <show-error :form-name="resetForm" prop-name="password_confirmation"></show-error>
                            </div>
                            <div class="form-group text-center m-t-20">
                                <button-spinner
                                        :btn-text="trans('passwords.reset_password')"
                                        :class="'btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light'"
                                        :is-loading="isLoading"
                                        :disabled="isLoading">
                                </button-spinner>
                            </div>
                        </form>
                    </div>
                    <div v-else class="text-center">
                        <h4 v-text="message" class="alert alert-success" v-if="status"></h4>
                        <h4 v-text="message" class="alert alert-danger" v-else></h4>
                    </div>
                    <div class="form-group m-b-0">
                        <div class="col-sm-12 text-center">
                            <p>{{ trans('auth.back_to_login?') }}
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
    import buttonSpinner from '../../components/ButtonSpinner';

    export default {
        data() {
            return {
                resetForm: new Form({
                    email: '',
                    password: '',
                    password_confirmation: '',
                    token: this.$route.params.token,
                }),
                message: '',
                status: true,
                showMessage: false,
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

            axios.post('/api/auth/validate-password-reset', { token: this.resetForm.token })
                .then(response => {
                    this.showMessage = false;
                })
                .catch(error => {
                    this.message = helper.fetchDataErrorMsg(error);
                    this.showMessage = true;
                    this.status = false;
                });
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
                this.resetForm.post('/api/auth/reset')
                    .then(response => {
                        this.message = response.message;
                        this.showMessage = true;
                        this.status = true;
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                        this.isLoading = false;
                    });
            }
        }
    }
</script>
