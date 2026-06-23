<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Projects;
use App\Models\News;
// use App\Models\Member;
// use App\Models\Testimonial;
// use App\Models\Inquiry;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'projects_count' => Projects::count(),
            'news_count' => News::count(),
            // 'members_count' => Member::count(),
            // 'inquiries_count' => Inquiry::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
