<template>
    <router-link class="card" :to="`${post.category.name}/${post.slug}`">
        <div class="card-img"
             :class="[post.cover !== 'uploads/images/cover-default.png' ? 'cover-img' : '']">
            <img class="card-img-top img-responsive" :src="post.cover" :alt="post.title">
        </div>
        <div class="card-body">
            <h3 class="card-title post-title">{{ post.title }}</h3>
            <h5 class="card-text">{{ limitWords(post.stripped_body, 35) }}</h5>
            <p class="card-text">
                <small class="text-muted card-caps">
                    {{ toWord(post.category.name) }} / {{ post.created_at }}
                </small>
            </p>
        </div>
    </router-link>
</template>

<script>
    export default {
        props: ['post'],
        methods: {
            limitWords(textToLimit, wordLimit) {
                let finalText = "";
                let text2 = textToLimit.replace(/\s+/g, ' ');
                let text3 = text2.split(' ');
                let numberOfWords = text3.length;
                let i = 0;

                if (numberOfWords > wordLimit) {
                    for (i = 0; i < wordLimit; i++) {
                        finalText = finalText + " " + text3[i] + " ";
                    }

                    return finalText + "...";
                }

                return textToLimit;
            },
            toWord(str) {
                return helper.toWord(str);
            }
        }
    }
</script>
