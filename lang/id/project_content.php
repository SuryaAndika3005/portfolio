<?php

/**
 * Project content translations (Bahasa Indonesia), keyed by project ID --
 * see App\Models\Project::localized() for how these are looked up and
 * safely fall back to the project's own DB column when a key or an
 * individual field is missing. Only role/description/problem/process/
 * result are ever translated here: title, client, year, and tool/tech
 * names are intentionally left in the DB record and never touched.
 *
 * Covers every currently PUBLISHED project (Language Content Completion
 * pass): the 5 originally-Featured projects plus the remaining 9
 * published graphic-design/web projects. Projects 6 and 8 are deliberately
 * excluded -- both are unpublished (is_published=false) and already 404
 * on their public page, so translating them would be dead work with no
 * visible effect. Any future project added later simply has no entry
 * here yet and transparently falls back to its DB copy -- expected, not
 * a bug.
 *
 * Named project_content.php, not projects.php: the app already calls
 * __('Project') / __('Projects') as plain UI strings (nav, footer,
 * archive). On a case-insensitive filesystem, a bare __('Projects') call
 * resolves its translation GROUP name case-insensitively too, so a file
 * literally named projects.php gets loaded as the "Projects" group and
 * returns this whole array where a plain string was expected -- Blade's
 * {{ }} escaping then hard-crashes trying to htmlspecialchars() an array.
 * Verified this collision by reproducing it against the (also lowercase)
 * "projects.php" filename before renaming to this one.
 */
