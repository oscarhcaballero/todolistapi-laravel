const API_URL = "/api/tasks"; 


// Llamar primero a este endpoint para establecer el token CSRF
async function getCsrfToken() {
    await fetch("/sanctum/csrf-cookie", {
        method: "GET",
        credentials: "include"
    });
}


async function login(email, password) {
    const response = await fetch("/api/login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password }),
    });

    const data = await response.json();

    if (response.ok) {
        localStorage.setItem("api_token", data.token); // Guardar el token
        console.log("Login exitoso. Token:", data.token);
    } else {
        console.error("Error de login:", data.message);
    }
}



async function fetchTasks() {
    await getCsrfToken();
    await login("user1@example.com","password1");

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

async function addTask() {

    const token = localStorage.getItem("api_token");
    if (!token) {
        console.error("No API Token found. Please log in first.");
        return;
    }


    const taskText = document.getElementById("taskInput").value;
    if (!taskText) return;
    

    const response = await fetch(API_URL, {
        method: "POST",
        credentials: "include",
        headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ title: taskText, status: "pending", user_id: 1 })
    });

    if (!response.ok) {
        console.error("Error adding a new task:", response.statusText);
        return;
    }


    document.getElementById("taskInput").value = "";
    fetchTasks();
}



async function editTask(id) {
    const newName = prompt("Edita el nombre de la tarea:");
    if (!newName) return;

    const token = localStorage.getItem("api_token");
    if (!token) {
        console.error("No API Token found. Please log in first.");
        return;
    }

    await fetch(`${API_URL}/${id}`, {
        method: "PATCH",
        headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ title: newName })
    });

    fetchTasks(); // Vuelve a cargar las tareas
}



async function deleteTask(id) {

    const token = localStorage.getItem("api_token");
    if (!token) {
        console.error("No API Token found. Please log in first.");
        return;
    }

    await fetch(`${API_URL}/${id}`, { 
        method: "DELETE",
        headers: {
            "Authorization": `Bearer ${token}`,
            "Content-Type": "application/json",
        },
    });
    fetchTasks();
}



function displayTasks(tasks) {
    console.log(tasks);
    
    const taskList = document.getElementById("taskList");
    taskList.innerHTML = "";


    // Verificar si hay tareas en "data"
    if (!Array.isArray(tasks) || tasks.length === 0) {
        taskList.innerHTML = "<p>No tasks available</p>";
        return;
    }
    
    
    tasks.forEach(task => {
        const div = document.createElement("div");
        div.className = "task";
        div.innerHTML = `
            <span>${task.id}</span>
            <span>${task.title}</span>
            <span>${task.status}</span>
            <span>${task.user_id}</span>
            <button class="edit-btn" data-id="${task.id}">EDIT</button>
            <button class="delete-btn" data-id="${task.id}">DELETE</button>
        `;
        taskList.appendChild(div);
    });
}

document.addEventListener("DOMContentLoaded", () => {
    fetchTasks(); // Loading tasks on page load

    // Add button    
    document.querySelector("button").addEventListener("click", addTask);
    
    // Edit and delete buttons
    document.getElementById("taskList").addEventListener("click", async (event) => {
        const taskId = event.target.dataset.id;
    
        // Editar tarea
        if (event.target.classList.contains("edit-btn")) {
            editTask(taskId);
        }
    
        // Eliminar tarea
        if (event.target.classList.contains("delete-btn")) {
            deleteTask(taskId);
        }
    });
    

});

