<template>
    <form @submit.prevent="submit" @keydown="postForm.errors.clear($event.target.name)">
        <div class="row">
            <div class="col-12 col-md-8">
                <div class="form-group">
                    <label>{{ trans('post.title') }}</label>
                    <input class="form-control" type="text" value="" v-model="postForm.title" name="title"
                           :placeholder="trans('post.title')" maxlength="191">
                    <show-error :form-name="postForm" prop-name="title"></show-error>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="form-group">
                    <label>{{ trans('category.category') }}</label>
                    <select class="form-control custom-select" name="category_id"
                            v-model="postForm.category_id">
                        <option value="">{{ trans('category.select_category') }}</option>
                        <option v-for="category in categories" :value="category.value">
                            {{ toWord(category.text) }}
                        </option>
                    </select>
                    <show-error :form-name="postForm" prop-name="category_id"></show-error>
                </div>
            </div>
            <div class="col-12 col-md-12">
                <div class="form-group">
                    <label>{{ trans('post.body') }}</label>
                    <summernote-editor
                            type="post"
                            :model.sync="postForm.body"
                            :isUpdate="slug ? true : false"
                            @clearErrors="postForm.errors.clear('body')">
                    </summernote-editor>
                    <show-error :form-name="postForm" prop-name="body"></show-error>
                </div>
            </div>
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
                    is_draft: 0,
                    category_id: ''
                }),
                statistics: {
                    published: 0,
                    draft: 0,
                },
                categories : []
            };
        },
        mounted() {
            axios.get('/api/post/pre-requisite')
                .then(response => response.data)
                .then(response => {
                    this.categories = response;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });

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
                        this.postForm.category_id = response.post.category_id;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            }
        },
        methods: {
            submit() {
                this.postForm.is_draft = 0;
                this.postForm.body = this.addAttributes(this.postForm.body);
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
                this.postForm.body = this.addAttributes(this.postForm.body);
                this.postForm.post('/api/post/new')
                    .then(response => {
                        toastr.success(response.post);
                        this.$router.push('/post/draft');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    })
            },
            toWord(str) {
                return helper.toWord(str);
            },
            addAttributes(bodyHtml) {
                bodyHtml = bodyHtml.replace(new RegExp('<a ', 'g'), '<a target="_blank" rel="nofollow" ');
                bodyHtml = bodyHtml.replace(new RegExp('<img ', 'g'), '<img class="img-fluid" ');

                return bodyHtml;
            }
        }
    }
</script>
