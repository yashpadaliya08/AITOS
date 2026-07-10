@extends('layouts.app')

@section('title', 'AITOS - Team Planning')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Team Planning & Task Assignment</h3>
        <p class="text-muted mb-0">Drag and drop tasks to balance ownership across developers and AI agents.</p>
    </div>
    <span class="badge bg-secondary px-3 py-2">Phase 4: Planning</span>
</div>

<!-- Task board section -->
<div class="row g-4 mb-4">
    <!-- Backlog Column -->
    <div class="col-lg-3">
        <div class="card aitos-card border-light-subtle h-100 shadow-sm" style="background-color: #fcfdfe;">
            <div class="aitos-card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-list-task me-1"></i> Task Backlog</span>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="triggerAddTaskModal()">
                    <i class="bi bi-plus-lg"></i> Add
                </button>
            </div>
            <div class="aitos-card-body">
                <div class="team-task-column" id="col-backlog" ondragover="allowDrop(event)" ondragenter="dragEnter(event)" ondragleave="dragLeave(event)" ondrop="drop(event, 'backlog')">
                    <!-- Task Cards injected by JS -->
                </div>
            </div>
        </div>
    </div>

    <!-- Dynamic Team Member Columns -->
    <div class="col-lg-9">
        <div class="row g-3" id="teamColumnsContainer">
            <!-- Members injected dynamically by JS -->
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center border-top pt-4 mb-4">
    <a href="/blueprint" class="btn btn-outline-secondary px-4 py-2"><i class="bi bi-arrow-left me-1"></i> Back to Blueprints</a>
    <button type="button" class="btn btn-primary btn-lg px-5 py-3 fw-bold shadow-sm" onclick="confirmTeamPlanning()">
        Confirm Planning & Compile Context <i class="bi bi-terminal ms-1"></i>
    </button>
</div>

