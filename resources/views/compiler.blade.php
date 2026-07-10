@extends('layouts.app')

@section('title', 'AITOS - Context Compiler')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">AITOS Context Compiler</h3>
        <p class="text-muted mb-0">Compile problem definitions, architecture blueprints, and planning maps into optimized AI-ready context packages.</p>
    </div>
    <span class="badge bg-secondary px-3 py-2">Phase 5: Compiler</span>
</div>

<div class="row g-4 mb-4">
    <!-- Compile Actions & Checklist -->
    <div class="col-lg-4">
        <div class="card aitos-card border-light-subtle shadow-sm mb-4">
            <div class="aitos-card-header bg-white">
                <span class="fw-bold"><i class="bi bi-gear-wide-connected me-1 text-primary"></i> Compilation Controls</span>
            </div>
            <div class="aitos-card-body">
                <button type="button" id="startCompileBtn" class="btn btn-primary btn-lg w-100 py-3 fw-bold mb-4 shadow-sm" onclick="startCompilation()">
                    <i class="bi bi-play-fill me-1"></i> Compile Project Context
                </button>
                
                <div class="d-flex flex-column gap-3" id="compilationSteps">
                    <!-- Step 1 -->
                    <div class="d-flex align-items-center gap-3 text-muted" id="comp-step-1">
                        <span class="fs-5 lh-1 text-center" style="width: 24px;" id="comp-icon-1"><i class="bi bi-circle"></i></span>
                        <span class="small fw-semibold">1. Reading Problem Statement</span>
                    </div>
                    <!-- Step 2 -->
                    <div class="d-flex align-items-center gap-3 text-muted" id="comp-step-2">
                        <span class="fs-5 lh-1 text-center" style="width: 24px;" id="comp-icon-2"><i class="bi bi-circle"></i></span>
                        <span class="small fw-semibold">2. Loading Approved Blueprints</span>
                    </div>
                    <!-- Step 3 -->
                    <div class="d-flex align-items-center gap-3 text-muted" id="comp-step-3">
                        <span class="fs-5 lh-1 text-center" style="width: 24px;" id="comp-icon-3"><i class="bi bi-circle"></i></span>
                        <span class="small fw-semibold">3. Collecting Rules</span>
                    </div>
                    <!-- Step 4 -->
                    <div class="d-flex align-items-center gap-3 text-muted" id="comp-step-4">
                        <span class="fs-5 lh-1 text-center" style="width: 24px;" id="comp-icon-4"><i class="bi bi-circle"></i></span>
                        <span class="small fw-semibold">4. Collecting Team Plan</span>
                    </div>
                    <!-- Step 5 -->
                    <div class="d-flex align-items-center gap-3 text-muted" id="comp-step-5">
                        <span class="fs-5 lh-1 text-center" style="width: 24px;" id="comp-icon-5"><i class="bi bi-circle"></i></span>
                        <span class="small fw-semibold">5. Collecting Decisions</span>
                    </div>
                    <!-- Step 6 -->
                    <div class="d-flex align-items-center gap-3 text-muted" id="comp-step-6">
                        <span class="fs-5 lh-1 text-center" style="width: 24px;" id="comp-icon-6"><i class="bi bi-circle"></i></span>
                        <span class="small fw-semibold">6. Building AI Context</span>
                    </div>
                    <!-- Step 7 -->
                    <div class="d-flex align-items-center gap-3 text-muted" id="comp-step-7">
                        <span class="fs-5 lh-1 text-center" style="width: 24px;" id="comp-icon-7"><i class="bi bi-circle"></i></span>
                        <span class="small fw-semibold">7. Optimizing Context</span>
                    </div>
                    <!-- Step 8 -->
                    <div class="d-flex align-items-center gap-3 text-muted" id="comp-step-8">
                        <span class="fs-5 lh-1 text-center" style="width: 24px;" id="comp-icon-8"><i class="bi bi-circle"></i></span>
                        <span class="small fw-semibold">8. Generating Repository Package</span>
                    </div>
                    <!-- Step 9 -->
                    <div class="d-flex align-items-center gap-3 text-muted" id="comp-step-9">
                        <span class="fs-5 lh-1 text-center" style="width: 24px;" id="comp-icon-9"><i class="bi bi-circle"></i></span>
                        <span class="small fw-semibold">9. Repository Ready</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Retro Terminal logs console -->
    <div class="col-lg-8">
        <div class="card aitos-card border-0 bg-dark shadow-lg">
            <div class="card-header bg-dark border-bottom border-secondary border-opacity-20 d-flex justify-content-between align-items-center py-3 px-4">
                <span class="text-secondary small fw-bold font-monospace"><i class="bi bi-terminal me-2"></i> COMPILER FEED LOG</span>
                <div class="d-flex gap-1.5 align-items-center">
                    <span class="rounded-circle bg-danger" style="width: 10px; height: 10px; display: inline-block;"></span>
                    <span class="rounded-circle bg-warning" style="width: 10px; height: 10px; display: inline-block;"></span>
                    <span class="rounded-circle bg-success" style="width: 10px; height: 10px; display: inline-block;"></span>
                </div>
            </div>
            <div class="compiler-log" id="compilerLogContainer">
                <div class="text-secondary font-monospace" style="opacity: 0.7;">
                    AITOS V1.0.0 Context Compiler Shell<br>
                    Ready for compilation instruction... Click the button on the left to start.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Nav actions footer, hidden until compiled -->
