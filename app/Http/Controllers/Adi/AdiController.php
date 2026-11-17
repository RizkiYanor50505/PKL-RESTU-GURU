<?php

namespace App\Http\Controllers\Adi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdiController extends Controller
{
    /**
     * Halaman Utama / Dashboard Modul Adi.
     */
    public function index(Request $request): View
    {
        $user = $request->session()->get('auth_user');

        return view('adi.index', [
            'user' => $user,
            'title' => 'Modul Projek Adi',
        ]);
    }
}

