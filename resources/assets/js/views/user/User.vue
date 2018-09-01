<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{ trans('user.users') }}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <router-link to="/home">{{ trans('general.home') }}</router-link>
                    </li>
                    <li class="breadcrumb-item active">{{ trans('user.users') }}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <transition name="fade">
                    <div class="card" v-if="showFilterPanel">
                        <div class="card-body">
                            <button class="btn btn-info btn-sm pull-right filter-opened" v-if="showFilterPanel"
                                    @click="showFilterPanel = !showFilterPanel"><i class="fas fa-filter"></i>
                                {{ trans('general.filter') }}
                            </button>
                            <h4 class="card-title">{{ trans('general.filter') }}</h4>
                            <div class="row">
                                <div class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('user.first_name') }}</label>
                                        <input @keydown.enter.prevent="getUsers()" class="form-control" name="first_name"
                                               v-model="filterUserForm.first_name">
                                    </div>
                                </div>
                                <div class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('user.last_name') }}</label>
                                        <input @keydown.enter.prevent="getUsers()" class="form-control" name="last_name" v-model="filterUserForm.last_name">
                                    </div>
                                </div>
                                <div class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('user.email') }}</label>
                                        <input @keydown.enter.prevent="getUsers()" class="form-control" name="email" v-model="filterUserForm.email">
                                    </div>
                                </div>
                                <div class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('role.role') }}</label>
                                        <select v-model="filterUserForm.role_id" class="custom-select col-12 form-control">
                                            <option value="">{{ trans('general.select_one') }}</option>
                                            <option v-for="role in roles" v-bind:value="role.id">
                                                {{ role.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('user.status') }}</label>
                                        <select v-model="filterUserForm.status" class="custom-select col-12 form-control">
                                            <option value="">{{ trans('general.select_one') }}</option>
                                            <option value="activated">{{ trans('user.status_activated') }}</option>
                                            <option value="pending_activation">
                                                {{ trans('user.status_pending_activation') }}
                                            </option>
                                            <option value="pending_approval">
                                                {{ trans('user.status_pending_approval') }}
                                            </option>
                                            <option value="banned">{{ trans('user.status_banned') }}</option>
                                            <option value="disapproved">{{ trans('user.status_disapproved') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <date-range-picker
                                                :start-date.sync="filterUserForm.created_at_start_date"
                                                :end-date.sync="filterUserForm.created_at_end_date"
                                               :label="trans('user.created_at')">
                                        </date-range-picker>
                                    </div>
                                </div>
                                <div class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('general.sort_by') }}</label>
                                        <select name="order" class="custom-select form-control" v-model="filterUserForm.sort_by">
                                            <option value="first_name">{{ trans('user.first_name') }}</option>
                                            <option value="last_name">{{ trans('user.last_name') }}</option>
                                            <option value="email">{{ trans('user.email') }}</option>
                                            <option value="status">{{ trans('user.status') }}</option>
                                            <option value="created_at">{{ trans('user.created_at') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-3">
                                    <div class="form-group">
                                        <label>{{ trans('general.order') }}</label>
                                        <select name="order" class="custom-select form-control" v-model="filterUserForm.order">
                                            <option value="asc">{{ trans('general.ascending') }}</option>
                                            <option value="desc">{{ trans('general.descending') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>
                <transition name="fade" v-if="hasPermission('create-user')">
                    <div class="card" v-if="showCreatePanel">
                        <div class="card-body">
                            <button class="btn btn-info btn-sm pull-right" v-if="showCreatePanel"
                                    @click="showCreatePanel = !showCreatePanel">{{ trans('general.hide') }}
                            </button>
                            <h4 class="card-title">{{ trans('user.add_new_user') }}</h4>
                            <form @submit.prevent="submit" @keydown="userForm.errors.clear($event.target.name)">
                                <div class="row">
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label>{{ trans('user.first_name') }}</label>
                                            <input class="form-control" type="text" value=""
                                                   v-model="userForm.first_name" name="first_name"
                                                   :placeholder="trans('user.first_name')">
                                            <show-error :form-name="userForm"
                                                        prop-name="first_name"></show-error>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label>{{ trans('user.last_name') }}</label>
                                            <input class="form-control" type="text" value=""
                                                   v-model="userForm.last_name" name="last_name"
                                                   :placeholder="trans('user.last_name')">
                                            <show-error :form-name="userForm"
                                                        prop-name="last_name"></show-error>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label>{{ trans('user.email') }}</label>
                                            <input class="form-control" type="text" value=""
                                                   v-model="userForm.email" name="email"
                                                   :placeholder="trans('user.email')">
                                            <show-error :form-name="userForm" prop-name="email"></show-error>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label>{{ trans('user.password') }}</label>
                                            <input class="form-control" type="password" value=""
                                                   v-model="userForm.password" name="password"
                                                   :placeholder="trans('user.password')">
                                            <show-error :form-name="userForm" prop-name="password"></show-error>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label>{{ trans('user.password_confirmation') }}</label>
                                            <input class="form-control" type="password" value=""
                                                   v-model="userForm.password_confirmation"
                                                   name="password_confirmation"
                                                   :placeholder="trans('user.password_confirmation')">
                                            <show-error :form-name="userForm"
                                                        prop-name="password_confirmation"></show-error>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label>{{ trans('user.address_line_1') }}</label>
                                            <input class="form-control" type="text" value=""
                                                   v-model="userForm.address_line_1" name="address_line_1"
                                                   :placeholder="trans('user.address_line_1')">
                                            <show-error :form-name="userForm"
                                                        prop-name="address_line_1"></show-error>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label>{{ trans('user.address_line_2') }}</label>
                                            <input class="form-control" type="text" value=""
                                                   v-model="userForm.address_line_2" name="address_line_2"
                                                   :placeholder="trans('user.address_line_2')">
                                            <show-error :form-name="userForm"
                                                        prop-name="address_line_2"></show-error>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label>{{ trans('user.city') }}</label>
                                            <input class="form-control" type="text" value=""
                                                   v-model="userForm.city" name="city"
                                                   :placeholder="trans('user.city')">
                                            <show-error :form-name="userForm" prop-name="city"></show-error>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label>{{ trans('user.state') }}</label>
                                            <input class="form-control" type="text" value=""
                                                   v-model="userForm.state" name="state"
                                                   :placeholder="trans('user.state')">
                                            <show-error :form-name="userForm" prop-name="state"></show-error>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label>{{ trans('user.zipcode') }}</label>
                                            <input class="form-control" type="text" value=""
                                                   v-model="userForm.zipcode" name="zipcode"
                                                   :placeholder="trans('user.zipcode')">
                                            <show-error :form-name="userForm" prop-name="zipcode"></show-error>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <div class="form-group">
                                            <label>{{ trans('user.country') }}</label>
                                            <select class="form-control custom-select" name="country_id"
                                                    v-model="userForm.country_id">
                                                <option value="">{{ trans('user.country') }}</option>
                                                <option v-for="country in countries" :value="country.value">
                                                    {{ country.text }}
                                                </option>
                                            </select>
                                            <show-error :form-name="userForm"
                                                        prop-name="country_id"></show-error>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-info waves-effect waves-light">
                                    {{ trans('general.add') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </transition>
                <div class="card">
                    <div class="card-body">
                        <button class="btn btn-info btn-sm pull-right" v-if="!showFilterPanel"
                                @click="showFilterPanel = !showFilterPanel">
                            <i class="fas fa-filter"></i>
                            {{ trans('general.filter') }}
                        </button>
                        <button class="btn btn-info btn-sm pull-right m-r-5"
                                v-if="users.total && !showCreatePanel && hasPermission('create-user')"
                                @click="showCreatePanel = !showCreatePanel">
                            <i class="fas fa-user-plus"></i>
                            {{ trans('user.create_user') }}
                        </button>
                        <h4 class="card-title">{{trans('user.user_list')}}</h4>
                        <h6 class="card-subtitle" v-if="users">
                            {{ trans('general.total_result_found',{'count' : users.total}) }}</h6>
                        <h6 class="card-subtitle" v-else>{{ trans('general.no_result_found') }}</h6>
                        <div class="table-responsive" v-if="hasPermission('list-user') && users.total">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ trans('user.name') }}</th>
                                    <th>{{ trans('user.email') }}</th>
                                    <th>{{ trans('role.role') }}</th>
                                    <th>{{ trans('user.status') }}</th>
                                    <th>{{ trans('user.created_at') }}</th>
                                    <th class="table-option">{{ trans('general.action') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="user in users.data">
                                    <td v-text="user.profile.first_name + ' ' + user.profile.last_name"></td>
                                    <td v-text="user.email"></td>
                                    <td>
                                        <ul style="list-style: none; padding:0;">
                                            <li v-for="role in user.roles">
                                                <span class="label label-info m-r-5">{{ role.name }}</span>
                                            </li>
                                        </ul>
                                    </td>
                                    <td>
                                        <span v-for="status in getUserStatus(user)"
                                              :class="['label','label-'+status.color,'m-r-5']">{{status.label}}</span>
                                    </td>
                                    <td>{{ user.created_at }}</td>
                                    <td class="table-option">
                                        <div class="btn-group">

                                            <router-link :to="`/user/${user.id}`" class="btn btn-info btn-sm"
                                                         v-tooltip="trans('user.view_user')"><i
                                                    class="fas fa-arrow-circle-right"></i></router-link>
                                            <button class="btn btn-danger btn-sm"
                                                    :key="user.id"
                                                    v-if="hasPermission('delete-user')"
                                                    v-confirm="{ok: confirmDelete(user)}"
                                                    v-tooltip="trans('user.delete_user')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <pagination-record
                                :page-length.sync="filterUserForm.page_length"
                                :records="users"
                                @updateRecords="getUsers">
                        </pagination-record>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import switches from 'vue-switches'
    import vSelect from 'vue-multiselect'
    import dateRangePicker from '../../components/DateRangePicker'

    export default {
        components: {switches, vSelect, dateRangePicker},
        data() {
            return {
                showCreatePanel: false,
                users: {
                    total: 0,
                    data: []
                },
                filterUserForm: {
                    first_name: '',
                    last_name: '',
                    email: '',
                    role_id: '',
                    status: '',
                    created_at_start_date: '',
                    created_at_end_date: '',
                    sort_by: 'created_at',
                    order: 'desc',
                    page_length: helper.getConfig('page_length')
                },
                userForm: new Form({
                    first_name: '',
                    last_name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    address_line_1: '',
                    address_line_2: '',
                    city: '',
                    state: '',
                    zipcode: '',
                    country_id: '',
                    send_email: ''
                }),
                countries: [],
                roles: [],
                showFilterPanel: false
            };
        },
        mounted() {
            if (!helper.hasPermission('list-user') && !helper.hasPermission('create-user')) {
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }

            this.fetchPreRequisites();
            this.getUsers();
        },
        methods: {
            hasPermission(permission) {
                return helper.hasPermission(permission);
            },
            fetchPreRequisites() {
                helper.showSpinner();
                axios.get('/api/user/pre-requisite')
                    .then(response => response.data)
                    .then(response => {
                        helper.hideSpinner();
                        this.countries = response.countries;
                        this.roles = response.roles;
                    })
                    .catch(error => {
                        helper.hideSpinner();
                        helper.showDataErrorMsg(error)
                    });
            },
            submit() {
                this.userForm.post('/api/user')
                    .then(response => {
                        toastr.success(response.message);
                        this.getUsers();
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getUsers(page) {
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterUserForm);
                axios.get('/api/user?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.users = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    })

            },
            confirmDelete(user) {
                return dialog => this.deleteUser(user);
            },
            deleteUser(user) {
                axios.delete('/api/user/' + user.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getUsers();
                    }).catch(error => {
                    helper.showDataErrorMsg(error);
                });
            },
            getUserStatus(user) {
                return helper.getUserStatus(user);
            },
            getAvatar(user) {
                return helper.getAvatar(user);
            }
        },
        watch: {
            'filterUserForm.role_id': function (newVal, oldVal) {
                this.getUsers();
            },
            'filterUserForm.status': function (newVal, oldVal) {
                this.getUsers();
            },
            'filterUserForm.category_id': function (newVal, oldVal) {
                this.getUsers();
            },
            'filterUserForm.created_at_start_date': function (newVal, oldVal) {
                this.getUsers();
            },
            'filterUserForm.created_at_end_date': function (newVal, oldVal) {
                this.getUsers();
            },
            'filterUserForm.sort_by': function (newVal, oldVal) {
                this.getUsers();
            },
            'filterUserForm.order': function (newVal, oldVal) {
                this.getUsers();
            },
            'filterUserForm.page_length': function (newVal, oldVal) {
                this.getUsers();
            }
        }
    }
</script>
