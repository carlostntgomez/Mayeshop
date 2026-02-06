<?php

namespace App\Http\Controllers;

use App\Models\Faq; // Import the Faq model
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::all(); // Fetch all FAQs
        $crumbs = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Preguntas Frecuentes'],
        ];
        return view('faq', compact('faqs', 'crumbs')); // Pass them to the view
    }
}
