<template>
    <section id="wrapper" class="error-page">
        <div class="error-box">
            <div class="error-body text-center">
                <h1 class="text-muted m-t-30 m-b-30">{{ trans('general.page_not_found_error') }}</h1>
                <h2 class="text-muted m-t-30 m-b-30 text-uppercase">{{ trans('general.page_not_found_heading') }}</h2>
                <h2 class="text-muted m-t-30 m-b-30">{{ trans('general.page_not_found_message') }}</h2>
                <router-link to="/" class="btn btn-info btn-rounded waves-effect waves-light m-b-40">
                    {{ trans('general.back_to_home') }}
                </router-link>
            </div>
        </div>
    </section>
</template>

