<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    //
    public function index(){
        $projects = Project::all();
        return view('projects.index', ['title' => 'Projects', 'projects' => $projects]);
    }

    public function show($slug){
        $project = Project::where('slug', $slug)->first();

        return view('projects.show', [
            'title' => $project->name, 
            'project' => $project
        ]);
    }

    public function create(){
        return view('projects.create', ['title' => 'Create Project']);
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'link' => 'required|url',
        ]);

        $data = $request->except('main_image');
        
        // Generate a slug from the name
        $slug = \Str::slug($request->name);
        $data['slug'] = $slug;
        
        // Handle image upload
        if ($request->hasFile('main_image')) {
            $file = $request->file('main_image');
            $filename = 'main.png'; // Standardize filename
            
            // Store the file in storage/app/public/projects/{slug}
            $file->storeAs('projects/'.$slug, $filename, 'public');
            
        }
        
        $project = Project::create($data);
        return redirect()->route('projects.show', $project->slug);
    }

    public function edit($slug){
        $project = Project::where('slug', $slug)->first();
        return view('projects.edit', ['title' => 'Edit Project', 'project' => $project]);
    }

    public function update(Request $request, $slug){
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'main_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048|nullable',
            'link' => 'url|nullable',
        ]);
        
        dd($request->all());
        $project = Project::where('slug', $slug)->first();
        $data = $request->except('main_image');
        $project->update($data);
        
        // Handle image upload if a new image was provided
        if ($request->hasFile('main_image')) {
            $file = $request->file('main_image');
            $filename = 'main.png'; // Standardize filename
            
            // Store the file in storage/app/public/projects/{slug}
            $file->storeAs('projects/'.$slug, $filename, 'public');
        }
        
        
        return redirect()->route('projects.show', $project->slug);
    }

    public function destroy($slug){
        $project = Project::where('slug', $slug)->first();
        $project->delete();
        return redirect()->route('projects.index');
    }
    
}
