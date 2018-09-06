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
                    <li class="breadcrumb-item active">{{ trans('configuration.system_configuration') }}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <configuration-sidebar menu="system"></configuration-sidebar>
                            <div class="col-10 col-lg-10 col-md-10">
                                <h4 class="card-title">{{ trans('general.system') }}</h4>
                                <form @submit.prevent="submit" @keydown="configForm.errors.clear($event.target.name)">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label>{{ trans('configuration.color_theme') }}</label>
                                                <select v-model="configForm.color_theme"
                                                        class="custom-select col-12 form-control">
                                                    <option v-for="option in systemConfigVariables.color_themes"
                                                            v-bind:value="option.value">
                                                        {{ option.text }}
                                                    </option>
                                                </select>
                                                <show-error :form-name="configForm"
                                                            prop-name="color_theme">
                                                </show-error>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label>{{ trans('configuration.notification_position') }}</label>
                                                <select v-model="configForm.notification_position"
                                                        class="custom-select col-12 form-control">
                                                    <option v-for="option in systemConfigVariables.notification_positions"
                                                            v-bind:value="option.value">
                                                        {{ option.text }}
                                                    </option>
                                                </select>
                                                <show-error :form-name="configForm"
                                                            prop-name="notification_position">
                                                </show-error>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6" v-if="getConfig('multilingual')">
                                            <div class="form-group">
                                                <label>{{ trans('locale.locale') }}</label>
                                                <select v-model="configForm.locale"
                                                        class="custom-select col-12 form-control">
                                                    <option v-for="option in systemConfigVariables.locales"
                                                            v-bind:value="option.value">
                                                        {{ option.text }}
                                                    </option>
                                                </select>
                                                <show-error :form-name="configForm"
                                                            prop-name="locale">
                                                </show-error>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label>{{ trans('configuration.page_length') }}</label>
                                                <select v-model="configForm.page_length"
                                                        class="custom-select col-12 form-control">
                                                    <option v-for="option in getConfig('pagination')"
                                                            v-bind:value="option">
                                                        {{ option + ' ' + trans('general.per_page') }}
                                                    </option>
                                                </select>
                                                <show-error :form-name="configForm"
                                                            prop-name="page_length">
                                                </show-error>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label>{{ trans('configuration.timezone') }}</label>
                                                <select v-model="configForm.timezone"
                                                        class="custom-select col-12 form-control">
                                                    <option v-for="option in systemConfigVariables.timezones"
                                                            v-bind:value="option.value">
                                                        {{ option.text }}
                                                    </option>
                                                </select>
                                                <show-error :form-name="configForm"
                                                            prop-name="timezone">
                                                </show-error>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label>{{ trans('configuration.footer_credit') }}</label>
                                                <input class="form-control" type="text" value=""
                                                       v-model="configForm.footer_credit" name="footer_credit"
                                                       :placeholder="trans('configuration.footer_credit')">
                                                <show-error :form-name="configForm"
                                                            prop-name="footer_credit">
                                                </show-error>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label>{{ trans('configuration.company_name') }}</label>
                                                <input class="form-control" type="text" value=""
                                                       v-model="configForm.company_name" name="company_name"
                                                       :placeholder="trans('configuration.company_name')">
                                                <show-error :form-name="configForm"
                                                            prop-name="company_name">
                                                </show-error>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label>{{ trans('configuration.facebook_group') }}</label>
                                                <input class="form-control" type="text" value=""
                                                       v-model="configForm.facebook_group" name="facebook_group"
                                                       :placeholder="trans('configuration.facebook_group')">
                                                <show-error :form-name="configForm"
                                                            prop-name="facebook_group">
                                                </show-error>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label>{{ trans('configuration.company_description') }}</label>
                                                <input class="form-control" type="text" value=""
                                                       v-model="configForm.company_description" name="company_description"
                                                       :placeholder="trans('configuration.company_description')">
                                                <show-error :form-name="configForm"
                                                            prop-name="company_description">
                                                </show-error>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label>{{ trans('configuration.contact_info') }}</label>
                                                <input class="form-control" type="text" value=""
                                                       v-model="configForm.contact_info" name="contact_info"
                                                       :placeholder="trans('configuration.contact_info')">
                                                <show-error :form-name="configForm"
                                                            prop-name="contact_info">
                                                </show-error>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label>
                                                    {{ trans('configuration.public_login') }}
                                                </label>
                                                <div>
                                                    <switches class="" v-model="configForm.public_login"
                                                              theme="bootstrap" color="success">
                                                    </switches>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label>
                                                    {{ trans('configuration.multilingual') }}
                                                </label>
                                                <div>
                                                    <switches class="" v-model="configForm.multilingual"
                                                              theme="bootstrap" color="success">
                                                    </switches>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label>
                                                    {{ trans('configuration.maintenance_mode') }}
                                                </label>
                                                <div>
                                                    <switches class="" v-model="configForm.maintenance_mode"
                                                              theme="bootstrap" color="success">
                                                    </switches>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label>
                                                    {{ trans('configuration.https') }}
                                                </label>
                                                <div>
                                                    <switches class="" v-model="configForm.https"
                                                              theme="bootstrap" color="success">
                                                    </switches>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group" v-if="configForm.maintenance_mode">
                                                <label>
                                                    {{ trans('configuration.maintenance_mode_message') }}
                                                </label>
                                                <input class="form-control" type="text" value=""
                                                       v-model="configForm.maintenance_mode_message"
                                                       name="maintenance_mode_message"
                                                       :placeholder="trans('configuration.maintenance_mode_message')">
                                                <show-error :form-name="configForm"
                                                            prop-name="maintenance_mode_message">
                                                </show-error>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-info waves-effect waves-light pull-right m-t-10">
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
    import {EventBus} from '../../../event-bus'

    export default {
        components: {configurationSidebar, switches},
        data() {
            return {
                configForm: new Form({
                    color_theme: '',
                    notification_position: '',
                    timezone: '',
                    page_length: 10,
                    locale: '',
                    footer_credit: '',
                    company_name: '',
                    company_description: '',
                    contact_info: '',
                    facebook_group: '',
                    https: 0,
                    multilingual: 0,
                    maintenance_mode: 0,
                    public_login: 0,
                    maintenance_mode_message: '',
                    config_type: ''
                }, false),
                systemConfigVariables: {
                    color_themes: [],
                    notification_positions: [],
                    timezones: [],
                    locales: []
                },
                locale: '',
            };
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
                    helper.hideSpinner();
                    this.configForm = helper.formAssign(this.configForm, response);
                    this.locale = response.locale;
                })
                .catch(error => {
                    helper.hideSpinner();
                    helper.showDataErrorMsg(error);
            });
            axios.get('/api/configuration/variable?type=system')
                .then(response => response.data)
                .then(response => {
                    this.systemConfigVariables.color_themes = response.color_themes;
                    this.systemConfigVariables.notification_positions = response.notification_positions;
                    this.systemConfigVariables.timezones = response.timezones;
                    this.systemConfigVariables.locales = response.locales;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });
        },
        methods: {
            submit() {
                this.configForm.config_type = 'system';
                this.configForm.https = (this.configForm.https) ? 1 : 0;
                this.configForm.multilingual = (this.configForm.multilingual) ? 1 : 0;
                this.configForm.maintenance_mode = (this.configForm.maintenance_mode) ? 1 : 0;
                this.configForm.public_login = (this.configForm.public_login) ? 1 : 0;
                this.configForm.post('/api/configuration')
                    .then(response => {
                        this.$store.dispatch('setConfig', this.configForm);
                        $('#theme').attr('href', '/css/colors/' + helper.getConfig('color_theme') + '.css');
                        if (helper.getConfig('locale') !== this.locale) {
                            location.reload();
                        }
                        EventBus.$emit('config::set');
                        this.$forceUpdate();
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
