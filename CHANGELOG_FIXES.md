### Fix #4 — Perbaikan layout tanda tangan PDF anak: label sejajar horizontal, tanggal di tengah garis
Tanggal: 2026-08-20
File: resources/views/children/pdf-export.blade.php
Masalah: Label "Mengetahui / Disetujui" tidak sejajar dengan "Disusun oleh"; tanggal pada kolom kiri mepet dan berantakan.
Akar: Margin-bottom terlalu besar (45px) pada signature-label; tanggal dibungkus div terpisah dengan margin negatif sebelumnya.
Fix: Gunakan margin-bottom 35px pada wrapper div untuk kedua kolom; tanggal diposisikan di antara label dan garis tanda tangan.
Verifikasi: Label sejajar horizontal, tanggal rapi di tengah sebelum garis.
Pelajaran: Untuk layout dua kolom parallel, gunakan wrapper div dengan margin konsisten.
Log Keyword: signature, tanda tangan, layout PDF
Deploy: Langsung ke production.

### Fix #3 — PDF data anak landscape & perbaikan tata letak tanda tangan
Tanggal: 2026-08-20
File: resources/views/children/pdf-export.blade.php, app/Http/Controllers/ChildController.php
Masalah: PDF data anak masih portrait sehingga tabel menjadi terlalu padat; posisi tanggal pada kolom Disusun oleh tidak rapi karena margin negatif (-50px).
Akar: Controller menggunakan orientasi default portrait; signature-label menggunakan margin-top: -50px yang membuat tanggal tumpang tindih dengan teks Disusun oleh.
Fix: Tambah ->setPaper('a4', 'landscape') di controller; pisahkan label tanggal menjadi signature-date dengan margin-bottom normal.
Verifikasi: Tabel akan lebih lebar, 11 kolom muat tanpa overflow. Tanda tangan rapi.
Pelajaran: Gunakan method setPaper untuk orientasi, hindari margin negatif pada elemen bertumpuk.
Log Keyword: pdf landscape, tanda tangan, orientation
Deploy: Langsung ke production.

### Fix #2 — Export PDF anak: kolom "Total Layanan" dihilangkan, kolom Terapi menampilkan total biaya terapi, kolom Tagihan menampilkan invoice bersih (gross - subsidi)
Tanggal: 2026-08-20
File: app/Models/Child.php, resources/views/children/pdf-export.blade.php, app/Exports/ChildExport.php
Masalah: Kolom "Total Layanan" pada laporan PDF dan Excel menampilkan gross amount, padahal kolom Terapi hanya berisi daftar nama layanan tanpa nilai nominal, membuat pembaca bingung dengan total yang ditampilkan.
Akar: View PDF menggunakan `getTherapyDetails()` untuk menampilkan deskripsi text terapi, dan `calculateGrossAmount()` untuk Total Layanan, bukan menggunakan nilai riil per layanan.
Fix: Tambah method `getTherapyTotal()` pada model Child untuk menghitung total biaya terapi. Ubah tampilan PDF dan export Excel: hilangkan kolom "Total Layanan", ubah kolom Terapi menjadi nilai nominal (Rp), dan kolom Tagihan tetap menampilkan `calculateInvoiceAmount()` (gross - subsidi).
Verifikasi: Anak ADEEVA (terapi saja) = getTherapyTotal 1.100.000, invoice 1.100.000. Anak AHMAD AMEER (sekolah + terapi) = getTherapyTotal 4.275.000, invoice 7.050.000.
Pelajaran: Gunakan helper method yang mengembalikan nilai numerik saat perlu menampilkan angka di laporan, jangan hanya deskriptif text.
Log Keyword: pdf-export, total terapi, tagihan anak, calculateInvoiceAmount
Deploy: Langsung ke production tanpa migration.

### Fix #1 — Laba/Rugi total pendapatan tidak sesuai: subsidi harus mengurangi SPP/Terapi, bukan kategori terpisah
Tanggal: 2026-08-20
File: app/Http/Controllers/ReportController.php, app/Exports/ProfitLossExport.php
Masalah: Parser laba/rugi menjumlahkan bruto per layanan dari notes income + menambahkan subsidi sebagai kategori baru, padahal income disimpan sebagai net (gross - subsidi) sehingga breakdown hasil penjumlahan melebihi total pendapatan sesungguhnya.
Akar: Income dicatat sebagai satu record per anak dengan `amount = gross - subsidi`. Notes berisi rincian bruto termasuk `Subsidi: -Rp X`. Substitusi sebelumnya menambahkan subsidi sebagai entry positif ke breakdown, menggeset seluruh total ke atas selisih keseluruhan subsidi.
Fix: Modifikasi `parseIncomeBreakdown()` agar subsidi dipisahkan dari breakdown bruto; jika anak terdaftar sekolah (class_name/spp_fee) subsidi kurangi SPP, jika tidak kurangi Terapi. Kategori "Subsidi" dihilangkan dari breakdown.
Verifikasi: Untuk Aug 2026, raw total income = 122.970.000. Breakdown baru: Terapi 38.950.000 + SPP 83.270.000 + Parent Support 750.000 = 122.970.000 (sesuai).
Pelajaran: Jika income disimpan net, breakdown harus beroperasi pada bruto dikurangi subsidi yang dialokasikan, bukan menambah kategori subsidi.
Log Keyword: parseIncomeBreakdown, subsidi, income breakdown
Deploy: Manually via tinker/console, tidak ada migration.
