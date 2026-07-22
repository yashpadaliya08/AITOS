@extends('layouts.app')

@section('title', 'AITOS - AI Team Operating System')

@section('content')
<div class="row align-items-center justify-content-between py-5">
    <div class="col-lg-6 mb-5 mb-lg-0">
        <h6 class="text-primary fw-bold text-uppercase tracking-wider mb-3">AI Team Operating System</h6>
        <h1 class="display-4 fw-bold mb-4" style="line-height: 1.15;">The project memory for AI-first teams.</h1>
        <p class="lead text-muted mb-5">
            AITOS compiles your requirements, architecture, and team plans into a structured project package. Keep your human developers and multiple autonomous AI coding assistants in sync without context loss or drift.
        </p>
        
        <div class="d-flex flex-wrap gap-3">
            <button onclick="startNewProject()" class="btn btn-primary btn-lg px-4 py-3 fw-semibold shadow-sm">
                <i class="bi bi-plus-circle-fill me-2"></i> Create New Project
            </button>
            <button onclick="openDemoProject()" class="btn btn-outline-secondary btn-lg px-4 py-3 fw-semibold bg-white">
                <i class="bi bi-folder-check me-2"></i> Open Demo Project
            </button>
            <button onclick="triggerImport()" class="btn btn-outline-secondary btn-lg px-4 py-3 fw-semibold bg-white">
                <i class="bi bi-upload me-2"></i> Import Project
            </button>
            <input type="file" id="importFileInput" accept=".zip" style="display: none;" onchange="handleImportFile(event)">
        </div>
    </div>
    
    <div class="col-lg-5">
        <div class="card aitos-card border-0 shadow-sm p-4 text-center">
            <div class="aitos-card-body py-5">
                <div class="mb-4">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light border border-secondary border-opacity-10 text-primary" style="width: 80px; height: 80px; font-size: 2.5rem;">
                        <i class="bi bi-cpu-fill"></i>
                    </span>
                </div>
                <h4 class="fw-bold mb-3">AITOS Philosophy</h4>
                <div class="d-flex flex-column gap-3 my-4">
                    <div class="border rounded-3 p-3 bg-light bg-gradient">
                        <small class="text-uppercase text-muted fw-bold d-block mb-1">Human Role</small>
                        <span class="fs-5 fw-bold text-dark">Humans Decide.</span>
                    </div>
                    <div class="border rounded-3 p-3 bg-light bg-gradient">
                        <small class="text-uppercase text-muted fw-bold d-block mb-1">AI Agent Role</small>
                        <span class="fs-5 fw-bold text-primary">AI Builds.</span>
                    </div>
                    <div class="border rounded-3 p-3 bg-light bg-gradient">
                        <small class="text-uppercase text-muted fw-bold d-block mb-1">System Core</small>
                        <span class="fs-5 fw-bold text-success">AITOS Remembers.</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Resume Current Project Card (hidden by default, shown via JS if project exists) -->
<div id="resumeProjectCard" class="resume-project-card mb-5" style="display: none;">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center justify-content-center rounded-circle bg-white border" style="width: 48px; height: 48px;">
                <i class="bi bi-arrow-clockwise text-primary fs-4"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0" id="resumeProjectName">—</h6>
                <span class="text-muted small" id="resumeProjectPhase">—</span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button onclick="resumeProject()" class="btn btn-primary px-4 py-2 fw-semibold">
                <i class="bi bi-play-fill me-1"></i> Resume Project
            </button>
            <button onclick="clearProject()" class="btn btn-outline-danger px-3 py-2">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>
</div>

<hr class="my-5 border-light-subtle">

