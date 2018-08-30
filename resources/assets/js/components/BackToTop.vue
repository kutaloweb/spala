<template>
    <transition name="scroll-to-top-fade">
        <div class="scroll-to-top" v-show="visible" @click="backToTop">
        <span>
            <i class="fas fa-chevron-up"></i>
        </span>
        </div>
    </transition>
</template>

<script>
    export default {
        props: {
            visibleOffset: {
                type: [String, Number],
                default: 600
            }
        },
        data () {
            return {
                visible: false
            }
        },
        created () {
            let catchScroll = () => {
                this.visible = (window.pageYOffset > parseInt(this.visibleOffset));
            };
            window.smoothscroll = () => {
                let currentScroll = document.documentElement.scrollTop || document.body.scrollTop;
                if (currentScroll > 0) {
                    window.requestAnimationFrame(window.smoothscroll);
                    window.scrollTo(0, currentScroll - (currentScroll / 5));
                }
            };
            window.onscroll = catchScroll;
        },
        methods: {
            backToTop () {
                window.smoothscroll();
            }
        }
    }
</script>

<style>
    .scroll-to-top-fade-enter-active, .scroll-to-top-fade-leave-active {
        transition: opacity .7s;
    }
    .scroll-to-top-fade-enter, .scroll-to-top-fade-leave-to {
        opacity: 0;
    }
    .scroll-to-top {
        position: fixed;
        bottom: 10px;
        right: 10px;
        line-height: 40px;
        width: 40px;
        color: #ffffff;
        text-align: center;
        background: rgba(100, 100, 100, 0.4);
        border-radius: 3px;
        z-index: 1000;
        cursor: pointer;
    }
    .scroll-to-top:hover {
        background-color: #ccc;
    }
</style>