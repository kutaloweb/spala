<template>
    <aside class="left-sidebar d-print-none">
        <div class="scroll-sidebar">
            <div class="user-profile">
                <div class="profile-img mt-2"><img :src="getAuthUser('avatar')" alt="Avatar"></div>
            </div>
            <nav class="sidebar-nav m-t-20">
                <div class="text-center" v-if="getConfig('maintenance_mode')">
                    <span class="badge badge-danger m-b-10">{{ trans('configuration.under_maintenance') }}</span>
                </div>
                <ul id="sidebarnav">
                    <li>
                        <router-link to="/home" exact>
                            <i class="fas fa-home fa-fw"></i>
                            <span class="hide-menu">{{ trans('general.home') }}</span>
                        </router-link>
                    </li>
                    <li v-if="hasPermission('access-configuration')">
                        <router-link to="/configuration" exact>
                            <i class="fas fa-cogs fa-fw"></i>
                            <span class="hide-menu">{{ trans('configuration.configuration') }}</span>
                        </router-link>
                    </li>
                    <li v-if="hasPermission('list-user')">
                        <router-link to="/user" exact>
                            <i class="fas fa-users fa-fw"></i>
                            <span class="hide-menu">{{ trans('user.users') }}</span>
                        </router-link>
                    </li>
                    <li v-if="hasPermission('access-post')">
                        <router-link to="/post/published" exact>
                            <i class="far fa-share-square fa-fw"></i>
                            <span class="hide-menu">{{ trans('post.posts') }}</span>
                        </router-link>
                    </li>
                    <li v-if="hasPermission('access-category')">
                        <router-link to="/category" exact>
                            <i class="fas fa-sitemap fa-fw"></i>
                            <span class="hide-menu">{{ trans('category.categories') }}</span>
                        </router-link>
                    </li>
                    <li v-if="hasPermission('access-page')">
                        <router-link to="/page/published" exact>
                            <i class="fas fa-columns fa-fw"></i>
                            <span class="hide-menu">{{ trans('page.pages') }}</span>
                        </router-link>
                    </li>
                    <li v-if="hasPermission('access-configuration')">
                        <router-link to="/activity-log" exact>
                            <i class="fas fa-bars fa-fw"></i>
                            <span class="hide-menu">{{ trans('activity.activity_log') }}</span>
                        </router-link>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="sidebar-footer">
            <router-link v-if="hasPermission('access-configuration')" to="/configuration" class="link"
                         v-tooltip="trans('configuration.configuration')">
                <i class="fas fa-cogs"></i>
            </router-link>
            <router-link v-else to="/change-password" class="link"
                         v-tooltip="trans('user.change_password')">
                <i class="fas fa-cogs"></i>
            </router-link>
            <router-link to="/profile" class="link" v-tooltip="trans('user.profile')">
                <i class="fas fa-user"></i>
            </router-link>
            <a href="#" class="link" v-tooltip="trans('auth.logout')" @click.prevent="logout">
                <i class="fas fa-power-off"></i>
            </a>
        </div>
    </aside>
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
            logout() {
                helper.logout().then(() => {
                    this.$store.dispatch('resetAuthUserDetail');
                    this.$router.push('/login');
                })
            },
            getAuthUser(name) {
                return helper.getAuthUser(name);
            },
            hasPermission(permission) {
                return helper.hasPermission(permission);
            },
            getConfig(config) {
                return helper.getConfig(config);
            }
        }
    }
</script>
