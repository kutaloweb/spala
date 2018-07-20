<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{ trans('post.posts') }}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <router-link to="/home">{{ trans('general.home') }}</router-link>
                    </li>
                    <li class="breadcrumb-item active">{{ trans('post.posts') }}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <post-sidebar menu="new" :statistics="statistics"></post-sidebar>
                            <div class="col-10 col-lg-10 col-md-10">
                                <h4 class="card-title">{{ trans('post.new') }}</h4>
                                <post-form></post-form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import postForm from './Form';
    import postSidebar from './PostSidebar'

    export default {
        components: {postForm, postSidebar},
        data() {
            return {
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
            axios.post('/api/post/statistics')
                .then(response => response.data)
                .then(response => {
                    this.statistics.published = response.published;
                    this.statistics.draft = response.draft;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });
        }
    }
</script>
