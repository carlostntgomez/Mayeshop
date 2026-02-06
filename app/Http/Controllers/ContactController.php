<?php

namespace App\Http\Controllers;

use App\Models\ContactPageContent; // Import the ContactPageContent model
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $contactContent = ContactPageContent::first(); // Fetch the first (and likely only) record
        $crumbs = [
            ['name' => 'Inicio', 'url' => route('home')],
            ['name' => 'Contacto'],
        ];
        return view('contact', compact('contactContent', 'crumbs')); // Pass it to the view
    }
}
