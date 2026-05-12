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
            Category::create([
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
                'image_path' => 'projects/Luxsuits/luxsuits.png',
            ],
            [
                'category_id' => $graphicId,
                'title' => 'Manufer Super League',
                'description' => 'Pengembangan aset visual untuk event kompetitif Manufer Super League. Mencakup desain media sosial hingga elemen fisik di lapangan seperti desain gate, dengan identitas warna berani dan struktur yang kuat.',
                'image_path' => 'projects/MSL/manufer.png',
            ],
            [
                'category_id' => $uiUxId,
                'title' => 'Money Tracker App',
                'description' => 'Desain antarmuka aplikasi mobile untuk melacak keuangan pribadi (Money Tracker). Berfokus pada kesederhanaan, kemudahan penggunaan (usability), dan visualisasi data keuangan yang jelas.',
                'image_path' => 'projects/moneytracker.png',
            ],
            [
                'category_id' => $itId,
                'title' => 'SPMB Universitas Adzkia',
                'description' => 'Merancang antarmuka terstruktur untuk sistem penerimaan mahasiswa baru (SPMB) Universitas Adzkia. Berfokus pada navigasi yang jelas, kemudahan formulir, dan alur informasi yang efisien.',
                'image_path' => 'projects/spmb-adzkia.png',
            ],
            [
                'category_id' => $graphicId,
                'title' => 'Top Scorer Arena',
                'description' => 'Eksplorasi visual berenergi tinggi untuk bisnis minisoccer Top Scorer Arena. Mempertahankan identitas olahraga yang kuat dan dinamis secara konsisten di berbagai platform media sosial.',
                'image_path' => 'projects/TSA/topscorer.png',
            ],
            [
                'category_id' => $itId,
                'title' => 'Landing Page Informatika\'23',
                'description' => 'Desain landing page modern untuk menampilkan informasi terstruktur dan meningkatkan keterlibatan pengguna, berfokus pada hierarki yang jelas dan presentasi visual yang bersih.',
                'image_path' => 'projects/if23.png',
            ],
            [
                'category_id' => $graphicId,
                'title' => '523 Studio',
                'description' => 'Adaptasi berbagai gaya visual untuk kebutuhan klien agensi 523 Studio. Mencakup desain poster rekrutmen dan kampanye media sosial dengan fokus pada komunikasi yang jelas.',
                'image_path' => 'projects/523/523studio.png',
            ],
            [
                'category_id' => $graphicId,
                'title' => 'FTI UNAND',
                'description' => 'Merancang materi visual akademik untuk Fakultas Teknologi Informasi Universitas Andalas dengan menyeimbangkan informasi padat melalui tata letak yang bersih dan hierarki visual.',
                'image_path' => 'projects/FTI/fti-unand.png',
            ]
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }
    }
}