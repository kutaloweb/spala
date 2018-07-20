<template>
    <form @submit.prevent="submit" @keydown="postForm.errors.clear($event.target.name)">
        <div class="form-group">
            <input class="form-control" type="text" value="" v-model="postForm.title" name="title"
                   :placeholder="trans('post.title')">
            <show-error :form-name="postForm" prop-name="title"></show-error>
        </div>
        <div class="form-group">
            <summernote-editor
                    type="post"
                    :model.sync="postForm.body"
                    :isUpdate="slug ? true : false"
                    @clearErrors="postForm.errors.clear('body')">
            </summernote-editor>
            <show-error :form-name="postForm" prop-name="body"></show-error>
        </div>
        <div class="form-group pull-right">
            <button type="button" @click="saveAsDraft" class="btn btn-info waves-effect waves-light">
                <i class="fas fa-edit"></i> {{ trans('post.save_as_draft') }}
            </button>
            <router-link to="/post/draft" v-if="postForm.id" class="btn btn-warning waves-effect waves-light">
                <i class="fas fa-times"></i> {{ trans('post.cancel') }}
            </router-link>
            <button type="submit" class="btn btn-success waves-effect waves-light">
                <i class="far fa-share-square"></i> {{ trans('post.publish') }}
            </button>
        </div>
    </form>
</template>

<script>
    import summernoteEditor from '../../components/SummernoteEditor'

    export default {
        components: {summernoteEditor},
        props: ['slug'],
        data() {
            return {
                postForm: new Form({
                    id: '',
                    title: '',
                    body: '',
                    is_draft: 0
                }),
                statistics: {
                    published: 0,
                    draft: 0,
                }
            };
        },
        mounted() {
            axios.post('/api/post/statistics')
                .then(response => response.data)
                .then(response => {
                    this.statistics.published = response.published;
                    this.statistics.draft = response.draft;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });

            if (this.slug) {
                axios.get('/api/post/' + this.slug)
                    .then(response => response.data)
                    .then(response => {
                        this.postForm.title = response.post.title;
                        this.postForm.body = response.post.body;
                        this.postForm.id = response.post.id;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            }
        },
        methods: {
            submit() {
                this.postForm.is_draft = 0;
                this.postForm.post('/api/post/new')
                    .then(response => {
                        toastr.success(response.post);
                        this.$router.push('/post/published');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    })
            },
            saveAsDraft() {
                this.postForm.is_draft = 1;
                this.postForm.post('/api/post/new')
                    .then(response => {
                        toastr.success(response.post);
                        this.$router.push('/post/draft');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    })
            }
        }
    }
</script>
