<template>
    <div class="col-12 col-lg-4 col-md-4" v-if="user">
        <div class="card">
            <div class="card-body">
                <div class="m-t-30 text-center">
                    <img :src="getAvatar(user)" class="img-circle" width="150">
                    <h4 class="card-title m-t-10">
                        {{ user.profile.first_name + ' ' + user.profile.last_name }}
                    </h4>
                    <div>
                        <span v-for="status in getUserStatus(user)" :class="['label', 'label-' + status.color, 'm-r-5']">
                            {{ status.label }}
                        </span>
                    </div>
                    <div class="row m-t-10" v-if="user.id != getAuthUser('id')">
                        <div class="col-12" v-if="user.status == 'activated'">
                            <button type="button" class="btn btn-block btn-danger" @click="updateStatus('banned')">
                                <i class="fas fa-ban"></i>
                                {{ trans('user.user_action_ban') }}
                            </button>
                        </div>
                        <div class="col-12" v-if="user.status == 'disapproved'">
                            <button type="button" class="btn btn-block btn-success" @click="updateStatus('activated')">
                                <i class="fas fa-check"></i>
                                {{ trans('user.user_action_approve') }}
                            </button>
                        </div>
                        <div class="col-6"
                             v-if="user.status == 'pending_activation' || user.status == 'pending_approval'">
                            <button type="button" class="btn btn-block btn-success" @click="updateStatus('activated')">
                                <i class="fas fa-user-plus"></i>
                                {{ trans('user.user_action_approve') }}
                            </button>
                        </div>
                        <div class="col-6"
                             v-if="user.status == 'pending_activation' || user.status == 'pending_approval'">
                            <button type="button" class="btn btn-block btn-danger" @click="updateStatus('disapproved')">
                                <i class="fas fa-user-times"></i>
                                {{ trans('user.user_action_disapprove') }}
                            </button>
                        </div>
                        <div class="col-12" v-if="user.status == 'banned'">
                            <button type="button" class="btn btn-block btn-success" @click="updateStatus('activated')">
                                <i class="fas fa-check"></i>
                                {{ trans('user.user_action_activate') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="card-body">
                <h6 class="text-muted">{{ trans('user.email') }}</h6>
                <h6>{{ user.email }}</h6>
                <h6 class="text-muted">{{ trans('user.created_at') }}</h6>
                <h6>{{ user.created_at }}</h6>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            user: {
                default: {
                    profile: {}
                },
                required: true
            }
        },
        methods: {
            getUserStatus(user) {
                return helper.getUserStatus(user);
            },
            updateStatus(status) {
                axios.post('/api/user/' + this.user.id + '/status', {
                    status: status
                })
                    .then(response => response.data)
                    .then(response => {
                        this.user.status = status;
                        toastr.success(response.message);
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getAuthUser(name) {
                return helper.getAuthUser(name);
            },
            getAvatar(user) {
                return helper.getAvatar(user);
            }
        },
        watch: {
            user(newVal) {
                this.user = newVal;
            }
        }
    }
</script>
