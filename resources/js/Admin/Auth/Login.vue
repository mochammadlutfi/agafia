<template>
    
    <el-config-provider namespace="ep" :locale="locale">
        <div id="page-container" class="main-content-boxed">
            <div id="main-container">
                <div class="content content-full">
                    <div class="row justify-content-center py-30 my-30">
                        <div class="col-md-4 col-xl-4 align-items-center">
                            <div class="pt-5 pb-3 text-center">
                                <a href="#">
                                    <img src="/images/logo/logo2.png" class="w-50 mx-auto"/>
                                </a>
                                <h1 class="h3 fw-bold mt-3 mb-2">Masuk Sekarang</h1>
                            </div>
                            <el-form
                                label-position="top"
                                label-width="100%"
                                @submit.prevent="submit"
                            >
                                <div class="block block-rounded block-fx-shadow">
                                    <!-- Database section -->
                                    <div class="block-content block-content-full">
                                        <el-form-item label="Username / Email" :error="errors.email">
                                            <el-input
                                                v-model="form.email"
                                                type="text"
                                                placeholder="Masukan Username / Email"
                                            />
                                        </el-form-item>
                                        <el-form-item label="Masukan password" :error="errors.password">
                                            <el-input
                                                v-model="form.password"
                                                type="password"
                                                placeholder="Masukan password"
                                                show-password
                                            />
                                        </el-form-item>
                                        <el-button native-type="submit" type="primary" class="w-100" :loading="loading">Login Sekarang</el-button>
                                    </div>
                                </div>
                            </el-form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </el-config-provider>
</template>

<script>
import id from 'element-plus/dist/locale/id.mjs';
export default {
    components: {
        ElConfigProvider,
    },
    setup() {
        return {
            zIndex: 3000,
            size: 'small',
            locale : id,
        }
    },
    props: {
        canResetPassword: Boolean,
        status: String,
        errors: Object,
    },
    data() {
        return {
            form: this.$inertia.form({
                'email': '',
                'password': '',
                'remember': false
            }),
            rules: {
                required: value => !!value || 'Required.',
                counter: value => value.length <= 20 || 'Max 20 characters',
                email: value => {
                    const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                    return pattern.test(value) || 'Invalid e-mail.'
                },
            },
            loading : false,
        }
    },

    methods: {
        submit() {
            this.loading = true;
            this.form.post(this.route('admin.login'), {
                onFinish: () => {
                    this.form.reset('password');
                    this.loading = false;
                },
            })
        }
    }
 }

</script>
