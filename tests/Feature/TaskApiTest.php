<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Artisan;


class TaskApiTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Test creating a new task.
     */
    public function test_create_task(): void
    {
        // Create a user to associate with the task
        $user = User::factory()->create();
        
        // Authenticate the user with Sanctum
        Sanctum::actingAs($user);

        $payload = [
            'title' => 'Test Task',
            'description' => 'This is a test task.',
            'status' => 'pending',
            'user_id' => $user->id,
        ];

        $response = $this->postJson('/api/tasks', $payload);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Task created successfully.',
                     'data' => [
                         'title' => 'Test Task',
                         'description' => 'This is a test task.',
                         'status' => 'pending',
                     ],
                 ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'description' => 'This is a test task.',
            'status' => 'pending',
        ]);
    }


    /**
     * Test updating an existing task.
     */
    public function test_update_task(): void
    {
        // Create a user to associate with the task
        $user = User::factory()->create();
        // Authenticate the user with Sanctum
        Sanctum::actingAs($user);

        // Create a task to update
        $task = Task::factory()->create(['user_id' => $user->id]);


        $payload = [
            'title' => 'Updated Task',
            'description' => 'This is an updated task.',
            'status' => 'completed',
        ];

        $response = $this->patchJson("/api/tasks/{$task->id}", $payload);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Task updated successfully.',
                     'data' => [
                         'title' => 'Updated Task',
                         'description' => 'This is an updated task.',
                         'status' => 'completed',
                     ],
                 ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Task',
            'description' => 'This is an updated task.',
            'status' => 'completed',
        ]);
    }


    /**
    * Test updating an existing task with error in status
    */
    public function test_update_task_with_error_in_status(): void
    {
        // Create a user to associate with the task
        $user = User::factory()->create();
        // Authenticate the user with Sanctum
        Sanctum::actingAs($user);

        // Create a task to update
        $task = Task::factory()->create(['user_id' => $user->id]);


        $payload = [
            'title' => 'Updated Task',
            'description' => 'This is an updated task.',
            'status' => 'invalid', // Invalid status
        ];

        // Send the PATCH request
        $response = $this->patchJson("/api/tasks/{$task->id}", $payload);

        // Assert that the response status is 422 (Unprocessable Entity)
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['status']); // Assert that the 'status' field has validation errors

        // Assert that the task in the database has not been updated
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => $task->title, // Original title
            'description' => $task->description, // Original description
            'status' => $task->status, // Original status
        ]);
    }


    /**
    * Test updating an existing task with error in status
    */
    public function test_update_task_with_error_in_user_id(): void
    {
        // Create a user to associate with the task
        $user = User::factory()->create();
        // Authenticate the user with Sanctum
        Sanctum::actingAs($user);

        // Create a task to update
        $task = Task::factory()->create(['user_id' => $user->id]);


        $payload = [
            'title' => 'Updated Task',
            'description' => 'This is an updated task.',
            'status' => 'completed', 
            'user_id' => 9999, // Invalid user_id
        ];

        // Send the PATCH request
        $response = $this->patchJson("/api/tasks/{$task->id}", $payload);

        // Assert that the response status is 422 (Unprocessable Entity)
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['user_id']); // Assert that the 'user_id' field has validation errors
        // Assert that the task in the database has not been updated
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => $task->title, // Original title
            'description' => $task->description, // Original description
            'status' => $task->status, // Original status
        ]);
        
    }


    /**
     * Test retrieving a list of tasks.
     */
    public function test_get_tasks(): void
    {
        // Create a user to associate with the tasks
        $user = User::factory()->create();
        // Authenticate the user with Sanctum
        Sanctum::actingAs($user);

        // Create multiple tasks for the user
        // This will create 3 tasks associated with the authenticated user
        Task::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         '*' => ['id', 'title', 'description', 'status', 'user_id', 'created_at', 'updated_at'],
                     ],
                 ]);
    }



    /**
     * Test retrieving one task.
     */
    public function test_get_one_task(): void 
    {
        // Create a user to associate with the tasks
        $user = User::factory()->create();
        // Authenticate the user with Sanctum
        Sanctum::actingAs($user);

        // Create a task for the user
        $task = Task::factory()->create(['user_id' => $user->id]);
        
        // Send a GET request to retrieve the task
        $response = $this->getJson("/api/tasks/{$task->id}");
        
        // Assert the response status and structure
        $response->assertStatus(200)
                 ->assertJson([
                     'data' => [
                         'id' => $task->id,
                         'title' => $task->title,
                         'description' => $task->description,
                         'status' => $task->status,
                         'user_id' => $task->user_id,
                     ],
                 ]);

    }


    /**
     * Test retrieving a task that does not exist.
     */
    public function test_get_nonexistent_task(): void
    {
        // Create a user and authenticate with Sanctum
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Use a task ID that does not exist
        $nonexistentTaskId = 9999;

        // Send a GET request to retrieve the nonexistent task
        $response = $this->getJson("/api/tasks/{$nonexistentTaskId}");

        // Assert that the response status is 404 (Not Found)
        $response->assertStatus(404)
                ->assertJson([
                    'message' => 'Task not found.',  
                ]);
    }


    /**
     * Test deleting a task.
     */
    public function test_delete_task(): void
    {
        // Create a user to associate with the tasks
        $user = User::factory()->create();
        // Authenticate the user with Sanctum
        Sanctum::actingAs($user);

        // Create a task to delete
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Task deleted successfully.',
                 ]);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }


}
