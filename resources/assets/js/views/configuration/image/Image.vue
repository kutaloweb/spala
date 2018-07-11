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
                    <li class="breadcrumb-item active">{{ trans('general.image') }}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <configuration-sidebar menu="image"></configuration-sidebar>
                            <div class="col-10 col-lg-10 col-md-10">
                                <div class="row">
                                    <div class="col-12 col-lg-6 col-md-6">
                                        <h4 class="card-title text-center">
                                            {{ trans('general.background') }}
                                        </h4>
                                        <upload-image
                                                id="background"
                                                upload-path="/configuration/image/background"
                                                remove-path="/configuration/image/background/remove"
                                                :image-source="image.background"
                                                @uploaded="updateBackgroundImage"
                                                @removed="updateBackgroundImage">
                                        </upload-image>
                                    </div>
                                    <div class="col-12 col-lg-6 col-md-6">
                                        <h4 class="card-title text-center">
                                            {{ trans('general.logo') }}
                                        </h4>
                                        <upload-image
                                                id="logo"
                                                upload-path="/configuration/image/logo"
                                                remove-path="/configuration/image/logo/remove"
                                                :image-source="image.logo"
                                                @uploaded="updateLogo"
                                                @removed="updateLogo">
                                        </upload-image>
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
    import uploadImage from '../../../components/UploadImage'
    import {EventBus} from '../../../event-bus'

    export default {
        components: {configurationSidebar, uploadImage},
        data() {
            return {
                image: {
                    logo: '',
                    background: ''
                }
            }
        },
        mounted() {
            if (!helper.hasPermission('access-configuration')) {
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            this.image.logo = helper.getConfig('logo');
            this.image.background = helper.getConfig('background');
        },
        methods: {
            updateLogo(val) {
                this.$store.dispatch('setConfig', {
                    logo: val
                });
                EventBus.$emit('config::set');
            },
            updateBackgroundImage(val) {
                this.$store.dispatch('setConfig', {
                    background: val
                });
            }
        }
    }
</script>
