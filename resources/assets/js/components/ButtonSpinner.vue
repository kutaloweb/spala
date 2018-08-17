<template>
    <button class="spinner-btn">
        <i v-if="loading" class="fas fa-spinner fa-spin"></i>
        <span v-else>{{ btnText }}</span>
    </button>
</template>

<script>
    export default {
        props: {
            isLoading: {
                type: Boolean,
                default: false
            },
            btnText: {
                type: String,
                default: ''
            }
        },
        computed: {
            loading() {
                return this.isLoading
            }
        }
    }
</script>