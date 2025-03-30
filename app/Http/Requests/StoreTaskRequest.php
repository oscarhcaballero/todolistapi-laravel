<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class StoreTaskRequest
 *
 * This class handles the validation logic for creating a new task.
 * It ensures that the incoming request data meets the required rules
 * before proceeding to the controller.
 */
class StoreTaskRequest extends FormRequest
{
    public const VALID_STATUSES = ['pending', 'in_progress', 'completed'];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string> The validation rules for the request.
     */
    public function rules():array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'status' => 'required|string|in:' . implode(',', self::VALID_STATUSES),
            'user_id' => 'required|integer|exists:users,id',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string> The custom error messages for validation failures.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The title field is required.',
            'title.string' => 'The title must be a string.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'description.string' => 'The description must be a string.',
            'status.required' => 'The status field is required.',
            'status.in' => 'The status must be one of the following: ' . implode(', ', self::VALID_STATUSES) . '.',
            'user_id.required' => 'The user ID is required.',
            'user_id.integer' => 'The user ID must be an integer.',
            'user_id.exists' => 'The user ID must exist in the users table.',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool True if the user is authorized, false otherwise.
     */
    public function authorize(): bool
    {
        return true; // allow validation
    }
}