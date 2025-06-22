<template>
    <div>
        <el-row :gutter="20" class="mb-4">
            <el-col :md="12">
                <el-row class="mb-2" :gutter="10">
                    <el-col :md="10">Nama Ayah</el-col>
                    <el-col :md="14">
                        : <span class="fw-semibold">{{ modelValue.nama_ayah }}</span>
                    </el-col>
                </el-row>
                <el-row class="mb-2" :gutter="10">
                    <el-col :md="10">Tempat / Tanggal Lahir Ayah</el-col>
                    <el-col :md="14">
                        : <span class="fw-semibold">{{ modelValue.tmp_lahir_ayah }} /
                            {{ modelValue.tgl_lahir_ayah }}</span>
                    </el-col>
                </el-row>
                <el-row class="mb-2" :gutter="10">
                    <el-col :md="10">No HP Ayah</el-col>
                    <el-col :md="14">
                        : <span class="fw-semibold">{{ modelValue.telp_ayah }}</span>
                    </el-col>
                </el-row>
                <el-row class="mb-2" :gutter="10">
                    <el-col :md="10">Pendidkan Terakhir Ayah</el-col>
                    <el-col :md="14">
                        : <span class="fw-semibold">{{ modelValue.pendidikan_ayah }}</span>
                    </el-col>
                </el-row>
                <el-row class="mb-2" :gutter="10">
                    <el-col :md="10">Agama Ayah</el-col>
                    <el-col :md="14">
                        : <span class="fw-semibold">{{ modelValue.agama_ayah }}</span>
                    </el-col>
                </el-row>
                <el-row class="mb-2" :gutter="10">
                    <el-col :md="10">Alamat Ayah</el-col>
                    <el-col :md="14">
                        : <span class="fw-semibold">{{ modelValue.alamat_ayah }}</span>
                    </el-col>
                </el-row>
                <el-row class="mb-2" :gutter="10">
                    <el-col :md="10">Pekerjaan Ayah</el-col>
                    <el-col :md="14">
                        : <span class="fw-semibold">{{ modelValue.pekerjaan_ayah }}</span>
                    </el-col>
                </el-row>
                <el-row class="mb-2" :gutter="10">
                    <el-col :md="10">Penghasilan Ayah</el-col>
                    <el-col :md="14">
                        : <span class="fw-semibold">{{ modelValue.penghasilan_ayah }}</span>
                    </el-col>
                </el-row>
                <el-row class="mb-2" :gutter="10">
                    <el-col :md="10">Alamat Kantor Ayah</el-col>
                    <el-col :md="14">
                        : <span class="fw-semibold">{{ modelValue.alamat_kantor_ayah }}</span>
                    </el-col>
                </el-row>
                <el-row class="mb-2" :gutter="10">
                    <el-col :md="10">Scan KTP Ayah</el-col>
                    <el-col :md="14">
                        :  
                        <a :href="`${modelValue.scan_ktp_ayah}`" class="ep-button ep-button--primary ep-button--small" target="_blank" v-if="modelValue.scan_ktp_ayah">
                            Lihat KTP Ayah
                        </a>
                        <template v-else>
                            -
                        </template>
                    </el-col>
                </el-row>
                <el-row class="mb-2" :gutter="10">
                    <el-col :md="10">Scan Kartu Keluarga</el-col>
                    <el-col :md="14">
                        :  
                        <a :href="`${modelValue.scan_kk}`" class="ep-button ep-button--primary ep-button--small" target="_blank" v-if="modelValue.scan_kk">
                            Lihat Kartu Keluarga
                        </a>
                        <template v-else>
                            -
                        </template>
                    </el-col>
                </el-row>
            </el-col>
            <el-col :md="12">
                <el-row class="mb-2" :gutter="10">
                    <el-col :md="10">Nama Ibu</el-col>
                    <el-col :md="14">
                        : <span class="fw-semibold">{{ modelValue.nama_ibu }}</span>
                    </el-col>
                </el-row>
                <el-row class="mb-2" :gutter="10">
                    <el-col :md="10">Tempat / Tanggal Lahir Ibu</el-col>
                    <el-col :md="14">
                        : <span class="fw-semibold">{{ modelValue.tmp_lahir_ibu }} /
                            {{ modelValue.tgl_lahir_ibu }}</span>
                    </el-col>
                </el-row>
                <el-row class="mb-2" :gutter="10">
                    <el-col :md="10">No HP Ibu</el-col>
                    <el-col :md="14">
                        : <span class="fw-semibold">{{ modelValue.telp_ibu }}</span>
                    </el-col>
                </el-row>
                <el-row class="mb-2" :gutter="10">
                    <el-col :md="10">Pendidkan Terakhir Ibu</el-col>
                    <el-col :md="14">
                        : <span class="fw-semibold">{{ modelValue.pendidikan_ibu }}</span>
                    </el-col>
                </el-row>
                <el-row class="mb-2" :gutter="10">
                    <el-col :md="10">Agama Ibu</el-col>
                    <el-col :md="14">
                        : <span class="fw-semibold">{{ modelValue.agama_ibu }}</span>
                    </el-col>
                </el-row>
                <el-row class="mb-2" :gutter="10">
                    <el-col :md="10">Alamat Ibu</el-col>
                    <el-col :md="14">
                        : <span class="fw-semibold">{{ modelValue.alamat_ibu }}</span>
                    </el-col>
                </el-row>
                <el-row class="mb-2" :gutter="10">
                    <el-col :md="10">Pekerjaan Ibu</el-col>
                    <el-col :md="14">
                        : <span class="fw-semibold">{{ modelValue.pekerjaan_ibu }}</span>
                    </el-col>
                </el-row>
                <el-row class="mb-2" :gutter="10">
                    <el-col :md="10">Penghasilan Ibu</el-col>
                    <el-col :md="14">
                        : <span class="fw-semibold">{{ modelValue.penghasilan_ibu }}</span>
                    </el-col>
                </el-row>
                <el-row class="mb-2" :gutter="10">
                    <el-col :md="10">Alamat Kantor Ibu</el-col>
                    <el-col :md="14">
                        : <span class="fw-semibold">{{ modelValue.alamat_kantor_ibu }}</span>
                    </el-col>
                </el-row>
                <el-row class="mb-2" :gutter="10">
                    <el-col :md="10">Scan KTP Ibu</el-col>
                    <el-col :md="14">
                        :  
                        <a :href="`${modelValue.scan_ktp_ibu}`" class="ep-button ep-button--primary ep-button--small" target="_blank" v-if="modelValue.scan_ktp_ibu">
                            Lihat KTP Ibu
                        </a>
                        <template v-else>
                            -
                        </template>
                    </el-col>
                </el-row>
            </el-col>
        </el-row>
    </div>
</template>
<script>
export default {
    setup() {
        
    },
    props : {
        modelValue : Object,
    }
}
</script>