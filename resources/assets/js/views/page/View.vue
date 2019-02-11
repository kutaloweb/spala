<template>
    <div v-if="page">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 m-t-30">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-9">
                                    <h1 class="card-title post-title">{{ page.title }}</h1>
                                    <div class="card-text" v-html="page.body"></div>
                                </div>
                                <div class="col-md-3" v-if="page.body">
                                    <div class="text-muted card-caps mb-1">{{ trans('general.share') }}</div>
                                    <social-sharing
                                            :url="`${getConfig('app_url')}/${page.slug}`"
                                            :title="`${page.title}`">
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
                    {name: 'description', content: this.page ? this.page.totally_stripped_body : ''},
                    {name: 'twitter:card', content: 'summary_large_image'},
                    {name: 'twitter:title', content: this.page ? this.page.title : ''},
                    {name: 'twitter:description', content: this.page ? this.page.totally_stripped_body : ''},
                    {name: 'twitter:image', content: `${this.getConfig('app_url')}/uploads/images/cover-default.png`},
                    {property: 'og:type', content: 'website'},
                    {property: 'og:site_name', content: this.getConfig('company_name')},
                    {property: 'og:url', content: this.page ? `${this.getConfig('app_url')}/${this.page.slug}` : ''},
                    {property: 'og:title', content: this.page ? this.page.title : ''},
                    {property: 'og:description', content: this.page ? this.page.totally_stripped_body : ''},
                    {property: 'og:image', content: `${this.getConfig('app_url')}/uploads/images/cover-default.png`}
                ]
            }
        },
        components: {
            pageNotFound, socialSharing
        },
        data() {
            return {
                slug: '',
                page: {},
                documentTitle: '',
                categories: []
            };
        },
        mounted() {
            this.slug = this.$route.params.slug;
            helper.showSpinner();
            axios.get('/api/pages/' + this.slug)
                .then(response => response.data)
                .then(response => {
                    this.page = response.page;
                    this.categories = response.categories;
                    if (this.page) {
                        this.documentTitle = `${this.page.title} | ${helper.getConfig('company_name')}`;
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
            }
        }
    }
</script>
