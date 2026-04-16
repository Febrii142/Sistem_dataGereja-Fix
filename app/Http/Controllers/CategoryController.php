<?php

namespace App\Http\Controllers;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_categories')->only(['index']);
    }

    public function index()
    {
        return view('categories.index');
    }
}
