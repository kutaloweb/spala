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
                    <li class="breadcrumb-item active">{{ trans('post.draft') }}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <post-sidebar menu="draft" :statistics="statistics"></post-sidebar>
                            <div class="col-10 col-lg-10 col-md-10">
                                <h4 class="card-title">{{ trans('post.draft') }}</h4>
                                <h6 class="card-subtitle" v-if="posts">
                                    {{ trans('general.total_result_found',{'count': posts.total}) }}
                                </h6>
                                <h6 class="card-subtitle" v-else>{{ trans('general.no_result_found') }}</h6>
                                <div class="table-responsive">
                                    <table class="table" v-if="posts.total">
                                        <thead>
                                        <tr>
                                            <th>{{ trans('post.user') }}</th>
                                            <th>{{ trans('post.title') }}</th>
                                            <th>{{ trans('post.drafted_at') }}</th>
                                            <th class="table-option">{{ trans('general.action') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="draft in posts.data">
                                            <td v-text="draft.user.name"></td>
                                            <td v-text="draft.title"></td>
                                            <td>{{ draft.updated_at }}</td>
                                            <td class="table-option">
                                                <div class="btn-group">
                                                    <router-link :to="`/post/${draft.slug}/edit`"
                                                                 class="btn btn-info btn-sm"
                                                                 v-tooltip="trans('post.edit_draft')">
                                                        <i class="fas fa-edit"></i>
                                                    </router-link>
                                                    <button class="btn btn-danger btn-sm"
                                                            :key="draft.id"
                                                            v-confirm="{ok: confirmDelete(draft)}"
                                                            v-tooltip="trans('post.delete_draft')">
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

    export default {
        components: {postSidebar},
        data() {
            return {
                posts: {
                    total: 0,
                    data: []
                },
                filterPostForm: {
                    page_length: helper.getConfig('page_length')
                },
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

            if (!helper.featureAvailable('post')) {
                helper.featureNotAvailableMsg();
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
                axios.get('/api/post/draft?page=' + page + url)
                    .then(response => response.data)
                    .then(response => this.posts = response)
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            confirmDelete(draft) {
                return dialog => this.deleteDraft(draft);
            },
            deleteDraft(draft) {
                axios.delete('/api/post/' + draft.slug)
                    .then(response => response.data)
                    .then(response => {
                        toastr.success(response.post);
                        this.statistics.draft -= 1;
                        this.getPosts();
                    }).catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            }
        }
    }
</script>
