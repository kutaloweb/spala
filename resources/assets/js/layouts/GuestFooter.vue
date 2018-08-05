<template>
    <footer class="footer d-print-none text-center" style="left:0">
        {{ getConfig('footer_credit') }}
    </footer>
</template>

<script>
    export default {
        methods: {
            getConfig(config) {
                return helper.getConfig(config);
            }
        }
    }
</script>
