<template>
    <router-link @click.native="showSpinner" class="card" :to="`${post.category.slug}/${post.slug}`">
        <div class="card-img"
             :class="[post.cover !== 'uploads/images/cover-default.png' ? 'cover-img' : '']">
            <img class="card-img-top img-responsive" :src="post.cover" :alt="post.title">
        </div>
        <div class="card-body">
            <h3 class="card-title post-title list-title">{{ post.title }}</h3>
            <h5 class="card-text list-text" v-html="limitWords(post.stripped_body)"></h5>
            <p class="card-text">
                <small class="text-muted card-caps">
                    {{ post.category.name }} / {{ post.created_at }}
                </small>
            </p>
        </div>
    </router-link>
</template>

<script>
    export default {
        props: ['post'],
        methods: {
            limitWords(str) {
                return helper.limitWords(str, 35);
            },
            showSpinner() {
                return helper.showSpinner();
            }
        }
    }
</script>
