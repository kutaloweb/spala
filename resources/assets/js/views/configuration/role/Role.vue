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
                    <li class="breadcrumb-item active">{{ trans('role.role') }}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <configuration-sidebar menu="role"></configuration-sidebar>
                            <div class="col-10 col-lg-10 col-md-10">
                                <div class="row">
                                    <div class="col-12 col-sm-4 col-md-4">
                                        <h4 class="card-title">{{ trans('role.add_new_role') }}</h4>
                                        <role-form @completed="getRoles"></role-form>

                                    </div>
                                    <div class="col-12 col-sm-8 col-md-8">
                                        <h4 class="card-title">{{ trans('role.role_list') }}</h4>
                                        <h6 class="card-subtitle" v-if="roles">
                                            {{ trans('general.total_result_found',{'count' : roles.total}) }}</h6>
                                        <h6 class="card-subtitle" v-else>{{ trans('general.no_result_found') }}</h6>
                                        <div class="table" v-if="roles.total">
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th>{{ trans('role.name') }}</th>
                                                    <th class="table-option">Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="role in roles.data">
                                                    <td v-text="role.name"></td>
                                                    <td class="table-option">
                                                        <div class="btn-group">
                                                            <button class="btn btn-danger btn-sm" :key="role.id"
                                                                    v-confirm="{ok: confirmDelete(role)}"
                                                                    v-tooltip="trans('role.delete_role')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <pagination-record
                                                :page-length.sync="filterRoleForm.page_length"
                                                :records="roles"
                                                @updateRecords="getRoles"
                                                @change.native="getRoles">
                                        </pagination-record>
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
    import roleForm from './Form'

    export default {
        components: {configurationSidebar, roleForm},
        data() {
            return {
                roles: {
                    total: 0,
                    data: []
                },
                filterRoleForm: {
                    page_length: helper.getConfig('page_length')
                }
            };
        },
        mounted() {
            if (!helper.hasPermission('access-configuration')) {
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            this.getRoles();
        },
        methods: {
            getRoles(page) {
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterRoleForm);
                helper.showSpinner();
                axios.get('/api/role?page=' + page + url)
                    .then(response => response.data)
                    .then(response => {
                        this.roles = response;
                        helper.hideSpinner();
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                        helper.hideSpinner();
                    });
            },
            confirmDelete(role) {
                return dialog => this.deleteRole(role);
            },
            deleteRole(role) {
                axios.delete('/api/role/' + role.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getRoles();
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            }
        }
    }
</script>
