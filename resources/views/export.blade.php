@extends('layouts.app')

@section('title', 'AITOS - Export Center')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Export Center</h3>
        <p class="text-muted mb-0">Retrieve your compiled AI context files and load them into your code editors.</p>
    </div>
    <span class="badge bg-success px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i> Compilation Up-to-Date</span>
</div>

<div class="row g-4 mb-4">
    <!-- Package Summary Panel -->
    <div class="col-lg-4">
        <div class="card aitos-card border-light-subtle shadow-sm">
            <div class="aitos-card-header bg-white">
                <span class="fw-bold"><i class="bi bi-info-circle text-primary me-1"></i> Context Package Metadata</span>
            </div>
            <div class="aitos-card-body p-3">
                <div class="list-group list-group-flush" style="font-size: 0.9rem;">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5">
                        <span class="text-muted">Project ID Name:</span>
                        <strong class="text-dark" id="summaryProjectName">AITOS_Project</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5">
                        <span class="text-muted">Blueprint Version:</span>
                        <strong class="text-success font-monospace" id="summaryBpVer">v1.0.0 (Locked)</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5">
                        <span class="text-muted">Target AI Target:</span>
                        <strong class="text-dark" id="summaryPrimaryAI">Cursor, Claude</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5">
                        <span class="text-muted">Total Structure:</span>
                        <strong class="text-dark">3 Folders / 17 Files</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5">
                        <span class="text-muted">Estimated Package Size:</span>
                        <strong class="text-primary font-monospace">~ 28.5 KB</strong>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2.5">
                        <span class="text-muted">Package Version:</span>
                        <strong class="text-dark font-monospace">v1.0.0</strong>
                    </div>
                </div>

                <div class="mt-4 p-3 bg-light rounded border border-light-subtle small">
                    <strong class="text-dark d-block mb-1"><i class="bi bi-box-seam-fill text-secondary me-1"></i> Package Structure:</strong>
                    <div class="font-monospace text-secondary" style="font-size: 0.75rem; line-height: 1.4;">
                        Project/ <br>
                        ├── README.md <br>
                        ├── START_HERE.md <br>
                        └── .aitos/ <br>
                        &nbsp;&nbsp;&nbsp;&nbsp;├── data/ (JSON data-source) <br>
                        &nbsp;&nbsp;&nbsp;&nbsp;└── context/ (AI context layers)
                    </div>
                </div>
            </div>
        </div>

        <!-- Quality Review Checklist Card -->
        <div class="card aitos-card border-light-subtle shadow-sm mt-4">
            <div class="card-header bg-white py-3">
                <span class="fw-bold text-dark"><i class="bi bi-shield-check-fill text-success me-1"></i> Architecture Compliance Gate</span>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">Review and check off each stage of compiled project architecture to unlock ZIP packaging.</p>
                <div class="d-flex flex-column gap-2.5" style="font-size: 0.9rem;">
                    <div class="form-check pb-1 border-bottom border-light-subtle">
                        <input class="form-check-input" type="checkbox" id="checkReq" onchange="toggleExportBtn()">
                        <label class="form-check-label fw-semibold text-dark" for="checkReq">Requirements Checked <i class="bi bi-check-circle text-success ms-1"></i></label>
                    </div>
                    <div class="form-check pb-1 border-bottom border-light-subtle">
                        <input class="form-check-input" type="checkbox" id="checkBp" onchange="toggleExportBtn()">
                        <label class="form-check-label fw-semibold text-dark" for="checkBp">Blueprint Checked <i class="bi bi-check-circle text-success ms-1"></i></label>
                    </div>
                    <div class="form-check pb-1 border-bottom border-light-subtle">
                        <input class="form-check-input" type="checkbox" id="checkDb" onchange="toggleExportBtn()">
                        <label class="form-check-label fw-semibold text-dark" for="checkDb">Database Schema Checked <i class="bi bi-check-circle text-success ms-1"></i></label>
                    </div>
                    <div class="form-check pb-1 border-bottom border-light-subtle">
                        <input class="form-check-input" type="checkbox" id="checkApi" onchange="toggleExportBtn()">
                        <label class="form-check-label fw-semibold text-dark" for="checkApi">API Endpoint Checked <i class="bi bi-check-circle text-success ms-1"></i></label>
                    </div>
                    <div class="form-check pb-1 border-bottom border-light-subtle">
                        <input class="form-check-input" type="checkbox" id="checkUi" onchange="toggleExportBtn()">
                        <label class="form-check-label fw-semibold text-dark" for="checkUi">UI Component Checked <i class="bi bi-check-circle text-success ms-1"></i></label>
                    </div>
                    <div class="form-check pb-1 border-bottom border-light-subtle">
                        <input class="form-check-input" type="checkbox" id="checkPlanning" onchange="toggleExportBtn()">
                        <label class="form-check-label fw-semibold text-dark" for="checkPlanning">Project Workload Checked <i class="bi bi-check-circle text-success ms-1"></i></label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="checkDocs" onchange="toggleExportBtn()">
                        <label class="form-check-label fw-semibold text-dark" for="checkDocs">Documentation Checked <i class="bi bi-check-circle text-success ms-1"></i></label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Cards -->
    <div class="col-lg-8">
        <div class="row g-3">
            <!-- Primary Action Card (Repository ZIP Package) -->
            <div class="col-md-6">
                <div class="card h-100 border-primary border-2 shadow-sm text-center p-3 opacity-75" id="zipDownloadCard">
                    <div class="card-body d-flex flex-column justify-content-between align-items-center py-4">
                        <div class="mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary" style="width: 60px; height: 60px; font-size: 2rem;">
                                <i class="bi bi-file-earmark-zip-fill"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-2">Repository Package</h5>
                            <p class="text-muted small mb-0 px-2">Complete context folder hierarchy bundled as a offline-ready <code>.zip</code> package.</p>
                        </div>
                        <div class="mt-4 w-100">
                            <button type="button" class="btn btn-primary w-100 fw-bold py-2.5 disabled" id="downloadZipBtn" onclick="triggerZipDownload()" disabled>
                                <i class="bi bi-download me-1"></i> Download ZIP
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Brief ZIP Bundle Card -->
            <div class="col-md-6">
                <div class="card h-100 border-success border-2 shadow-sm text-center p-3 opacity-75" id="briefDownloadCard">
                    <div class="card-body d-flex flex-column justify-content-between align-items-center py-4">
                        <div class="mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success bg-opacity-10 text-success" style="width: 60px; height: 60px; font-size: 2rem;">
                                <i class="bi bi-file-earmark-zip-fill"></i>
                            </span>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-2">Project Brief Bundle</h5>
                            <p class="text-muted small mb-0 px-2">ZIP containing an interactive HTML brief, a clean printable PDF, and ER diagram source files for mentor review.</p>
                        </div>
                        <div class="mt-4 w-100">
                            <button type="button" class="btn btn-success w-100 fw-bold py-2.5 disabled" id="downloadBriefBtn" onclick="triggerBriefDownload()" disabled>
                                <i class="bi bi-file-earmark-zip-fill me-1"></i> Download Brief ZIP
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cursor Context Card -->
            <div class="col-md-6">
                <div class="card h-100 border-light-subtle shadow-sm text-center p-3 opacity-75">
                    <div class="card-body d-flex flex-column justify-content-between align-items-center py-4">
                        <div class="mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light text-secondary border" style="width: 60px; height: 60px; font-size: 1.75rem;">
                                <i class="bi bi-cursor-fill"></i>
                            </span>
                        </div>
                        <div>
                            <span class="badge bg-secondary mb-2">Coming Soon</span>
                            <h5 class="fw-bold text-muted mb-2">Cursor Config</h5>
                            <p class="text-muted small mb-0 px-2">Auto-imports <code>.cursorrules</code> preferences directly mapped into Cursor project directories.</p>
                        </div>
                        <div class="mt-4 w-100">
                            <button type="button" class="btn btn-outline-secondary w-100 py-2.5 disabled">Unavailable</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gemini Rules Card -->
            <div class="col-md-6">
                <div class="card h-100 border-light-subtle shadow-sm text-center p-3 opacity-75">
                    <div class="card-body d-flex flex-column justify-content-between align-items-center py-4">
                        <div class="mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light text-secondary border" style="width: 60px; height: 60px; font-size: 1.75rem;">
                                <i class="bi bi-gem"></i>
                            </span>
                        </div>
                        <div>
                            <span class="badge bg-secondary mb-2">Coming Soon</span>
                            <h5 class="fw-bold text-muted mb-2">Gemini System Prompt</h5>
                            <p class="text-muted small mb-0 px-2">JSON configuration context mappings optimized for Gemini Code Assist engines.</p>
                        </div>
                        <div class="mt-4 w-100">
                            <button type="button" class="btn btn-outline-secondary w-100 py-2.5 disabled">Unavailable</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-start align-items-center gap-3 border-top pt-4">
    <a href="/preview" class="btn btn-outline-primary px-4 py-2 bg-white"><i class="bi bi-eye me-1"></i> Preview Virtual Repository Files</a>
