<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Category;
use App\Models\Experience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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
public function contact(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'message' => 'required|string',
    ]);

    $data = [
        'name' => $request->name,
        'email' => $request->email,
        'bodyMessage' => $request->message,
    ];

    Mail::send([], [], function ($message) use ($data) {
        $message->to('surdik2811@gmail.com')
                ->subject('New Portfolio Message from ' . $data['name'])
                ->html("
                    <div style='font-family: sans-serif; padding: 20px; color: #334155; max-width: 600px; border: 1px solid #e2e8f0; rounded: 12px;'>
                        <h2 style='color: #1e3a8a; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;'>New Portfolio Inquiry</h2>
                 5       <p style='margin-top: 20px;'><strong>Name:</strong> {$data['name']}</p>
                        <p><strong>Email:</strong> <a href='mailto:{$data['email']}'>{$data['email']}</a></p>
                        <div style='margin-top: 20px; padding: 15px; bg-color: #f8fafc; border-left: 4px solid #3b82f6; background: #f8fafc;'>
                            <p style='margin: 0; font-weight: bold; color: #475569; margin-bottom: 5px;'>Message:</p>
                            <p style='margin: 0; white-space: pre-wrap; line-height: 1.6;'>{$data['bodyMessage']}</p>
                        </div>
                        <p style='font-size: 11px; color: #94a3b8; margin-top: 30px; border-top: 1px solid #e2e8f0; padding-top: 10px;'>Sent automatically from your portfolio website.</p>
                    </div>
                ");
    });

    // 3. Kembali ke halaman sebelumnya dengan pesan sukses berbahasa Inggris sesuai tema
    return redirect()->back()->with('success', 'Thank you! Your message has been sent successfully.');
}
}