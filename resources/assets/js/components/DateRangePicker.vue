<template>
    <div class="form-group">
        <label>{{ label || trans('general.date_range') }}</label>
        <div class="row">
            <div class="col-12 col-md-6">
                <datepicker
                        :language="locale"
                        :monday-first="true"
                        :bootstrapStyling="true"
                        input-class="form-control"
                        :value="startDate"
                        :placeholder="trans('general.start_date')"
                        :clear-button="clearButton"
                        @selected="updateStartDate"
                        @cleared="clearStartDate">
                </datepicker>
            </div>
            <div class="col-12 col-md-6">
                <datepicker
                        :language="locale"
                        :monday-first="true"
                        :bootstrapStyling="true"
                        input-class="form-control"
                        :value="endDate"
                        :placeholder="trans('general.end_date')"
                        @selected="updateEndDate"
                        @cleared="clearEndDate">
                </datepicker>
            </div>
        </div>
    </div>
</template>

<script>
    import datepicker from 'vuejs-datepicker'

    export default {
        components: {datepicker},
        props: ['startDate', 'endDate', 'label'],
        data() {
            return {
                clearButton: true,
                locale: 'en'
            }
        },
        mounted() {
            this.locale = helper.getConfig('locale');
        },
        methods: {
            updateStartDate(val) {
                let date = moment(val).format('YYYY-MM-DD HH:mm Z');
                this.$emit('update:startDate', date);
            },
            updateEndDate(val) {
                let date = moment(val).format('YYYY-MM-DD HH:mm Z');
                this.$emit('update:endDate', date);
            },
            clearStartDate() {
                this.$emit('update:startDate', '');
                this.$emit('update:endDate', '');
            },
            clearEndDate() {
                this.$emit('update:endDate', '');
            }
        }
    }
</script>
