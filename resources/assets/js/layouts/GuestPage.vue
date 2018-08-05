<template>
    <div id="main-wrapper">
        <app-header :toggle="false"></app-header>
        <div class="page-wrapper" style="margin-left:0">
            <router-view></router-view>
            <back-to-top visibleOffset="200"></back-to-top>
            <app-footer></app-footer>
        </div>
    </div>
</template>

<script>
    import AppHeader from './Header'
    import AppFooter from './GuestFooter'
    import BackToTop from '../components/BackToTop.vue'

    export default {
        data() {
            return {}
        },
        components: {
            AppHeader,
            AppFooter,
            BackToTop
        },
        mounted() {
            helper.notification();
        }
    }
</script>
