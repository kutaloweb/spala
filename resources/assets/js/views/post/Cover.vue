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
                    <li class="breadcrumb-item active">{{ trans('post.cover') }}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <post-sidebar :statistics="statistics"></post-sidebar>
                            <div class="col-10 col-lg-10 col-md-10">
                                <h4 class="card-title">{{ trans('post.cover') }}</h4>
                                <h6 class="card-subtitle">
                                    {{ post.title }}
                                </h6>
                                <upload-image
                                        id="cover"
                                        :upload-path="`/post/cover/${id}`"
                                        :remove-path="`/post/cover/remove/${id}`"
                                        :image-source="cover.post">
                                </upload-image>
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
    import uploadImage from '../../components/UploadImage'

    export default {
        components: {postSidebar, uploadImage},
        data() {
            return {
                id: '',
                slug: '',
                post: '',
                cover: {
                    post: ''
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
            this.slug = this.$route.params.slug;
            axios.post('/api/post/statistics')
                .then(response => response.data)
                .then(response => {
                    this.statistics.published = response.published;
                    this.statistics.draft = response.draft;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });
            helper.showSpinner();
            axios.get('/api/post/' + this.slug)
                .then(response => response.data)
                .then(response => {
                    this.id = response.post.id;
                    this.post = response.post;
                    if (response.post.cover !== 'uploads/images/cover-default.png') {
                        this.cover.post = response.post.cover;
                    }
                    helper.hideSpinner();
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                    this.$router.push('/post');
                    helper.hideSpinner();
                })
        }
    }
</script>
