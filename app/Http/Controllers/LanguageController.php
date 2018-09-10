<?php

namespace App\Http\Controllers;

use App\Language;
use Illuminate\Http\Request;
use \Illuminate\Http\Response;
class LanguageController extends Controller
{
    public function getLanguages()
    {
        return Language::all();
    }
}
