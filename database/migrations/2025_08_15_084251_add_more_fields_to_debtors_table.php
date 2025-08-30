<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('debtors', function (Blueprint $t) {
            // Kolom dari Excel
            $t->unsignedInteger('row_no')->nullable();          // NO
            $t->string('kode_unik', 100)->nullable();           // KODE UNIK

            // Identitas pemohon
            $t->string('name_ktp')->nullable();                 // NAMA DEBITUR KTP
            $t->string('name_skep')->nullable();                // NAMA DEBITUR SKEP
            $t->string('kategori')->nullable();                 // KATEGORI
            $t->string('pengelola')->nullable();                // PENGELOLA
            $t->date('tgl_pengajuan')->nullable();              // TGL.PENGAJUAN
            $t->string('usia_pengajuan')->nullable();           // USIA SAAT PENGAJUAN (teks)
            $t->date('tgl_lunas')->nullable();                  // TGL LUNAS
            $t->string('usia_lunasi')->nullable();              // USIA SAAT LUNASI

            $t->string('no_ktpa')->nullable();
            $t->string('nip_nrp')->nullable();
            $t->string('nik_ktp')->nullable();
            $t->string('masa_berlaku_ktp')->nullable();

            $t->string('no_sk_pensiun')->nullable();
            $t->date('tgl_sk_pensiun')->nullable();
            $t->string('lokasi_sk_pensiun')->nullable();
            $t->date('tmt_pensiun')->nullable();
            $t->string('pangkat_golongan')->nullable();
            $t->string('penerbit_sk')->nullable();

            $t->string('kota_lahir')->nullable();
            $t->date('tgl_lahir')->nullable();
            $t->string('tempat_tgl_lahir')->nullable();
            $t->string('jenis_kelamin')->nullable();
            $t->string('agama')->nullable();
            $t->string('gelar_pendidikan')->nullable();

            // Alamat
            $t->text('alamat')->nullable();
            $t->string('kelurahan')->nullable();
            $t->string('kecamatan')->nullable();
            $t->string('kota_kab')->nullable();
            $t->string('provinsi')->nullable();
            $t->string('kode_pos')->nullable();
            $t->string('no_telp')->nullable();
            $t->string('no_npwp')->nullable();
            $t->string('nama_pasangan')->nullable();
            $t->string('nama_ibu_kandung')->nullable();
            $t->string('status_rumah')->nullable();
            $t->string('masa_kerja')->nullable();
            $t->string('pekerjaan_sekarang')->nullable();
            $t->text('alamat_pekerjaan_sekarang')->nullable();

            // Kerabat
            $t->string('nama_kerabat')->nullable();
            $t->string('hubungan_kerabat')->nullable();
            $t->string('nik_ktp_kerabat')->nullable();
            $t->string('kota_lahir_kerabat')->nullable();
            $t->date('tgl_lahir_kerabat')->nullable();
            $t->text('alamat_kerabat')->nullable();
            $t->string('provinsi_kerabat')->nullable();
            $t->string('kota_kab_kerabat')->nullable();
            $t->string('kecamatan_kerabat')->nullable();
            $t->string('kelurahan_kerabat')->nullable();
            $t->string('kode_pos_kerabat')->nullable();
            $t->string('no_telp_kerabat')->nullable();
            $t->string('masa_berlaku_ktp_kerabat')->nullable();

            // Project/fasilitas
            $t->string('regional')->nullable();
            $t->string('cabang')->nullable();
            $t->string('status_mutasi')->nullable();
            $t->string('status_pembiayaan')->nullable();
            $t->string('kreditur_take_over')->nullable();
            $t->string('kantor_bayar_tujuan')->nullable();
            $t->string('cabang_tujuan')->nullable();
            $t->string('no_rekening_baru')->nullable();
            $t->string('kantor_bayar_asal')->nullable();
            $t->string('cabang_asal')->nullable();
            $t->string('no_rekening_lama')->nullable();
            $t->string('produk_loan')->nullable();
            $t->string('kreditur')->nullable();

            // Finansial & biaya
            $t->string('rate_kreditur')->nullable();
            $t->string('rate_gg')->nullable();
            $t->string('rate_asuransi')->nullable();
            $t->string('kode_jiwa')->nullable();

            $t->decimal('gaji_kotor', 18, 2)->nullable();
            $t->decimal('gaji_bersih', 18, 2)->nullable();
            // catatan: 'plafond' & 'installment' SUDAH ada di tabel

            $t->string('suku_bunga')->nullable();
            $t->decimal('biaya_provisi', 18, 2)->nullable();
            $t->decimal('biaya_adm_kredit', 18, 2)->nullable();
            $t->decimal('biaya_materai', 18, 2)->nullable();
            $t->decimal('biaya_flagging', 18, 2)->nullable();
            $t->decimal('biaya_proses_bank', 18, 2)->nullable();
            $t->decimal('premi_asuransi', 18, 2)->nullable();
            $t->decimal('persen_extra_premi', 18, 2)->nullable();
            $t->decimal('nilai_extra_premi', 18, 2)->nullable();
            $t->decimal('total_premi_asuransi', 18, 2)->nullable();

            $t->string('jum_blokir')->nullable(); // contoh: "1 X Angsuran"
            $t->decimal('nilai_blokir', 18, 2)->nullable();

            $t->unsignedInteger('jumlah_hari_grace_periode')->nullable();
            $t->decimal('nilai_grace_periode', 18, 2)->nullable();
            $t->decimal('biaya_admin_angsuran', 18, 2)->nullable();
            $t->decimal('angs_efektif_per_bulan', 18, 2)->nullable();
            $t->decimal('take_over', 18, 2)->nullable();
            $t->string('kreditur_asal_take_over')->nullable();
            $t->decimal('jum_potongan', 18, 2)->nullable();
            $t->decimal('terima_bersih', 18, 2)->nullable();
            $t->string('nilai_dbr')->nullable();

            $t->date('tgl_pembiayaan')->nullable();
            $t->date('tgl_jatuh_tempo')->nullable();
            $t->string('no_pk')->nullable();
            $t->date('tgl_pk')->nullable();
            $t->string('kota_pelaksanaan')->nullable();
            $t->string('jenis_fasilitas')->nullable();
            $t->string('bentuk_fasilitas')->nullable();
            $t->string('penggunaan')->nullable();

            $t->date('tgl_pembayaran')->nullable();
            $t->date('tgl_jatuh_tempo2')->nullable();
            $t->decimal('denda_terlambat', 18, 2)->nullable();
            $t->decimal('denda_pelunasan', 18, 2)->nullable();
            $t->string('no_sk_komite')->nullable();
            $t->string('branch_manager')->nullable();
            $t->string('supervisor')->nullable();
            $t->string('verifikator')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('debtors', function (Blueprint $t) {
            $cols = [
                'row_no','kode_unik','name_ktp','name_skep','kategori','pengelola','tgl_pengajuan','usia_pengajuan',
                'tgl_lunas','usia_lunasi','no_ktpa','nip_nrp','nik_ktp','masa_berlaku_ktp','no_sk_pensiun','tgl_sk_pensiun',
                'lokasi_sk_pensiun','tmt_pensiun','pangkat_golongan','penerbit_sk','kota_lahir','tgl_lahir','tempat_tgl_lahir',
                'jenis_kelamin','agama','gelar_pendidikan','alamat','kelurahan','kecamatan','kota_kab','provinsi','kode_pos',
                'no_telp','no_npwp','nama_pasangan','nama_ibu_kandung','status_rumah','masa_kerja','pekerjaan_sekarang',
                'alamat_pekerjaan_sekarang','nama_kerabat','hubungan_kerabat','nik_ktp_kerabat','kota_lahir_kerabat',
                'tgl_lahir_kerabat','alamat_kerabat','provinsi_kerabat','kota_kab_kerabat','kecamatan_kerabat',
                'kelurahan_kerabat','kode_pos_kerabat','no_telp_kerabat','masa_berlaku_ktp_kerabat','regional','cabang',
                'status_mutasi','status_pembiayaan','kreditur_take_over','kantor_bayar_tujuan','cabang_tujuan','no_rekening_baru',
                'kantor_bayar_asal','cabang_asal','no_rekening_lama','produk_loan','kreditur','rate_kreditur','rate_gg',
                'rate_asuransi','kode_jiwa','gaji_kotor','gaji_bersih','suku_bunga','biaya_provisi','biaya_adm_kredit',
                'biaya_materai','biaya_flagging','biaya_proses_bank','premi_asuransi','persen_extra_premi','nilai_extra_premi',
                'total_premi_asuransi','jum_blokir','nilai_blokir','jumlah_hari_grace_periode','nilai_grace_periode',
                'biaya_admin_angsuran','angs_efektif_per_bulan','take_over','kreditur_asal_take_over','jum_potongan',
                'terima_bersih','nilai_dbr','tgl_pembiayaan','tgl_jatuh_tempo','no_pk','tgl_pk','kota_pelaksanaan',
                'jenis_fasilitas','bentuk_fasilitas','penggunaan','tgl_pembayaran','tgl_jatuh_tempo2','denda_terlambat',
                'denda_pelunasan','no_sk_komite','branch_manager','supervisor','verifikator',
            ];
            foreach ($cols as $c) if (Schema::hasColumn('debtors', $c)) $t->dropColumn($c);
        });
    }
};
