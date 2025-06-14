<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    public function switch($lang)
    {
        Session::put('locale', $lang);
        App::setLocale($lang);
        return redirect()->back();
    }
}
