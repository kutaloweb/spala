<template>
    <section id="wrapper">
        <div class="hero d-flex" :style="{ 'background-image' : 'url(\'' + getBackground + '\')' }">
            <div class="login-box card row justify-content-center align-self-center">
                <div class="card-body">
                    <div class="m-t-30 text-center">
                        <img :src="getAuthUser('avatar')" class="img-circle" width="100">
                        <h4 class="card-title m-t-10">{{ getAuthUser('full_name') }}</h4>
                    </div>
                    <form class="form-horizontal form-material" id="twoFactorForm" @submit.prevent="submit"
                          @keydown="twoFactorForm.errors.clear($event.target.name)">
                        <h3 class="box-title m-b-20 text-center">{{ trans('auth.two_factor_security') }}</h3>
                        <div class="form-group ">
                            <input type="text" name="two_factor_code" class="form-control text-center"
                                   :placeholder="trans('auth.two_factor_code')" v-model="twoFactorForm.two_factor_code">
                            <show-error :form-name="twoFactorForm" prop-name="two_factor_code"></show-error>
                        </div>
                        <div class="form-group text-center m-t-20">
                            <div class="col-xs-12">
                                <button-spinner
                                        :btn-text="trans('auth.confirm')"
                                        :class="'btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light'"
                                        :is-loading="isLoading"
                                        :disabled="isLoading">
                                </button-spinner>
                            </div>
                        </div>
                        <div class="form-group m-b-0">
                            <div class="col-sm-12 text-center">
                                <p>
                                    <a href="#" @click.prevent="logout">
                                        <i class="fas fa-power-off"></i> {{ trans('auth.logout') }}
                                    </a>
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
                twoFactorForm: new Form({
                    two_factor_code: '',
                }),
                isLoading: false
            }
        },
        components: {
            buttonSpinner
        },
        mounted() {
            if (!helper.getConfig('two_factor_security') || !helper.getTwoFactorCode()) {
                this.$router.push('/home');
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
            logout() {
                helper.logout().then(() => {
                    this.$store.dispatch('resetAuthUserDetail');
                    this.$router.push('/login')
                })
            },
            submit() {
                this.isLoading = true;
                if (this.twoFactorForm.two_factor_code == helper.getTwoFactorCode()) {
                    this.$store.dispatch('resetTwoFactorCode');
                    toastr.success(i18n.auth.two_factor_security_verified);
                    this.$router.push('/home');
                } else {
                    toastr.error(i18n.auth.invalid_two_factor_code);
                    this.isLoading = false;
                }
            },
            getAuthUser(name) {
                return helper.getAuthUser(name);
            }
        }
    }
</script>
