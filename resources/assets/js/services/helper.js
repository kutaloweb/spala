import store from '../vuex/store'
import router from '../routes'

export default {

    logout() {
        return axios.post('/api/auth/logout')
            .then(response => response.data)
            .catch(error => {
                this.showDataErrorMsg(error);
            });
    },

    authCheck() {
        return axios.post('/api/auth/check')
            .then(response => response.data)
            .then(response => {
                store.dispatch('setConfig', response.config);
                if (response.ip_restricted)
                    localStorage.setItem('ip_restricted', true);
                else
                    localStorage.removeItem('ip_restricted');
                if (response.authenticated) {
                    store.dispatch('setAuthUserDetail', {
                        id: response.user.id,
                        first_name: response.user.profile.first_name,
                        last_name: response.user.profile.last_name,
                        email: response.user.email,
                        avatar: response.user.profile.avatar,
                        roles: response.user_roles
                    });
                    store.dispatch('setPermission', response.permissions);
                    store.dispatch('setDefaultRole', response.default_role);
                    this.setLastActivity();
                } else {
                    store.dispatch('resetAuthUserDetail');
                }

                return response.authenticated;
            }).catch(error => {
                store.dispatch('resetAuthUserDetail');
                store.dispatch('resetConfig');
            });
    },

    notification() {
        let notificationPosition = this.getConfig('notification_position') || 'toast-top-right';
        toastr.options = {
            "positionClass": notificationPosition
        };
        this.setLastActivity();
        $('[data-toastr]').on('click', function () {
            let type = $(this).data('toastr'), message = $(this).data('message'), title = $(this).data('title');
            toastr[type](message, title);
        });
    },

    setLastActivity() {
        if (!this.isScreenLocked()) {
            store.dispatch('setLastActivity');
        }
    },

    isScreenLocked() {
        let lastActivity = this.getLastActivity();
        let lockScreenTimeout = this.getConfig('lock_screen_timeout');
        let lastActivityAfterTimeout = moment(lastActivity).add(lockScreenTimeout, 'minutes').format('LLL');

        return (moment().format('LLL') > lastActivityAfterTimeout);
    },

    getLastActivity() {
        return store.getters.getLastActivity;
    },

    getFilterURL(data) {
        let url = '';
        $.each(data, function (key, value) {
            url += (value) ? '&' + key + '=' + encodeURI(value) : '';
        });

        return url;
    },

    getTwoFactorCode() {
        return store.getters.getTwoFactorCode;
    },

    isAuth() {
        return store.getters.getAuthStatus;
    },

    getAuthUser(name) {
        if (name === 'full_name') {
            return store.getters.getAuthUser('first_name') + ' ' + store.getters.getAuthUser('last_name');
        }
        else if (name === 'avatar') {
            if (store.getters.getAuthUser('avatar')) {
                return '/' + store.getters.getAuthUser('avatar');
            } else {
                return '/uploads/avatar/avatar.png';
            }
        } else {
            return store.getters.getAuthUser(name);
        }
    },

    getAvatar(user) {
        if (user && user.profile.avatar)
            return '/' + user.profile.avatar;
        else
            return '/uploads/avatar/avatar.png';
    },

    getConfig(config) {
        return store.getters.getConfig(config);
    },

    getDefaultRole(role) {
        return store.getters.getDefaultRole(role);
    },

    hasRole(role) {
        return store.getters.hasRole(this.getDefaultRole(role));
    },

    hasPermission(permission) {
        return store.getters.hasPermission(permission);
    },

    getSearchQuery() {
        return store.getters.getSearchQuery;
    },

    getSearchCategory() {
        return store.getters.getSearchCategory;
    },

    userHasRole(user, roleName) {
        if (!user.roles) {
            return false;
        }
        let userRole = user.roles.filter(role => role.name === this.getDefaultRole(roleName));

        return !!userRole.length;
    },

    featureAvailable(feature) {
        return this.getConfig(feature);
    },

    notAccessibleMsg() {
        toastr.error(i18n.permission.permission_denied);
    },

    featureNotAvailableMsg() {
        toastr.error(i18n.general.feature_not_available);
    },

    getUserStatus(user) {
        let status = [];
        if (user.status === 'activated')
            status.push({'color': 'success', 'label': i18n.user.status_activated});
        else if (user.status === 'pending_activation')
            status.push({'color': 'warning', 'label': i18n.user.status_pending_activation});
        else if (user.status === 'pending_approval')
            status.push({'color': 'warning', 'label': i18n.user.status_pending_approval});
        else if (user.status === 'banned')
            status.push({'color': 'danger', 'label': i18n.user.status_banned});
        else if (user.status === 'disapproved')
            status.push({'color': 'danger', 'label': i18n.user.status_disapproved});

        return status;
    },

    formAssign(form, data) {
        for (let key of Object.keys(form)) {
            if (key !== "originalData" && key !== "errors" && key !== "autoReset" && key !== "providers") {
                form[key] = data[key] || '';
            }
        }

        return form;
    },

    toWord(value) {
        if (!value) return;

        value = value.replace(/-/g, ' ');
        value = value.replace(/_/g, ' ');

        return value.toLowerCase().replace(/\b[a-z]/g, function (value) {
            return value.toUpperCase();
        });
    },

    showDataErrorMsg(error) {
        this.setLastActivity();
        if (error.hasOwnProperty("error")) {
            toastr.error(i18n.general[error.error]);
            if (error.error === 'token_expired') {
                router.push('/login');
            }
        } else if (error.hasOwnProperty("response") && error.response.status === 403) {
            toastr.error(i18n.general.permission_denied);
        } else if (error.hasOwnProperty("response") && error.response.status === 500) {
            toastr.error('500 Internal Server Error');
        } else if (error.response && error.response.data.errors.hasOwnProperty("message")) {
            toastr.error(error.response.data.errors.message[0]);
        } else if (error) {
            console.log(error);
        }
    },

    fetchDataErrorMsg(error) {
        return error.response.data.errors.message[0];
    },

    showErrorMsg(error) {
        this.setLastActivity();
        if (error.hasOwnProperty("error")) {
            toastr.error(i18n.general[error.error]);
            if (error.error === 'token_expired') {
                router.push('/login');
            }
        } else if (error.hasOwnProperty("response") && error.response.status === 403) {
            toastr.error(i18n.general.permission_denied);
        } else if (error.hasOwnProperty("response") && error.response.status === 500) {
            toastr.error('500 Internal Server Error');
        } else if (error.errors.hasOwnProperty("message"))
            toastr.error(error.errors.message[0]);
    },

    limitWords(textToLimit, wordLimit) {
        if (!textToLimit) return;
        let finalText = "";
        let text2 = textToLimit.replace(/\s+/g, ' ');
        let text3 = text2.split(' ');
        let numberOfWords = text3.length;
        let i = 0;

        if (numberOfWords > wordLimit) {
            for (i = 0; i < wordLimit; i++) {
                finalText = finalText + " " + text3[i] + " ";
            }

            return finalText + "...";
        }

        return textToLimit;
    },

    showSpinner() {
        document.getElementsByClassName("preloader")[0].style.display = "block";
    },

    hideSpinner() {
        document.getElementsByClassName("preloader")[0].style.display = "none";
    }
}
