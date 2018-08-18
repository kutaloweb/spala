<template>
    <section id="wrapper">
        <div class="hero d-flex" :style="{ 'background-image' : 'url(\'' + getBackground + '\')' }">
            <div class="login-box card row justify-content-center align-self-center">
                <div class="card-body">
                    <form class="form-horizontal form-material" id="registerform" @submit.prevent="submit"
                          @keydown="registerForm.errors.clear($event.target.name)">
                        <h3 class="box-title m-b-20">{{ trans('auth.sign_up') }}</h3>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group ">
                                    <input type="text" name="first_name" class="form-control"
                                           :placeholder="trans('auth.first_name')" v-model="registerForm.first_name">
                                    <show-error :form-name="registerForm" prop-name="first_name"></show-error>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group ">
                                    <input type="text" name="last_name" class="form-control"
                                           :placeholder="trans('auth.last_name')" v-model="registerForm.last_name">
                                    <show-error :form-name="registerForm" prop-name="last_name"></show-error>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <input type="email" name="email" class="form-control" :placeholder="trans('auth.email')"
                                   v-model="registerForm.email" autocapitalize="none">
                            <show-error :form-name="registerForm" prop-name="email"></show-error>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <div>
                                        <input type="password" name="password" class="form-control"
                                               :placeholder="trans('auth.password')" v-model="registerForm.password">
                                    </div>
                                    <show-error :form-name="registerForm" prop-name="password"></show-error>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <input type="password" name="password_confirmation" class="form-control"
                                           :placeholder="trans('auth.confirm_password')"
                                           v-model="registerForm.password_confirmation">
                                    <show-error :form-name="registerForm"
                                                prop-name="password_confirmation"></show-error>
                                </div>
                            </div>
                        </div>
                        <div class="form-group text-center m-t-20">
                            <button-spinner
                                    :btn-text="trans('auth.register')"
                                    :class="'btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light'"
                                    :is-loading="isLoading"
                                    :disabled="isLoading">
                            </button-spinner>
                        </div>
                        <div class="form-group m-b-0">
                            <div class="col-sm-12 text-center">
                                <p>{{ trans('auth.already_have_account?') }}
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
                registerForm: new Form({
                    email: '',
                    password: '',
                    password_confirmation: '',
                    first_name: '',
                    last_name: ''
                }),
                isLoading: false
            }
        },
        components: {
            buttonSpinner
        },
        mounted() {
            if (!helper.featureAvailable('registration')) {
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
                this.registerForm.post('/api/auth/register')
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
