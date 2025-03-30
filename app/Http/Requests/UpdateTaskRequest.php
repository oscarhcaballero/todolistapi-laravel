<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateTaskRequest
 *
 * This class handles the validation logic for updating an existing task.
 * It ensures that the incoming request data meets the required rules
 * before proceeding to the controller.
 */
class UpdateTaskRequest extends FormRequest
{
    public const VALID_STATUSES = ['pending', 'in_progress', 'completed'];

    /**
     * Get the validation rules that apply to the request.
     *
     * The rules are applied conditionally using "sometimes", meaning
     * the fields are only validated if they are present in the request.
     *
     * @return array<string, string> The validation rules for the request.
     */
    public function rules():array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string|max:500',
            'status' => 'sometimes|required|string|in:' . implode(',', self::VALID_STATUSES), 
            'user_id' => 'sometimes|required|integer|exists:users,id',
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
            'title.required' => 'The title field is required when provided.',
            'title.string' => 'The title must be a valid string.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'description.string' => 'The description must be a valid string.',
            'status.required' => 'The status field is required when provided.',
            'status.string' => 'The status must be a valid string.',
            'status.in' => 'The status must be one of the following: ' . implode(', ', self::VALID_STATUSES) . '.',
            'user_id.required' => 'The user ID is required when provided.',
            'user_id.integer' => 'The user ID must be a valid integer.',
            'user_id.exists' => 'The selected user does not exist in the database.',
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