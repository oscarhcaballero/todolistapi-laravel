const API_URL = "/api/tasks"; 


async function login(email, password) {
    const response = await fetch("/api/login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password }),
    });

    const data = await response.json();

    if (response.ok) {
        localStorage.setItem("api_token", data.token); // Saves the token
        console.log("Login exitoso. Token:", data.token); // for debugging, erase in production! 
    } else {
        console.error("Error de login:", data.message);
    }
}


// Fetch List of Tasks
async function fetchTasks() {
    
    //authentication
    const token = localStorage.getItem("api_token");
    if (!token) {
        console.error("No API Token found. Please log in first.");
        return;
    }



    const response = await fetch(API_URL, {
        method: "GET",
        credentials: "include", // IMPORTANTE: Permite que se envíen las cookies de sesión
        headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json",
        }
    });
    
    if (!response.ok) {
        console.error("Error al obtener tareas:", response.statusText);
        return;
    }

    
    const response_tasks = await response.json();

    const tasks = response_tasks.data;

    displayTasks(tasks);
}


// Function for Add New Task button
async function addTask() {

    //authentication
    const token = localStorage.getItem("api_token");
    if (!token) {
        console.error("No API Token found. Please log in first.");
        return;
    }

    // form data
    const taskName = document.getElementById("taskName").value;
    const statusSelected = document.getElementById("statusSelect").value;
    const userIdSelected = document.getElementById("userSelect").value;
    //warning message
    const warningMessage = document.getElementById("warningMessage");  
    //success message
    const successMessage = document.getElementById("successMessage"); 

    if (!taskName) {
        warningMessage.style.display = "block";  
        setTimeout(() => {
            warningMessage.style.display = "none";
        }, 3000);
        return;  
    } else {
        warningMessage.style.display = "none"; 
    }

  

    const response = await fetch(API_URL, {
        method: "POST",
        credentials: "include",
        headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ title: taskName, status: statusSelected, user_id: userIdSelected })
    });

    if (!response.ok) {
        console.error("Error adding a new task:", response.statusText);
        return;
    }

    //reset form
    document.getElementById("taskName").value = "";
    fetchTasks();

    // Mostrar el mensaje de éxito
    successMessage.style.display = "block"; 

    // Ocultar el mensaje de éxito después de 3 segundos
    setTimeout(() => {
        successMessage.style.display = "none";
    }, 3000);

}
window.addTask = addTask; // Add button






let editingTaskId = null;

// function for Edit Task Button
async function editTask(id) {
    // Mostrar el formulario de edición
    const editForm = document.getElementById("editTaskForm");
    editForm.style.display = "block";

    // Mostrar el overlay
    const overlay = document.getElementById("overlay");
    overlay.style.display = "block"; // Mostrar el overlay


    // Obtener la tarea que se va a editar
    const token = localStorage.getItem("api_token");
    if (!token) {
        console.error("No API Token found. Please log in first.");
        return;
    }

    const response = await fetch(`${API_URL}/${id}`, {
        method: "GET",
        headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json",
        }
    });

    if (!response.ok) {
        console.error("Error fetching task details:", response.statusText);
        return;
    }

    const responseJson = await response.json();
    const task = responseJson.data;

    // Llenar los campos del formulario con los datos de la tarea
    document.getElementById("editTaskTitle").value = task.title;
    document.getElementById("editTaskDescription").value = task.description || "";
    document.getElementById("editStatusSelect").value = task.status;
    document.getElementById("editUserSelect").value = task.user_id;

    // Guardar el ID de la tarea que se está editando
    editingTaskId = task.id;
    
    // Cargar los usuarios para el selector
    await loadUsersInEditSelector(task.user_id);

    
}


// Load users in edit form of the task
async function loadUsersInEditSelector(selectedUserId = null) {
    const token = localStorage.getItem("api_token");
    if (!token) {
        console.error("No API Token found. Please log in first.");
        return;
    }

    const response = await fetch("/api/users", {
        method: "GET",
        headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json",
        },
    });

    if (!response.ok) {
        console.error("Error retrieving Users:", response.statusText);
        return;
    }

    const users = await response.json();
    const userSelect = document.getElementById("editUserSelect");
    userSelect.innerHTML = "";  

    users.forEach(user => {
        const option = document.createElement("option");
        option.value = user.id;
        option.textContent = user.name;
        if (user.id === selectedUserId) {
            option.selected = true;  
        }
        userSelect.appendChild(option);
    });
}



