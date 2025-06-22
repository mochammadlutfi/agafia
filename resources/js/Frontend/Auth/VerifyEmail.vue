<template>
    <base-layout>
        <div class="content content-full">
            <div class="row justify-content-center py-30 my-30">
                <div class="col-md-6 col-xl-6 align-items-center">
                    <div class="block block-rounded">
                        <div class="block-content p-4">
                            <div class="text-center mb-lg-5">
                                <h2 class="font-size-h2 text-center">Verifikasi Email Dikirim</h2>
                                <!-- <img :src="asset('media/email.svg')" width="300px"/> -->
                            </div>
                            <div class="font-size-h5">Hai {{ $page.props.user.nama }}!</div>
                            <p class="nice-copy mb-2">
                                Kami telah mengirimkan tautan verifikasi ke alamat email Anda. Silakan periksa kotak
                                masuk Anda dan ikuti petunjuk untuk memverifikasi email Anda. Jika Anda tidak melihat
                                email dari kami, periksa folder spam atau junk mail Anda.
                            </p>
                            <Link :href="route('verification.send')" method="post" as="button" class="btn btn-primary btn-block w-100">
                                <span class="font-size-sm font-w500">Kirim Ulang Email</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </base-layout>
</template>

<script>
import { Link } from '@inertiajs/vue3';
export default {
    components: {
        Link,
    },
    props: {
        canResetPassword: Boolean,
        status: String,
        errors: Object,
    },
    data() {
        return {
            form: {
                name : null,
                email : null,
                password: null,
                password_confirmation: null,
            },
            loading : false,
        }
    },

    methods: {
        submit() {
            let form = this.$inertia.form(this.form);
            let url = this.route("register");
            form.post(url, {
                preserveScroll: true,
                onSuccess: () => {
                    this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: `Data saved successfully!`,
                        showConfirmButton: false,
                    });
                },
                onError:(error) => {
                    this.$swal.close();
                },
            });
        }
    }
 }

</script>
