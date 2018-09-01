<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{ trans('post.posts') }}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <router-link to="/home">{{ trans('general.home') }}</router-link>
                    </li>
                    <li class="breadcrumb-item">
                        <router-link to="/post">{{ trans('post.posts') }}</router-link>
                    </li>
                    <li class="breadcrumb-item active">{{ trans('post.published_box') }}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <post-sidebar menu="published" :statistics="statistics"></post-sidebar>
                            <div class="col-10 col-lg-10 col-md-10">
                                <transition name="fade">
                                    <div v-if="showFilterPanel">
                                        <button class="btn btn-info btn-sm pull-right filter-opened" v-if="showFilterPanel"
                                                @click="showFilterPanel = !showFilterPanel">
                                            <i class="fas fa-filter"></i>
                                            {{ trans('general.filter') }}
                                        </button>
                                        <h4 class="card-title">{{ trans('general.filter') }}</h4>
                                        <div class="row">
                                            <div class="col-12 col-md-3">
                                                <div class="form-group">
                                                    <label>{{ trans('post.search_query') }}</label>
                                                    <input @keydown.enter.prevent="getPosts()" class="form-control" name="search_query"
                                                           v-model="filterPostForm.search_query">
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <div class="form-group">
                                                    <label>{{ trans('category.category') }}</label>
                                                    <select v-model="filterPostForm.category_id" class="custom-select col-12 form-control">
                                                        <option value="">{{ trans('general.select_one') }}</option>
                                                        <option v-for="category in categories" v-bind:value="category.id">
                                                            {{ category.name }}
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <div class="form-group">
                                                    <date-range-picker
                                                            :start-date.sync="filterPostForm.created_at_start_date"
                                                            :end-date.sync="filterPostForm.created_at_end_date">
                                                    </date-range-picker>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </transition>
                                <div v-if="!showFilterPanel">
                                    <button class="btn btn-info btn-sm pull-right"
                                            @click="showFilterPanel = !showFilterPanel">
                                        <i class="fas fa-filter"></i>
                                        {{ trans('general.filter') }}
                                    </button>
                                </div>
                                <h4 class="card-title">{{ trans('post.published_box') }}</h4>
                                <h6 class="card-subtitle" v-if="posts">
                                    {{ trans('general.total_result_found',{'count': posts.total}) }}</h6>
                                <h6 class="card-subtitle" v-else>{{ trans('general.no_result_found') }}</h6>
                                <div class="table-responsive">
                                    <table class="table" v-if="posts.total">
                                        <thead>
                                        <tr>
                                            <th>{{ trans('post.user') }}</th>
                                            <th>{{ trans('category.category') }}</th>
                                            <th>{{ trans('post.title') }}</th>
                                            <th>{{ trans('post.published_at') }}</th>
                                            <th class="table-option">{{ trans('general.action') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="published in posts.data">
                                            <td v-text="published.user.name"></td>
                                            <td v-text="published.category.name"></td>
                                            <td v-text="published.title"></td>
                                            <td>{{ published.created_at }}</td>
                                            <td class="table-option">
                                                <div class="btn-group">
                                                    <router-link :to="`/post/${published.slug}/cover`"
                                                                 class="btn btn-success btn-sm"
                                                                 v-tooltip="trans('post.upload_cover')">
                                                        <i class="far fa-images"></i>
                                                    </router-link>
                                                    <router-link :to="`/post/${published.slug}/edit`"
                                                                 class="btn btn-info btn-sm"
                                                                 v-tooltip="trans('post.edit_published')">
                                                        <i class="fas fa-edit"></i>
                                                    </router-link>
                                                    <button class="btn btn-danger btn-sm"
                                                            :key="published.id"
                                                            v-confirm="{ok: confirmDelete(published)}"
                                                            v-tooltip="trans('post.delete_published')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <pagination-record :page-length.sync="filterPostForm.page_length" :records="posts"
                                                   @updateRecords="getPosts"
                                                   @change.native="getPosts"></pagination-record>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import postSidebar from './PostSidebar'
    import dateRangePicker from '../../components/DateRangePicker'

    export default {
        components: {postSidebar, dateRangePicker},
        data() {
            return {
                posts: {
                    total: 0,
                    data: []
                },
                filterPostForm: {
                    search_query: '',
                    category_id: '',
                    created_at_start_date: '',
                    created_at_end_date: '',
                    page_length: helper.getConfig('page_length')
                },
                showFilterPanel: false,
                categories: [],
                statistics: {
                    published: 0,
                    draft: 0
                }
            };
        },
        mounted() {
            if (!helper.hasPermission('access-post')) {
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            this.getPosts();
            this.getStatistics();
        },
        methods: {
            getStatistics() {
                axios.post('/api/post/statistics')
                    .then(response => response.data)
                    .then(response => {
                        this.statistics.published = response.published;
                        this.statistics.draft = response.draft;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getPosts(page) {
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterPostForm);
                helper.showSpinner();
                axios.get('/api/post/published?page=' + page + url)
                    .then(response => response.data)
                    .then(response => {
                        this.posts = response.posts;
                        this.categories = response.categories;
                        helper.hideSpinner();
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                        helper.hideSpinner();
                    });
            },
            confirmDelete(published) {
                return dialog => this.deletePost(published);
            },
            deletePost(published) {
                axios.delete('/api/post/' + published.slug)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.post);
                        this.statistics.published -= 1;
                        this.getPosts();
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            }
        },
        watch: {
            'filterPostForm.category_id': function (newVal, oldVal) {
                this.getPosts();
            },
            'filterPostForm.created_at_start_date': function (newVal, oldVal) {
                this.getPosts();
            },
            'filterPostForm.created_at_end_date': function (newVal, oldVal) {
                this.getPosts();
            },
            'filterPostForm.page_length': function (newVal, oldVal) {
                this.getPosts();
            }
        }
    }
</script>
