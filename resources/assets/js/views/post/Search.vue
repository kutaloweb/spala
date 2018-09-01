<template>
    <section id="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 m-t-20 m-b-20">
                    <transition name="fade">
                        <div class="card" v-if="showFilterPanel">
                            <div class="card-body">
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
                        </div>
                    </transition>
                    <div class="card" v-if="!showFilterPanel">
                        <div class="card-body">
                            <button class="btn btn-info btn-sm pull-right"
                                    @click="showFilterPanel = !showFilterPanel">
                                <i class="fas fa-filter"></i>
                                {{ trans('general.filter') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="splitted.length !== 0">
                <div class="row" v-for="items in splitted">
                    <div class="col-12 m-t-20 m-b-20">
                        <div v-if="items.length === 3" class="card-deck">
                            <post-card v-for="post in items" :post="post" :key="post.id"></post-card>
                        </div>
                        <div v-if="items.length === 2" class="card-deck">
                            <post-card v-for="post in items" :post="post" :key="post.id"></post-card>
                            <div class="card card-hidden"></div>
                        </div>
                        <div v-if="items.length === 1" class="card-deck">
                            <post-card v-for="post in items" :post="post" :key="post.id"></post-card>
                            <div class="card card-hidden"></div>
                            <div class="card card-hidden"></div>
                        </div>
                    </div>
                </div>
                <pagination-record
                        :page-length.sync="filterPostForm.page_length"
                        :records="posts"
                        :show-page-length="false"
                        @updateRecords="getPosts"
                        @change.native="getPosts">
                </pagination-record>
            </div>
            <div v-else>
                <div class="row">
                    <div class="col-12 m-t-20 m-b-20">
                        <h1 class="text-center">{{ trans('general.no_result_found') }}</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    import postCard from './PostCard'
    import dateRangePicker from '../../components/DateRangePicker'

    export default {
        data() {
            return {
                posts: {
                    total: 0,
                    data: []
                },
                splitted: [],
                filterPostForm: {
                    search_query: '',
                    category_id: '',
                    created_at_start_date: '',
                    created_at_end_date: '',
                    page_length: 9
                },
                showFilterPanel: true,
                categories: [],
            }
        },
        components: {
            postCard, dateRangePicker
        },
        mounted() {
            this.filterPostForm.search_query = helper.getSearchQuery();
            this.filterPostForm.category_id = helper.getSearchCategory();
            this.getPosts();
        },
        beforeDestroy () {
            this.$store.dispatch('setSearchQuery', '');
            this.$store.dispatch('setSearchCategory', '');
        },
        methods: {
            getPosts(page) {
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterPostForm);
                helper.showSpinner();
                axios.get('/api/posts?page=' + page + url)
                    .then(response => response.data)
                    .then(response => {
                        this.posts = response.posts;
                        this.splitted = this.chunk(response.posts.data, 3);
                        this.categories = response.categories;
                        window.scrollTo(0, 0);
                        helper.hideSpinner();
                        document.title = `${i18n.general.search_for} | ${helper.getConfig('company_name')}`;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                        helper.hideSpinner();
                    });
            },
            chunk(arr, len) {
                let chunks = [];
                let i = 0;
                let n = arr.length;
                while (i < n) {
                    chunks.push(arr.slice(i, i += len));
                }
                return chunks;
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
