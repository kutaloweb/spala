<template>
    <form @submit.prevent="proceed" @keydown="localeForm.errors.clear($event.target.name)">
        <div class="form-group">
            <label>{{ trans('locale.name') }}</label>
            <input class="form-control" type="text" value="" v-model="localeForm.name" name="name"
                   :placeholder="trans('locale.name')">
            <show-error :form-name="localeForm" prop-name="name"></show-error>
        </div>
        <div class="form-group">
            <label>{{ trans('locale.locale') }}</label>
            <input class="form-control" type="text" value="" v-model="localeForm.locale" name="locale"
                   :placeholder="trans('locale.locale')">
            <show-error :form-name="localeForm" prop-name="locale"></show-error>
        </div>
        <button type="submit" class="btn btn-info waves-effect waves-light pull-right">
            <span v-if="id">{{ trans('general.update') }}</span>
            <span v-else>{{ trans('general.save') }}</span>
        </button>
        <router-link to="/configuration/locale" class="btn btn-danger waves-effect waves-light pull-right m-r-10"
                     v-show="id">
            {{ trans('general.cancel') }}
        </router-link>
    </form>
</template>

<script>
    export default {
        data() {
            return {
                localeForm: new Form({
                    name: '',
                    locale: ''
                })
            };
        },
        props: ['id'],
        mounted() {
            if (this.id) this.getLocale();
        },
        methods: {
            proceed() {
                if (this.id) {
                    this.updateLocale();
                } else {
                    this.storeLocale();
                }
            },
            storeLocale() {
                this.localeForm.post('/api/locale')
                    .then(response => {
                        toastr.success(response.message);
                        this.$emit('completed')
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            getLocale() {
                helper.showSpinner();
                axios.get('/api/locale/' + this.id)
                    .then(response => response.data)
                    .then(response => {
                        this.localeForm.name = response.name;
                        this.localeForm.locale = response.locale;
                        helper.hideSpinner();
                    })
                    .catch(error => {
                        helper.showDataErrorMsg(error);
                        this.$router.push('/configuration/locale');
                        helper.hideSpinner();
                    });
            },
            updateLocale() {
                this.localeForm.patch('/api/locale/' + this.id)
                    .then(response => {
                        toastr.success(response.message);
                        this.$router.push('/configuration/locale');
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            }
        }
    }
</script>
