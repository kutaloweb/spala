<template>
    <div v-if="post">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 m-t-30">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-9">
                                    <span class="text-muted card-caps">
                                        {{ categoryName }} <span v-if="categoryName">/</span> {{ post.created_at }}
                                    </span>
                                    <h1 class="card-title post-title">{{ post.title }}</h1>
                                    <div class="card-text" v-html="post.body"></div>
                                    <div class="card-text" v-if="hasRole('admin')">
                                        <router-link :to="`/post/${post.slug}/edit`"
                                                     class="btn btn-info btn-sm"
                                                     v-tooltip="trans('post.edit_published')">
                                            <i class="fas fa-edit"></i>
                                        </router-link>
                                    </div>
                                </div>
                                <div class="col-md-3" v-if="post.body">
                                    <div class="text-muted card-caps mb-1">{{ trans('general.share') }}</div>
                                    <social-sharing
                                            :url="`${getConfig('app_url')}/${categorySlug}/${post.slug}`"
                                            :title="`${post.title}`">
                                    </social-sharing>
                                    <div class="text-muted card-caps mt-3 mb-1">{{ trans('category.categories') }}</div>
                                    <div class="list-group">
                                        <a href="#" @click="searchCategory(category.id)" v-for="category in categories" class="list-group-item ist-group-item-action d-flex justify-content-between align-items-center">
                                            {{ category.name }}
                                            <span class="badge badge-primary badge-pill">{{ category.posts_count }}</span>
                                        </a>
                                    </div>
                                    <div class="text-muted card-caps mt-3 mb-1">{{ trans('general.contact_info') }}</div>
                                    <b>{{ getConfig('contact_info') }}</b>
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
    import socialSharing from '../../components/SocialSharing'

    export default {
        metaInfo() {
            return {
                title: `${this.documentTitle}`,
                meta: [
                    {name: 'description', content: this.post ? this.post.totally_stripped_body : ''},
                    {name: 'twitter:card', content: 'summary_large_image'},
                    {name: 'twitter:title', content: this.post ? this.post.title : ''},
                    {name: 'twitter:description', content: this.post ? this.post.totally_stripped_body : ''},
                    {name: 'twitter:image', content: this.post ? `${this.getConfig('app_url')}/${this.post.cover}` : ''},
                    {property: 'og:type', content: 'website'},
                    {property: 'og:site_name', content: this.getConfig('company_name')},
                    {property: 'og:url', content: this.post ? `${this.getConfig('app_url')}/${this.categorySlug}/${this.post.slug}` : ''},
                    {property: 'og:title', content: this.post ? this.post.title : ''},
                    {property: 'og:description', content: this.post ? this.post.totally_stripped_body : ''},
                    {property: 'og:image', content: this.post ? `${this.getConfig('app_url')}/${this.post.cover}` : ''}
                ]
            }
        },
        components: {
            pageNotFound, socialSharing
        },
        data() {
            return {
                category: '',
                categoryName: '',
                categorySlug: '',
                slug: '',
                post: {},
                documentTitle: '',
                categories: []
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
                    this.categories = response.categories;
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
                    helper.hideSpinner();
                });
        },
        methods: {
            getConfig(name) {
                return helper.getConfig(name);
            },
            searchCategory(categoryId) {
                helper.showSpinner();
                this.$store.dispatch('setSearchCategory', categoryId);
                this.$router.push('/search');
            },
            hasRole(role) {
                return helper.hasRole(role);
            }
        }
    }
</script>
