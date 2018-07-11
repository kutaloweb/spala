<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{ trans('locale.edit_locale') }}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <router-link to="/home">{{ trans('general.home') }}</router-link>
                    </li>
                    <li class="breadcrumb-item">
                        <router-link to="/configuration/locale">{{ trans('configuration.configuration') }}</router-link>
                    </li>
                    <li class="breadcrumb-item active">{{ trans('locale.edit_locale') }}</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">
                            {{ trans('locale.edit_locale') }}
                        </h4>
                        <locale-form :id="id"></locale-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import localeForm from './Form';

    export default {
        components: {localeForm},
        data() {
            return {
                id: this.$route.params.id
            }
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
        }
    }
</script>
