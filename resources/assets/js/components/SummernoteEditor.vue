<template>
    <textarea class="form-control"></textarea>
</template>

<script>
    export default {
        props: {
            isUpdate: {
                default: false,
            },
            model: {
                required: true
            },
            type: {
                type: String,
                default: 'post'
            },
            height: {
                type: String,
                default: '350'
            },
            reloadContent: {
                default: false
            }
        },
        data() {
            return {
                loadContent: false
            }
        },
        mounted() {
            let config = {
                height: this.height,
                disableResizeEditor: true,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol']],
                    ['insert', ['link', 'picture', 'video', 'hr']],
                    ['view', ['fullscreen', 'codeview']],
                    ['help', ['help']]
                ],
            };
            let vm = this;
            config.callbacks = {
                onInit: function () {
                    $(vm.$el).summernote("code", vm.model);
                },
                onChange: function () {
                    vm.$emit('update:model', $(vm.$el).summernote('code'));
                    vm.$emit('clearErrors');
                },
                onBlur: function () {
                    vm.$emit('update:model', $(vm.$el).summernote('code'));
                },
                onImageUpload: function (files) {
                    vm.sendFile(files[0]);
                },
                onPaste: function () {
                    setTimeout(function () {
                        vm.updatePastedText();
                    }, 1000);
                }
            };
            $(this.$el).summernote(config);
        },
        watch: {
            model(val) {
                if (!this.loadContent && this.isUpdate) {
                    $(this.$el).summernote("code", this.model);
                    this.loadContent = true;
                }
                if (!this.model) $(this.$el).summernote("code", '');
            },
            reloadContent(val) {
                if (val) this.loadContent = false;
            }
        },
        methods: {
            sendFile(file) {
                let data = new FormData();
                data.append("file", file);
                axios.post('/api/' + this.type + '/upload/image', data)
                    .then(response => response.data)
                    .then(response => {
                        $(this.$el).summernote('insertImage', response.image_url);
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            toastr.error(error.response.data.errors.file[0]);
                        } else {
                            toastr.error(i18n.general.no_file_uploaded);
                        }
                    })
            },
            cleanPastedHTML(input) {
                let stringStripper = /(\n|\r| class=(")?Mso[a-zA-Z]+(")?)/g;
                let output = input.replace(stringStripper, ' ');

                let commentSripper = new RegExp('<!--(.*?)-->', 'g');
                output = output.replace(commentSripper, '');

                let allowedTags = [
                    '<h1>', '<h2>', '<h3>', '<h4>', '<h5>', '<h6>', '<p>', '<br>', '<blockquote>', '<code>',
                    '<ul>', '<ol>', '<li>', '<b>', '<strong>', '<i>', '<u>', '<a>', '<img>', '<iframe>', '<hr>'
                ];
                allowedTags = (((allowedTags||'') + '').toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');
                let tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi;
                output = output.replace(tags, function($0, $1) {
                    return allowedTags.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : ''
                });

                let badAttributes = ['style', 'class'];
                for (let i = 0; i < badAttributes.length; i++) {
                    let attributeStripper = new RegExp(' ' + badAttributes[i] + '="(.*?)"', 'gi');
                    output = output.replace(attributeStripper, '');
                }

                return output;
            },
            updatePastedText() {
                let original = this.model;
                let cleanedModel = this.cleanPastedHTML(original);
                $(this.$el).summernote("code", cleanedModel);
            }
        }
    }
</script>
