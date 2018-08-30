<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{ trans('user.user_detail') }}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <router-link to="/home">{{ trans('general.home') }}</router-link>
                    </li>
                    <li class="breadcrumb-item">
                        <router-link to="/user">{{ trans('user.user') }}</router-link>
                    </li>
                    <li class="breadcrumb-item active">{{ trans('user.password') }}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-lg-8 col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <user-sidebar menu="password" :id="id"></user-sidebar>
                            <div class="col-9 col-lg-9 col-md-9">
                                <h4 class="card-title">{{ trans('user.password') }}</h4>
                                <form @submit.prevent="submit" @keydown="passwordForm.errors.clear($event.target.name)">
                                    <h4 class="card-title">{{ trans('auth.change_password') }}</h4>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>{{ trans('auth.new_password') }}</label>
                                                <div>
                                                    <input type="password" name="new_password" class="form-control"
                                                           :placeholder="trans('auth.new_password')"
                                                           v-model="passwordForm.new_password">
                                                </div>
                                                <show-error :form-name="passwordForm"
                                                            prop-name="new_password"></show-error>
                                            </div>
                                        </div>
                                        <div class="col-12">
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
                                    <button type="submit" class="btn btn-info waves-effect waves-light m-t-10 pull-right">
                                        {{ trans('auth.change_password') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <user-summary :user="user"></user-summary>
        </div>
    </div>
</template>


<script>
    import userSidebar from './UserSidebar'
    import userSummary from './Summary'

    export default {
        components: {userSidebar, userSummary},
        data() {
            return {
                id: this.$route.params.id,
                passwordForm: new Form({
                    new_password: '',
                    new_password_confirmation: ''
                }),
                user: ''
            };
        },
        mounted() {
            if (this.id == helper.getAuthUser('id'))
                this.$router.push('/user');

            if (!helper.hasPermission('force-reset-user-password')) {
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            helper.showSpinner();
            axios.get('/api/user/' + this.id)
                .then(response => response.data)
                .then(response => {
                    this.user = response.user;
                    helper.hideSpinner();
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                    helper.hideSpinner();
                });
        },
        methods: {
            submit() {
                this.passwordForm.patch('/api/user/' + this.id + '/force-reset-password')
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
