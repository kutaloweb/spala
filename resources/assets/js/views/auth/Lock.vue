<template>
    <section id="wrapper">
        <div class="hero d-flex" :style="{ 'background-image' : 'url(\'' + getBackground + '\')' }">
            <div class="login-box card row justify-content-center align-self-center">
                <div class="card-body">
                    <div class="m-t-30 text-center">
                        <img :src="getAuthUser('avatar')" class="img-circle" width="100">
                        <h4 class="card-title m-t-10">{{ getAuthUser('full_name') }}</h4>
                    </div>
                    <form class="form-horizontal form-material" id="lockScreenForm" @submit.prevent="submit"
                          @keydown="lockScreenForm.errors.clear($event.target.name)">
                        <div class="form-group ">
                            <input type="password" name="password" class="form-control text-center"
                                   :placeholder="trans('auth.password')" v-model="lockScreenForm.password">
                            <show-error :form-name="lockScreenForm" prop-name="password"></show-error>
                        </div>
                        <div class="form-group text-center m-t-20">
                            <button-spinner
                                    :btn-text="trans('auth.confirm')"
                                    :class="'btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light'"
                                    :is-loading="isLoading"
                                    :disabled="isLoading">
                            </button-spinner>
                        </div>
                        <div class="form-group m-b-0">
                            <div class="col-sm-12 text-center">
                                <p>
                                    <a href="#" @click.prevent="logout">
                                        <i class="fas fa-power-off"></i>
                                        {{ trans('auth.logout') }}
                                    </a>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    import buttonSpinner from '../../components/ButtonSpinner';

    export default {
        data() {
            return {
                lockScreenForm: new Form({
                    password: '',
                }),
                isLoading: false
            }
        },
        components: {
            buttonSpinner
        },
        mounted() {
            if (!helper.getConfig('lock_screen') || !helper.isScreenLocked())
                this.$router.push('/home');
        },
        computed: {
            getBackground() {
                if (helper.getConfig('background')) {
                    return '/' + helper.getConfig('background');
                }

                return '/uploads/config/background/background.jpg'
            }
        },
        methods: {
            logout() {
                helper.logout().then(() => {
                    this.$store.dispatch('resetAuthUserDetail');
                    this.$router.push('/login')
                })
            },
            submit() {
                this.isLoading = true;
                this.lockScreenForm.post('/api/auth/lock')
                    .then(response => {
                        this.$store.dispatch('setLastActivity');
                        toastr.success(response.message);
                        this.$router.push('/home');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                        this.isLoading = false;
                    });
            },
            getAuthUser(name) {
                return helper.getAuthUser(name);
            }
        }
    }
</script>
