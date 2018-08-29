import VueRouter from 'vue-router'
import store from './vuex/store'
import helper from './services/helper'

let appName = helper.getConfig('company_name');

let routes = [
    {
        path: '/',
        component: require('./layouts/GuestPage.vue'),
        children: [
            {
                path: '/',
                component: require('./views/pages/Main.vue'),
                name: 'main',
                meta: {title: appName}
            },
            {
                path: '/search',
                component: require('./views/post/Search.vue'),
                name: 'search',
                meta: {title: i18n.general.search_for + ' | ' + appName}
            }
        ]
    },
    {
        path: '/',
        component: require('./layouts/GuestPage.vue'),
        meta: {validate: ['guest']},
        children: [
            {
                path: '/login',
                component: require('./views/auth/Login.vue'),
                name: 'login',
                meta: {title: i18n.auth.login + ' | ' + appName}
            },
            {
                path: '/password',
                component: require('./views/auth/Password.vue'),
                meta: {title: i18n.user.reset_password + ' | ' + appName}
            },
            {
                path: '/register',
                component: require('./views/auth/Register.vue'),
                meta: {title: i18n.auth.register + ' | ' + appName}
            },
            {
                path: '/auth/:token/activate',
                component: require('./views/auth/Activate.vue'),
                meta: {title: i18n.auth.two_factor_code + ' | ' + appName}
            },
            {
                path: '/password/reset/:token',
                component: require('./views/auth/Reset.vue'),
                meta: {title: i18n.passwords.reset_password + ' | ' + appName}
            }
        ]
    },
    {
        path: '/',
        component: require('./layouts/GuestPage.vue'),
        meta: {validate: ['auth']},
        children: [
            {
                path: '/auth/security',
                component: require('./views/auth/Security.vue'),
                meta: {title: i18n.auth.two_factor_code + ' | ' + appName}
            },
            {
                path: '/auth/lock',
                component: require('./views/auth/Lock.vue'),
                meta: {title: i18n.auth.lock_screen + ' | ' + appName}
            },
        ]
    },
    {
        path: '/',
        component: require('./layouts/DefaultPage.vue'),
        meta: {validate: ['auth', 'two_factor', 'lock_screen']},
        children: [
            {
                path: '/',
                component: require('./views/pages/Home.vue'),
                meta: {title: i18n.general.home + ' | ' + appName}
            },
            {
                path: '/home',
                component: require('./views/pages/Home.vue'),
                meta: {title: i18n.general.home + ' | ' + appName}
            },
            {
                path: '/profile',
                component: require('./views/pages/Profile.vue'),
                meta: {title: i18n.user.profile + ' | ' + appName}
            },
            {
                path: '/change-password',
                component: require('./views/pages/ChangePassword.vue'),
                meta: {title: i18n.auth.change_password + ' | ' + appName}
            },
            {
                path: '/configuration',
                component: require('./views/configuration/system/System.vue'),
                meta: {title: i18n.configuration.system + ' | ' + appName}
            },
            {
                path: '/configuration/image',
                component: require('./views/configuration/image/Image.vue'),
                meta: {title: i18n.configuration.configuration + ' - ' + appName}
            },
            {
                path: '/configuration/system',
                component: require('./views/configuration/system/System.vue'),
                meta: {title: i18n.configuration.system + ' | ' + appName}
            },
            {
                path: '/configuration/role',
                component: require('./views/configuration/role/Role.vue'),
                meta: {title: i18n.configuration.configuration + ' - ' + i18n.role.role + ' | ' + appName}
            },
            {
                path: '/configuration/authentication',
                component: require('./views/configuration/authentication/Auth.vue'),
                meta: {title: i18n.configuration.configuration + ' - ' + i18n.auth.authentication + ' | ' + appName}
            },
            {
                path: '/configuration/permission',
                component: require('./views/configuration/permission/Permission.vue'),
                meta: {title: i18n.configuration.configuration + ' - ' + i18n.permission.permission + ' | ' + appName}
            },
            {
                path: '/configuration/permission/assign',
                component: require('./views/configuration/permission/Assign.vue'),
                meta: {title: i18n.configuration.configuration + ' - ' + i18n.permission.assign_permission + ' | ' + appName}
            },
            {
                path: '/configuration/locale',
                component: require('./views/configuration/locale/Locale.vue'),
                meta: {title: i18n.configuration.configuration + ' - ' + i18n.locale.locale + ' | ' + appName}
            },
            {
                path: '/configuration/locale/:id/edit',
                component: require('./views/configuration/locale/Edit.vue'),
                meta: {title: i18n.configuration.configuration + ' - ' + i18n.locale.edit_locale + ' | ' + appName}
            },
            {
                path: '/activity-log',
                component: require('./views/activity-log/ActivityLog.vue'),
                meta: {title: i18n.activity.activity_log + ' | ' + appName}
            },
            {
                path: '/user',
                component: require('./views/user/User.vue'),
                meta: {title: i18n.user.users + ' | ' + appName}
            },
            {
                path: '/user/:id',
                component: require('./views/user/Basic.vue'),
                meta: {title: i18n.user.users + ' - ' + i18n.general.basic + ' | ' + appName}
            },
            {
                path: '/user/:id/basic',
                component: require('./views/user/Basic.vue'),
                meta: {title: i18n.user.users + ' - ' + i18n.general.basic + ' | ' + appName}
            },
            {
                path: '/user/:id/contact',
                component: require('./views/user/Contact.vue'),
                meta: {title: i18n.user.users + ' - ' + i18n.user.contact + ' | ' + appName}
            },
            {
                path: '/user/:id/avatar',
                component: require('./views/user/Avatar.vue'),
                meta: {title: i18n.user.users + ' - ' + i18n.user.avatar + ' | ' + appName}
            },
            {
                path: '/user/:id/password',
                component: require('./views/user/Password.vue'),
                meta: {title: i18n.user.users + ' - ' + i18n.user.password + ' | ' + appName}
            },
            {
                path: '/post',
                component: require('./views/post/Index.vue'),
                meta: {title: i18n.post.new + ' | ' + appName}
            },
            {
                path: '/post/new',
                component: require('./views/post/Index.vue'),
                meta: {title: i18n.post.new + ' | ' + appName}
            },
            {
                path: '/post/published',
                component: require('./views/post/Published.vue'),
                meta: {title: i18n.post.published_box + ' | ' + appName}
            },
            {
                path: '/post/draft',
                component: require('./views/post/Draft.vue'),
                meta: {title: i18n.post.drafts + ' | ' + appName}
            },
            {
                path: '/post/:slug/edit',
                component: require('./views/post/Edit'),
                meta: {title: i18n.post.edit + ' | ' + appName}
            },
            {
                path: '/post/:slug/cover',
                component: require('./views/post/Cover'),
                meta: {title: i18n.post.cover + ' | ' + appName}
            },
            {
                path: '/category',
                component: require('./views/category/Category'),
                meta: {title: i18n.category.categories + ' | ' + appName}
            },
            {
                path: '/page',
                component: require('./views/page/Index.vue'),
                meta: {title: i18n.page.new + ' | ' + appName}
            },
            {
                path: '/page/new',
                component: require('./views/page/Index.vue'),
                meta: {title: i18n.page.new + ' | ' + appName}
            },
            {
                path: '/page/published',
                component: require('./views/page/Published.vue'),
                meta: {title: i18n.page.published_box + ' | ' + appName}
            },
            {
                path: '/page/:slug/edit',
                component: require('./views/page/Edit'),
                meta: {title: i18n.page.edit + ' | ' + appName}
            }
        ]
    },
    {
        path: '/',
        component: require('./layouts/ErrorPage.vue'),
        children: [
            {
                path: '/maintenance',
                component: require('./views/errors/Maintenance.vue'),
                meta: {title: i18n.general.permission_denied + ' | ' + appName}
            }
        ]
    },
    {
        path: '/',
        component: require('./layouts/GuestPage.vue'),
        children: [
            {
                path: '/:category/:slug',
                component: require('./views/post/View.vue'),
                meta: {title: appName}
            },
            {
                path: '/:slug',
                component: require('./views/page/View.vue'),
                meta: {title: appName}
            }
        ]
    },
    {
        path: '*',
        component: require('./layouts/ErrorPage.vue'),
        children: [
            {
                path: '*',
                component: require('./views/errors/PageNotFound.vue'),
                meta: {title: i18n.general.page_not_found_heading + ' | ' + appName}
            }
        ]
    }
];

