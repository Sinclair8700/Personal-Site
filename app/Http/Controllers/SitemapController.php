<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Education;

class SitemapController extends Controller
{
    public function index()
    {
        $content = view('sitemap', [
            // Only projects that have images — mirrors the /projects index and
            // keeps thin, empty project pages out of the sitemap.
            'projects' => Project::whereHas('images')->get(),
            'education' => Education::all()
        ]);
        
        return response($content)
            ->header('Content-Type', 'text/xml');
    }
}