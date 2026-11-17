<?php

namespace App\Http\Controllers\Yanor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class YanorController extends Controller
{
    /**
     * Halaman Utama / Dashboard Modul Yanor.
     */
    public function index(Request $request): View
    {
        $user = $request->session()->get('auth_user');

        return view('yanor.index', [
            'user' => $user,
            'title' => 'Modul Projek Yanor',
        ]);
    }
}

