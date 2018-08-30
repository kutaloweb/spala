export const getAuthUser = (state) => (name) => {
    return state.auth[name];
};

export const getAuthStatus = (state) => {
    return state.is_auth;
};

export const hasRole = (state) => (name) => {
    return state.auth.roles.indexOf(name) >= 0
};

export const getConfig = (state) => (name) => {
    return state.config[name];
};

export const hasPermission = (state) => (name) => {
    return state.permissions.indexOf(name) > -1;
};

export const getTwoFactorCode = (state) => {
    return state.two_factor_code;
};

export const getLastActivity = (state) => {
    return state.last_activity;
};

export const getDefaultRole = (state) => (name) => {
    return state.default_role[name];
};

export const getSearchQuery = (state) => {
    return state.search_query;
};

export const getSearchCategory = (state) => {
    return state.search_category_id;
};

export default {
    getAuthUser,
    getAuthStatus,
    hasRole,
    getConfig,
    hasPermission,
    getTwoFactorCode,
    getLastActivity,
    getDefaultRole,
    getSearchQuery,
    getSearchCategory
};