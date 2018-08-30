<template>
    <social-sharing :url="url" :title="title" inline-template>
        <div>
            <div class="social-icons row">
                <div class="col-md-3">
                    <network network="facebook">
                        <button class="btn btn-lg btn-block btn-sm social-icon facebook">
                            <i class="fab fa-facebook-f"></i>
                        </button>
                    </network>
                </div>
                <div class="col-md-3">
                    <network network="twitter">
                        <button class="btn btn-lg btn-block btn-sm social-icon twitter">
                            <i class="fab fa-twitter"></i>
                        </button>
                    </network>
                </div>
                <div class="col-md-3">
                    <network network="whatsapp">
                        <button class="btn btn-lg btn-block btn-sm social-icon whatsapp">
                            <i class="fab fa-whatsapp"></i>
                        </button>
                    </network>
                </div>
                <div class="col-md-3">
                    <network network="telegram">
                        <button class="btn btn-lg btn-block btn-sm social-icon telegram">
                            <i class="fab fa-telegram"></i>
                        </button>
                    </network>
                </div>
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