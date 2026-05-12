<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Category;
use App\Models\Experience;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $projects = Project::with('category')->latest()->get();
        $experiences = Experience::latest()->get();

        return view('portfolio.index', compact('categories', 'projects', 'experiences'));
    }

    // Method baru untuk halaman detail
    public function show($id)
    {
        // Mencari project berdasarkan ID, dan memuat relasi kategorinya
        $project = Project::with('category')->findOrFail($id);
        
        return view('portfolio.show', compact('project'));
    }
}