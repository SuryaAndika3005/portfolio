<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Project;

class PortfolioSeeder extends Seeder
{
    public function run()
    {
        // 1. Membuat Kategori
        $categories = [
            'Graphic Design',
            'UI/UX Design',
            'IT & Development',
            'Fotografi',
            'Modeling'
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate([
                'name' => $category,
                'slug' => strtolower(str_replace([' & ', ' '], ['-', '-'], $category))
            ]);
        }

        // Mengambil ID Kategori untuk relasi data
        $graphicId = Category::where('name', 'Graphic Design')->first()->id;
        $uiUxId = Category::where('name', 'UI/UX Design')->first()->id;
        $itId = Category::where('name', 'IT & Development')->first()->id;

        $projects = [
            [
                'category_id' => $graphicId,
                'title' => 'LuxSuits',
                'description' => 'Perancangan visual promosi untuk LuxSuits yang mengomunikasikan citra merek premium dan elegan. Menekankan komposisi minimalis dengan kontras kuat untuk menonjolkan nilai produk pakaian formal.',
                'image_path' => 'projects/Luxsuits/luxsuits.webp',
            ],
            [
                'category_id' => $graphicId,
                'title' => 'Manufer Super League',
                'description' => 'Pengembangan aset visual untuk event kompetitif Manufer Super League. Mencakup desain media sosial hingga elemen fisik di lapangan seperti desain gate, dengan identitas warna berani dan struktur yang kuat.',
                'image_path' => 'projects/MSL/manufer.webp',
            ],
            [
                'category_id' => $uiUxId,
                'title' => 'Money Tracker App',
                'description' => 'Desain antarmuka aplikasi mobile untuk melacak keuangan pribadi (Money Tracker). Berfokus pada kesederhanaan, kemudahan penggunaan (usability), dan visualisasi data keuangan yang jelas.',
                'image_path' => 'projects/moneytracker.webp',
            ],
            [
                'category_id' => $itId,
                'title' => 'SPMB Universitas Adzkia',
                'description' => 'Membangun front-end sistem pendaftaran mahasiswa menggunakan arsitektur kode yang responsif. Mengimplementasikan validasi input yang dinamis, integrasi desain pixel-perfect dari Figma ke Tailwind CSS, dan memastikan performa loading yang optimal.',
                'image_path' => 'projects/SPMB/spmbweb.webp',
            ],
            [
                'category_id' => $graphicId,
                'title' => 'Top Scorer Arena',
                'description' => 'Eksplorasi visual berenergi tinggi untuk bisnis minisoccer Top Scorer Arena. Mempertahankan identitas olahraga yang kuat dan dinamis secara konsisten di berbagai platform media sosial.',
                'image_path' => 'projects/TSA/topscorer.webp',
            ],
            [
                'category_id' => $itId,
                'title' => 'Landing Page Informatika\'23',
                'description' => 'Implementasi landing page menggunakan framework modern. Berfokus pada animasi yang halus, optimasi gambar (WebP), dan struktur komponen yang re-usable untuk kemudahan pengembangan di masa depan.',
                'image_path' => 'projects/IF/if23web.webp',
            ],
            [
                'category_id' => $graphicId,
                'title' => '523 Studio',
                'description' => 'Adaptasi berbagai gaya visual untuk kebutuhan klien agensi 523 Studio. Mencakup desain poster rekrutmen dan kampanye media sosial dengan fokus pada komunikasi yang jelas.',
                'image_path' => 'projects/523/523studio.webp',
            ],
            [
                'category_id' => $graphicId,
                'title' => 'FTI UNAND',
                'description' => 'Merancang materi visual akademik untuk Fakultas Teknologi Informasi Universitas Andalas dengan menyeimbangkan informasi padat melalui tata letak yang bersih dan hierarki visual.',
                'image_path' => 'projects/FTI/fti-unand.webp',
            ],
            [
                'category_id' => $graphicId, // Ubah $uiUxId / $graphicId / $itId sesuai kategori proyeknya
                'title' => 'Metro Software',
                'description' => 'Developed visual for a digital service brand, aiming to reflect a modern and tech-oriented identity. The challenge was balancing clarity with a contemporary feel. The design uses structured layouts and subtle digital elements.',
                'image_path' => 'projects/Metro/metro.webp', // Pastikan gambar ini sudah ada di folder storage/app/public/projects/
            ],
            [
                'category_id' => $graphicId, // Ubah $uiUxId / $graphicId / $itId sesuai kategori proyeknya
                'title' => 'Alir Pictures',
                'description' => 'Developed visual for a digital service brand, aiming to reflect a modern and tech-oriented identity. The challenge was balancing clarity with a contemporary feel. The design uses structured layouts and subtle digital elements.',
                'image_path' => 'projects/Alir/alir.webp', // Pastikan gambar ini sudah ada di folder storage/app/public/projects/
            ],
            [
                'category_id' => $uiUxId,
                'title' => 'Landing Page Informatika\'23',
                'description' => 'Perancangan identitas visual dan tata letak landing page angkatan Informatika 2023. Eksplorasi gaya modern-futuristik dengan navigasi intuitif untuk memudahkan akses informasi bagi mahasiswa dan pengunjung.',
                'image_path' => 'projects/IF/if23.webp',
            ],
            [
                'category_id' => $uiUxId,
                'title' => 'SPMB Universitas Adzkia',
                'description' => 'Merancang pengalaman pengguna untuk sistem penerimaan mahasiswa baru. Berfokus pada kemudahan pengisian formulir pendaftaran yang kompleks, hierarki informasi yang jelas, dan desain antarmuka yang bersih untuk meningkatkan konversi pendaftar.',
                'image_path' => 'projects/SPMB/spmb-adzkia.webp',
            ]
        ];

        // Mencegah duplikasi saat seeder dijalankan ulang
        foreach ($projects as $project) {
            Project::firstOrCreate(
                ['title' => $project['title']], // Cek apakah judul sudah ada
                $project // Jika belum, buat baru
            );
        }
    }
}