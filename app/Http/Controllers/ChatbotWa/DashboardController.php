<?php

namespace App\Http\Controllers\ChatbotWa;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('chatbotwa.dashboard');
    }
}
