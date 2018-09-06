<template>
    <form @submit.prevent="submit" @keydown="pageForm.errors.clear($event.target.name)">
        <div class="row">
            <div class="col-12 col-md-12">
                <div class="form-group">
                    <label>{{ trans('page.title') }}</label>
                    <input class="form-control" type="text" value="" v-model="pageForm.title" name="title"
                           :placeholder="trans('page.title')" maxlength="191">
                    <show-error :form-name="pageForm" prop-name="title"></show-error>
                </div>
            </div>
            <div class="col-12 col-md-12">
                <div class="form-group">
                    <label>{{ trans('page.body') }}</label>
                    <summernote-editor
                            type="page"
                            :model.sync="pageForm.body"
                            :isUpdate="slug ? true : false"
                            @clearErrors="pageForm.errors.clear('body')">
                    </summernote-editor>
                    <show-error :form-name="pageForm" prop-name="body"></show-error>
                </div>
            </div>
        </div>
        <div class="form-group pull-right">
            <router-link to="/page/published" v-if="pageForm.id" class="btn btn-warning waves-effect waves-light">
                <i class="fas fa-times"></i> {{ trans('page.cancel') }}
            </router-link>
            <button type="submit" class="btn btn-success waves-effect waves-light">
                <i class="far fa-share-square"></i> {{ trans('page.publish') }}
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
                pageForm: new Form({
                    id: '',
                    title: '',
                    body: '',
                }),
                statistics: {
                    published: 0
                }
            };
        },
        mounted() {
            axios.post('/api/page/statistics')
                .then(response => response.data)
                .then(response => {
                    this.statistics.published = response.published;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });

            if (this.slug) {
                helper.showSpinner();
                axios.get('/api/page/' + this.slug)
                    .then(response => response.data)
                    .then(response => {
                        this.pageForm.title = response.page.title;
                        this.pageForm.body = response.page.body;
                        this.pageForm.id = response.page.id;
                        helper.hideSpinner();
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                        helper.hideSpinner();
                    });
            }
        },
        methods: {
            submit() {
                this.pageForm.body = this.cleanHTML(this.pageForm.body);
                this.pageForm.body = this.addAttributes(this.pageForm.body);
                this.pageForm.post('/api/page/new')
                    .then(response => {
                        toastr.success(response.page);
                        this.$router.push('/page/published');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    })
            },
            cleanHTML(bodyHtml) {
                let stringStripper = /(\n|\r| class=(")?Mso[a-zA-Z]+(")?)/g;
                let output = bodyHtml.replace(stringStripper, ' ');
                let commentStripper = new RegExp('<!--(.*?)-->', 'g');
                output = output.replace(commentStripper, '');
                let allowedTags = [
                    '<h1>', '<h2>', '<h3>', '<h4>', '<h5>', '<h6>', '<p>', '<br>', '<blockquote>', '<code>',
                    '<ul>', '<ol>', '<li>', '<b>', '<strong>', '<i>', '<u>', '<a>', '<img>', '<iframe>', '<hr>'
                ];
                allowedTags = (((allowedTags||'') + '').toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');
                let tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi;
                output = output.replace(tags, function($0, $1) {
                    return allowedTags.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : ''
                });
                let badAttributes = ['style', 'class', 'align'];
                for (let i = 0; i < badAttributes.length; i++) {
                    let attributeStripper = new RegExp(' ' + badAttributes[i] + '="(.*?)"', 'gi');
                    output = output.replace(attributeStripper, '');
                }
                output = output.replace(/[&]nbsp[;]/gi," ");

                return output;
            },
            addAttributes(bodyHtml) {
                bodyHtml = bodyHtml.replace(new RegExp('<a href', 'g'), '<a target="_blank" rel="nofollow" href');
                bodyHtml = bodyHtml.replace(new RegExp('<img src', 'g'), '<img class="img-fluid" src');

                return bodyHtml;
            }
        }
    }
</script>
