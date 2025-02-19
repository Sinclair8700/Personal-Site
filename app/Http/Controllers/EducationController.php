<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Education;
class EducationController extends Controller
{
    public function index(){
        $education = Education::all();
        return view('education.index', ['title' => 'Education', 'education' => $education]);
    }

    public function show($slug){
        $education = Education::where('slug', $slug)->first();

        return view('education.show', [
            'title' => $education->name, 
            'education' => $education
        ]);
    }
}
