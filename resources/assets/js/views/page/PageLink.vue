<template>
        <li class="page-nav-item">
            <router-link @click.native="showSpinner" :to="`${page.slug}`">
                {{ page.title }}
            </router-link>
        </li>
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
