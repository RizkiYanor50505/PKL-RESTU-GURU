<?php

namespace App\Http\Controllers\Aron;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AronController extends Controller
{
    /**
     * Halaman Utama / Dashboard Modul Aron.
     */
    public function index(Request $request): View
    {
        $user = $request->session()->get('auth_user');

        return view('aron.index', [
            'user' => $user,
            'title' => 'Modul Projek Aron',
        ]);
    }
}

