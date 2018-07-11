import VueRouter from 'vue-router'
import store from './store'
import helper from './services/helper'

let appName = helper.getConfig('company_name');

let routes = [
    {
        path: '/',
        component: require('./views/auth/Login.vue'),
        meta: {title: appName + ' | ' + i18n.auth.login}
    },
    {
        path: '/',
        component: require('./layouts/GuestPage.vue'),
        meta: {validate: ['guest']},
        children: [
            {
                path: '/login',
                component: require('./views/auth/Login.vue'),
                meta: {title: appName + ' | ' + i18n.auth.login}
            },
            {
                path: '/password',
                component: require('./views/auth/Password.vue'),
                meta: {title: appName + ' | ' + i18n.user.reset_password}
            },
            {
                path: '/register',
                component: require('./views/auth/Register.vue'),
                meta: {title: appName + ' | ' + i18n.auth.register}
            },
            {
                path: '/auth/:token/activate',
                component: require('./views/auth/Activate.vue'),
                meta: {title: appName + ' | ' + i18n.auth.two_factor_code}
            },
            {
                path: '/password/reset/:token',
                component: require('./views/auth/Reset.vue'),
                meta: {title: appName + ' | ' + i18n.passwords.reset_password}
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
                meta: {title: appName + ' | ' + i18n.auth.two_factor_code}
            },
            {
                path: '/auth/lock',
                component: require('./views/auth/Lock.vue'),
                meta: {title: appName + ' | ' + i18n.auth.lock_screen}
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
                meta: {title: appName + ' | ' + i18n.general.home}
            },
            {
                path: '/home',
                component: require('./views/pages/Home.vue'),
                meta: {title: appName + ' | ' + i18n.general.home}
            },
            {
                path: '/profile',
                component: require('./views/pages/Profile.vue'),
                meta: {title: appName + ' | ' + i18n.user.profile}
            },
            {
                path: '/change-password',
                component: require('./views/pages/ChangePassword.vue'),
                meta: {title: appName + ' | ' + i18n.auth.change_password}
            },
            {
                path: '/configuration',
                component: require('./views/configuration/system/System.vue'),
                meta: {title: appName + ' | ' + i18n.configuration.system}
            },
            {
                path: '/configuration/image',
                component: require('./views/configuration/image/Image.vue'),
                meta: {title: appName + ' | ' + i18n.configuration.configuration + ' - ' + i18n.general.image}
            },
            {
                path: '/configuration/system',
                component: require('./views/configuration/system/System.vue'),
                meta: {title: appName + ' | ' + i18n.configuration.system}
            },
            {
                path: '/configuration/role',
                component: require('./views/configuration/role/Role.vue'),
                meta: {title: appName + ' | ' + i18n.configuration.configuration + ' - ' + i18n.role.role}
            },
            {
                path: '/configuration/authentication',
                component: require('./views/configuration/authentication/Auth.vue'),
                meta: {title: appName + ' | ' + i18n.configuration.configuration + ' - ' + i18n.auth.authentication}
            },
            {
                path: '/configuration/permission',
                component: require('./views/configuration/permission/Permission.vue'),
                meta: {title: appName + ' | ' + i18n.configuration.configuration + ' - ' + i18n.permission.permission}
            },
            {
                path: '/configuration/permission/assign',
                component: require('./views/configuration/permission/Assign.vue'),
                meta: {title: appName + ' | ' + i18n.configuration.configuration + ' - ' + i18n.permission.assign_permission}
            },
            {
                path: '/configuration/locale',
                component: require('./views/configuration/locale/Locale.vue'),
                meta: {title: appName + ' | ' + i18n.configuration.configuration + ' - ' + i18n.locale.locale}
            },
            {
                path: '/configuration/locale/:id/edit',
                component: require('./views/configuration/locale/Edit.vue'),
                meta: {title: appName + ' | ' + i18n.configuration.configuration + ' - ' + i18n.locale.edit_locale}
            },
            {
                path: '/activity-log',
                component: require('./views/activity-log/ActivityLog.vue'),
                meta: {title: appName + ' | ' + i18n.activity.activity_log}
            },
            {
                path: '/user',
                component: require('./views/user/User.vue'),
                meta: {title: appName + ' | ' + i18n.user.user}
            },
            {
                path: '/user/:id',
                component: require('./views/user/Basic.vue'),
                meta: {title: appName + ' | ' + i18n.user.user + ' - ' + i18n.general.basic}
            },
            {
                path: '/user/:id/basic',
                component: require('./views/user/Basic.vue'),
                meta: {title: appName + ' | ' + i18n.user.user + ' - ' + i18n.general.basic}
            },
            {
                path: '/user/:id/contact',
                component: require('./views/user/Contact.vue'),
                meta: {title: appName + ' | ' + i18n.user.user + ' - ' + i18n.user.contact}
            },
            {
                path: '/user/:id/avatar',
                component: require('./views/user/Avatar.vue'),
                meta: {title: appName + ' | ' + i18n.user.user + ' - ' + i18n.user.avatar}
            },
            {
                path: '/user/:id/password',
                component: require('./views/user/Password.vue'),
                meta: {title: appName + ' | ' + i18n.user.user + ' - ' + i18n.user.password}
            },
        ]
    },
    {
        path: '/',
        component: require('./layouts/ErrorPage.vue'),
        children: [
            {
                path: '/maintenance',
                component: require('./views/errors/Maintenance.vue'),
                meta: {title: appName + ' | ' + i18n.general.permission_denied}
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
                meta: {title: appName + ' | ' + i18n.general.page_not_found_heading}
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
            if (helper.isAuth() && !helper.hasRole('admin') && helper.getConfig('maintenance_mode') && to.fullPath !== '/maintenance')
                return next({path: '/maintenance'});
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
