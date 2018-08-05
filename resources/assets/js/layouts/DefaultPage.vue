<template>
    <div id="main-wrapper">
        <app-header></app-header>
        <app-sidebar></app-sidebar>
        <div class="page-wrapper">
            <div class="container-fluid">
                <router-view></router-view>
            </div>
            <app-footer></app-footer>
        </div>
    </div>
</template>

<script>
    import AppHeader from './Header'
    import AppSidebar from './Sidebar'
    import AppFooter from './Footer'

    export default {
        data() {
            return {}
        },
        components: {
            AppHeader, AppSidebar, AppFooter
        },
        mounted() {
            helper.notification();
            $('body').addClass("card-no-border").trigger("resize");
            $(".scroll-sidebar, .slimScrollDiv").css("overflow-x", "visible").parent().css("overflow", "visible");
            $('#sidebarnav').metisMenu();
            $('.scroll-sidebar').slimScroll({
                position: 'left', size: "5px", height: '100%', color: '#dcdcdc'
            });
            $('.message-scroll').slimScroll({
                position: 'right', size: "5px", height: '70px', color: '#dcdcdc'
            });
        }
    }
</script>
