<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use App\Interfaces\TaskServiceInterface;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Helpers\ApiResponse;
use App\Exceptions\TaskException;

class TaskController extends Controller
{   
     
    protected $taskService;

    /**
     * Create a new controller instance, using the TaskServiceInterface
     * 
     * SOLID Principles used: 
     *  Dependency Inversion Principle (DIP)
     *
     * @param TaskServiceInterface $taskService
     */
    public function __construct(TaskServiceInterface $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of tasks.
     * 
     * SOLID Principles used: 
     *  Single Responsibility Principle (SRP)
     *  Open/Close Principle (OCP)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index():JsonResponse
    {
        // get all tasks using the TaskService (SRP)
        $tasks = $this->taskService->getAllTasks();

        if ($tasks->isEmpty()) {
            // if no tasks found, return a success response
            // is not an error that no tasks are found
            // managing the response (OCP)
            return ApiResponse::success($tasks, 'No tasks found.');
        }

        // if tasks found, return a success response
        // managing the response (OCP)
        return ApiResponse::success($tasks, 'Tasks retrieved successfully.');

    }

    /**
     * Display the specified task by id
     *
     * SOLID Principles used: 
     *  Single Responsibility Principle (SRP)
     *  Open/Close Principle (OCP)
     * 
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id):JsonResponse
    {
        // get the task by id, using the TaskService (SRP)
        $task = $this->taskService->getTaskById($id);
        
        if (!$task) {
            // if task does not exist, return an error response
            // managing the response (OCP)
            return ApiResponse::error('Task not found.', 404);
        }

        // if task exists, return a success response
        // managing the response (OCP)
        return ApiResponse::success($task, 'Task retrieved successfully.');
        
    }

    /**
     * Store a newly created task in storage.
     * 
     * SOLID Principles used: 
     *  Single Responsibility Principle (SRP)
     *  Open/Close Principle (OCP)
     * 
     * @param  \App\Http\Requests\StoreTaskRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTaskRequest $request):JsonResponse
    { 
        $validatedData = $request->validated();
        
        // validate the request using the StoreTaskRequest (SRP)
        $task = $this->taskService->createTask($validatedData);
        
        // if task creation is successful, return a success response
        // managing the response (OCP)
        return ApiResponse::success($task, 'Task created successfully.');
           
    }

    /**
     * Update the specified task by id, in storage.
     * 
     * SOLID Principles used: 
     *  Single Responsibility Principle (SRP)
     *  Open/Close Principle (OCP)
     *
     * @param  UpdateTaskRequest $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateTaskRequest $request, $id):JsonResponse
    {
        // get the task by id using the TaskService (SRP)
        $task = $this->taskService->getTaskById($id);
        if (!$task) {
            // if task does not exist, return an error response
            // managing the response (OCP)
            return ApiResponse::error('Task not found.', 404);
        }

        // validate the request using the UpdateTaskRequest (SRP)
        $updatedTask = $this->taskService->updateTask($task, $request->validated());
        if (!$updatedTask) {
            // if task update fails, return an error response
            // managing the response (OCP)
            return ApiResponse::error('Error updating the task.', 500);
        }
        
        // if task update is successful, return a success response
        // managing the response (OCP)
        return ApiResponse::success($updatedTask, 'Task updated successfully.');

    }

    


    /**
     * Remove the specified task by id from storage.
     * 
     * SOLID Principles used: 
     *  Single Responsibility Principle (SRP)
     *  Open/Close Principle (OCP)
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id):JsonResponse
    {
        // get the task by id using the TaskService (SRP)
        $task = $this->taskService->getTaskById($id);
        if (!$task) {
            // if task does not exist, return an error response
            // managing the response (OCP)
            return ApiResponse::error('Task not found.', 404);
        }

        // delete the task using the TaskService (SRP)
        $this->taskService->deleteTask($task);

        // if task deletion is successful, return a success response
        // managing the response (OCP)
        return ApiResponse::success(null, 'Task deleted successfully.');

    }
    
 
    
}
