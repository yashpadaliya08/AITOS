@extends('layouts.app')

@section('title', 'AITOS - Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Project Dashboard</h3>
        <p class="text-muted mb-0">Overview of your system context memory state and human decision logs.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-danger px-3" onclick="triggerResetConfirm()"><i class="bi bi-trash3 me-1"></i> Reset Project Data</button>
        <a href="/" class="btn btn-primary px-3"><i class="bi bi-arrow-left me-1"></i> Back to Start</a>
    </div>
</div>

<!-- Metrics Cards row -->
<div class="row g-3 mb-4">
    <!-- Metric 1: Current Phase -->
    <div class="col-md-6 col-lg-3">
        <div class="metric-card shadow-sm">
            <div class="metric-icon blue">
                <i class="bi bi-geo-alt-fill"></i>
            </div>
            <div class="metric-details">
                <h5 id="metricPhase">-</h5>
                <span>Current Phase</span>
            </div>
        </div>
    </div>
    <!-- Metric 2: Blueprint Version -->
    <div class="col-md-6 col-lg-3">
        <div class="metric-card shadow-sm">
            <div class="metric-icon purple">
                <i class="bi bi-journal-code"></i>
            </div>
            <div class="metric-details">
                <h5 id="metricBlueprint">-</h5>
                <span>Blueprint Release</span>
            </div>
        </div>
    </div>
    <!-- Metric 3: Active Tasks -->
    <div class="col-md-6 col-lg-3">
        <div class="metric-card shadow-sm">
            <div class="metric-icon orange">
                <i class="bi bi-list-task"></i>
            </div>
            <div class="metric-details">
                <h5 id="metricTasks">-</h5>
                <span>Planning Tasks</span>
            </div>
        </div>
    </div>
    <!-- Metric 4: Human Decisions -->
    <div class="col-md-6 col-lg-3">
        <div class="metric-card shadow-sm">
            <div class="metric-icon green">
                <i class="bi bi-hammer"></i>
            </div>
            <div class="metric-details">
                <h5 id="metricDecisions">-</h5>
                <span>Human Decisions</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Project Specs Summary -->
    <div class="col-lg-6">
        <div class="card aitos-card border-light-subtle h-100 shadow-sm">
            <div class="card-header bg-white py-3">
                <span class="fw-bold text-dark"><i class="bi bi-card-heading text-primary me-1"></i> Workspace Information</span>
            </div>
            <div class="card-body">
                <h4 class="fw-bold text-dark mb-2" id="dashProjName">Loading Project Name...</h4>
                <p class="text-muted mb-4" id="dashProjDesc">No description loaded. Run the wizard first.</p>

                <h6 class="fw-bold mb-3 border-bottom pb-2">Target Stack Config</h6>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Backend Core</small>
                        <strong class="text-dark d-block mt-0.5" id="dashFramework">-</strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Database Engine</small>
                        <strong class="text-dark d-block mt-0.5" id="dashDatabase">-</strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Frontend Layout</small>
                        <strong class="text-dark d-block mt-0.5" id="dashFrontend">-</strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Active Developer Team</small>
                        <strong class="text-dark d-block mt-0.5" id="dashTeamSize">0 Developers</strong>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-light rounded border border-light-subtle d-flex align-items-center gap-3">
                    <div class="fs-1 text-primary"><i class="bi bi-cpu-fill"></i></div>
                    <div>
                        <strong class="d-block text-dark">Data-First Architecture</strong>
                        <p class="text-muted small mb-0">Markdown context maps inside <code>.aitos/context</code> are auto-compiled from local JSON databases. No manual edits are required.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Project Health & Readiness -->
    <div class="col-lg-6">
        <div class="card aitos-card border-light-subtle h-100 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-heart-pulse-fill text-danger me-1"></i> AITOS Project Health</span>
                <span class="badge bg-primary fs-6 font-monospace" id="healthReadinessScore">0% Readiness</span>
            </div>
            <div class="card-body">
                <div class="progress mb-4" style="height: 10px;">
                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" id="healthReadinessProgress" style="width: 0%;"></div>
                </div>
                <div class="row g-3">
                    <div class="col-6 d-flex justify-content-between align-items-center border-bottom pb-2">
                        <span class="small text-muted"><i class="bi bi-file-earmark-check me-1"></i> Requirements:</span>
                        <span class="badge" id="healthRequirements">-</span>
                    </div>
                    <div class="col-6 d-flex justify-content-between align-items-center border-bottom pb-2">
                        <span class="small text-muted"><i class="bi bi-journal-code me-1"></i> Blueprint:</span>
                        <span class="badge" id="healthBlueprint">-</span>
                    </div>
                    <div class="col-6 d-flex justify-content-between align-items-center border-bottom pb-2">
                        <span class="small text-muted"><i class="bi bi-diagram-3 me-1"></i> Knowledge Graph:</span>
                        <span class="badge" id="healthGraph">-</span>
                    </div>
                    <div class="col-6 d-flex justify-content-between align-items-center border-bottom pb-2">
                        <span class="small text-muted"><i class="bi bi-database me-1"></i> Database:</span>
                        <span class="badge" id="healthDatabase">-</span>
                    </div>
                    <div class="col-6 d-flex justify-content-between align-items-center border-bottom pb-2">
                        <span class="small text-muted"><i class="bi bi-link-45deg me-1"></i> API Design:</span>
                        <span class="badge" id="healthApi">-</span>
                    </div>
                    <div class="col-6 d-flex justify-content-between align-items-center border-bottom pb-2">
                        <span class="small text-muted"><i class="bi bi-window-sidebar me-1"></i> UI Blueprint:</span>
                        <span class="badge" id="healthUi">-</span>
                    </div>
                    <div class="col-6 d-flex justify-content-between align-items-center border-bottom pb-2">
                        <span class="small text-muted"><i class="bi bi-people me-1"></i> Planning:</span>
                        <span class="badge" id="healthPlanning">-</span>
                    </div>
                    <div class="col-6 d-flex justify-content-between align-items-center border-bottom pb-2">
                        <span class="small text-muted"><i class="bi bi-file-earmark-text me-1"></i> Documentation:</span>
                        <span class="badge" id="healthDocs">-</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Active Workspace & Sprint Plan -->
    <div class="col-lg-7">
        <div class="card aitos-card border-light-subtle h-100 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-people-fill text-primary me-1"></i> Active Sprint & Team Collaboration</span>
                <span class="badge bg-light text-primary border" id="dashSprintLabel">Sprint 1</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Task Description</th>
                                <th>Assignee</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="sprintTasksTableBody">
                            <!-- Tasks loaded by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Developer Session Tracker -->
    <div class="col-lg-5">
        <div class="card aitos-card border-light-subtle h-100 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-robot text-success me-1"></i> AI Session Logs</span>
                <button class="btn btn-sm btn-primary" onclick="openLogSessionModal()"><i class="bi bi-plus-lg me-1"></i> Log Session</button>
            </div>
            <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                <div id="aiSessionsContainer" class="list-group list-group-flush">
                    <!-- Session cards loaded by JS -->
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <!-- Timeline of Decisions -->
    <div class="col-12">
        <div class="card aitos-card border-light-subtle shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-clock-history text-primary me-1"></i> AITOS Decision Timeline</span>
                <span class="badge bg-light text-secondary border font-monospace">Registry Active</span>
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                <div class="position-relative ps-4 border-start" id="timelineContainer" style="margin-left: 10px;">
                    <!-- Timelines injected by JS -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for logging AI Session -->
<div class="modal fade" id="logSessionModal" tabindex="-1" aria-labelledby="logSessionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark" id="logSessionModalLabel"><i class="bi bi-robot text-success me-1"></i> Log AI Development Session</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="logSessionForm" onsubmit="event.preventDefault(); saveAiSession();">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">AI Provider</label>
                            <select id="logSessionProvider" class="form-select border-light-subtle">
                                <option value="Gemini">Gemini</option>
                                <option value="OpenAI">OpenAI</option>
                                <option value="Anthropic">Anthropic</option>
                                <option value="Cursor">Cursor AI</option>
                                <option value="Claude Code">Claude Code</option>
                                <option value="Generic AI">Generic AI</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">AI Model Name</label>
                            <input type="text" id="logSessionModel" class="form-control border-light-subtle" placeholder="e.g. Claude 3.5 Sonnet">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Developer / Operator</label>
                            <input type="text" id="logSessionDeveloper" class="form-control border-light-subtle" placeholder="e.g. Yash Padaliya">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Assigned Task</label>
                            <input type="text" id="logSessionTask" class="form-control border-light-subtle" placeholder="e.g. Build User API endpoint">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Summary / Completed Work</label>
                            <textarea id="logSessionSummary" class="form-control border-light-subtle" rows="3" placeholder="Describe what was accomplished in this session."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Files Modified (comma separated)</label>
                            <input type="text" id="logSessionFiles" class="form-control border-light-subtle" placeholder="e.g. routes/api.php, app/Models/User.php">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select id="logSessionStatus" class="form-select border-light-subtle">
                                <option value="completed">Completed</option>
                                <option value="in_progress">In Progress</option>
                                <option value="blocked">Blocked</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveAiSession()">Save Session</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal confirmation for reset -->
<div class="modal fade" id="confirmResetModal" tabindex="-1" aria-labelledby="confirmResetModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-danger" id="confirmResetModalLabel">Reset Local Project Data?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Are you sure you want to clear your local storage project memory? This action deletes all active drafts, blueprints edits, and task columns. This cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="confirmResetProject()">Reset Project</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        loadDashboardMetrics();
    });

    function loadDashboardMetrics() {
        const state = getProjectState();

        // Metric calculations
        let phase = "Landing Page";
        if (state.wizardCompleted) phase = "Analysis";
        if (state.requirementsApproved) phase = "Blueprints";
        if (state.blueprintApproved) phase = "Planning";
        if (state.teamAssigned) phase = "Compiler";
        if (state.contextCompiled) phase = "Export";

        document.getElementById("metricPhase").innerText = phase;
        document.getElementById("metricBlueprint").innerText = state.blueprintApproved ? `v${state.blueprints.version || "1.0.0"}` : "Draft Pending";
        document.getElementById("metricTasks").innerText = state.tasks ? `${state.tasks.length} Tasks` : "0 Tasks";
        document.getElementById("metricDecisions").innerText = state.decisions ? `${state.decisions.length} Decisions` : "0";

        // Specs summary
        document.getElementById("dashProjName").innerText = state.projectName || "Create a new project";
        document.getElementById("dashProjDesc").innerText = state.projectDescription || "Paste requirements in the wizard to start compiling contexts.";

        if (state.techStack) {
            document.getElementById("dashFramework").innerText = state.techStack.framework || "-";
            document.getElementById("dashDatabase").innerText = state.techStack.database || "-";
            document.getElementById("dashFrontend").innerText = state.techStack.frontend || "-";
        }
        
        document.getElementById("dashTeamSize").innerText = state.teamMembers ? `${state.teamMembers.length} Members` : "0 Members";

        // Project Health Status computations
        const healthItems = [
            { id: "healthRequirements", check: state.requirementsApproved },
            { id: "healthBlueprint", check: state.blueprintApproved },
            { id: "healthGraph", check: state.knowledgeGraph && state.knowledgeGraph.relations && state.knowledgeGraph.relations.length > 0 },
            { id: "healthDatabase", check: state.databaseSchema && state.databaseSchema.tables && state.databaseSchema.tables.length > 0 },
            { id: "healthApi", check: state.apiDesign && state.apiDesign.resources && state.apiDesign.resources.length > 0 },
            { id: "healthUi", check: state.uiBlueprint && state.uiBlueprint.pages && state.uiBlueprint.pages.length > 0 },
            { id: "healthPlanning", check: state.teamAssigned },
            { id: "healthDocs", check: state.documentation && Object.keys(state.documentation).length > 0 }
        ];

        let completeCount = 0;
        healthItems.forEach(item => {
            const el = document.getElementById(item.id);
            if (!el) return;
            if (item.check) {
                completeCount++;
                el.className = "badge bg-success";
                el.innerText = "Complete";
            } else {
                el.className = "badge bg-danger";
                el.innerText = "Incomplete";
            }
        });

        const readinessScore = Math.round((completeCount / healthItems.length) * 100);
        document.getElementById("healthReadinessScore").innerText = `${readinessScore}% Readiness`;
        document.getElementById("healthReadinessProgress").style.width = `${readinessScore}%`;

        // Timeline Builder
        const timeline = document.getElementById("timelineContainer");
        timeline.innerHTML = "";
        
        const logs = state.decisions || [];
        if (logs.length === 0) {
            timeline.innerHTML = `<div class="text-muted small">No decisions recorded yet. Finish the wizard.</div>`;
        } else {
            // Sort newer first
            const sortedLogs = [...logs].reverse();
            
            sortedLogs.forEach((log, index) => {
                const node = document.createElement("div");
                node.className = "mb-4 position-relative";
                node.innerHTML = `
                    <span class="position-absolute bg-primary rounded-circle border border-white" style="width: 12px; height: 12px; left: -26px; top: 5px;"></span>
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.9rem;">${escapeHtml(log.title)}</h6>
                        <small class="text-muted font-monospace" style="font-size: 0.7rem;">${escapeHtml(log.date)}</small>
                    </div>
                    <p class="text-muted small mb-0">${escapeHtml(log.desc)}</p>
                `;
                timeline.appendChild(node);
            });
        }

        // Render Active Sprint Tasks
        const tasksTable = document.getElementById("sprintTasksTableBody");
        tasksTable.innerHTML = "";
        const sprintTasks = state.tasks || [];
        if (sprintTasks.length === 0) {
            tasksTable.innerHTML = `<tr><td colspan="3" class="text-center text-muted small py-3">No tasks found. Create a plan first.</td></tr>`;
        } else {
            sprintTasks.forEach(t => {
                const row = document.createElement("tr");
                const col = t.column || "todo";
                let badgeClass = "bg-warning";
                if (col.toLowerCase() === "done" || col.toLowerCase() === "completed") badgeClass = "bg-success";
                if (col.toLowerCase() === "blocked") badgeClass = "bg-danger";

                const member = state.teamMembers && state.teamMembers.length > 0 
                    ? state.teamMembers[Math.floor(Math.random() * state.teamMembers.length)].name 
                    : "Unassigned";

                row.innerHTML = `
                    <td class="small text-dark fw-medium">${escapeHtml(t.text)}</td>
                    <td class="small text-muted">${escapeHtml(member)}</td>
                    <td><span class="badge ${badgeClass}">${escapeHtml(col.toUpperCase())}</span></td>
                `;
                tasksTable.appendChild(row);
            });
        }

        // Render AI Developer Session tracker list
        const sessionsContainer = document.getElementById("aiSessionsContainer");
        sessionsContainer.innerHTML = "";
        const sessions = state.aiSessions || [
            {
                provider: "Gemini",
                model: "Gemini Pro",
                date: "2026-07-10 14:15",
                developer: "Yash",
                task: "Compiler Core setup",
                summary: "Initialized the main pipeline stage orchestrator.",
                status: "completed"
            }
        ];

        sessions.forEach(s => {
            const card = document.createElement("div");
            card.className = "list-group-item py-3 px-0 border-light-subtle";
            let statusBadge = "bg-success-subtle text-success";
            if (s.status === "in_progress") statusBadge = "bg-warning-subtle text-warning";
            if (s.status === "blocked") statusBadge = "bg-danger-subtle text-danger";

            card.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="fw-semibold text-dark small"><i class="bi bi-robot text-success me-1"></i> ${escapeHtml(s.provider)} (${escapeHtml(s.model)})</span>
                    <span class="badge ${statusBadge} font-monospace" style="font-size:0.7rem;">${escapeHtml(s.status.toUpperCase())}</span>
                </div>
                <div class="text-muted small mb-2"><strong>Task:</strong> ${escapeHtml(s.task)}</div>
                <p class="text-secondary small mb-1 bg-light p-2 rounded border border-light-subtle">${escapeHtml(s.summary)}</p>
                <div class="d-flex justify-content-between text-muted" style="font-size:0.7rem;">
                    <span>By: ${escapeHtml(s.developer)}</span>
                    <span>${escapeHtml(s.date)}</span>
                </div>
            `;
            sessionsContainer.appendChild(card);
        });

        // Ensure state includes default mocks if empty
        if (!state.aiSessions) {
            state.aiSessions = sessions;
            saveProjectState(state);
        }
    }

    function openLogSessionModal() {
        document.getElementById("logSessionForm").reset();
        document.getElementById("logSessionModel").value = "Gemini 1.5 Pro";
        document.getElementById("logSessionDeveloper").value = "Yash Padaliya";
        
        const modal = new bootstrap.Modal(document.getElementById("logSessionModal"));
        modal.show();
    }

    function saveAiSession() {
        const state = getProjectState();
        if (!state.aiSessions) state.aiSessions = [];

        const newSession = {
            provider: document.getElementById("logSessionProvider").value,
            model: document.getElementById("logSessionModel").value.trim() || "Gemini 1.5 Pro",
            date: new Date().toISOString().slice(0, 16).replace('T', ' '),
            developer: document.getElementById("logSessionDeveloper").value.trim() || "Yash Padaliya",
            task: document.getElementById("logSessionTask").value.trim() || "General Feature Implementation",
            summary: document.getElementById("logSessionSummary").value.trim() || "Completed code tasks.",
            files_modified: document.getElementById("logSessionFiles").value.split(",").map(f => f.trim()).filter(f => f !== ""),
            status: document.getElementById("logSessionStatus").value
        };

        state.aiSessions.push(newSession);

        state.decisions.push({
            date: new Date().toISOString().slice(0, 16).replace('T', ' '),
            title: "AI Session Logged",
            desc: `Recorded development session utilizing ${newSession.provider} for task "${newSession.task}".`
        });

        saveProjectState(state);

        // Hide Modal
        const modalEl = document.getElementById("logSessionModal");
        const modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();

        loadDashboardMetrics();
    }

    function triggerResetConfirm() {
        const modal = new bootstrap.Modal(document.getElementById("confirmResetModal"));
        modal.show();
    }

    function confirmResetProject() {
        resetProjectState();
        
        // Hide Modal
        const modalEl = document.getElementById("confirmResetModal");
        const modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();

        window.location.href = "/";
    }

    function escapeHtml(text) {
        if (!text) return "";
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
</script>
@endsection
