<template>
    <router-link @click.native="showSpinner" :to="`${page.slug}`">
        <b class="mr-5 ml-5 page-title">{{ page.title }}</b>
    </router-link>
</template>

<script>
    export default {
        props: ['page'],
        methods: {
            showSpinner() {
                return helper.showSpinner();
            }
        }
    }
</script>
