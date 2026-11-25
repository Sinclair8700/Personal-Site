<?php

namespace App\Http\Controllers;

use App\Models\Cv;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CvController extends Controller
{
    public function show(Request $request): View
    {
        $cv = Cv::current();
        $isAdmin = Gate::allows('manage-cv');

        return view('cv.show', [
            'title' => 'CV',
            'description' => Str::limit(strip_tags(Cv::markdown($cv->field('profile'))), 155),
            'cv' => $cv,
            'isAdmin' => $isAdmin,
            // The phone number only shows publicly when switched on; admins always see it,
            // so a PDF saved while signed in includes it.
            'showPhone' => $cv->field('phone') !== '' && ($cv->field('show_phone', false) || $isAdmin),
        ]);
    }

    public function edit(): View
    {
        return view('cv.edit', [
            'title' => 'Edit CV',
            'cv' => Cv::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'headline' => ['nullable', 'string', 'max:160'],
            'location' => ['nullable', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:40'],
            'website' => ['nullable', 'url', 'max:255'],
            'github' => ['nullable', 'url', 'max:255'],
            'extra' => ['nullable', 'string', 'max:160'],
            'profile' => ['nullable', 'string', 'max:5000'],
            'skills' => ['nullable', 'string', 'max:5000'],
            'experience' => ['nullable', 'string', 'max:30000'],
            'projects' => ['nullable', 'string', 'max:20000'],
            'education' => ['nullable', 'string', 'max:10000'],
            'interests' => ['nullable', 'string', 'max:3000'],
        ]);

        $data = array_map(fn ($v) => $v === null ? '' : str_replace("\r\n", "\n", $v), $data);
        $data['show_phone'] = $request->boolean('show_phone');

        $cv = Cv::current();
        $cv->data = $data;
        $cv->save();

        return redirect()->route('cv.edit')->with('success', 'CV saved.');
    }

    public function markdown(): Response
    {
        $cv = Cv::current();
        $includePhone = $cv->field('show_phone', false) || Gate::allows('manage-cv');
        $filename = Str::slug($cv->field('name', 'cv')).'-cv.md';

        return response($cv->toMarkdown($includePhone), 200, [
            'Content-Type' => 'text/markdown; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