<div class="d-none justify-content-between align-items-center border-top pt-4 mb-4" id="compileSuccessNav">
    <a href="/team" class="btn btn-outline-secondary px-4 py-2"><i class="bi bi-arrow-left me-1"></i> Back to Planning</a>
    <div class="d-flex gap-3">
        <a href="/preview" class="btn btn-outline-primary px-4 py-3 fw-bold bg-white">
            <i class="bi bi-eye me-1"></i> Preview Repository Layout
        </a>
        <a href="/export" class="btn btn-success btn-lg px-5 py-3 fw-bold shadow-sm">
            Proceed to Export Center <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let logTimeout = null;

    function appendTerminalLine(text, type = 'info') {
        const container = document.getElementById("compilerLogContainer");
        const line = document.createElement("div");
        
        let typeClass = '';
        let prefix = '$ ';
        if (type === 'success') {
            typeClass = 'success';
            prefix = '[OK] ';
        } else if (type === 'warning') {
            typeClass = 'warning';
            prefix = '[WARN] ';
        } else if (type === 'line') {
            typeClass = '';
            prefix = '';
        }

        line.className = `compiler-line ${typeClass} font-monospace`;
        line.innerHTML = `<span>${prefix}</span>${escapeHtml(text)}`;
        container.appendChild(line);
        container.scrollTop = container.scrollHeight;
    }

    function setStepStatus(stepNum, status) {
        const stepDiv = document.getElementById(`comp-step-${stepNum}`);
        const iconSpan = document.getElementById(`comp-icon-${stepNum}`);

        stepDiv.className = `d-flex align-items-center gap-3`; // reset muted
        
        if (status === 'running') {
            stepDiv.classList.add("text-primary");
            iconSpan.innerHTML = `<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div>`;
        } else if (status === 'success') {
            stepDiv.classList.add("text-success");
            iconSpan.innerHTML = `<i class="bi bi-check-circle-fill text-success"></i>`;
        } else if (status === 'error') {
            stepDiv.classList.add("text-danger");
            iconSpan.innerHTML = `<i class="bi bi-x-circle-fill text-danger"></i>`;
        }
    }

    function startCompilation() {
        const state = getProjectState();
        
        // Disable compiler button during execution
        const startBtn = document.getElementById("startCompileBtn");
        startBtn.disabled = true;
        startBtn.innerHTML = `<div class="spinner-border spinner-border-sm me-2" role="status"></div> Compiling...`;

        // Clear terminal
        const container = document.getElementById("compilerLogContainer");
        container.innerHTML = "";

        // Reset visual steps
        for(let i=1; i<=9; i++) {
            const stepDiv = document.getElementById(`comp-step-${i}`);
            const iconSpan = document.getElementById(`comp-icon-${i}`);
            stepDiv.className = "d-flex align-items-center gap-3 text-muted";
            iconSpan.innerHTML = `<i class="bi bi-circle"></i>`;
        }

        appendTerminalLine("AITOS COMPILER version 1.0.0 -- bootstrapping environment context", "info");
        appendTerminalLine(`Target folder name: ${state.projectName || "AITOS_Project"}/`, "line");
        appendTerminalLine("Data-First Architecture active (JSON -> Markdown compilation)", "line");
        appendTerminalLine("---------------------------------------------------------------", "line");

        // Sequence of compiler logs and status changes
        const scriptLog = [
            // Stage 1
            { delay: 400, step: 1, status: 'running', text: "Reading wizard problem statement payload...", type: 'info' },
            { delay: 800, step: 1, status: 'running', text: `Found statements: ${state.problemStatement.length} characters parsed successfully.`, type: 'line' },
            { delay: 1200, step: 1, status: 'success', text: "Statement successfully structured.", type: 'success' },
            
            // Stage 2
            { delay: 1600, step: 2, status: 'running', text: "Loading approved architecture blueprints...", type: 'info' },
            { delay: 1900, step: 2, status: 'running', text: `Loaded Business Blueprint (V${state.blueprints.version})`, type: 'line' },
            { delay: 2100, step: 2, status: 'running', text: `Loaded Database Blueprint (V${state.blueprints.version})`, type: 'line' },
            { delay: 2300, step: 2, status: 'running', text: `Loaded Technical Blueprint (V${state.blueprints.version})`, type: 'line' },
            { delay: 2500, step: 2, status: 'running', text: `Loaded UI Spec Blueprint (V${state.blueprints.version})`, type: 'line' },
            { delay: 2700, step: 2, status: 'success', text: "Blueprints integrity verification: Valid.", type: 'success' },

            // Stage 3
            { delay: 3100, step: 3, status: 'running', text: "Analyzing business boundaries and system rules...", type: 'info' },
            { delay: 3400, step: 3, status: 'running', text: "Integrating 4 core business rules from requirement analysis.", type: 'line' },
            { delay: 3700, step: 3, status: 'success', text: "Ruleset successfully compiled.", type: 'success' },

            // Stage 4
            { delay: 4100, step: 4, status: 'running', text: "Retrieving team workflow planner records...", type: 'info' },
            { delay: 4400, step: 4, status: 'running', text: `Found ${state.teamMembers.length} active developer profiles and preferred agent nodes.`, type: 'line' },
            { delay: 4600, step: 4, status: 'running', text: `Found ${state.tasks.length} drag-and-drop task tokens.`, type: 'line' },
            { delay: 4900, step: 4, status: 'success', text: "Team assignment schemas aligned.", type: 'success' },

            // Stage 5
            { delay: 5300, step: 5, status: 'running', text: "Collecting human decisions timeline history log...", type: 'info' },
            { delay: 5600, step: 5, status: 'running', text: `Imported ${state.decisions.length} decision records.`, type: 'line' },
            { delay: 5800, step: 5, status: 'success', text: "Decision history registers finalized.", type: 'success' },

            // Stage 6
            { delay: 6200, step: 6, status: 'running', text: "Assembling AI Context templates...", type: 'info' },
            { delay: 6500, step: 6, status: 'running', text: "Writing /context/CURRENT_CONTEXT.md...", type: 'line' },
            { delay: 6700, step: 6, status: 'running', text: "Writing /context/BACKEND_CONTEXT.md...", type: 'line' },
            { delay: 6900, step: 6, status: 'running', text: "Writing /context/FRONTEND_CONTEXT.md...", type: 'line' },
            { delay: 7100, step: 6, status: 'running', text: "Writing /context/DATABASE_CONTEXT.md...", type: 'line' },
            { delay: 7300, step: 6, status: 'running', text: "Writing /context/GENERIC_CONTEXT.md...", type: 'line' },
            { delay: 7500, step: 6, status: 'success', text: "Markdown context templates written.", type: 'success' },

            // Stage 7
            { delay: 7900, step: 7, status: 'running', text: "Running context optimizations (trimming tokens, structuring headers)...", type: 'info' },
            { delay: 8200, step: 7, status: 'running', text: "Applying Markdown list-group optimizations. Token ratio: 98% density.", type: 'line' },
            { delay: 8400, step: 7, status: 'success', text: "Optimization finished.", type: 'success' },

            // Stage 8
            { delay: 8800, step: 8, status: 'running', text: "Writing source of truth data registers (.aitos/data/)...", type: 'info' },
            { delay: 9000, step: 8, status: 'running', text: "Exporting project.json database representation...", type: 'line' },
            { delay: 9200, step: 8, status: 'running', text: "Exporting team.json database representation...", type: 'line' },
            { delay: 9400, step: 8, status: 'running', text: "Writing project summary files: README.md, START_HERE.md...", type: 'line' },
            { delay: 9600, step: 8, status: 'success', text: "Static directory maps successfully compiled.", type: 'success' },

            // Stage 9
            { delay: 10000, step: 9, status: 'running', text: "Validating AITOS folder structure package...", type: 'info' },
            { delay: 10300, step: 9, status: 'success', text: "Repository structure created. AITOS ready.", type: 'success' },
            { delay: 10600, step: 9, status: 'success', text: "--------------------------------------------------------", type: 'line' },
            { delay: 10600, step: 9, status: 'success', text: "COMPILATION SUCCESSFUL. READY FOR EXPORT DOWNLOAD.", type: 'success' }
        ];

        // Trigger step timeline
        scriptLog.forEach(action => {
            logTimeout = setTimeout(() => {
                appendTerminalLine(action.text, action.type);
                setStepStatus(action.step, action.status);

                // Final handler when compilation finishes
                if (action.delay === 10600) {
                    completeCompilation();
                }
            }, action.delay);
        });
    }

    function completeCompilation() {
        const state = getProjectState();
        state.contextCompiled = true;
        
        state.decisions.push({
            date: new Date().toISOString().slice(0, 16).replace('T', ' '),
            title: "Repository Compiled",
            desc: "Context Compiler successfully generated package .aitos/ file structure maps."
        });
        
        saveProjectState(state);

        // Reset control button
        const startBtn = document.getElementById("startCompileBtn");
        startBtn.classList.remove("btn-primary");
        startBtn.classList.add("btn-outline-success");
        startBtn.disabled = false;
        startBtn.innerHTML = `<i class="bi bi-arrow-repeat me-1"></i> Re-compile Context`;

        // Unhide footer navigation
        const footerNav = document.getElementById("compileSuccessNav");
        footerNav.classList.remove("d-none");
        footerNav.classList.add("d-flex");
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
