<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Education;
class EducationController extends Controller
{
    public function index(){
        $education = Education::all();
        return view('education.index', [
            'title' => 'Education',
            'description' => 'The education and academic background of Alex Davies, including a Computer Science degree from Keele University.',
            'education' => $education,
        ]);
    }

    public function show($slug){
        $education = Education::where('slug', $slug)->firstOrFail();

        return view('education.show', [
            'title' => $education->name,
            'description' => Str::limit(strip_tags($education->description ?? ''), 155),
            'education' => $education
        ]);
    }
}
