<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Education;

class SitemapController extends Controller
{
    public function index()
    {
        $content = view('sitemap', [
            'projects' => Project::all(),
            'education' => Education::all()
        ]);
        
        return response($content)
            ->header('Content-Type', 'text/xml');
    }
}