<!-- How AITOS Works Section -->
<div class="py-4">
    <div class="text-center mb-5">
        <h2 class="fw-bold">How AITOS Works</h2>
        <p class="text-muted">A streamlined path from requirements to compiled AI context.</p>
    </div>
    
    <div class="row g-4 justify-content-center">
        <!-- Step 1 -->
        <div class="col-md-4 col-lg-2">
            <div class="card aitos-card h-100 text-center border-0 p-3 shadow-sm">
                <div class="aitos-card-body p-2 d-flex flex-column justify-content-between h-100">
                    <div class="fs-2 text-primary mb-3"><i class="bi bi-file-earmark-text"></i></div>
                    <div>
                        <h6 class="fw-bold mb-2">1. Paste Idea</h6>
                        <p class="text-muted small mb-0">Provide your product concept or problem statement.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Step 2 -->
        <div class="col-md-4 col-lg-2">
            <div class="card aitos-card h-100 text-center border-0 p-3 shadow-sm">
                <div class="aitos-card-body p-2 d-flex flex-column justify-content-between h-100">
                    <div class="fs-2 text-warning mb-3"><i class="bi bi-robot"></i></div>
                    <div>
                        <h6 class="fw-bold mb-2">2. AI Analysis</h6>
                        <p class="text-muted small mb-0">Review structured system cards suggesting rules and entities.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Step 3 -->
        <div class="col-md-4 col-lg-2">
            <div class="card aitos-card h-100 text-center border-0 p-3 shadow-sm">
                <div class="aitos-card-body p-2 d-flex flex-column justify-content-between h-100">
                    <div class="fs-2 text-success mb-3"><i class="bi bi-shield-check"></i></div>
                    <div>
                        <h6 class="fw-bold mb-2">3. Approve Architecture</h6>
                        <p class="text-muted small mb-0">Inspect and lock four types of blueprints.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Step 4 -->
        <div class="col-md-4 col-lg-2">
            <div class="card aitos-card h-100 text-center border-0 p-3 shadow-sm">
                <div class="aitos-card-body p-2 d-flex flex-column justify-content-between h-100">
                    <div class="fs-2 text-info mb-3"><i class="bi bi-terminal-box"></i></div>
                    <div>
                        <h6 class="fw-bold mb-2">4. Compile Package</h6>
                        <p class="text-muted small mb-0">Compile knowledge base into AI-optimized memory.</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Step 5 -->
        <div class="col-md-4 col-lg-2">
            <div class="card aitos-card h-100 text-center border-0 p-3 shadow-sm">
                <div class="aitos-card-body p-2 d-flex flex-column justify-content-between h-100">
                    <div class="fs-2 text-danger mb-3"><i class="bi bi-code-square"></i></div>
                    <div>
                        <h6 class="fw-bold mb-2">5. Start Coding</h6>
                        <p class="text-muted small mb-0">Download repository package and run your AI editor.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        checkForExistingProject();
    });

    function checkForExistingProject() {
        const state = getProjectState();
        const card = document.getElementById('resumeProjectCard');
        
        if (state.wizardCompleted && state.projectName) {
            card.style.display = 'block';
            document.getElementById('resumeProjectName').innerText = state.projectName;
            
            // Determine current phase
            let phase = 'Wizard Complete';
            if (state.contextCompiled) phase = '✅ Context Compiled — Ready to Export';
            else if (state.teamAssigned) phase = '📋 Team Planned — Compile Next';
            else if (state.blueprintApproved) phase = '🏗️ Blueprints Approved — Plan Team Next';
            else if (state.requirementsApproved) phase = '📐 Requirements Approved — Blueprints Next';
            else phase = '📝 Wizard Done — Requirements Analysis Next';
            
            document.getElementById('resumeProjectPhase').innerText = phase;
        }
    }

    function resumeProject() {
        const state = getProjectState();
        
        // Navigate to the most relevant page
        if (state.contextCompiled) {
            window.location.href = "/export";
        } else if (state.teamAssigned) {
            window.location.href = "/compiler";
        } else if (state.blueprintApproved) {
            window.location.href = "/team";
        } else if (state.requirementsApproved) {
            window.location.href = "/blueprint";
        } else {
            window.location.href = "/requirements";
        }
    }

    function clearProject() {
        if (!confirm('This will clear your current project state. Are you sure?')) return;
        localStorage.removeItem('aitos_project_state');
        window.location.reload();
    }

    function startNewProject() {
        // Reset state, set default template name
        const state = getProjectState();
        state.projectName = "";
        state.projectDescription = "";
        state.problemStatement = "";
        state.wizardCompleted = false;
        state.requirementsApproved = false;
        state.blueprintApproved = false;
        state.teamAssigned = false;
        state.contextCompiled = false;
        state.analysisHash = ""; // Clear AI Cache
        
        saveProjectState(state);
        window.location.href = "/wizard";
    }

    async function openDemoProject() {
        // Load completed project data
        const state = getProjectState();
        state.projectName = "AITOS Platform Core";
        state.projectDescription = "The central environment bootstrap tool for autonomous coding agents";
        state.projectGoal = "Production";
        state.problemStatement = "Build a lightweight developer workflow system that sits on top of Git. It needs a client-side layout where users can insert problem statements, generate requirements analysis, define and version technical blueprints, assign tasks to members, compile this data into markdown context folders (.aitos/), and preview/download the package as a zip archive.";
        state.wizardCompleted = true;
        state.requirementsApproved = true;
        state.blueprintApproved = true;
        state.teamAssigned = true;
        state.contextCompiled = true;
        
        // Calculate matching hash so demo is in sync
        const currentText = state.projectName + '|' + state.projectGoal + '|' + state.problemStatement;
        state.analysisHash = await calculateHash(currentText);

        // Add a compile history item
        state.decisions = [
            { date: "2026-07-10 14:02", title: "Project Initialized", desc: "Bootstrapped project layout matching V2 specification." },
            { date: "2026-07-10 15:40", title: "Requirements Approved", desc: "Locked business modules, entity definitions, and developer boundaries." },
            { date: "2026-07-10 16:15", title: "Blueprints Approved (V2)", desc: "Locked database schema, technical blade stack, and UI style systems." },
            { date: "2026-07-10 17:30", title: "Team Planning Saved", desc: "Divided technical scopes amongst Alex, Sarah, and Dave." },
            { date: "2026-07-10 19:30", title: "Compiled Repository package", desc: "Compiled Markdown contexts and structured project data." }
        ];

        saveProjectState(state);
        
        // Go to Dashboard
        window.location.href = "/dashboard";
    }

    // --- Import Project ---
    function triggerImport() {
        document.getElementById('importFileInput').click();
    }

    async function handleImportFile(event) {
        const file = event.target.files[0];
        if (!file) return;

        if (!file.name.endsWith('.zip')) {
            showToast('Please select a valid .zip file exported from AITOS.', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('file', file);

        try {
            showToast('Importing project...', 'info', 8000);
            
            const response = await fetch('/import', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: formData
            });

            const result = await response.json();

            if (result.success && result.state) {
                // Merge imported state into localStorage
                const currentState = getProjectState();
                const merged = Object.assign(currentState, result.state);
                saveProjectState(merged);
                
                showToast(`Project "${merged.projectName || 'Imported'}" restored successfully!`, 'success');
                setTimeout(() => window.location.href = "/dashboard", 1200);
            } else {
                showToast(result.message || 'Import failed. The ZIP may not contain valid AITOS data.', 'error');
            }
        } catch (err) {
            showToast('Import request failed: ' + err.message, 'error');
        }
        
        // Reset file input
        event.target.value = '';
    }

    async function calculateHash(text) {
        const msgBuffer = new TextEncoder().encode(text);
        const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
    }
</script>
@endsection
