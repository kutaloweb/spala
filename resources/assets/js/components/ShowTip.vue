<template>
    <span>
        <p class="alert alert-info" v-if="!type">
            <i class="fas fa-exclamation-triangle"></i> {{ trans(module + '.' + tip) }}
        </p>
        <i class="fas fa-exclamation-circle" v-if="type" v-tooltip="trans(module + '.' + tip)"></i>
    </span>
</template>

<script>
    export default {
        props: ['tip', 'module', 'type']
    }
</script>
