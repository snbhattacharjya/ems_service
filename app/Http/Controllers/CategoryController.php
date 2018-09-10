<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use \Illuminate\Http\Response;
class CategoryController extends Controller
{
    public function getCategories()
    {
        return Category::all();
    }
}