</div>

<!-- Hidden form for ZIP payload submission -->
<form id="zipForm" action="/export/download" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="project_state" id="zipFormPayload">
</form>

<!-- Hidden form for Project Brief HTML payload submission -->
<form id="briefForm" action="/export/brief" method="POST" class="d-none">
    @csrf
    <input type="hidden" name="project_state" id="briefFormPayload">
</form>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        loadExportSummary();
        toggleExportBtn();
    });

    function loadExportSummary() {
        const state = getProjectState();
        document.getElementById("summaryProjectName").innerText = state.projectName || "AITOS_Project";
        document.getElementById("summaryBpVer").innerText = `v${state.blueprints.version || "1.0.0"} (Locked)`;

        // Preferred AI summary
        if (state.teamMembers && state.teamMembers.length > 0) {
            const ais = [...new Set(state.teamMembers.map(m => m.ai))];
            document.getElementById("summaryPrimaryAI").innerText = ais.join(", ");
        } else {
            document.getElementById("summaryPrimaryAI").innerText = "Cursor, Claude";
        }

        // Auto-check completed workflow milestones
        if (state.requirementsApproved) {
            document.getElementById("checkReq").checked = true;
        }
        if (state.blueprintApproved) {
            document.getElementById("checkBp").checked = true;
            document.getElementById("checkDb").checked = true;
            document.getElementById("checkApi").checked = true;
            document.getElementById("checkUi").checked = true;
        }
        if (state.teamAssigned) {
            document.getElementById("checkPlanning").checked = true;
        }
        if (state.contextCompiled) {
            document.getElementById("checkDocs").checked = true;
        }
    }

    function toggleExportBtn() {
        const checkboxes = ['checkReq', 'checkBp', 'checkDb', 'checkApi', 'checkUi', 'checkPlanning', 'checkDocs'];
        const allChecked = checkboxes.every(id => document.getElementById(id) && document.getElementById(id).checked);
        
        const zipBtn = document.getElementById("downloadZipBtn");
        const zipCard = document.getElementById("zipDownloadCard");
        const briefBtn = document.getElementById("downloadBriefBtn");
        const briefCard = document.getElementById("briefDownloadCard");
        
        if (allChecked) {
            zipBtn.removeAttribute("disabled");
            zipBtn.classList.remove("disabled");
            zipCard.classList.remove("opacity-75");

            briefBtn.removeAttribute("disabled");
            briefBtn.classList.remove("disabled");
            briefCard.classList.remove("opacity-75");
        } else {
            zipBtn.setAttribute("disabled", "true");
            zipBtn.classList.add("disabled");
            zipCard.classList.add("opacity-75");

            briefBtn.setAttribute("disabled", "true");
            briefBtn.classList.add("disabled");
            briefCard.classList.add("opacity-75");
        }
    }

    function triggerZipDownload() {
        const state = getProjectState();
        
        // Pass complete localStorage state as JSON payload
        document.getElementById("zipFormPayload").value = JSON.stringify(state);
        
        // Submit the form which calls PHP controller to assemble and stream the zip
        document.getElementById("zipForm").submit();
        
        // Push decision history log
        state.decisions.push({
            date: new Date().toISOString().slice(0, 16).replace('T', ' '),
            title: "Package Downloaded",
            desc: "Downloaded compiled project zip package. Extract into local project root to start coding."
        });
        saveProjectState(state);
    }

    function triggerBriefDownload() {
        const state = getProjectState();
        
        // Pass complete localStorage state as JSON payload
        document.getElementById("briefFormPayload").value = JSON.stringify(state);
        
        // Submit the form which calls PHP controller to assemble and render the brief HTML
        document.getElementById("briefForm").submit();
        
        // Push decision history log
        state.decisions.push({
            date: new Date().toISOString().slice(0, 16).replace('T', ' '),
            title: "Project Brief Bundle Exported",
            desc: "Exported ZIP bundle containing HTML brief, printable PDF, and ER diagram files for mentor review."
        });
        saveProjectState(state);
    }
</script>
@endsection
