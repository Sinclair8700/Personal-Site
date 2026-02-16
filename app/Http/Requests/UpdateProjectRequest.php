<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('project')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $project = $this->route('project');
        $projectImageIds = $project ? $project->images()->pluck('id')->toArray() : [];
        $allowedIds = array_merge($projectImageIds, array_map('strval', $projectImageIds));

        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:8192',
            'link' => 'url|nullable',
            'remove_images' => 'nullable|array',
            'remove_images.*' => [Rule::in($allowedIds)],
        ];
    }
}
