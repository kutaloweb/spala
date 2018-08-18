<template>
    <header class="topbar is_stuck" style="position:fixed;top:0;width:100%">
        <nav class="navbar top-navbar navbar-expand-md navbar-light">
            <div class="navbar-collapse">
                <ul class="nav navbar-nav navbar-logo mr-auto">
                    <li class="nav-item">
                        <router-link @click.native="showSpinner" class="nav-link nav-brand waves-effect waves-dark" to="/">
                            <img :src="getLogo()" alt="Logo" class="logo mr-2">
                            <b>{{ getConfig('company_name') }}</b>
                        </router-link>
                    </li>
                    <li v-if="toggle" class="nav-item">
                        <a class="nav-link nav-toggler hidden-md-up text-muted waves-effect waves-dark">
                            <i class="fas fa-bars fa-fw"></i>
                        </a>
                    </li>
                    <li v-if="toggle" class="nav-item">
                        <a class="nav-link nav-toggler sidebartoggler hidden-sm-down text-muted waves-effect waves-dark">
                            <i class="fas fa-bars fa-fw"></i>
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav mr-0 my-lg-0">
                    <li v-if="getConfig('facebook_group')" class="nav-item">
                        <a rel="nofollow" target="_blank" :href="getConfig('facebook_group')" class="nav-link text-muted waves-effect waves-dark">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    </li>
                    <li v-if="$route.name !=='search'" class="nav-item dropdown" ref="li">
                        <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href=""
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-search"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right mailbox animated bounceInDown" ref="div">
                            <ul>
                                <li>
                                    <div class="drop-title">
                                        <form class="custom-app-search" @keydown.enter.prevent="search()">
                                            <input type="text" class="form-control" v-model="search_query" :placeholder="trans('general.search_for')">
                                        </form>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
                <ul v-if="isAuth() && !getTwoFactorCode()" class="navbar-nav mr-0 my-lg-0">
                    <li class="nav-item">
                        <router-link class="nav-link text-muted waves-effect waves-dark" :to="'/home'">
                            <i class="fas fa-home fa-fw"></i>
                        </router-link>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href=""
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="username">{{ getAuthUser('full_name') }}</span>
                            <img :src="getAuthUser('avatar')" alt="Avatar" class="profile-pic ml-2">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right animated bounceInDown">
                            <ul class="dropdown-user">
                                <li>
                                    <div class="dw-user-box text-center">
                                        <div class="u-img">
                                            <img :src="getAuthUser('avatar')" alt="Avatar">
                                        </div>
                                        <div class="u-text">
                                            <h4>{{ getAuthUser('full_name') }}</h4>
                                            <h6 class="text-muted">{{ getAuthUser('email') }}</h6>
                                        </div>
                                    </div>
                                </li>
                                <li role="separator" class="divider"></li>
                                <li>
                                    <router-link to="/profile">
                                        <i class="fas fa-user"></i>
                                        {{ trans('user.update_profile') }}
                                    </router-link>
                                </li>
                                <li role="separator" class="divider"></li>
                                <li>
                                    <router-link to="/change-password">
                                        <i class="fas fa-cogs"></i>
                                        {{ trans('user.change_password') }}
                                    </router-link>
                                </li>
                                <li role="separator" class="divider"></li>
                                <li>
                                    <a href="#" @click.prevent="logout">
                                        <i class="fas fa-power-off"></i>
                                        {{ trans('auth.logout') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
</template>

<script>
    import {EventBus} from '../event-bus'

    export default {
        props: {
            toggle: {
                default: true
            }
        },
        data() {
            return {
                search_query: '',
            }
        },
        created() {
            EventBus.$on("config::set", () => {
                this.$forceUpdate();
            });
        },
        methods: {
            getLogo() {
                if (helper.getConfig('logo')) {
                    return '/' + helper.getConfig('logo');
                }

                return '/uploads/config/logo/logo.png';
            },
            logout() {
                helper.logout().then(() => {
                    this.$store.dispatch('resetAuthUserDetail');
                    this.$router.push('/login');
                })
            },
            getAuthUser(name) {
                return helper.getAuthUser(name);
            },
            getTwoFactorCode() {
                return helper.getTwoFactorCode();
            },
            isScreenLocked() {
                return helper.isScreenLocked();
            },
            isAuth() {
                return helper.isAuth();
            },
            getConfig(name) {
                return helper.getConfig(name);
            },
            search() {
                this.showSpinner();
                this.$store.dispatch('setSearchQuery', this.search_query);
                this.$refs.li.classList.remove("show");
                this.$refs.div.classList.remove("show");
                this.$router.push('/search');
            },
            showSpinner() {
                if (this.$route.name !=='main') return helper.showSpinner();
            }
        }
    }
</script>
