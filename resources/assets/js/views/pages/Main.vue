<template>
    <section id="wrapper">
        <div v-if="getConfig('public_login') && !isAuth" class="hero d-flex" :style="{ 'background-image' : 'url(\'' + getBackground + '\')' }">
            <div class="login-box card row justify-content-center align-self-center">
                <div class="card-body">
                    <form class="form-horizontal form-material" id="loginform" @submit.prevent="submit"
                          @keydown="loginForm.errors.clear($event.target.name)">
                        <h3 class="box-title m-b-20">{{ trans('auth.login') }}</h3>
                        <div class="form-group ">
                            <input type="text" name="email" class="form-control" :placeholder="trans('auth.email')"
                                   v-model="loginForm.email" autocapitalize="none">
                            <show-error :form-name="loginForm" prop-name="email"></show-error>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" class="form-control"
                                   :placeholder="trans('auth.password')" v-model="loginForm.password">
                            <show-error :form-name="loginForm" prop-name="password"></show-error>
                        </div>
                        <div class="form-group text-center m-t-20">
                            <button-spinner
                                    :btn-text="trans('auth.sign_in')"
                                    :class="'btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light'"
                                    :is-loading="isLoading"
                                    :disabled="isLoading">
                            </button-spinner>
                        </div>
                        <div class="form-group m-b-0">
                            <div class="col-sm-12 text-center">
                                <p v-if="getConfig('reset_password')">
                                    {{ trans('auth.forgot_your_password?') }}
                                    <router-link to="/password" class="text-info m-l-5">
                                        <b>{{ trans('auth.reset_here!') }}</b>
                                    </router-link>
                                </p>
                                <p v-if="getConfig('registration')">
                                    {{ trans('auth.create_account?') }}
                                    <router-link to="/register" class="text-info m-l-5">
                                        <b>{{trans('auth.sign_up')}}</b>
                                    </router-link>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="d-flex align-items-center justify-content-center hero" :style="{ 'background-image' : 'url(\'' + getBackground + '\')' }">
                <div class="d-flex flex-column">
                    <div class="flex-container-main">
                        <div class="flex-container-child text-center">
                            <h1 class="text align-self-center">{{ getConfig('company_description') }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <page-list></page-list>
        <post-list></post-list>
    </section>
</template>

<script>
    import postList from '../post/List'
    import pageList from '../page/List'
    import buttonSpinner from '../../components/ButtonSpinner';

    export default {
        data() {
            return {
                loginForm: new Form({
                    email: '',
                    password: ''
                }),
                isLoading: false
            }
        },
        components: {
            postList,
            pageList,
            buttonSpinner
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
            document.title = `${helper.getConfig('company_name')}`;
        },
        methods: {
            submit() {
                this.isLoading = true;
                this.loginForm.post('/api/auth/login')
                    .then(response => {
                        localStorage.setItem('auth_token', response.token);
                        axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('auth_token');
                        this.$store.dispatch('setAuthStatus');
                        this.$store.dispatch('setLastActivity');
                        if (helper.getConfig('two_factor_security') && response.two_factor_code) {
                            this.$store.dispatch('setTwoFactorCode', response.two_factor_code);
                            this.$router.push('/auth/security');
                        } else {
                            this.$store.dispatch('resetTwoFactorCode');
                            this.$router.push('/home');
                        }
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                        this.isLoading = false;
                    });
            },
            getConfig(config) {
                return helper.getConfig(config);
            },
            isAuth() {
                return helper.isAuth();
            }
        }
    }
</script>
