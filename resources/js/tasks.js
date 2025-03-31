const API_URL = "/api/tasks"; 

async function fetchTasks() {
    const response = await fetch(API_URL);
    const tasks = await response.json();
    displayTasks(tasks);
}

async function addTask() {
    const taskText = document.getElementById("taskInput").value;
    if (!taskText) return;
    
    await fetch(API_URL, {
        method: "POST",
        headers: { 
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ name: taskText })
    });

    document.getElementById("taskInput").value = "";
    fetchTasks();
}

async function deleteTask(id) {
    await fetch(`${API_URL}/${id}`, { 
        method: "DELETE",
        headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
    });
    fetchTasks();
}

function displayTasks(tasks) {
    const taskList = document.getElementById("taskList");
    taskList.innerHTML = "";
    tasks.forEach(task => {
        const div = document.createElement("div");
        div.className = "task";
        div.innerHTML = `
            <span>${task.name}</span>
            <button onclick="deleteTask('${task.id}')">Eliminar</button>
        `;
        taskList.appendChild(div);
    });
}

document.addEventListener("DOMContentLoaded", fetchTasks);
