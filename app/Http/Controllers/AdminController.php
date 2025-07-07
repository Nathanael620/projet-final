<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard');
    }

    public function users(): View
    {
        return view('admin.users');
    }

    public function sessions(): View
    {
        return view('admin.sessions');
    }
}
