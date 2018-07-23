<template>
    <form @submit.prevent="storeCategory" @keydown="categoryForm.errors.clear($event.target.name)">
        <div class="form-group">
            <label>{{ trans('category.name') }}</label>
            <input class="form-control" type="text" value="" v-model="categoryForm.name" name="name"
                   :placeholder="trans('category.name')">
            <show-error :form-name="categoryForm" prop-name="name"></show-error>
        </div>
        <button type="submit" class="btn btn-info waves-effect waves-light pull-right">
            <span>{{ trans('general.save') }}</span>
        </button>
    </form>
</template>

<script>
    export default {
        data() {
            return {
                categoryForm: new Form({
                    'name': ''
                })
            };
        },
        methods: {
            storeCategory() {
                this.categoryForm.post('/api/category')
                    .then(response => {
                        toastr.success(response.message);
                        this.$emit('completed')
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            }
        }
    }
</script>
