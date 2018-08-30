<template>
    <div class="page-nav-items">
        <ul>
            <page-link v-for="page in items" :page="page" :key="page.id"></page-link>
        </ul>
    </div>
</template>

<script>
    import pageLink from './PageLink'

    export default {
        metaInfo() {
            return {
                title: `${this.getConfig('company_name')}`,
                meta: [
                    {name: 'description', content: this.getConfig('company_description')},
                    {name: 'twitter:card', content: 'summary_large_image'},
                    {name: 'twitter:title', content: this.getConfig('company_name')},
                    {name: 'twitter:description', content: this.getConfig('company_description')},
                    {name: 'twitter:image', content: `${this.getConfig('app_url')}/uploads/images/cover-default.png`},
                    {property: 'og:type', content: 'website'},
                    {property: 'og:site_name', content: this.getConfig('company_name')},
                    {property: 'og:url', content: `${this.getConfig('app_url')}`},
                    {property: 'og:title', content: this.getConfig('company_name')},
                    {property: 'og:description', content: this.getConfig('company_description')},
                    {property: 'og:image', content: `${this.getConfig('app_url')}/uploads/images/cover-default.png`}
                ]
            }
        },
        data() {
            return {
                items: []
            }
        },
        components: {
            pageLink
        },
        mounted() {
            this.getPages();
        },
        methods: {
            getPages(page) {
                axios.get('/api/pages')
                    .then(response => response.data)
                    .then(response => {
                        this.items = response.pages;
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                    });
            },
            getConfig(name) {
                return helper.getConfig(name);
            }
        }
    }
</script>