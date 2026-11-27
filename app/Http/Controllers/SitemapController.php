<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use App\Models\Education;
use App\Models\Project;
use Illuminate\Support\Carbon;

class SitemapController extends Controller
{
    public function index()
    {
        // Only projects that have images, as on the /projects index. This
        // keeps thin, empty project pages out of the sitemap.
        $projects = Project::whereHas('images')->ordered()->get()->each(function (Project $project) {
            // Most of a project page is its write-up view, and editing that doesn't touch updated_at.
            $view = resource_path('views/projects/projects/'.$project->slug.'/index.blade.php');
            $project->lastmod = is_file($view)
                ? $project->updated_at->max(Carbon::createFromTimestamp(filemtime($view)))
                : $project->updated_at;
        });

        $content = view('sitemap', [
            'projects' => $projects,
            'projectsUpdated' => $projects->max('lastmod'),
            'cvUpdated' => Cv::current()->updated_at,
            'education' => Education::all(),
        ]);

        return response($content)
            ->header('Content-Type', 'text/xml');
    }
}
