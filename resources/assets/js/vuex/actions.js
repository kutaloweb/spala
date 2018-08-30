export const setAuthStatus = ({commit}) => {
    commit('setAuthStatus');
};
export const setAuthUserDetail = ({commit}, auth) => {
    commit('setAuthUserDetail', auth);
};
export const resetAuthUserDetail = ({commit}) => {
    commit('resetAuthUserDetail');
};
export const setConfig = ({commit}, data) => {
    commit('setConfig', data);
};
export const setPermission = ({commit}, data) => {
    commit('setPermission', data);
};
export const resetConfig = ({commit}) => {
    commit('resetConfig', data);
};
export const setTwoFactorCode = ({commit}, data) => {
    commit('setTwoFactorCode', data);
};
export const resetTwoFactorCode = ({commit}) => {
    commit('resetTwoFactorCode');
};
export const setLastActivity = ({commit}) => {
    commit('setLastActivity');
};
export const setDefaultRole = ({commit}, data) => {
    commit('setDefaultRole', data)
};
export const setSearchQuery = ({commit}, data) => {
    commit('setSearchQuery', data)
};
export const setSearchCategory = ({commit}, data) => {
    commit('setSearchCategory', data)
};

export default {
    setAuthStatus,
    setAuthUserDetail,
    resetAuthUserDetail,
    setConfig,
    setPermission,
    resetConfig,
    setTwoFactorCode,
    resetTwoFactorCode,
    setLastActivity,
    setDefaultRole,
    setSearchQuery,
    setSearchCategory
};