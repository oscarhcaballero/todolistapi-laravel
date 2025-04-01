<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TODO List API - Manager</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    @vite(['resources/js/app.js', 'resources/css/app.css', 'resources/css/tasks.css'])
</head>
<body>
    <div class="container">
        <h1>List of Tasks</h1>

        <!-- Title -->
        <input type="text" id="taskName" placeholder="Title...">
        <!-- Mensaje de advertencia -->
        <p id="warningMessage">Please enter a task title.</p>
        
        <!-- Selector de Status -->
        <select id="statusSelect">
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
        </select>

        <!-- Selector de Usuario -->
        <select id="userSelect"></select>


        <button class="add-btn" onclick="addTask()">ADD NEW TASK</button>

        <!-- Mensajes de éxito -->
        <p id="successMessage">Task added successfully!</p>
        <p id="successUpdatedMessage">Task updated successfully!</p>
        <p id="successDeletedMessage">Task deleted successfully!</p>


        <div id="taskList"></div>
    </div>

    <!-- Overlay para oscurecer el fondo -->
    <div id="overlay" class="overlay" style="display: none;"></div>

    <!-- Formulario de edición (oculto por defecto) -->
    <div id="editTaskForm" class="edit-task-form">
        <h2>Edit Task</h2>
        
        <input type="text" id="editTaskTitle" placeholder="Title...">
        <textarea id="editTaskDescription" placeholder="Description..."></textarea>
        
        <select id="editStatusSelect">
            <option value="pending">Pending</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
        </select>

        <select id="editUserSelect"></select>
        
        <button onclick="saveTaskChanges()">Save Changes</button>
        <button onclick="closeEditForm()">Cancel</button>
    </div>


</body>
</html>
