<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Register;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FileController extends Controller
{

    public function index(): Renderable
    {
        return view('files.index');
    }

    public function show(File $file): Renderable
    {
        return view('files.show', compact('file'));
    }
    
}
