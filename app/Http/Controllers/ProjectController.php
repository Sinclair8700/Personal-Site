<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\UploadedFile;

class ProjectController extends Controller
{
    public function index(): View
    {
        $projects = Project::with('images')
            ->whereHas('images')
            ->get();
        return view('projects.index', ['title' => 'Projects', 'projects' => $projects]);
    }

    public function show(Project $project): View
    {
        $project->load('images');

        return view('projects.show', [
            'title' => $project->name,
            'project' => $project
        ]);
    }

    public function create(): View
    {
        return view('projects.create', ['title' => 'Create Project']);
    }

    public function store(StoreProjectRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('images');
        $data['slug'] = Str::slug($request->name);

        $project = Project::create($data);
        $project->addImages($request->file('images'));

        return redirect()->route('projects.show', $project);
    }

    public function edit(Project $project): View
    {
        $project->load('images');
        return view('projects.edit', ['title' => 'Edit Project', 'project' => $project]);
    }

    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $project->load('images');

        DB::transaction(function () use ($request, $project) {
            $project->update($request->safe()->except(['images', 'remove_images']));

            if ($request->filled('remove_images')) {
                $remainingCount = $project->images->count() - count($request->remove_images);
                $addingNewImages = $request->hasFile('images') && count(array_filter($request->file('images'))) > 0;
                if ($remainingCount > 0 || $addingNewImages) {
                    foreach ($request->remove_images as $imageId) {
                        $image = ProjectImage::where('project_id', $project->id)->find($imageId);
                        if ($image) {
                            $image->delete();
                        }
                    }
                }
            }

            if ($request->hasFile('images')) {
                $files = $request->file('images');
                $files = is_array($files) ? Arr::flatten($files) : [$files];
                $validFiles = array_values(array_filter($files, fn ($f) => $f instanceof UploadedFile && $f->isValid()));
                if (!empty($validFiles)) {
                    $project->addImages($validFiles);
                } elseif (!empty($files)) {
                    session()->flash('warning', 'The uploaded file(s) could not be processed. Please check file size (max 8MB) and format (jpeg, png, jpg, gif, svg).');
                }
            }
        });

        return redirect()->route('projects.show', $project);
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();
        return redirect()->route('projects.index');
    }
}
