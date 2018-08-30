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
                    <li class="breadcrumb-item active">{{ trans('permission.permission') }}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <configuration-sidebar menu="permission"></configuration-sidebar>
                            <div class="col-10 col-lg-10 col-md-10">
                                <div class="row">
                                    <div class="col-12 col-sm-4 col-md-4">
                                        <h4 class="card-title">{{ trans('permission.add_new_permission') }}</h4>
                                        <permission-form @completed="getPermissions"></permission-form>
                                    </div>
                                    <div class="col-12 col-sm-8 col-md-8">
                                        <h4 class="card-title">{{ trans('permission.permission_list') }}</h4>
                                        <h6 class="card-subtitle" v-if="permissions">
                                            {{ trans('general.total_result_found', {'count' : permissions.total}) }}</h6>
                                        <h6 class="card-subtitle" v-else>{{ trans('general.no_result_found') }}</h6>
                                        <router-link to="/configuration/permission/assign" class="btn btn-info">
                                            {{ trans('permission.assign_permission') }}
                                        </router-link>
                                        <div class="table-responsive" v-if="permissions.total">
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th>{{ trans('permission.name') }}</th>
                                                    <th class="table-option">{{ trans('general.action') }}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="permission in permissions.data">
                                                    <td v-text="toWord(permission.name)"></td>
                                                    <td class="table-option">
                                                        <div class="btn-group">
                                                            <button class="btn btn-danger btn-sm"
                                                                    :key="permission.id"
                                                                    v-confirm="{ok: confirmDelete(permission)}"
                                                                    v-tooltip="trans('permission.delete_permission')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <pagination-record
                                                :page-length.sync="filterPermissionForm.page_length"
                                                :records="permissions"
                                                @updateRecords="getPermissions"
                                                @change.native="getPermissions">
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
    import permissionForm from './Form'

    export default {
        components: {configurationSidebar, permissionForm},
        data() {
            return {
                permissions: {
                    total: 0,
                    data: []
                },
                filterPermissionForm: {
                    page_length: helper.getConfig('page_length')
                }
            };
        },
        mounted() {
            if (!helper.hasPermission('access-configuration')) {
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            this.getPermissions();
        },
        methods: {
            getPermissions(page) {
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterPermissionForm);
                helper.showSpinner();
                axios.get('/api/permission?page=' + page + url)
                    .then(response => response.data)
                    .then(response => {
                        this.permissions = response;
                        helper.hideSpinner();
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                        helper.hideSpinner();
                    });
            },
            confirmDelete(permission) {
                return dialog => this.deletePermission(permission);
            },
            deletePermission(permission) {
                axios.delete('/api/permission/' + permission.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getPermissions();
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            toWord(str) {
                return helper.toWord(str);
            }
        }
    }
</script>
