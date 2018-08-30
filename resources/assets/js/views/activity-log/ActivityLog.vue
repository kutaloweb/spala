<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{ trans('activity.activity_log') }}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <router-link to="/home">{{ trans('general.home') }}</router-link>
                    </li>
                    <li class="breadcrumb-item active">{{ trans('activity.activity_log') }}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <transition name="fade">
                    <div class="card" v-if="showFilterPanel">
                        <div class="card-body">
                            <button class="btn btn-info btn-sm pull-right filter-opened" v-if="showFilterPanel"
                                    @click="showFilterPanel = !showFilterPanel">
                                <i class="fas fa-filter"></i>
                                {{ trans('general.filter') }}
                            </button>
                            <h4 class="card-title">{{ trans('general.filter') }}</h4>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group" v-show="users">
                                        <label>{{ trans('user.user') }}</label>
                                        <select v-model="filterActivityLogForm.user_id" class="custom-select col-12 form-control">
                                            <option value="">{{ trans('general.select_one') }}</option>
                                            <option v-for="user in users" v-bind:value="user.id">
                                                {{ user.profile.first_name + ' ' + user.profile.last_name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <date-range-picker
                                                :start-date.sync="filterActivityLogForm.created_at_start_date"
                                                :end-date.sync="filterActivityLogForm.created_at_end_date">
                                        </date-range-picker>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>
                <div class="card">
                    <div class="card-body">
                        <button class="btn btn-info btn-sm pull-right" v-if="!showFilterPanel"
                                @click="showFilterPanel = !showFilterPanel">
                            <i class="fas fa-filter"></i>
                            {{ trans('general.filter') }}
                        </button>
                        <h4 class="card-title">{{ trans('activity.activity_log') }}</h4>
                        <h6 class="card-subtitle" v-if="activity_logs">
                            {{ trans('general.total_result_found', {'count' : activity_logs.total}) }}
                        </h6>
                        <h6 class="card-subtitle" v-else>{{ trans('general.no_result_found') }}</h6>
                        <div class="table-responsive" v-if="activity_logs.total">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>{{ trans('activity.user') }}</th>
                                    <th>{{ trans('activity.ip') }}</th>
                                    <th>{{ trans('activity.user_agent') }}</th>
                                    <th>{{ trans('activity.activity') }}</th>
                                    <th>{{ trans('activity.date_time') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="activity_log in activity_logs.data">
                                    <td v-text="activity_log.user.profile.first_name + ' ' + activity_log.user.profile.last_name"></td>
                                    <td v-text="activity_log.ip"></td>
                                    <td v-text="activity_log.user_agent"></td>
                                    <td v-if="activity_log.sub_module == null">
                                        {{ trans('activity.' + activity_log.activity,
                                        { activity: trans(activity_log.module + '.' + activity_log.module) }) }}
                                    </td>
                                    <td v-else>
                                        {{trans('activity.' + activity_log.activity,
                                        {activity: trans(activity_log.module + '.' + activity_log.sub_module) }) }}
                                    </td>
                                    <td>{{ activity_log.created_at }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <pagination-record :page-length.sync="filterActivityLogForm.page_length"
                                           :records="activity_logs"
                                           @updateRecords="getActivityLogs">
                        </pagination-record>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import dateRangePicker from '../../components/DateRangePicker'

    export default {
        components: {dateRangePicker},
        data() {
            return {
                activity_logs: {
                    total: 0,
                    data: []
                },
                filterActivityLogForm: {
                    page_length: helper.getConfig('page_length'),
                    user_id: '',
                    created_at_start_date: '',
                    created_at_end_date: '',
                    sort_by: 'created_at',
                    order: 'desc'
                },
                users: [],
                showFilterPanel: false
            };
        },
        mounted() {
            if (!helper.featureAvailable('activity_log')) {
                helper.featureNotAvailableMsg();
                this.$router.push('/home');
            }
            if (!helper.hasPermission('access-configuration')) {
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            this.getActivityLogs();
        },
        methods: {
            getActivityLogs(page) {
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterActivityLogForm);
                helper.showSpinner();
                axios.get('/api/activity-log?page=' + page + url)
                    .then(response => response.data)
                    .then(response => {
                        this.users = response.users;
                        this.activity_logs = response.activity_logs;
                        helper.hideSpinner();
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                        helper.hideSpinner();
                    });

            }
        },
        watch: {
            filterActivityLogForm: {
                handler(val) {
                    this.getActivityLogs();
                },
                deep: true
            }
        }
    }
</script>
