<template>
    <div class="container-fluid">
        <div class="row" v-for="items in splitted">
            <div class="col-12 m-b-20">
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
</template>

<script>
    import postCard from './PostCard'

    export default {
        metaInfo() {
            return {
                title: `${this.getConfig('company_name')}`,
                meta: [
                    {name: 'description', content: this.getConfig('company_description')},
                    {name: 'twitter:card', content: 'summary_large_image'},
                    {name: 'twitter:title', content: this.getConfig('company_name')},
                    {name: 'twitter:description', content: this.getConfig('company_description')},
                    {name: 'twitter:image', content: `${this.getConfig('app_url')}/uploads/images/cover-default.png`},
                    {property: 'og:type', content: 'website'},
                    {property: 'og:site_name', content: this.getConfig('company_name')},
                    {property: 'og:url', content: `${this.getConfig('app_url')}`},
                    {property: 'og:title', content: this.getConfig('company_name')},
                    {property: 'og:description', content: this.getConfig('company_description')},
                    {property: 'og:image', content: `${this.getConfig('app_url')}/uploads/images/cover-default.png`}
                ]
            }
        },
        data() {
            return {
                posts: {
                    total: 0,
                    data: []
                },
                splitted: [],
                filterPostForm: {
                    page_length: 9
                }
            }
        },
        components: {
            postCard
        },
        mounted() {
            this.getPosts();
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
                        window.scrollTo(0, 0);
                        helper.hideSpinner();
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
            },
            getConfig(name) {
                return helper.getConfig(name);
            }
        },
        watch: {
            filterPostForm: {
                handler(val) {
                    this.getPosts();
                },
                deep: true
            }
        }

    }
</script>