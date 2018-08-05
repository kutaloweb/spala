<template>
    <section id="wrapper">
        <div class="login-register" :style="{ 'background-image' : 'url(\'' + getBackground + '\')' }">
            <div class="login-box card">
                <div class="card-body">
                    <form class="form-horizontal form-material" id="loginform" @submit.prevent="submit"
                          @keydown="loginForm.errors.clear($event.target.name)">
                        <h3 class="box-title m-b-20">{{ trans('auth.login') }}</h3>
                        <div class="form-group ">
                            <input type="email" name="email" class="form-control" :placeholder="trans('auth.email')"
                                   v-model="loginForm.email" autocapitalize="none">
                            <show-error :form-name="loginForm" prop-name="email"></show-error>
                        </div>
                        <div class="form-group">
                            <input type="password" name="password" class="form-control"
                                   :placeholder="trans('auth.password')" v-model="loginForm.password">
                            <show-error :form-name="loginForm" prop-name="password"></show-error>
                        </div>
                        <div class="form-group text-center m-t-20">
                            <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light"
                                    type="submit">
                                {{ trans('auth.sign_in') }}
                            </button>
                        </div>
                        <div class="form-group m-b-0">
                            <div class="col-sm-12 text-center">
                                <p v-if="getConfig('reset_password')">
                                    {{ trans('auth.forgot_your_password?') }}
                                    <router-link to="/password" class="text-info m-l-5">
                                        <b>{{ trans('auth.reset_here!') }}</b>
                                    </router-link>
                                </p>
                                <p v-if="getConfig('registration')">
                                    {{ trans('auth.create_account?') }}
                                    <router-link to="/register" class="text-info m-l-5">
                                        <b>{{trans('auth.sign_up')}}</b>
                                    </router-link>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="container-fluid">
            <div class="row" v-for="items in splitted">
                <div class="col-12 m-t-20 m-b-20">
                    <div class="card-deck">
                        <post-card v-for="post in items" :post="post" :key="post.id"></post-card>
                    </div>
                </div>
            </div>
            <pagination-record
                    :page-length.sync="filterPostForm.page_length"
                    :records="posts"
                    :show-page-length="false"
                    @updateRecords="getPosts"
                    @change.native="getPosts">
            </pagination-record>
        </div>
    </section>
</template>

<script>
    import postCard from '../post/PostCard'

    export default {
        data() {
            return {
                posts: {
                    total: 0,
                    data: []
                },
                splitted: [],
                filterPostForm: {
                    page_length: 9
                },
                loginForm: new Form({
                    email: '',
                    password: ''
                })
            }
        },
        components: {
            postCard
        },
        computed: {
            getBackground() {
                if (helper.getConfig('background')) {
                    return '/' + helper.getConfig('background');
                }

                return '/uploads/config/background/background.jpg'
            }
        },
        mounted() {
            document.title = `${helper.getConfig('company_name')}`;
            this.getPosts();
        },
        methods: {
            submit() {
                this.loginForm.post('/api/auth/login')
                    .then(response => {
                        localStorage.setItem('auth_token', response.token);
                        axios.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('auth_token');
                        this.$store.dispatch('setAuthStatus');
                        this.$store.dispatch('setLastActivity');
                        if (helper.getConfig('two_factor_security') && response.two_factor_code) {
                            this.$store.dispatch('setTwoFactorCode', response.two_factor_code);
                            this.$router.push('/auth/security');
                        } else {
                            this.$store.dispatch('resetTwoFactorCode');
                            this.$router.push('/home');
                        }
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getConfig(config) {
                return helper.getConfig(config);
            },
            getPosts(page) {
                if (typeof page !== 'number') {
                    page = 1;
                }
                let url = helper.getFilterURL(this.filterPostForm);
                axios.get('/api/posts?page=' + page + url)
                    .then(response => response.data)
                    .then(response => {
                        this.posts = response;
                        this.splitted = this.chunk(response.data, 3);
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            chunk(arr, len) {
                let chunks = [];
                let i = 0;
                let n = arr.length;
                while (i < n) {
                    chunks.push(arr.slice(i, i += len));
                }
                return chunks;
            }
        }
    }
</script>
