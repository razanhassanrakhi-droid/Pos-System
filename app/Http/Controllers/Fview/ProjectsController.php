<?php

namespace App\Http\Controllers\Fview;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Projects; // هنا احنا بنستعمل الـ Model

class ProjectsController extends Controller
{
    public function index()
    {
        $projects = Projects::all(); // جلب كل المشاريع
        return view('inde', compact('projects'));
    }
}
