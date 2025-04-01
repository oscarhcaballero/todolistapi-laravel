<?php
namespace App\Services;

use App\Interfaces\TaskServiceInterface;
use App\Models\Task;
use Illuminate\Support\Facades\Log;
use App\Exceptions\TaskException;

/**
 * TaskService class
 *  
 * SOLID Principles used here:
 * 
 * (SRP) The TaskService class has a single responsibility:
 * to handle the business logic related to tasks (Task)
 * therefore, it complies with the Single Responsibility Principle 
 * 
 * (OCP) It complies with the Open/Closed Principle 
 * because you can extend the TaskService class or implement new classes 
 * that comply with the TaskServiceInterface interface without modifying existing code.
 * 
 * (LSP) Any class that implements this interface can override TaskService 
 * without breaking code that depends on it
 * 
 * (ISP) The TaskService class implements the TaskServiceInterface
 * and does not depend on methods that it does not use
 * 
 * (DIP) The TaskService implements TaskServiceInterface    
 * so it depends on abstractions (interfaces) rather than concrete classes.
 * You can easily swap out the TaskService with another implementation.
 * 
 */
class TaskService implements TaskServiceInterface
{
    /**
     * Get all tasks.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllTasks()
    {
        // Load the user relationship  
        // and select only the id and name columns from the user table
        return Task::with('user:id,name,email')->get();
    }

    /**
     * Get a task by its ID.
     *
     * @param int $id
     */
    public function getTaskById($id)
    {
        return Task::find($id);
    }

    /**
     * Create a new task.
     *
     * @param array $data
     * @return Task|null
     * 
     */
    public function createTask(array $data):?Task
    {
        
        try {
            // create a new task 
            $createdTask = Task::create($data);
    
            // Verificar si la creación falló
            if (!$createdTask) {
                throw TaskException::createFailed('Failed to create the task.', $data);
            }
    
            // return the created task
            return $createdTask;
    
        } catch (\Exception $e) {
            // Launch an exception if the task creation fails
            throw TaskException::createFailed($e->getMessage(), $data);
        }
         
    }


    /**
     * Update an existing task.
     *
     * @param Task $task
     * @param array $data
     * @return Task|null
     * @throws TaskException
     */

    public function updateTask(Task $task, array $data):?Task
    {

        try {
            // updating the task
            $updatedTask = $task->update($data);
    
            // if the update fails, log the error and throw a custom exception
            if (!$updatedTask) {
                throw TaskException::updateFailed('Failed to update the task.', $task->id, $data);
            }
    
            // return the updated task
            return $task;

        } catch (\Exception $e) {
            // exception handling throwing a custom exception
            throw TaskException::updateFailed($e->getMessage(), $task->id, $data);
        }
    }


    /**
     * Delete a task.
     *
     * @param Task $task
     * @return void
     * @throws TaskException
     */
    public function deleteTask(Task $task):void
    {
        try {
            $task->delete();
        } catch (\Exception $e) {
            // if deletion fails, log the error and throw a custom exception
            Log::error('Error deleting task: ' . $e->getMessage(), [
                'task_id' => $task->id,
            ]);
    
            throw TaskException::deleteFailed($e->getMessage(), $task->id);
        }
    }
}