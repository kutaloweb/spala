<template>
    <div id="main-wrapper">
        <app-header :toggle="false"></app-header>
        <div class="page-wrapper" style="margin-left:0">
            <router-view></router-view>
            <app-footer></app-footer>
        </div>
    </div>
</template>

<script>
    import AppHeader from './Header'
    import AppFooter from './GuestFooter'

    export default {
        data() {
            return {}
        },
        components: {
            AppHeader,
            AppFooter
        },
        mounted() {
            helper.notification();
        }
    }
</script>
