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
            <div class="aitos-card-header bg-white">
                <span class="fw-bold"><i class="bi bi-card-heading text-primary me-1"></i> Workspace Information</span>
            </div>
            <div class="aitos-card-body">
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

    <!-- Timeline of Decisions -->
    <div class="col-lg-6">
        <div class="card aitos-card border-light-subtle h-100 shadow-sm">
            <div class="aitos-card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="bi bi-clock-history text-primary me-1"></i> AITOS Decision Timeline</span>
                <span class="badge bg-light text-secondary border font-monospace">Registry Active</span>
            </div>
            <div class="aitos-card-body" style="max-height: 450px; overflow-y: auto;">
                <div class="position-relative ps-4 border-start" id="timelineContainer" style="margin-left: 10px;">
                    <!-- Timelines injected by JS -->
                </div>
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
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
</script>
@endsection
