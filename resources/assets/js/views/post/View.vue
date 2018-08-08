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
                                        {{ categoryName }} / {{ post.created_at }}
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
        components: {
            pageNotFound,
        },
        data() {
            return {
                category: '',
                categoryName: '',
                slug: '',
                post: {}
            };
        },
        mounted() {
            this.category = this.$route.params.category;
            this.slug = this.$route.params.slug;
            axios.get('/api/posts/' + this.category + '/' + this.slug)
                .then(response => response.data)
                .then(response => {
                    this.post = response.post;
                    this.categoryName = response.post.category.name;
                    if (this.post) {
                        document.title = `${helper.getConfig('company_name')} | ${this.post.title}`;
                    } else {
                        document.title = `${helper.getConfig('company_name')} | ${i18n.general.page_not_found_heading}`;
                    }
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });
        }
    }
</script>
