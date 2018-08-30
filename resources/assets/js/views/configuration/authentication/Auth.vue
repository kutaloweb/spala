<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{ trans('configuration.configuration') }}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <router-link to="/home">{{ trans('general.home') }}</router-link>
                    </li>
                    <li class="breadcrumb-item">
                        <router-link to="/configuration">{{ trans('configuration.configuration') }}</router-link>
                    </li>
                    <li class="breadcrumb-item active">{{ trans('auth.authentication') }}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <configuration-sidebar menu="authentication"></configuration-sidebar>
                            <div class="col-10 col-lg-10 col-md-10">
                                <h4 class="card-title">{{ trans('auth.authentication') }}</h4>
                                <form @submit.prevent="submit" @keydown="configForm.errors.clear($event.target.name)">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label>
                                                    {{ trans('auth.registration') }}
                                                </label>
                                                <div>
                                                    <switches class="" v-model="configForm.registration"
                                                              theme="bootstrap" color="success"
                                                              v-bind:true-value="1"
                                                              v-bind:false-value="0"></switches>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label>
                                                    {{ trans('auth.two_factor_security') }}
                                                </label>
                                                <div>
                                                    <switches class="" v-model="configForm.two_factor_security"
                                                              theme="bootstrap" color="success"></switches>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label>
                                                    {{ trans('auth.email_verification') }}
                                                </label>
                                                <div>
                                                    <switches class="" v-model="configForm.email_verification"
                                                              theme="bootstrap" color="success"></switches>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label>
                                                    {{ trans('auth.account_approval') }}
                                                </label>
                                                <div>
                                                    <switches class="" v-model="configForm.account_approval"
                                                              theme="bootstrap" color="success"></switches>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label>
                                                    {{ trans('auth.reset_password') }}
                                                </label>
                                                <div>
                                                    <switches class="" v-model="configForm.reset_password"
                                                              theme="bootstrap" color="success"></switches>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label>
                                                    {{ trans('auth.lock_screen') }}
                                                </label>
                                                <div>
                                                    <switches class="" v-model="configForm.lock_screen"
                                                              theme="bootstrap" color="success"></switches>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group" v-if="configForm.reset_password">
                                                <label>
                                                    {{ trans('auth.reset_password_token_lifetime') + ' ' +
                                                    trans('auth.in_minute') }}
                                                </label>
                                                <input class="form-control" type="number" value=""
                                                       v-model="configForm.reset_password_token_lifetime"
                                                       name="reset_password_token_lifetime"
                                                       :placeholder="trans('auth.reset_password_token_lifetime')">
                                                <show-error :form-name="configForm"
                                                            prop-name="reset_password_token_lifetime"></show-error>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group" v-if="configForm.lock_screen">
                                                <label>
                                                    {{ trans('auth.lock_screen_timeout') + ' ' + trans('auth.in_minute')
                                                    }}
                                                </label>
                                                <input class="form-control" type="number" value=""
                                                       v-model="configForm.lock_screen_timeout"
                                                       name="lock_screen_timeout"
                                                       :placeholder="trans('auth.lock_screen_timeout')">
                                                <show-error :form-name="configForm"
                                                            prop-name="lock_screen_timeout"></show-error>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit"
                                            class="btn btn-info waves-effect waves-light m-t-10 pull-right">
                                        {{ trans('general.save') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import configurationSidebar from '../SystemConfigSidebar'
    import switches from 'vue-switches'

    export default {
        components: {configurationSidebar, switches},
        data() {
            return {
                configForm: new Form({
                    config_type: '',
                    token_lifetime: '',
                    reset_password_token_lifetime: '',
                    registration: 0,
                    email_verification: 0,
                    account_approval: 0,
                    reset_password: 0,
                    two_factor_security: 0,
                    lock_screen: 0,
                    lock_screen_timeout: '',
                }, false)
            }
        },
        mounted() {
            if (!helper.hasPermission('access-configuration')) {
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            helper.showSpinner();
            axios.get('/api/configuration')
                .then(response => response.data)
                .then(response => {
                    this.configForm = helper.formAssign(this.configForm, response);
                    helper.hideSpinner();
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                    helper.hideSpinner();
                });
        },
        methods: {
            submit() {
                this.configForm.config_type = 'authentication';
                this.configForm.registration = (this.configForm.registration) ? 1 : 0;
                this.configForm.email_verification = (this.configForm.email_verification) ? 1 : 0;
                this.configForm.account_approval = (this.configForm.account_approval) ? 1 : 0;
                this.configForm.reset_password = (this.configForm.reset_password) ? 1 : 0;
                this.configForm.two_factor_security = (this.configForm.two_factor_security) ? 1 : 0;
                this.configForm.lock_screen = (this.configForm.lock_screen) ? 1 : 0;
                this.configForm.post('/api/configuration')
                    .then(response => {
                        this.$store.dispatch('setConfig', this.configForm);
                        toastr.success(response.message);
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getConfig(config) {
                return helper.getConfig(config);
            }
        }
    }
</script>
