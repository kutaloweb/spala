<template>
    <div>
        <p class="text-center">
            <small>{{ getConfig('footer_credit') }}</small>
        </p>
    </div>
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