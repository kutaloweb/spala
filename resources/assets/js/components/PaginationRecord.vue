<template>
    <div class="row">
        <div class="col-md-8">
            <pagination :data="records" :limit=3 v-on:pagination-change-page="getRecords"></pagination>
        </div>
        <div class="col-md-4" v-if="records.total">
            <div class="pull-right">
                <select name="page_length" class="custom-select pagination-select form-control" :value="pageLength" @change="updateValue">
                    <option v-for="option in getConfig('pagination')" :value="option">
                        {{ option + ' ' + trans('general.per_page') }}
                    </option>
                </select>
            </div>
        </div>
    </div>
</template>

<script>
    import pagination from 'laravel-vue-pagination'

    export default {
        components: {pagination},
        props: ['pageLength', 'records'],
        methods: {
            getConfig(config) {
                return helper.getConfig(config);
            },
            updateValue(e) {
                this.$emit('update:pageLength', e.target.value);
            },
            getRecords(page) {
                this.$emit('updateRecords', page);
            }
        }
    }
</script>
