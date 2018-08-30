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
                    <li class="breadcrumb-item active">{{ trans('user.contact') }}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-lg-8 col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <user-sidebar menu="contact" :id="id"></user-sidebar>
                            <div class="col-9 col-lg-9 col-md-9">
                                <h4 class="card-title">{{ trans('user.contact') }}</h4>
                                <form @submit.prevent="submit" @keydown="userForm.errors.clear($event.target.name)">
                                    <div class="col-12 col-md-12">
                                        <div class="row">
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label>{{ trans('user.phone') }}</label>
                                                    <input class="form-control" type="text" value=""
                                                           v-model="userForm.phone" name="phone"
                                                           :placeholder="trans('user.phone')">
                                                    <show-error :form-name="userForm" prop-name="phone"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label>{{ trans('user.address_line_1') }}</label>
                                                    <input class="form-control" type="text" value=""
                                                           v-model="userForm.address_line_1" name="address_line_1"
                                                           :placeholder="trans('user.address_line_1')">
                                                    <show-error :form-name="userForm" prop-name="address_line_1"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label>{{ trans('user.address_line_2') }}</label>
                                                    <input class="form-control" type="text" value=""
                                                           v-model="userForm.address_line_2" name="address_line_2"
                                                           :placeholder="trans('user.address_line_2')">
                                                    <show-error :form-name="userForm" prop-name="address_line_2"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label>{{ trans('user.city') }}</label>
                                                    <input class="form-control" type="text" value=""
                                                           v-model="userForm.city" name="city"
                                                           :placeholder="trans('user.city')">
                                                    <show-error :form-name="userForm" prop-name="city"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label>{{ trans('user.state') }}</label>
                                                    <input class="form-control" type="text" value=""
                                                           v-model="userForm.state" name="state"
                                                           :placeholder="trans('user.state')">
                                                    <show-error :form-name="userForm" prop-name="state"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label>{{ trans('user.zipcode') }}</label>
                                                    <input class="form-control" type="text" value=""
                                                           v-model="userForm.zipcode" name="zipcode"
                                                           :placeholder="trans('user.zipcode')">
                                                    <show-error :form-name="userForm" prop-name="zipcode"></show-error>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <label>{{ trans('user.country') }}</label>
                                                    <select class="form-control custom-select" name="country_id" v-model="userForm.country_id">
                                                        <option value="">{{trans('user.country')}}</option>
                                                        <option v-for="country in countries" v-bind:value="country.value">
                                                            {{country.text}}
                                                        </option>
                                                    </select>
                                                    <show-error :form-name="userForm" prop-name="country_id"></show-error>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-info waves-effect waves-light m-t-10 pull-right">
                                        {{trans('general.save')}}
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
                userForm: new Form({
                    phone: '',
                    address_line_1: '',
                    address_line_2: '',
                    city: '',
                    state: '',
                    zipcode: '',
                    country_id: ''
                }, false),
                countries: [],
                user: ''
            };
        },
        mounted() {
            if (!helper.hasPermission('edit-user')) {
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            axios.get('/api/fetch/lists?lists=country')
                .then(response => response.data)
                .then(response => {
                    this.countries = response.data.country;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });
            helper.showSpinner();
            axios.get('/api/user/' + this.id)
                .then(response => response.data)
                .then(response => {
                    this.user = response.user;
                    this.userForm.phone = response.user.profile.phone;
                    this.userForm.address_line_1 = response.user.profile.address_line_1;
                    this.userForm.address_line_2 = response.user.profile.address_line_2;
                    this.userForm.city = response.user.profile.city;
                    this.userForm.state = response.user.profile.state;
                    this.userForm.zipcode = response.user.profile.zipcode;
                    this.userForm.country_id = response.user.profile.country_id || '';
                    helper.hideSpinner();
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                    this.$router.push('/user');
                    helper.hideSpinner();
                })
        },
        methods: {
            submit() {
                this.userForm.patch('/api/user/' + this.id + '/contact')
                    .then(response => {
                        toastr.success(response.message);
                        this.user.profile.phone = this.userForm.phone;
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            }
        }
    }
</script>
