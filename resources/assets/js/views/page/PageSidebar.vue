<template>
    <div class="col-2 col-lg-2 col-md-2">
        <div class="list-group">
            <router-link to="/page/new" :class="[menu === 'new' ? 'active' : '', 'list-group-item']">
                <span class="hidden-sm-up"><i class="far fa-plus-square"></i></span>
                <span class="hidden-xs-down">
                    {{ trans('page.new') }}
                </span>
            </router-link>
            <router-link to="/page/published" :class="[menu === 'published' ? 'active' : '', 'list-group-item']">
                <span class="hidden-sm-up"><i class="fas fa-columns"></i></span>
                <span class="hidden-xs-down">
                    {{ trans('page.published_box') }}
                    <span v-if="statistics.published">
                        ({{ statistics.published }})
                    </span>
                </span>
            </router-link>
        </div>
    </div>
</template>
2018_07_13_124411_create_posts_table.php
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
