<template>
    <div v-if="post">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 m-t-30">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-9 col-md-9">
                                    <span class="text-muted card-caps">
                                        {{ categoryName }} <span v-if="categoryName">/</span> {{ post.created_at }}
                                    </span>
                                    <h1 class="card-title post-title">{{ post.title }}</h1>
                                    <div class="card-text" v-html="post.body"></div>
                                </div>
                                <div class="col-3 col-md-3">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-else>
        <page-not-found></page-not-found>
    </div>
</template>

<script>
    import pageNotFound from '../errors/PageNotFound'

    export default {
        metaInfo() {
            return {
                title: `${this.documentTitle}`,
                meta: [
                    {name: 'description', content: this.post ? this.limitWords(this.post.stripped_body) : ''},
                    {name: 'twitter:card', content: 'summary_large_image'},
                    {name: 'twitter:title', content: this.post ? this.post.title : ''},
                    {name: 'twitter:description', content: this.post ? this.limitWords(this.post.stripped_body) : ''},
                    {name: 'twitter:image', content: this.post ? `${this.getConfig('app_url')}/${this.post.cover}` : ''},
                    {property: 'og:type', content: 'website'},
                    {property: 'og:site_name', content: this.getConfig('company_name')},
                    {property: 'og:url', content: `${this.getConfig('app_url')}/${this.categorySlug}/${this.post.slug}`},
                    {property: 'og:title', content: this.post ? this.post.title : ''},
                    {property: 'og:description', content: this.post ? this.limitWords(this.post.stripped_body) : ''},
                    {property: 'og:image', content: this.post ? `${this.getConfig('app_url')}/${this.post.cover}` : ''}
                ]
            }
        },
        components: {
            pageNotFound,
        },
        data() {
            return {
                category: '',
                categoryName: '',
                slug: '',
                post: {},
                documentTitle: ''
            };
        },
        mounted() {
            this.category = this.$route.params.category;
            this.slug = this.$route.params.slug;
            helper.showSpinner();
            axios.get('/api/posts/' + this.category + '/' + this.slug)
                .then(response => response.data)
                .then(response => {
                    this.post = response.post;
                    this.categoryName = this.post ? response.post.category.name : '';
                    this.categorySlug = this.post ? response.post.category.slug : '';
                    if (this.post) {
                        this.documentTitle = `${this.post.title} | ${helper.getConfig('company_name')}`;
                    } else {
                        this.documentTitle = `${i18n.general.page_not_found_heading} | ${helper.getConfig('company_name')}`;
                    }
                    helper.hideSpinner();
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });
        },
        methods: {
            getConfig(name) {
                return helper.getConfig(name);
            },
            limitWords(str) {
                return helper.limitWords(str, 35);
            }
        }
    }
</script>
