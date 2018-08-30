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
                    <li class="breadcrumb-item active">{{ trans('locale.locale') }}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <configuration-sidebar menu="locale"></configuration-sidebar>
                            <div class="col-10 col-lg-10 col-md-10">
                                <div class="row">
                                    <div class="col-12 col-sm-4 col-md-4">
                                        <h4 class="card-title">{{ trans('locale.add_new_locale') }}</h4>
                                        <locale-form @completed="getLocales"></locale-form>
                                    </div>
                                    <div class="col-12 col-sm-8 col-md-8">
                                        <h4 class="card-title">{{ trans('locale.locale_list') }}</h4>
                                        <h6 class="card-subtitle" v-if="locales">
                                            {{ trans('general.total_result_found', {count:locales.total}) }}</h6>
                                        <h6 class="card-subtitle" v-else>
                                            {{ trans('general.no_result_found') }}
                                        </h6>
                                        <div class="table-responsive" v-if="locales.total">
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th>{{ trans('configuration.name') }}</th>
                                                    <th>{{ trans('locale.locale') }}</th>
                                                    <th class="table-option">{{ trans('general.action') }}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="locale in locales.data">
                                                    <td v-text="locale.name"></td>
                                                    <td v-text="locale.locale"></td>
                                                    <td class="table-option">
                                                        <div class="btn-group">
                                                            <button class="btn btn-info btn-sm"
                                                                    v-tooltip="trans('locale.edit_locale')"
                                                                    @click.prevent="editLocale(locale)"><i
                                                                    class="fas fa-edit"></i></button>
                                                            <button class="btn btn-danger btn-sm" :key="locale.id"
                                                                    v-if="locale.locale !== 'en'"
                                                                    v-confirm="{ok: confirmDelete(locale)}"
                                                                    v-tooltip="trans('locale.delete_locale')"><i
                                                                    class="fas fa-trash"></i></button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <pagination-record :page-length.sync="filterLocaleForm.page_length"
                                                           :records="locales" @updateRecords="getLocales"
                                                           @change.native="getLocales"></pagination-record>
                                    </div>
                                </div>
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
    import localeForm from './Form'
    import vSelect from 'vue-multiselect'

    export default {
        components: {configurationSidebar, localeForm, vSelect},
        data() {
            return {
                locales: {
                    total: 0,
                    data: []
                },
                filterLocaleForm: {
                    page_length: helper.getConfig('page_length')
                },
                modules: []
            };
        },
        mounted() {
            if (!helper.hasPermission('access-configuration')) {
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            if (!helper.featureAvailable('multilingual')) {
                helper.featureNotAvailableMsg();
                this.$router.push('/home');
            }

            this.getLocales();
        },
        methods: {
            getLocales(page) {
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterLocaleForm);
                helper.showSpinner();
                axios.get('/api/locale?page=' + page + url)
                    .then(response => response.data)
                    .then(response => {
                        this.locales = response.locales;
                        this.modules = response.modules;
                        helper.hideSpinner();
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                        helper.hideSpinner();
                    });
            },
            editLocale(locale) {
                this.$router.push('/configuration/locale/' + locale.id + '/edit');
            },
            confirmDelete(locale) {
                return dialog => this.deleteLocale(locale);
            },
            deleteLocale(locale) {
                axios.delete('/api/locale/' + locale.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getLocales();
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            }
        }
    }
</script>
