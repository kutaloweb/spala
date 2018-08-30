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
                    <li class="breadcrumb-item active">{{ trans('user.basic') }}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-lg-8 col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <user-sidebar menu="basic" :id="id"></user-sidebar>
                            <div class="col-9 col-lg-9 col-md-9">
                                <h4 class="card-title">{{ trans('user.basic') }}</h4>
                                <form @submit.prevent="submit" @keydown="userForm.errors.clear($event.target.name)">
                                    <div class="row">
                                        <div class="col-12 col-md-12">
                                            <div class="row">
                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label>{{ trans('user.first_name') }}</label>
                                                        <input class="form-control" type="text" value=""
                                                               v-model="userForm.first_name" name="first_name"
                                                               :placeholder="trans('user.first_name')">
                                                        <show-error :form-name="userForm"
                                                                    prop-name="first_name"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label>{{ trans('user.last_name') }}</label>
                                                        <input class="form-control" type="text" value=""
                                                               v-model="userForm.last_name" name="last_name"
                                                               :placeholder="trans('user.last_name')">
                                                        <show-error :form-name="userForm"
                                                                    prop-name="last_name"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label>{{ trans('user.email') }}</label>
                                                        <input class="form-control" type="text" value=""
                                                               v-model="userForm.email" name="email"
                                                               :placeholder="trans('user.email')" readonly>
                                                        <show-error :form-name="userForm"
                                                                    prop-name="email"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label>{{ trans('user.date_of_birth') }}</label>
                                                        <datepicker :language="locale"
                                                                    :monday-first="true"
                                                                    v-model="userForm.date_of_birth"
                                                                    :bootstrapStyling="true"
                                                                    name="date_of_birth"
                                                                    @selected="userForm.errors.clear('date_of_birth')">
                                                        </datepicker>
                                                        <show-error :form-name="userForm"
                                                                    prop-name="date_of_birth"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <div class="form-group">
                                                        <label>{{ trans('user.gender') }}</label>
                                                        <div class="radio radio-info" v-for="gender in genders">
                                                            <input type="radio" :value="gender.id" :id="gender.id"
                                                                   v-model="userForm.gender"
                                                                   :checked="userForm.gender == gender.id"
                                                                   name="gender">
                                                            <label :for="gender.id">
                                                                {{ trans('list.' + gender.id) }}
                                                            </label>
                                                        </div>
                                                        <show-error :form-name="userForm"
                                                                    prop-name="gender"></show-error>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <template v-if="!userHasRole(user, 'admin')">
                                                        <div class="form-group">
                                                            <label>{{ trans('role.role') }}</label>
                                                            <v-select label="name"
                                                                      track-by="id"
                                                                      v-model="selected_roles"
                                                                      name="role_id"
                                                                      id="role_id"
                                                                      :options="roles"
                                                                      :placeholder="trans('role.select_role')"
                                                                      @select="onRoleSelect"
                                                                      :multiple="true"
                                                                      :close-on-select="false"
                                                                      :clear-on-select="false"
                                                                      :hide-selected="true"
                                                                      @remove="onRoleRemove"
                                                                      :selected="selected_roles"
                                                                      select-label=""
                                                                      :searchable="false">
                                                            </v-select>
                                                            <show-error :form-name="userForm"
                                                                        prop-name="role_id"></show-error>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-info pull-right">
                                        {{ trans('general.save') }}
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
    import datepicker from 'vuejs-datepicker'
    import vSelect from 'vue-multiselect'

    export default {
        components: {userSidebar, datepicker, vSelect, userSummary},
        data() {
            return {
                id: this.$route.params.id,
                userForm: new Form({
                    first_name: '',
                    last_name: '',
                    email: '',
                    gender: '',
                    date_of_birth: '',
                    role_id: []
                }, false),
                user: '',
                genders: [],
                roles: [],
                selected_roles: '',
                locale: helper.getConfig('locale')
            };
        },
        mounted() {
            if (!helper.hasPermission('edit-user')) {
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            this.fetchUserData();
            this.fetchPreRequisites();
        },
        methods: {
            fetchUserData() {
                helper.showSpinner();
                axios.get('/api/user/' + this.id)
                    .then(response => response.data)
                    .then(response => {
                        this.user = response.user;
                        this.userForm.first_name = response.user.profile.first_name;
                        this.userForm.last_name = response.user.profile.last_name;
                        this.userForm.email = response.user.email;
                        this.userForm.gender = response.user.profile.gender;
                        this.userForm.date_of_birth = response.user.profile.date_of_birth;
                        this.userForm.role_id = response.roles;
                        this.selected_roles = response.selected_roles;
                        helper.hideSpinner();
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                        this.$router.push('/user');
                        helper.hideSpinner();
                    })
            },
            fetchPreRequisites() {
                axios.get('/api/user/pre-requisite')
                    .then(response => response.data)
                    .then(response => {
                        this.countries = response.countries;
                        this.genders = response.genders;
                        this.roles = response.roles;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error)
                    });
            },
            onRoleSelect(selectedOption) {
                this.userForm.errors.clear('role_id');
                this.userForm.role_id.push(selectedOption.id);
            },
            onRoleRemove(removedOption) {
                this.userForm.role_id.splice(this.userForm.role_id.indexOf(removedOption.id), 1);
            },
            submit() {
                this.userForm.date_of_birth = (this.userForm.date_of_birth) ? moment(this.userForm.date_of_birth).format('YYYY-MM-DD') : null;
                this.userForm.patch('/api/user/' + this.id)
                    .then(response => {
                        this.fetchUserData();
                        toastr.success(response.message);
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            userHasRole(user, role) {
                return helper.userHasRole(user, role);
            }
        }
    }
</script>