// Save changes in edited task
async function saveTaskChanges() {
    const token = localStorage.getItem("api_token");
    if (!token) {
        console.error("No API Token found. Please log in first.");
        return;
    }

    const updatedTitle = document.getElementById("editTaskTitle").value;
    const updatedDescription = document.getElementById("editTaskDescription").value;
    const updatedStatus = document.getElementById("editStatusSelect").value;
    const updatedUserId = document.getElementById("editUserSelect").value;

    const response = await fetch(`${API_URL}/${editingTaskId}`, {
        method: "PATCH",
        headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json",
        },
        body: JSON.stringify({
            title: updatedTitle,
            description: updatedDescription,
            status: updatedStatus,
            user_id: updatedUserId
        }),
    });

    if (!response.ok) {
        console.error("Error updating task:", response.statusText);
        return;
    }

    // Close the edit form and update list of tasks 
    closeEditForm();
    fetchTasks();

    // Show the success message
    successUpdatedMessage.style.display = "block"; 

    // Hide the success message after 3 seconds
    setTimeout(() => {
        successUpdatedMessage.style.display = "none";
    }, 3000);

}
window.saveTaskChanges = saveTaskChanges;




// Close edit form without saving changes
function closeEditForm() {
    const editForm = document.getElementById("editTaskForm");
    editForm.style.display = "none";
    // Ocultar el overlay
    const overlay = document.getElementById("overlay");
    overlay.style.display = "none";
}
window.closeEditForm = closeEditForm;




// function for deleting tasks
async function deleteTask(id) {
    //authentication    
    const token = localStorage.getItem("api_token");
    if (!token) {
        console.error("No API Token found. Please log in first.");
        return;
    }

    console.log("Eliminando tarea con ID:", id); // debugging
    const confirmDelete = confirm("Are you sure to delete this task?");  
    if (!confirmDelete) return;

    await fetch(`${API_URL}/${id}`, { 
        method: "DELETE",
        headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json",
        },
    });
    fetchTasks();

    // Mostrar el mensaje de éxito
    successDeletedMessage.style.display = "block"; 

    // Ocultar el mensaje de éxito después de 3 segundos
    setTimeout(() => {
        successDeletedMessage.style.display = "none";
    }, 3000);
}



// function for writing the list os tasks retrieved
function displayTasks(tasks) {
    
    console.log("Tareas obtenidas:", tasks); // debugging

    const taskList = document.getElementById("taskList");
    taskList.innerHTML = "";


    // are there tasks in "data" ?
    if (!Array.isArray(tasks) || tasks.length === 0) {
        taskList.innerHTML = "<p>No tasks available</p>";
        return;
    }

    // header 
    const header = document.createElement("div");
    header.className = "task-header";
    header.innerHTML = `
        <span>ID</span>
        <span>Title</span>
        <span>Status</span>
        <span>User</span>
        <span>Actions</span>
    `;
    taskList.appendChild(header);
    

    // list of tasks
    tasks.forEach(task => {
        const userName = task.user ? task.user.name : "Unknown"; // Si no hay usuario, mostrar "Unknown"

       // div of each task
        const div = document.createElement("div");
        div.className = "task";
        div.innerHTML = `
                <span>${task.id}</span>
                <span>${task.title}</span>
                <span>${task.status}</span>
                <span>${userName}</span>
             
            <div class="task-actions">
                <button class="edit-btn" data-id="${task.id}"><i class="fas fa-edit"></i></button>
                <button class="delete-btn" data-id="${task.id}"><i class="fas fa-trash"></i></button>
            </div>
        `;
        taskList.appendChild(div);
    });
}


// Load users in the selector 
async function loadUsers() {

    //authentication
    const token = localStorage.getItem("api_token");
    if (!token) {
        console.error("No API Token found. Please log in first.");
        return;
    }

    // List of users
    const response = await fetch("/api/users", { 
        method: "GET",
        headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json",
        },
    });
    if (!response.ok) {
        console.error("Error retrieving Users:", response.statusText);
        return;
    }

    const data = await response.json();

    const userSelect = document.getElementById("userSelect");
    userSelect.innerHTML = "";  

    data.forEach(user => {
        const option = document.createElement("option");
        option.value = user.id;
        option.textContent = user.name;
        userSelect.appendChild(option);
    });
}



// Waiting for DOM is fully loaded before execute this code
document.addEventListener("DOMContentLoaded", () => {
    //login user for retrieving de API TOKEN
    login("admin@example.com","password1"); 

    // Load users in selector
    loadUsers();

    // Loading tasks on page load
    fetchTasks(); 

    
    // Edit and delete buttons
    document.getElementById("taskList").addEventListener("click", async (event) => {
        
        const taskId = event.target.closest("button")?.dataset.id;  
        if (!taskId) {
            console.log("No task ID found in the event target.");
            return; 
        } 
  
        // edit task button
        if (event.target.closest(".edit-btn")) {
            editTask(taskId);
        }
    
        // delete task button
        if (event.target.closest(".delete-btn")) {
            deleteTask(taskId);
        }
    });
    

});

