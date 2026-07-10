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
        <div class="card aitos-card border-light-subtle h-100 shadow-sm">
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
    </div>

    <!-- Export Cards -->
    <div class="col-lg-8">
        <div class="row g-3">
            <!-- Primary Action Card (Repository ZIP Package) -->
            <div class="col-md-6">
                <div class="card h-100 border-primary border-2 shadow-sm text-center p-3 cursor-pointer" onclick="triggerZipDownload()">
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
                            <button type="button" class="btn btn-primary w-100 fw-bold py-2.5">
                                <i class="bi bi-download me-1"></i> Download ZIP
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Claude Context Card -->
            <div class="col-md-6">
                <div class="card h-100 border-light-subtle shadow-sm text-center p-3 opacity-75">
                    <div class="card-body d-flex flex-column justify-content-between align-items-center py-4">
                        <div class="mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light text-secondary border" style="width: 60px; height: 60px; font-size: 1.75rem;">
                                <i class="bi bi-robot"></i>
                            </span>
                        </div>
                        <div>
                            <span class="badge bg-secondary mb-2">Coming Soon</span>
                            <h5 class="fw-bold text-muted mb-2">Claude Context</h5>
                            <p class="text-muted small mb-0 px-2">Optimized single XML system prompt package for Anthropic Claude agents.</p>
                        </div>
                        <div class="mt-4 w-100">
                            <button type="button" class="btn btn-outline-secondary w-100 py-2.5 disabled">Unavailable</button>
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
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        loadExportSummary();
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
</script>
@endsection
