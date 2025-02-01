<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    //
    public function index(){
        $projects = Project::all();
        dd($projects);
        return view('projects.index', ['title' => 'Projects', 'projects' => $projects]);
    }

    public function show($slug){
        $project = Project::where('slug', $slug)->first();

        return view('projects.show', [
            'title' => $project->name, 
            'project' => $project
        ]);
    }
}