<!-- Add Task Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="addTaskModalLabel">Create Custom Task</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="customTaskText" class="form-label fw-semibold">Task Title / Workload Description</label>
                    <input type="text" class="form-control border-light-subtle" id="customTaskText" placeholder="e.g. Set up JWT middleware token auth" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="createCustomTask()">Add to Backlog</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let projectTasks = [];
    let projectMembers = [];

    document.addEventListener("DOMContentLoaded", () => {
        loadBoardState();
    });

    function loadBoardState() {
        const state = getProjectState();
        projectMembers = state.teamMembers || [];
        projectTasks = state.tasks || [];

        // Build Member Columns
        const container = document.getElementById("teamColumnsContainer");
        container.innerHTML = "";
        
        if (projectMembers.length === 0) {
            container.innerHTML = `
                <div class="col-12 text-center py-5">
                    <p class="text-muted">No team members defined. Return to Step 3 of the Wizard to add members.</p>
                </div>
            `;
            return;
        }

        const colWidth = Math.max(12 / projectMembers.length, 4); // Grid width distribution
        
        projectMembers.forEach((member, index) => {
            const memberId = member.name.toLowerCase().split(' ')[0] + '-' + index;
            
            // Render member card column
            const colDiv = document.createElement("div");
            colDiv.className = `col-md-${colWidth}`;
            colDiv.innerHTML = `
                <div class="card aitos-card border-light-subtle h-100 shadow-sm">
                    <div class="aitos-card-header bg-white pb-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">${escapeHtml(member.name)}</h6>
                                <small class="text-muted">${escapeHtml(member.role)}</small>
                            </div>
                            <span class="badge bg-light text-primary border">${escapeHtml(member.ai)}</span>
                        </div>
                        <div class="mt-2 text-muted" style="font-size: 0.75rem;">
                            <strong>Owned Scope:</strong> ${escapeHtml(member.role.split(' ')[0])} Modules
                        </div>
                        <!-- Progress info -->
                        <div class="mt-3">
                            <div class="d-flex justify-content-between align-items-center" style="font-size: 0.7rem; font-weight: 600;">
                                <span class="text-muted">Tasks Complete</span>
                                <span class="text-primary" id="prog-txt-${memberId}">0%</span>
                            </div>
                            <div class="progress mt-1" style="height: 5px;">
                                <div class="progress-bar" id="prog-bar-${memberId}" style="width: 0%;"></div>
                            </div>
                        </div>
                    </div>
                    <div class="aitos-card-body p-3">
                        <div class="team-task-column" id="col-${memberId}" ondragover="allowDrop(event)" ondragenter="dragEnter(event)" ondragleave="dragLeave(event)" ondrop="drop(event, '${memberId}')">
                            <!-- Drag task cards here -->
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(colDiv);
        });

        renderTasks();
    }

    function renderTasks() {
        // Clear all columns
        const cols = document.querySelectorAll(".team-task-column");
        cols.forEach(c => c.innerHTML = "");

        projectTasks.forEach((task, idx) => {
            // Check if column exists, else map to backlog
            let targetColId = `col-${task.column}`;
            const targetCol = document.getElementById(targetColId);
            
            const card = document.createElement("div");
            card.className = "task-card";
            card.setAttribute("draggable", "true");
            card.setAttribute("id", `task-card-${idx}`);
            card.setAttribute("ondragstart", `dragStart(event, ${idx})`);
            card.innerHTML = `
                <div class="d-flex justify-content-between align-items-start">
                    <p class="mb-0 text-dark small fw-medium">${escapeHtml(task.text)}</p>
                    <button type="button" class="btn btn-link text-muted p-0 ms-2 lh-1" onclick="deleteTask(${idx})">
                        <i class="bi bi-x-circle-fill small"></i>
                    </button>
                </div>
            `;

            if (targetCol) {
                targetCol.appendChild(card);
            } else {
                // Orphaned column names fallback to backlog
                document.getElementById("col-backlog").appendChild(card);
            }
        });

        // Add empty states helper if empty
        cols.forEach(col => {
            if (col.children.length === 0) {
                col.innerHTML = `<div class="text-center py-4 text-muted small drag-placeholder"><i class="bi bi-arrow-down-short"></i> Drag tasks here</div>`;
            }
        });

        calculateProgress();
    }

    // HTML5 Drag and Drop events
    function dragStart(e, taskIndex) {
        e.dataTransfer.setData("text/plain", taskIndex);
        e.dataTransfer.effectAllowed = "move";
    }

    function allowDrop(e) {
        e.preventDefault();
    }

    function dragEnter(e) {
        e.preventDefault();
        const col = e.currentTarget;
        col.classList.add("dragover");
    }

    function dragLeave(e) {
        const col = e.currentTarget;
        col.classList.remove("dragover");
    }

    function drop(e, columnTarget) {
        e.preventDefault();
        const col = e.currentTarget;
        col.classList.remove("dragover");

        const taskIdxStr = e.dataTransfer.getData("text/plain");
        if (taskIdxStr === "") return;

        const taskIndex = parseInt(taskIdxStr);
        
        // Update column mapping
        projectTasks[taskIndex].column = columnTarget;

        // Save immediately
        const state = getProjectState();
        state.tasks = projectTasks;
        saveProjectState(state);

        renderTasks();
    }

    function calculateProgress() {
        projectMembers.forEach((member, index) => {
            const memberId = member.name.toLowerCase().split(' ')[0] + '-' + index;
            const col = document.getElementById(`col-${memberId}`);
            
            if (!col) return;
            
            // Remove placeholders from count
            const cardCount = col.querySelectorAll(".task-card").length;
            
            // In V1, we simulate that some percentage of tasks are done, say 50% or based on card indices to represent live work!
            // Let's assume the first 1-2 tasks in any column are checked off, or just provide a mockup ratio.
            const progress = cardCount > 0 ? 50 : 0; // standard mock percentage
            
            document.getElementById(`prog-txt-${memberId}`).innerText = `${progress}%`;
            document.getElementById(`prog-bar-${memberId}`).style.width = `${progress}%`;
            document.getElementById(`prog-bar-${memberId}`).className = progress === 100 ? "progress-bar bg-success" : "progress-bar bg-primary";
        });
    }

    function triggerAddTaskModal() {
        const modal = new bootstrap.Modal(document.getElementById("addTaskModal"));
        modal.show();
    }

    function createCustomTask() {
        const input = document.getElementById("customTaskText");
        const taskText = input.value.trim();
        if (!taskText) {
            alert("Task title is required.");
            return;
        }

        projectTasks.push({
            id: `task-${projectTasks.length + 1}`,
            text: taskText,
            column: "backlog"
        });

        // Save
        const state = getProjectState();
        state.tasks = projectTasks;
        saveProjectState(state);

        // Reset & Close
        input.value = "";
        const modalEl = document.getElementById("addTaskModal");
        const modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();

        renderTasks();
    }

    function deleteTask(index) {
        projectTasks.splice(index, 1);
        
        const state = getProjectState();
        state.tasks = projectTasks;
        saveProjectState(state);
        
        renderTasks();
    }

    function confirmTeamPlanning() {
        const state = getProjectState();
        state.teamAssigned = true;
        
        // Lock subsequent progress to enforce flow
        state.contextCompiled = false;

        state.decisions.push({
            date: new Date().toISOString().slice(0, 16).replace('T', ' '),
            title: "Team Plan Confirmed",
            desc: "Developer and AI workloads locked. Preparing Context Compilation phase."
        });

        saveProjectState(state);
        window.location.href = "/compiler";
    }

    function escapeHtml(text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
</script>
@endsection
