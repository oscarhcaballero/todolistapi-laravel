<?php
namespace App\Interfaces;

use App\Models\Task;

/**
 * Interface TaskServiceInterface
 *
 * This interface defines the contract for the TaskService.
 * It ensures that any class implementing this interface provides
 * the necessary methods to manage tasks.
 */
interface TaskServiceInterface
{
    /**
     * Retrieve all tasks.
     *
     * @return \Illuminate\Database\Eloquent\Collection|Task[] List of all tasks.
     */
    public function getAllTasks();
    
    /**
     * Retrieve a specific task by its ID.
     *
     * @param int $id The ID of the task to retrieve.
     * @return Task|null The task if found, or null if not.
     */
    public function getTaskById($id);

    /** 
     * Create a new task.
     *
     * @param array $data The data to create the task (e.g., title, description, status, user_id).
     * @return Task|null The created task, or null if creation failed.
     */
    public function createTask(array $data):?Task;

    /**
     * Update an existing task.
     *
     * @param Task $task The task to update.
     * @param array $data The data to update the task (e.g., title, description, status).
     * @return Task|null The updated task, or null if the update failed.
     */
    public function updateTask(Task $task, array $data):?Task;

    /**
     * Delete a specific task.
     *
     * @param Task $task The task to delete.
     * @return void
     */
    public function deleteTask(Task $task):void;
}
