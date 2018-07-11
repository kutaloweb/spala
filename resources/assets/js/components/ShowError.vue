<template>
    <span class="help has-error" v-if="formName.errors.has(propName)" v-text="formName.errors.get(propName)"></span>
</template>

<script>
    export default {
        props: ['formName', 'propName']
    }
</script>