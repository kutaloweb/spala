<template>
    <header class="topbar">
        <nav class="navbar top-navbar navbar-expand-md navbar-light">
            <div class="navbar-collapse">
                <ul class="navbar-nav mt-md-0">
                    <li class="nav-item">
                        <a class="nav-link nav-toggler hidden-md-up text-muted waves-effect waves-dark"
                           href="javascript:void(0)">
                            <i class="fas fa-bars"></i>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link sidebartoggler hidden-sm-down text-muted" href="javascript:void(0)">
                            <i class="fas fa-bars fa-fw"></i>
                        </a>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-logo mr-auto">
                    <li class="nav-item">
                        <router-link class="nav-link nav-brand" to="/">
                            <img :src="getLogo()" alt="Logo" class="logo mr-2">
                            <b>{{ getConfig('company_name') }}</b>
                        </router-link>
                    </li>
                </ul>
                <ul class="navbar-nav mr-0 my-lg-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href=""
                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ getAuthUser('full_name') }}
                            <img :src="getAuthUser('avatar')" alt="Avatar" class="profile-pic ml-2">
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
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
            getConfig(name) {
                return helper.getConfig(name);
            }
        }
    }
</script>
