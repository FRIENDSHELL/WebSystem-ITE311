<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function template()
    {
        return view('template'); // Loads contact.php from Views
    }
    public function index()
    {
        return view('index'); // Loads index.php from Views
    }

    public function about()
    {
        return view('about'); // Loads about.php from Views
    }

    public function contact()
    {
        return view('contact'); // Loads contact.php from Views
    }
}
