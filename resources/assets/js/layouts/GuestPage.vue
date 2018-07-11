<template>
    <div>
        <router-view></router-view>
    </div>
</template>

<script>
    export default {
        mounted() {
            helper.notification();
        }
    }
</script>
