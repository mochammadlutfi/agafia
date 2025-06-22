<template>
    <base-layout>
        <div class="content">
            <el-row justify="center" class="mt-md-5">
                <el-col :md="10">
                    <el-card>
                        <h2 class="h3 fw-bold mt-4 mb-2">Masuk</h2>
                        <p>Belum Punya Akun ?<a :href="route('register')" class="fw-bold"> Daftar Sekarang</a></p>
                        <el-form
                                label-position="top"
                                label-width="100%"
                                @submit.prevent="submit"
                            >
                            <el-form-item label="Email" :error="errors.email">
                                <el-input v-model="form.email" type="text" placeholder="Masukan Email"/>
                            </el-form-item>
                            <el-form-item label="Password" :error="errors.password">
                                <el-input
                                    v-model="form.password"
                                    type="password"
                                    placeholder="Masukan password"
                                    show-password
                                />
                            </el-form-item>
                            <el-button native-type="submit" type="primary" class="w-100" :loading="loading">
                                Masuk
                            </el-button>
                        </el-form>
                    </el-card>
                </el-col>
            </el-row>
                
        </div>
    </base-layout>
</template>

<script>
export default {
    props: {
        canResetPassword: Boolean,
        status: String,
        errors: Object,
    },
    data() {
        return {
            form: this.$inertia.form({
                'nama': '',
                'email': '',
                'password': '',
                'password_confirmation': '',
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
            this.form.post(this.route('login'), {
                onFinish: () => {
                    this.form.reset('password');
                    this.loading = false;
                },
            })
        }
    }
 }

</script>
