<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TODO List API - Manager</title>
    @vite(['resources/js/app.js', 'resources/css/app.css', 'resources/css/tasks.css'])
</head>
<body>
    <div class="container">
        <h1>List of Tasks</h1>
        <input type="text" id="taskInput" placeholder="Title...">
        <button onclick="addTask()">ADD TASK</button>

        <div id="taskList"></div>
    </div>
</body>
</html>
