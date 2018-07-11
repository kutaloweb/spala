<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{ trans('auth.change_password') }}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <router-link to="/home">{{ trans('general.home') }}</router-link>
                    </li>
                    <li class="breadcrumb-item active">{{ trans('auth.change_password') }}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form @submit.prevent="changePassword" @keydown="passwordForm.errors.clear($event.target.name)">
                            <h4 class="card-title">{{ trans('auth.change_password') }}</h4>
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label>{{ trans('auth.current_password') }}</label>
                                        <input class="form-control" type="password" value=""
                                               v-model="passwordForm.current_password" name="current_password"
                                               :placeholder="trans('auth.current_password')">
                                        <show-error :form-name="passwordForm" prop-name="current_password"></show-error>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label>{{ trans('auth.new_password') }}</label>
                                        <div>
                                            <input type="password" name="new_password" class="form-control"
                                                   :placeholder="trans('auth.new_password')"
                                                   v-model="passwordForm.new_password">
                                        </div>
                                        <show-error :form-name="passwordForm" prop-name="new_password"></show-error>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <div class="form-group">
                                        <label>{{ trans('auth.new_password_confirmation') }}</label>
                                        <input class="form-control" type="password" value=""
                                               v-model="passwordForm.new_password_confirmation"
                                               name="new_password_confirmation"
                                               :placeholder="trans('auth.new_password_confirmation')">
                                        <show-error :form-name="passwordForm"
                                                    prop-name="new_password_confirmation"></show-error>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-info waves-effect waves-light m-t-10">
                                {{ trans('auth.change_password') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    export default {
        data() {
            return {
                passwordForm: new Form({
                    current_password: '',
                    new_password: '',
                    new_password_confirmation: ''
                })
            };
        },
        mounted() {

        },
        methods: {
            changePassword() {
                this.passwordForm.post('/api/change-password')
                    .then(response => {
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
