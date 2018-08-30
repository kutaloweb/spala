<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{ trans('user.user_detail') }}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <router-link to="/home">{{ trans('general.home') }}</router-link>
                    </li>
                    <li class="breadcrumb-item">
                        <router-link to="/user">{{ trans('user.user') }}</router-link>
                    </li>
                    <li class="breadcrumb-item active">{{ trans('user.avatar') }}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-lg-8 col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <user-sidebar menu="avatar" :id="id"></user-sidebar>
                            <div class="col-9 col-lg-9 col-md-9">
                                <h4 class="card-title">{{ trans('user.avatar') }}</h4>
                                <upload-image
                                        id="avatar"
                                        :upload-path="`/user/profile/avatar/${id}`"
                                        :remove-path="`/user/profile/avatar/remove/${id}`"
                                        :image-source="avatar.user"
                                        @uploaded="updateAvatar"
                                        @removed="updateAvatar">
                                </upload-image>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <user-summary :user="user"></user-summary>
        </div>
    </div>
</template>

<script>
    import userSidebar from './UserSidebar'
    import uploadImage from '../../components/UploadImage'
    import userSummary from './Summary'

    export default {
        components: {userSidebar, uploadImage, userSummary},
        data() {
            return {
                id: this.$route.params.id,
                user: '',
                avatar: {
                    user: ''
                }
            };
        },
        mounted() {
            if (!helper.hasPermission('edit-user')) {
                helper.notAccessibleMsg();
                this.$router.push('/home');
            }
            helper.showSpinner();
            axios.get('/api/user/' + this.id)
                .then(response => response.data)
                .then(response => {
                    this.user = response.user;
                    this.avatar.user = response.user.profile.avatar;
                    helper.hideSpinner();
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                    this.$router.push('/user');
                    helper.hideSpinner();
                })
        },
        methods: {
            updateAvatar(val) {
                if (helper.getAuthUser('id') == this.id) {
                    this.$store.dispatch('setAuthUserDetail', {
                        avatar: val
                    });
                }
                this.user.profile.avatar = val;
            }
        }
    }
</script>
