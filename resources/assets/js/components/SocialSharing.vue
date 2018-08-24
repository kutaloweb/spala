<template>
    <social-sharing :url="url" :title="title" inline-template>
        <div>
            <div class="parallel-label">{{ trans('general.share') }}</div>
            <div class="social-icons">
                <a href="#" class="social-icon">
                    <network network="facebook">
                        <i class="fab fa-facebook-f"></i>
                    </network>
                </a>
                <a href="#" class="social-icon">
                    <network network="twitter">
                        <i class="fab fa-twitter"></i>
                    </network>
                </a>
                <a href="#" class="social-icon">
                    <network network="whatsapp">
                        <i class="fab fa-whatsapp"></i>
                    </network>
                </a>
                <a href="#" class="social-icon">
                    <network network="telegram">
                        <i class="fab fa-telegram"></i>
                    </network>
                </a>
                <a href="#" class="social-icon">
                    <network network="email">
                        <i class="fas fa-envelope"></i>
                    </network>
                </a>
            </div>
        </div>
    </social-sharing>
</template>

<script>
    const SocialSharing = require('vue-social-sharing');

    export default {
        name: 'socialSharingComp',
        props: {
            url: {
                type: String,
                required: true
            },
            title: {
                type: String,
                required: true
            }
        },
        components: {
            'social-sharing': SocialSharing
        }
    }
</script>