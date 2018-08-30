<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{ trans('configuration.configuration') }}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <router-link to="/home">{{ trans('general.home') }}</router-link>
                    </li>
                    <li class="breadcrumb-item active">{{ trans('category.categories') }}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-lg-12 col-md-12">
                                <div class="row">
                                    <div class="col-12 col-sm-4 col-md-4">
                                        <h4 class="card-title">{{ trans('category.add_new_category') }}</h4>
                                        <category-form @completed="getCategories"></category-form>

                                    </div>
                                    <div class="col-12 col-sm-8 col-md-8">
                                        <h4 class="card-title">{{ trans('category.category_list') }}</h4>
                                        <h6 class="card-subtitle" v-if="categories">
                                            {{ trans('general.total_result_found',{'count' : categories.total}) }}</h6>
                                        <h6 class="card-subtitle" v-else>{{ trans('general.no_result_found') }}</h6>
                                        <div class="table" v-if="categories.total">
                                            <table class="table">
                                                <thead>
                                                <tr>
                                                    <th>{{ trans('category.name') }}</th>
                                                    <th>{{ trans('category.slug') }}</th>
                                                    <th class="table-option">Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="category in categories.data">
                                                    <td v-text="category.name"></td>
                                                    <td v-text="category.slug"></td>
                                                    <td class="table-option">
                                                        <div class="btn-group">
                                                            <button class="btn btn-danger btn-sm" :key="category.id"
                                                                    v-confirm="{ok: confirmDelete(category)}"
                                                                    v-tooltip="trans('category.delete_category')">
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
                                                :records="categories"
                                                @updateRecords="getCategories"
                                                @change.native="getCategories">
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
    import categoryForm from './Form'

    export default {
        components: {categoryForm},
        data() {
            return {
                categories: {
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
            this.getCategories();
        },
        methods: {
            getCategories(page) {
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterRoleForm);
                helper.showSpinner();
                axios.get('/api/category?page=' + page + url)
                    .then(response => response.data)
                    .then(response => {
                        this.categories = response;
                        helper.hideSpinner();
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                        helper.hideSpinner();
                    });
            },
            confirmDelete(category) {
                return dialog => this.deleteRole(category);
            },
            deleteRole(category) {
                axios.delete('/api/category/' + category.id)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.message);
                        this.getCategories();
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            }
        }
    }
</script>
