import Vue from 'vue'
import Vuex from 'vuex'
import getters from './getters'
import actions from './actions'
import createPersistedState from 'vuex-persistedstate'

Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        auth: {
            id: '',
            first_name: '',
            last_name: '',
            email: '',
            avatar: '',
            roles: [],
        },
        is_auth: false,
        two_factor_code: null,
        config: {},
        permissions: [],
        last_activity: null,
        default_role: {
            admin: '',
            user: ''
        },
        search_query: '',
        search_category_id: ''
    },
    mutations: {
        setAuthStatus(state) {
            state.is_auth = true;
        },
        setAuthUserDetail(state, auth) {
            for (let key of Object.keys(auth)) {
                state.auth[key] = auth[key] !== null ? auth[key] : '';
            }
            if ('avatar' in auth)
                state.auth.avatar = auth.avatar !== null ? auth.avatar : '';
            state.is_auth = true;
            state.auth.roles = auth.roles;
        },
        resetAuthUserDetail(state) {
            for (let key of Object.keys(state.auth)) {
                state.auth[key] = '';
            }
            state.is_auth = false;
            state.auth.roles = [];
            state.last_activity = null;
            localStorage.removeItem('auth_token');
            axios.defaults.headers.common['Authorization'] = null;
        },
        setConfig(state, config) {
            for (let key of Object.keys(config)) {
                state.config[key] = config[key];
            }
        },
        resetConfig(state) {
            for (let key of Object.keys(state.config)) {
                state.config[key] = '';
            }
        },
        setPermission(state, data) {
            state.permissions = [];
            data.forEach(permission => state.permissions.push(permission.name));
        },
        setTwoFactorCode(state, data) {
            state.two_factor_code = data;
        },
        resetTwoFactorCode(state) {
            state.two_factor_code = null;
        },
        setLastActivity(state) {
            state.last_activity = moment().format();
        },
        setDefaultRole(state, data) {
            state.default_role = data;
        },
        setSearchQuery(state, data) {
            state.search_query = data;
        },
        setSearchCategory(state, data) {
            state.search_category_id = data;
        }
    },
    actions,
    getters,
    plugins: [
        createPersistedState({storage: window.sessionStorage})
    ]
});

export default store;
