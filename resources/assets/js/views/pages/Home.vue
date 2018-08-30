<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{ trans('general.home') }}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active">{{ trans('general.home') }}</li>
                </ol>
            </div>
        </div>
        <div class="row" v-if="hasRole('admin')">
            <div class="col-lg-12 col-md-12">
                <h3>{{ trans('dashboard.users') }}</h3>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card card-inverse card-info">
                    <div class="card-body">
                        <h4 class="card-title text-white">{{ trans('dashboard.total') }}</h4>
                        <div class="text-right">
                            <h2 class="font-light m-b-0 text-white"><i class="fas fa-users fa-lg pull-right"></i> <span
                                    class="pull-left">{{ all_registered_users }}</span></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card card-inverse card-primary">
                    <div class="card-body">
                        <h4 class="card-title text-white">{{ trans('dashboard.today') }}</h4>
                        <div class="text-right">
                            <h2 class="font-light m-b-0 text-white"><i class="fas fa-users fa-lg pull-right"></i> <span
                                    class="pull-left">{{ today_registered_users }}</span></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card card-inverse card-success">
                    <div class="card-body">
                        <h4 class="card-title text-white">{{trans('dashboard.week') }}</h4>
                        <div class="text-right">
                            <h2 class="font-light m-b-0 text-white"><i class="fas fa-users fa-lg pull-right"></i> <span
                                    class="pull-left">{{ weekly_registered_users }}</span></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card card-inverse card-warning">
                    <div class="card-body">
                        <h4 class="card-title text-white">{{ trans('dashboard.month') }}</h4>
                        <div class="text-right">
                            <h2 class="font-light m-b-0 text-white"><i class="fas fa-users fa-lg pull-right"></i> <span
                                    class="pull-left">{{ monthly_registered_users }}</span></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" v-if="hasRole('admin')">
            <div class="col-lg-12 col-md-12">
                <h3>{{ trans('dashboard.posts') }}</h3>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card card-inverse card-info">
                    <div class="card-body">
                        <h4 class="card-title text-white">{{ trans('dashboard.total') }}</h4>
                        <div class="text-right">
                            <h2 class="font-light m-b-0 text-white"><i class="far fa-share-square fa-lg pull-right"></i> <span
                                    class="pull-left">{{ all_published_posts }}</span></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card card-inverse card-primary">
                    <div class="card-body">
                        <h4 class="card-title text-white">{{ trans('dashboard.today') }}</h4>
                        <div class="text-right">
                            <h2 class="font-light m-b-0 text-white"><i class="far fa-share-square fa-lg pull-right"></i> <span
                                    class="pull-left">{{ today_published_posts }}</span></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card card-inverse card-success">
                    <div class="card-body">
                        <h4 class="card-title text-white">{{trans('dashboard.week') }}</h4>
                        <div class="text-right">
                            <h2 class="font-light m-b-0 text-white"><i class="far fa-share-square fa-lg pull-right"></i> <span
                                    class="pull-left">{{ weekly_published_posts }}</span></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card card-inverse card-warning">
                    <div class="card-body">
                        <h4 class="card-title text-white">{{ trans('dashboard.month') }}</h4>
                        <div class="text-right">
                            <h2 class="font-light m-b-0 text-white"><i class="far fa-share-square fa-lg pull-right"></i> <span
                                    class="pull-left">{{ monthly_published_posts }}</span></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" v-if="hasRole('admin')">
            <div class="col-12 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ trans('activity.last_activity_log') }}</h4>
                        <h6 class="card-subtitle" v-if="!activity_logs.length">
                            {{ trans('general.no_result_found') }}
                        </h6>
                        <div class="table-responsive" v-if="activity_logs.length">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th v-if="hasRole('admin')">{{ trans('user.user') }}</th>
                                    <th>{{ trans('activity.activity') }}</th>
                                    <th class="table-option">{{ trans('activity.date_time') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="activity_log in activity_logs">
                                    <td v-text="activity_log.user.profile.first_name + ' ' + activity_log.user.profile.last_name"></td>
                                    <td v-if="activity_log.sub_module == null">
                                        {{ trans('activity.' + activity_log.activity, {activity:
                                        trans(activity_log.module + '.' + activity_log.module)}) }}
                                    </td>
                                    <td v-else>
                                        {{ trans('activity.' + activity_log.activity, {activity:
                                        trans(activity_log.module + '.' + activity_log.sub_module)}) }}
                                    </td>
                                    <td class="table-option">{{ activity_log.created_at }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" v-if="hasRole('admin')">
            <div class="col-12 col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ trans('post.last_posts') }}</h4>
                        <h6 class="card-subtitle" v-if="!posts.length">
                            {{ trans('general.no_result_found') }}
                        </h6>
                        <div class="table-responsive" v-if="posts.length">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th v-if="hasRole('admin')">{{ trans('user.user') }}</th>
                                    <th>{{ trans('post.title') }}</th>
                                    <th class="table-option">{{ trans('activity.date_time') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="post in posts">
                                    <td v-text="post.user.profile.first_name + ' ' + post.user.profile.last_name"></td>
                                    <td v-text="post.title"></td>
                                    <td class="table-option">{{ post.created_at }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                all_registered_users: 0,
                today_registered_users: 0,
                weekly_registered_users: 0,
                monthly_registered_users: 0,
                all_published_posts: 0,
                today_published_posts: 0,
                weekly_published_posts: 0,
                monthly_published_posts: 0,
                activity_logs: {},
                posts: {}
            }
        },
        mounted() {
            helper.showSpinner();
            axios.get('/api/dashboard')
                .then(response => response.data)
                .then(response => {
                    this.all_registered_users = response.all_registered_users;
                    this.today_registered_users = response.today_registered_users;
                    this.weekly_registered_users = response.weekly_registered_users;
                    this.monthly_registered_users = response.monthly_registered_users;

                    this.all_published_posts = response.all_published_posts;
                    this.today_published_posts = response.today_published_posts;
                    this.weekly_published_posts = response.weekly_published_posts;
                    this.monthly_published_posts = response.monthly_published_posts;

                    this.activity_logs = response.activity_logs;
                    this.posts = response.posts;
                    helper.hideSpinner();
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                    helper.hideSpinner();
                })
        },
        methods: {
            hasRole(role) {
                return helper.hasRole(role);
            }
        }
    }
</script>
