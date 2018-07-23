<template>
    <div class="col-2 col-lg-2 col-md-2">
        <div class="list-group">
            <router-link to="/post/new" :class="[menu === 'new' ? 'active' : '', 'list-group-item']">
                <span class="hidden-sm-up"><i class="far fa-plus-square"></i></span>
                <span class="hidden-xs-down">
                    {{ trans('post.new') }}
                </span>
            </router-link>
            <router-link to="/post/draft" :class="[menu === 'draft' ? 'active' : '', 'list-group-item']">
                <span class="hidden-sm-up"><i class="fas fa-edit"></i></span>
                <span class="hidden-xs-down">
                    {{ trans('post.drafts') }}
                    <span v-if="statistics.draft">
                        ({{ statistics.draft }})
                    </span>
                </span>
            </router-link>
            <router-link to="/post/published" :class="[menu === 'published' ? 'active' : '', 'list-group-item']">
                <span class="hidden-sm-up"><i class="far fa-share-square"></i></span>
                <span class="hidden-xs-down">
                    {{ trans('post.published_box') }}
                    <span v-if="statistics.published">
                        ({{ statistics.published }})
                    </span>
                </span>
            </router-link>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            menu: {
                default: ''
            },
            statistics: {}
        }
    }
</script>
