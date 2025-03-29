<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;


class TaskController extends Controller
{   
    /**
     * Display a listing of tasks.
     *
     * @return \Illuminate\Http\Response
     */
    public function index():JsonResponse
    {
        $tasks = Task::all();

        $data = [
            'task' => $tasks,
            'status' => 200 ,
        ];

        return response()->json($data, 200);
        
    }

    /**
     * Display the specified task.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id):JsonResponse
    {
        $task = Task::find($id);
        
        if (!$task) {
            return response()->json([
                'message' => 'Task not found.',
                'status' => 404,
            ], 404);
        }

        $data = [
            'task' => $task,
            'status' => 200,
        ];

        return response()->json($data, 200);
        
    }

    /**
     * Store a newly created task in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request):JsonResponse
    {
        // validations of the request
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:pending,in_progress,completed',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error creating the task.',
                'errors' => $validator->errors(),
                'status' => 422,
            ], 422);
        }

        // create the task
        $task = Task::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'status' => $request->input('status'),
            'user_id' => $request->input('user_id'),
        ]);

        if (!$task) {
            return response()->json([
                'message' => 'Error creating the task.',
                'status' => 500,
            ], 500);
        }


        return response()->json([
            'message' => 'Task successfully created.',
            'task' => $task,
            'status' => 201,
        ], 201);  
    }

    /**
     * Update the specified task in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id):JsonResponse
    {
        $task = Task::find($id);
        if (!$task) {
            return response()->json([
                'message' => 'Task not found.',
                'status' => 404,
            ], 404);
        }


        // validations of the request
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:pending,in_progress,completed',
            'user_id' => 'required|integer|exists:users,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error updating the task.',
                'errors' => $validator->errors(),
                'status' => 422,
            ], 422);
        }

        // update the task
        $task->title = $request->input('title');
        $task->description = $request->input('description');
        $task->status = $request->input('status');
        $task->user_id = $request->input('user_id');
        $task->save();

        return response()->json([
            'message' => 'Task successfully updated.',
            'task' => $task,
            'status' => 200,
        ], 200);
    }

    /**
     * Update the specified task in storage partially.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updatePartial(Request $request, $id):JsonResponse
    {
        $task = Task::find($id);
        if (!$task) {
            return response()->json([
                'message' => 'Task not found.',
                'status' => 404,
            ], 404);
        }

        // validations of the request
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'status' => 'sometimes|required|string|in:pending,in_progress,completed',
            'user_id' => 'sometimes|required|integer|exists:users,id',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error updating the task.',
                'errors' => $validator->errors(),
                'status' => 422,
            ], 422);
        }

        // update the task
        $task->update($request->only(['title', 'description', 'status', 'user_id']));

        return response()->json([
            'message' => 'Task successfully updated.',
            'task' => $task,
            'status' => 200,
        ], 200);
    }


    /**
     * Remove the specified task from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id):JsonResponse
    {
        $task = Task::find($id);
        if (!$task) {
            return response()->json([
                'message' => 'Task not found.',
                'status' => 404,
            ], 404);
        }

        $task->delete();

        return response()->json([
            'message' => 'Task successfully deleted.',
            'status' => 200,
        ], 200);
    }
    
 
    
}
