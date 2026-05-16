<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Category;
use App\Models\Experience;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    // Fungsi untuk Halaman Utama (Dibatasi 6 Proyek)
public function index()
{
    // Mengambil 5 proyek terbaru agar rapi (1 besar + 4 kecil)
    $projects = Project::with('category')->latest()->take(5)->get();
    $categories = Category::all();
    $experiences = Experience::latest()->get();

    return view('portfolio.index', compact('projects', 'categories', 'experiences'));
}

    // Fungsi Baru untuk Halaman Archive (Semua Proyek)
    public function projects()
    {
        $projects = Project::with('category')->latest()->get();
        $categories = Category::all();

        return view('portfolio.projects', compact('projects', 'categories'));
    }

    // Fungsi untuk Detail Proyek
    public function show($id)
    {
        $project = Project::with('category')->findOrFail($id);
        return view('portfolio.show', compact('project'));
    }
}