return [
    21 => [
        'role' => 'Pengembang Computer Vision & Web',
        'description' => 'Vision AI adalah sistem verifikasi presensi berbasis web yang menggabungkan pengenalan wajah, pendaftaran multi-sampel terpandu, dan pemeriksaan liveness hybrid. Proyek ini berkembang dari prototipe akademik dengan satu referensi menjadi V2 yang lebih efisien dan teruji, berfokus pada ketahanan pengenalan, panduan pendaftaran yang lebih jelas, dan evaluasi performa berbasis bukti.',
        'problem' => "Presensi berbasis wajah perlu menyelesaikan lebih dari sekadar mendeteksi wajah. Prototipe awal mengandalkan satu gambar referensi, mengulang tahap deteksi CNN yang mahal saat alignment, dan menampilkan nilai kemiripan yang mudah disalahartikan sebagai akurasi model. Keterbatasan ini meningkatkan biaya pemrosesan dan membuat pencocokan identitas kurang andal ketika pose atau kondisi pengambilan gambar pengguna berubah.",
        'process' => "Saya mengaudit keseluruhan pipeline computer vision dan mengukur setiap tahap pemrosesan sebelum mengubah arsitekturnya. Tahap deteksi wajah CNN yang redundan saat alignment dihilangkan melalui jalur FAST_ALIGN yang dioptimalkan, dan alur pendaftaran satu gambar digantikan dengan pendaftaran multi-sampel terpandu untuk pose tengah, kiri, dan kanan. Pipeline pengenalan kini mendukung beberapa embedding referensi per identitas sekaligus tetap kompatibel dengan pengguna lama. Saya juga menambahkan gerbang kualitas pendaftaran, instrumentasi benchmark, pengujian otomatis, dan tooling kalibrasi ambang batas sehingga keputusan pencocokan dan performa dapat dievaluasi dari bukti terukur, bukan persentase sembarangan.",
        'result' => "Vision AI V2 menyediakan alur kerja presensi end-to-end mulai dari pendaftaran wajah terpandu, pencocokan identitas, verifikasi liveness acak, pencegahan presensi ganda, hingga analitik administratif. Pengujian pengembangan langsung menunjukkan bahwa penghapusan deteksi alignment yang redundan secara signifikan menurunkan biaya pemrosesan pada tahap tersebut, sementara pendaftaran multi-referensi memungkinkan setiap identitas terwakili dalam beberapa pose tanpa overhead pencocokan yang berarti pada skala proyek saat ini. Keterbatasan sistem yang masih ada, termasuk biaya CPU, deteksi spoof pasif yang bersifat heuristik, dan data kalibrasi ambang batas yang terbatas, didokumentasikan secara eksplisit, bukan disembunyikan di balik klaim akurasi yang tidak didukung bukti.",
    ],

    // Curation follow-up (Language Content Completion pass): this
    // project's DB `description` used to be the one field in the whole
    // portfolio written natively in Indonesian, which needed an English
    // override in lang/en/project_content.php to work correctly. Rather
    // than carry that exception forever, the DB column was normalized
    // back to English (Section 5) -- the exact same text that override
    // used, verified as an accurate translation before the swap -- and
    // this file now holds the Indonesian version explicitly instead, the
    // same shape every other project already uses. lang/en/project_content.php
    // is back to empty.
    22 => [
        'role' => 'Content Analytics & Strategi AI (Proyek Tim)',
        'description' => "Dashboard Kreatif 523 Studio adalah dashboard operasional internal yang dikembangkan untuk menghubungkan proses kerja kreatif, mulai dari pengelolaan klien, perencanaan dan produksi konten, hingga analitik performa media sosial dan rekomendasi strategi berbasis AI.\n\nProject ini dikembangkan secara kolaboratif, dengan kontribusi saya berfokus pada Content Analytics dan AI Strategy.",
        'problem' => "Tim agensi kreatif sering mengelola perencanaan, status produksi, dan performa media sosial di alat yang terpisah-pisah. Pemisahan ini membuat sulit melihat bagaimana konten yang diproduksi terhubung dengan performa aktualnya, dan lebih sulit lagi mengubah hasil tersebut menjadi keputusan konten berikutnya. Tantangannya adalah membangun Dashboard Kreatif 523 Studio sebagai sistem di mana alur kerja operasional dan data performa dapat saling memberi masukan.",
        'process' => "Dalam tim yang membangun Dashboard Kreatif 523 Studio, pekerjaan saya berfokus pada lapisan analitik dan strategi AI. Saya menyusun data performa konten agar dapat dianalisis lintas klien, platform, item konten, dan periode waktu, bukan sekadar dilihat sebagai angka yang berdiri sendiri. Lapisan analitik ini juga mendukung wawasan audiens dan sinyal performa yang digunakan sebagai konteks untuk analisis strategi. Untuk AI Strategy, konteks performa, audiens, dan anomali diagregasi di sisi aplikasi lalu dikirim ke Google Gemini sebagai konteks terstruktur; responsnya kembali sebagai rekomendasi terstruktur, bukan perubahan otomatis pada rencana konten. Menerapkan sebuah insight ke Content Plan adalah tindakan terpisah dan eksplisit yang dilakukan pengguna: desain human-in-the-loop yang menjaga keputusan kreatif akhir tetap berada di tangan tim.",
        'result' => "Dashboard Kreatif 523 Studio menciptakan siklus terhubung antara operasi kreatif dan evaluasi performa: Content Plan, Production, Publishing, Social Performance, Analytics, AI Strategy, Human Decision, dan kembali ke Content Plan berikutnya. Kontribusi saya membantu mengubah metrik media sosial menjadi lapisan pendukung keputusan, bukan sekadar dashboard pelaporan yang berdiri sendiri, sehingga data performa dapat memberi masukan bagi strategi konten ke depan dalam sistem operasional yang sama.",
    ],

    7 => [
        'role' => 'Pengembang Web & Implementasi UI (Proyek Tim)',
        'description' => "SPMB Adzkia adalah platform pendaftaran mahasiswa berbasis web yang dikembangkan secara kolaboratif oleh sebuah tim, mendukung perjalanan pendaftaran mulai dari informasi program studi dan onboarding pendaftar hingga validasi administratif, pembayaran, dan alur daftar ulang. Kontribusi saya berfokus pada implementasi web dan UI di alur-alur utama pendaftar dan administrasi, termasuk pengalaman Program Studi, validasi pendaftaran, dan kolaborasi pada proses daftar ulang.",
        'problem' => "Sistem pendaftaran mahasiswa perlu melayani dua jenis pengguna yang sangat berbeda sekaligus: pendaftar yang membutuhkan pengalaman pendaftaran yang jelas dan terpandu, serta administrator yang membutuhkan alat terstruktur untuk meninjau dan memvalidasi data pendaftar dalam jumlah besar. Tantangannya adalah mengubah kebutuhan operasional tersebut menjadi antarmuka dan alur kerja yang tetap mudah dipahami di kedua sisi sistem.",
        'process' => "Sebagai bagian dari tim pengembang, saya bekerja menerjemahkan alur pendaftaran menjadi antarmuka web yang dapat digunakan dan fungsionalitas berbasis Laravel. Implementasi saya mencakup pengalaman Program Studi (tempat pendaftar menjelajahi program yang tersedia secara terstruktur) serta antarmuka validasi pendaftaran untuk administrasi. Saya juga berkolaborasi pada alur daftar ulang, menghubungkan proses yang dihadapi pendaftar dengan alur validasi yang digunakan administrator. Pekerjaan ini berarti menjaga konsistensi antara halaman publik, alur yang dihadapi pendaftar, dan antarmuka admin, sekaligus terintegrasi dengan sistem yang lebih luas yang dibangun tim.",
        'result' => "Sistem yang dihasilkan menyediakan alur pendaftaran yang saling terhubung, mencakup eksplorasi program, pendaftaran pendaftar, validasi administratif, pembayaran, dan daftar ulang. Kontribusi saya membantu membuat interaksi utama antara pendaftar dan administrator lebih terstruktur dan mudah dinavigasi, dalam arsitektur SPMB yang lebih luas yang dibangun oleh tim.",
    ],

    10 => [
        'role' => 'Desainer UI/UX',
        'description' => 'Merancang alur pengguna yang intuitif dan antarmuka visual modern untuk memudahkan pencatatan keuangan pribadi, visualisasi data, dan pencatatan harian yang lancar.',
        'problem' => 'Aplikasi keuangan pribadi sering kali membebani pengguna dengan terlalu banyak data; tujuan di sini adalah membuat pencatat keuangan yang membuat pencatatan dan peninjauan pengeluaran harian terasa ringan, bukan seperti beban.',
        'process' => 'Merancang alur inti di sekitar dua hal yang paling sering dilakukan pengguna, yaitu mencatat transaksi dan melihat ke mana uang mereka pergi, didukung oleh visualisasi data yang jelas untuk gambaran yang lebih besar.',
        'result' => 'Sebuah desain UI yang mengeksplorasi bagaimana pencatatan keuangan pribadi dapat terasa cukup sederhana untuk benar-benar dijalankan sehari-hari.',
    ],

    14 => [
        'role' => 'Desainer Grafis',
        'description' => 'Identitas visual dan materi promosi untuk Yasmin International Boarding School, menyampaikan citra yang tepercaya dan modern kepada calon siswa dan orang tua.',
        'problem' => 'Sebagai sekolah asrama internasional, Yasmin membutuhkan materi promosi yang terasa tepercaya dan cukup modern untuk menonjol di mata calon siswa dan orang tua.',
        'process' => 'Merancang sistem visual yang bersih dan profesional untuk materi promosi sekolah, menyeimbangkan kesan internasional dengan keramahan.',
        'result' => 'Satu set visual yang kohesif dan siap mendukung materi promosi dan penerimaan siswa sekolah.',
    ],

    // ---- Language Content Completion pass: remaining published projects
    // (all previously fell back to DB/English; every field below keeps
    // the same length/depth as its English source -- short Graphic
    // Design case studies stay short, nothing is elaborated). ----

    1 => [
        'role' => 'Desainer Grafis',
        'description' => 'Desain visual promosi untuk LuxSuits, brand penyewaan pakaian formal, yang menyampaikan citra premium dan elegan. Menekankan komposisi minimalis dengan kontras kuat untuk menonjolkan nilai produk pakaian formal.',
        'problem' => 'LuxSuits membutuhkan visual promosi yang mampu bersaing dengan brand penyewaan jas yang sudah mapan secara online, namun belum memiliki bahasa visual yang konsisten untuk menyatukan unggahan media sosialnya.',
        'process' => 'Membangun sistem visual minimalis dengan tipografi yang percaya diri, warna-warna netral, dan fotografi produk yang kuat, lalu menerapkannya pada serangkaian poster yang mengeksplorasi berbagai sudut pandang tema "formal wear".',
        'result' => 'Satu set visual yang kohesif, memberi LuxSuits kehadiran yang lebih premium dan mudah dikenali di media sosial, serta template yang dapat digunakan kembali untuk kampanye berikutnya.',
    ],

    2 => [
        'role' => 'Desainer Grafis',
        'description' => 'Pengembangan aset visual untuk event kompetisi Manufer Super League. Mencakup desain media sosial hingga elemen fisik di lapangan, dengan tetap menjaga identitas warna yang berani.',
        'problem' => 'Turnamen ini membutuhkan identitas visual yang cukup kuat untuk mendukung promosi online maupun signage fisik di lapangan, dengan linimasa produksi event yang ketat.',
        'process' => 'Membangun sistem warna yang berani dengan kontras tinggi serta satu set template yang fleksibel, mencakup pengumuman pertandingan hingga banner di lokasi, sehingga aset baru dapat diproduksi dengan cepat seiring berjalannya turnamen.',
        'result' => 'Identitas yang konsisten dan penuh energi, yang mengiringi event ini mulai dari teaser media sosial pertama hingga signage hari pertandingan.',
    ],

    3 => [
        'role' => 'Desainer Grafis',
        'description' => 'Eksplorasi visual yang penuh energi untuk bisnis mini soccer Top Scorer Arena. Menjaga identitas olahraga yang kuat dan dinamis secara konsisten di berbagai platform media sosial.',
        'problem' => 'Sebagai venue mini soccer yang masih baru, Top Scorer Arena membutuhkan identitas visual yang terasa sekompetitif dan sedinamis olahraga itu sendiri, untuk membangun audiens di media sosial dari nol.',
        'process' => 'Merancang bahasa visual yang berani dan bernuansa olahraga, lalu menerapkannya secara konsisten pada serangkaian unggahan media sosial berkala untuk membangun tingkat pengenalan dari waktu ke waktu.',
        'result' => 'Kehadiran yang khas dan energik, yang membedakan venue ini dari branding tempat olahraga lokal pada umumnya.',
    ],

    4 => [
        'role' => 'Desainer Grafis',
        'description' => 'Adaptasi berbagai gaya visual untuk klien-klien agensi kreatif 523 Studio. Mencakup desain poster rekrutmen hingga kampanye media sosial yang menyeluruh.',
        'problem' => 'Bekerja secara in-house di agensi kreatif berarti harus cepat beradaptasi dengan berbagai brief klien, gaya brand, dan format, mulai dari rekrutmen hingga kampanye media sosial, tanpa waktu pemanasan yang lama di setiap proyek.',
        'process' => 'Menerjemahkan setiap brief yang masuk menjadi layout yang tetap setia pada brand klien, sekaligus memenuhi ekspektasi kecepatan kerja agensi, mulai dari poster rekrutmen hingga rangkaian kampanye media sosial.',
        'result' => 'Sepenggal nyata hasil kerja agensi yang menunjukkan keluasan format, mulai dari rekrutmen, kampanye, hingga media sosial, untuk beberapa klien sekaligus.',
    ],

    5 => [
        'role' => 'Desainer Grafis',
        'description' => 'Merancang materi visual akademik untuk Fakultas Teknologi Informasi Universitas Andalas, dengan menyeimbangkan informasi yang padat melalui layout yang bersih dan terstruktur.',
        'problem' => 'Materi fakultas perlu menyampaikan informasi akademik yang padat (program studi, persyaratan, jadwal) tanpa terasa penuh sesak atau sulit didekati oleh calon mahasiswa.',
        'process' => 'Menerapkan sistem layout yang bersih dan terstruktur untuk mengatur hierarki informasi secara jelas, sekaligus menjaga konsistensi visual materi dengan identitas universitas.',
        'result' => 'Materi yang membuat konten akademik yang padat lebih mudah dipahami sekilas, digunakan di berbagai kanal promosi fakultas.',
    ],

    9 => [
        'role' => 'Pengembang Web & Aplikasi',
        'description' => 'Implementasi landing page menggunakan framework modern. Berfokus pada animasi yang halus, optimasi gambar, dan struktur komponen yang dapat digunakan kembali untuk skalabilitas ke depan.',
        'problem' => 'Mengubah desain yang telah disetujui menjadi situs nyata berarti menyeimbangkan kehalusan visual, seperti animasi dan gambar, dengan performa dan kemudahan pemeliharaan jangka panjang.',
        'process' => 'Membangun landing page dengan struktur berbasis komponen, berfokus pada animasi yang halus dan gambar yang teroptimasi agar halaman tetap cepat tanpa mengurangi kehalusan visualnya.',
        'result' => 'Landing page yang berfungsi dan beranimasi, disusun agar dapat dikembangkan seiring perubahan kebutuhan angkatan dari waktu ke waktu.',
    ],

    11 => [
        'role' => 'Desainer Grafis',
        'description' => 'Merancang aset visual dan konsep antarmuka pengguna untuk platform mobilitas urban, dengan fokus pada kejelasan, layout, dan visual yang ramah pengguna.',
        'problem' => 'Konsep mobilitas urban ini membutuhkan aset visual dan konsep antarmuka yang terasa tepercaya dan mudah dipahami sekilas, baik pada titik sentuh pemasaran maupun produk.',
        'process' => 'Merancang bahasa visual yang jelas dan mudah didekati, dipadukan dengan konsep antarmuka, dengan mengutamakan keterbacaan dan kejelasan dibanding dekorasi.',
        'result' => 'Satu set konsep yang kohesif, mencakup visual pemasaran dan arahan antarmuka untuk platform tersebut.',
    ],

    12 => [
        'role' => 'Desainer Grafis',
        'description' => 'Magang sebagai Desainer Grafis di sebuah rumah produksi, bertanggung jawab merancang konten visual, elemen branding, dan materi promosi.',
        'problem' => 'Sebagai peserta magang di rumah produksi, dituntut menghasilkan konten visual dan materi branding untuk proyek klien nyata dengan tenggat waktu studio yang sesungguhnya.',
        'process' => 'Mengerjakan materi promosi dan elemen branding untuk proyek-proyek klien studio, beradaptasi dengan arahan kreatif spesifik setiap proyek.',
        'result' => 'Kumpulan hasil kerja magang yang membangun pengalaman nyata di rumah produksi, mengubah brief kreatif menjadi aset visual jadi.',
    ],

    13 => [
        'role' => 'Desainer Grafis',
        'description' => 'Identitas visual dan materi promosi untuk PT Guna Griya Abadi, memberikan perusahaan ini kehadiran visual yang lebih profesional dan konsisten di seluruh materinya.',
        'problem' => 'PT Guna Griya Abadi membutuhkan kehadiran visual yang profesional, namun belum memiliki identitas konsisten yang menyatukan materi promosinya.',
        'process' => 'Mengembangkan sistem visual yang bersih dan menerapkannya pada materi promosi utama perusahaan, menjaga tampilan tetap konsisten dan mudah digunakan kembali.',
        'result' => 'Satu set visual yang kohesif, memberi perusahaan kehadiran yang lebih profesional dan mudah dikenali.',
    ],
];
