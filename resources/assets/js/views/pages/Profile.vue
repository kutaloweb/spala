<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor m-b-0 m-t-0">{{ trans('user.profile') }}</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <router-link to="/home">{{ trans('general.home') }}</router-link>
                    </li>
                    <li class="breadcrumb-item active">{{ trans('user.profile') }}</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ trans('user.update_profile') }}</h4>
                        <form @submit.prevent="updateProfile" @keydown="userForm.errors.clear($event.target.name)">
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('user.first_name') }}</label>
                                        <input class="form-control" type="text" name="first_name"
                                               v-model="userForm.first_name">
                                        <show-error :form-name="userForm" prop-name="first_name"></show-error>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('user.last_name') }}</label>
                                        <input class="form-control" type="text" name="last_name"
                                               v-model="userForm.last_name">
                                        <show-error :form-name="userForm" prop-name="last_name"></show-error>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('user.gender') }}</label>
                                        <div class="radio radio-info" v-for="gender in genders">
                                            <input type="radio" :value="gender.id" :id="gender.id"
                                                   v-model="userForm.gender"
                                                   :checked="userForm.gender == gender.id" name="gender"
                                                   @click="userForm.errors.clear('gender')">
                                            <label :for="gender.id"> {{ trans('list.' + gender.id) }} </label>
                                        </div>
                                        <show-error :form-name="userForm" prop-name="gender"></show-error>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('user.date_of_birth') }}</label>
                                        <datepicker :language="locale"
                                                    :monday-first="true"
                                                    v-model="userForm.date_of_birth"
                                                    :bootstrapStyling="true"
                                                    name="date_of_birth"
                                                    @selected="userForm.errors.clear('date_of_birth')">
                                        </datepicker>
                                        <show-error :form-name="userForm" prop-name="date_of_birth"></show-error>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('user.phone') }}</label>
                                        <input class="form-control" type="text" value=""
                                               v-model="userForm.phone" name="phone"
                                               :placeholder="trans('user.phone')">
                                        <show-error :form-name="userForm" prop-name="phone"></show-error>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('user.address_line_1') }}</label>
                                        <input class="form-control" type="text" value=""
                                               v-model="userForm.address_line_1" name="address_line_1"
                                               :placeholder="trans('user.address_line_1')">
                                        <show-error :form-name="userForm" prop-name="address_line_1"></show-error>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('user.address_line_2') }}</label>
                                        <input class="form-control" type="text" value=""
                                               v-model="userForm.address_line_2" name="address_line_2"
                                               :placeholder="trans('user.address_line_2')">
                                        <show-error :form-name="userForm" prop-name="address_line_2"></show-error>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('user.city') }}</label>
                                        <input class="form-control" type="text" value=""
                                               v-model="userForm.city" name="city"
                                               :placeholder="trans('user.city')">
                                        <show-error :form-name="userForm" prop-name="city"></show-error>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('user.state') }}</label>
                                        <input class="form-control" type="text" value=""
                                               v-model="userForm.state" name="state"
                                               :placeholder="trans('user.state')">
                                        <show-error :form-name="userForm" prop-name="state"></show-error>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('user.zipcode') }}</label>
                                        <input class="form-control" type="text" value=""
                                               v-model="userForm.zipcode" name="zipcode"
                                               :placeholder="trans('user.zipcode')">
                                        <show-error :form-name="userForm" prop-name="zipcode"></show-error>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label>{{ trans('user.country') }}</label>
                                        <select class="form-control custom-select" name="country_id"
                                                v-model="userForm.country_id">
                                            <option value="">{{ trans('user.country') }}</option>
                                            <option v-for="country in countries"
                                                    v-bind:value="country.value">
                                                {{ country.text }}
                                            </option>
                                        </select>
                                        <show-error :form-name="userForm" prop-name="country_id"></show-error>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-info waves-effect waves-light m-t-10 pull-right">
                                {{ trans('general.save') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">{{ trans('user.avatar') }}</h4>
                        <upload-image
                                id="avatar"
                                :upload-path="`/user/profile/avatar/${getAuthUser('id')}`"
                                :remove-path="`/user/profile/avatar/remove/${getAuthUser('id')}`"
                                :image-source="avatar.user" @uploaded="updateAvatar"
                                 @removed="updateAvatar">
                        </upload-image>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import datepicker from 'vuejs-datepicker'
    import uploadImage from '../../components/UploadImage'

    export default {
        components: {datepicker, uploadImage},
        data() {
            return {
                genders: [],
                userForm: new Form({
                    first_name: '',
                    last_name: '',
                    date_of_birth: '',
                    gender: '',
                    phone: '',
                    address_line_1: '',
                    address_line_2: '',
                    city: '',
                    state: '',
                    zipcode: '',
                    country_id: ''
                }, false),
                avatar: {
                    user: ''
                },
                locale: helper.getConfig('locale'),
                countries: []
            };
        },
        mounted() {
            axios.get('/api/fetch/lists?lists=country')
                .then(response => response.data)
                .then(response => {
                    this.countries = response.data.country;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });
            axios.get('/api/fetch/lists?lists=gender')
                .then(response => response.data)
                .then(response => {
                    this.genders = response.data.gender;
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                });
            helper.showSpinner();
            axios.get('/api/user/detail')
                .then(response => response.data)
                .then(response => {
                    this.userForm.first_name = response.profile.first_name;
                    this.userForm.last_name = response.profile.last_name;
                    this.userForm.date_of_birth = response.profile.date_of_birth;
                    this.userForm.gender = response.profile.gender;
                    this.avatar.user = response.profile.avatar;
                    this.userForm.phone = response.profile.phone;
                    this.userForm.address_line_1 = response.profile.address_line_1;
                    this.userForm.address_line_2 = response.profile.address_line_2;
                    this.userForm.city = response.profile.city;
                    this.userForm.state = response.profile.state;
                    this.userForm.zipcode = response.profile.zipcode;
                    this.userForm.country_id = response.profile.country_id || '';
                    helper.hideSpinner();
                })
                .catch(error => {
                    helper.showDataErrorMsg(error);
                    helper.hideSpinner();
                });
        },
        methods: {
            updateProfile() {
                this.userForm.date_of_birth = (this.userForm.date_of_birth) ? moment(this.userForm.date_of_birth).format('YYYY-MM-DD') : null;
                this.userForm.post('/api/user/profile/update')
                    .then(response => {
                        toastr.success(response.message);
                        this.$store.dispatch('setAuthUserDetail', {
                            first_name: this.userForm.first_name,
                            last_name: this.userForm.last_name
                        });
                    })
                    .catch(error => {
                        helper.showErrorMsg(error);
                    });
            },
            updateAvatar(val) {
                this.$store.dispatch('setAuthUserDetail', {
                    avatar: val
                });
            },
            getAuthUser(name) {
                return helper.getAuthUser(name);
            }
        }
    }
</script>
