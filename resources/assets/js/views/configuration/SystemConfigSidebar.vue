<template>
    <div class="col-2 col-lg-2 col-md-2">
        <div class="list-group">
            <router-link to="/configuration/system" :class="[menu === 'system' ? 'active' : '', 'list-group-item']">
                <span class="hidden-sm-up"><i class="fas fa-cogs"></i> </span>
                <span class="hidden-xs-down">{{ trans('general.system') }}</span>
            </router-link>
            <router-link to="/configuration/authentication"
                         :class="[menu === 'authentication' ? 'active' : '', 'list-group-item']">
                <span class="hidden-sm-up"><i class="fas fa-key"></i> </span>
                <span class="hidden-xs-down">{{ trans('auth.authentication') }}</span>
            </router-link>
            <router-link v-if="getConfig('multilingual')" to="/configuration/locale"
                         :class="[menu === 'locale' ? 'active' : '', 'list-group-item']">
                <span class="hidden-sm-up"><i class="fas fa-globe"></i> </span>
                <span class="hidden-xs-down">{{ trans('locale.locale') }}</span>
            </router-link>
            <router-link to="/configuration/image" :class="[menu === 'image' ? 'active' : '', 'list-group-item']">
                <span class="hidden-sm-up"><i class="fas fa-image"></i> </span>
                <span class="hidden-xs-down">{{ trans('general.image') }}</span>
            </router-link>
            <router-link to="/configuration/role" :class="[menu === 'role' ? 'active' : '', 'list-group-item']">
                <span class="hidden-sm-up"><i class="fas fa-users"></i> </span>
                <span class="hidden-xs-down">{{ trans('role.role') }}</span>
            </router-link>
            <router-link to="/configuration/permission"
                         :class="[menu === 'permission' ? 'active' : '', 'list-group-item']">
                <span class="hidden-sm-up"><i class="fas fa-users"></i> </span>
                <span class="hidden-xs-down">{{ trans('permission.permission') }}</span>
            </router-link>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            menu: {
                default: ''
            }
        },
        methods: {
            getConfig(config) {
                return helper.getConfig(config);
            }
        }
    }
</script>
