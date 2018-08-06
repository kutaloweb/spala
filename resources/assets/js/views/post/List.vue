<template>
    <div class="container-fluid">
        <div class="row" v-for="items in splitted">
            <div class="col-12 m-t-20 m-b-20">
                <div class="card-deck">
                    <post-card v-for="post in items" :post="post" :key="post.id"></post-card>
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
                axios.get('/api/posts?page=' + page + url)
                    .then(response => response.data)
                    .then(response => {
                        this.posts = response.posts;
                        this.splitted = this.chunk(response.posts.data, 3);
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
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
            filterPostForm: {
                handler(val) {
                    this.getPosts();
                },
                deep: true
            }
        }

    }
</script>