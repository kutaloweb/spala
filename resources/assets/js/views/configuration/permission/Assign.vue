<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{ trans('permission.assign_permission') }}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <router-link to="/home">{{ trans('general.home') }}</router-link>
                    </li>
                    <li class="breadcrumb-item">
                        <router-link to="/configuration/permission">{{ trans('permission.permission') }}</router-link>
                    </li>
                    <li class="breadcrumb-item active">{{ trans('permission.assign_permission') }}</li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <button type="button" class="btn btn-sm btn-info waves-effect waves-light pull-right"
                                @click="savePermission">
                            {{ trans('general.save') }}
                        </button>
                        <router-link to="/configuration/permission" class="btn btn-sm btn-danger pull-right m-r-10">
                            {{ trans('general.back') }}
                        </router-link>
                        <h4 class="card-title">{{ trans('permission.assign_permission') }}</h4>
                        <div class="table m-b-20">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>{{ trans('permission.permission') }}</th>
                                    <th v-for="role in roles" class="text-center">{{ role.name }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="permission in permissions">
                                    <td>{{ toWord(permission.name) }}</td>
                                    <td v-for="role in roles" class="text-center">
                                        <switches v-model="assignPermissionForm.data[role.id][permission.id]"
                                                  theme="bootstrap" color="success">
                                        </switches>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-sm btn-info waves-effect waves-light pull-right"
                                @click="savePermission">
                            {{ trans('general.save') }}
                        </button>
                        <router-link to="/configuration/permission" class="btn btn-sm btn-danger pull-right m-r-10">
                            {{ trans('general.back') }}
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import switches from 'vue-switches'

    export default {
        components: {switches},
        data() {
            return {
                roles: [],
                permissions: [],
                assignPermissionForm: new Form({
                    data: {}
                })
            }
        },
        mounted() {
            if (!helper.hasPermission('access-configuration')) {
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            helper.showSpinner();
            axios.get('/api/permission/assign/pre-requisite')
                .then(response => response.data)
                .then(response => {
                    this.permissions = response.permissions;
                    this.roles = response.roles;
                    this.assignPermissionForm.data = response.data;
                    helper.hideSpinner();
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                    helper.hideSpinner();
                });
        },
        methods: {
            savePermission() {
                axios.post('/api/permission/assign', {
                    data: this.assignPermissionForm.data
                })
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
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