const router = new VueRouter({
    routes,
    linkActiveClass: 'active',
    mode: 'history',
    scrollBehavior(to, from, savedPosition) {
        if (savedPosition) {
            return savedPosition
        } else {
            return {x: 0, y: 0}
        }
    }
});

router.beforeEach((to, from, next) => {
    helper.authCheck()
        .then(response => {
            helper.notification();
            if (
                !helper.hasRole('admin') &&
                helper.getConfig('maintenance_mode') &&
                to.fullPath !== '/maintenance' &&
                to.fullPath !== '/login' &&
                to.fullPath !== '/auth/security' &&
                to.fullPath !== '/auth/lock'
            ) {
                return next({path: '/maintenance'});
            }
            if (to.matched.some(m => m.meta.validate)) {
                const m = to.matched.find(m => m.meta.validate);
                if (m.meta.validate.indexOf('auth') > -1) {
                    if (!helper.isAuth()) {
                        return next({path: '/'});
                    }
                }
                if (m.meta.validate.indexOf('two_factor') > -1) {
                    if (helper.getConfig('two_factor_security') && helper.getTwoFactorCode()) {
                        return next({path: '/auth/security'});
                    }
                }
                if (m.meta.validate.indexOf('lock_screen') > -1) {
                    if (helper.getConfig('lock_screen') && helper.isScreenLocked()) {
                        return next({path: '/auth/lock'});
                    }
                }
                if (m.meta.validate.indexOf('guest') > -1) {
                    if (helper.isAuth()) {
                        toastr.error(i18n.auth.guest_required);
                        return next({path: '/home'});
                    }
                }
            }

            const nearestWithTitle = to.matched.slice().reverse().find(r => r.meta && r.meta.title);
            const nearestWithMeta = to.matched.slice().reverse().find(r => r.meta && r.meta.metaTags);
            if (nearestWithTitle) document.title = nearestWithTitle.meta.title;
            if (document.title.includes('undefined')) document.title = document.title.replace("undefined", helper.getConfig('company_name'));
            Array.from(document.querySelectorAll('[data-vue-router-controlled]')).map(el => el.parentNode.removeChild(el));
            if (!nearestWithMeta) return next();
            nearestWithMeta.meta.metaTags.map(tagDef => {
                const tag = document.createElement('meta');
                Object.keys(tagDef).forEach(key => {
                    tag.setAttribute(key, tagDef[key]);
                });
                tag.setAttribute('data-vue-router-controlled', '');
                return tag;
            }).forEach(tag => document.head.appendChild(tag));

            return next();
        })
        .catch(error => {
            store.dispatch('resetAuthUserDetail');
            return next({path: '/login'})
        });
});

export default router;
