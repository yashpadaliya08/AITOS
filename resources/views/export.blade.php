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

            <!-- Prompt Customizer Card -->
            <div class="col-12">
                <div class="card border-warning border-2 shadow-sm p-3" id="promptCustomizerCard">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-10 text-warning" style="width: 50px; height: 50px; font-size: 1.5rem;">
                                    <i class="bi bi-stars"></i>
                                </span>
                                <div>
                                    <h5 class="fw-bold text-dark mb-0">AI Prompt Customizer</h5>
                                    <p class="text-muted small mb-0">Select your target AI editor, preview and customize prompt packs before downloading.</p>
                                </div>
                            </div>
                            <span class="badge bg-warning text-dark px-3 py-2"><i class="bi bi-lightning-charge-fill me-1"></i>New</span>
                        </div>

                        <div class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label for="promptModelSelect" class="form-label fw-semibold small text-dark">Target AI Model</label>
                                <select class="form-select" id="promptModelSelect">
                                    <option value="cursor" selected>🖱️ Cursor AI — .cursorrules format</option>
                                    <option value="claude">🤖 Claude (Anthropic) — XML system prompts</option>
                                    <option value="gemini">💎 Gemini (Google) — Structured instructions</option>
                                    <option value="copilot">🐙 GitHub Copilot — Inline comments</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-outline-warning w-100 fw-bold py-2" id="previewPromptsBtn" onclick="previewPrompts()">
                                    <i class="bi bi-eye-fill me-1"></i> Preview & Customize Prompts
                                </button>
                            </div>
                            <div class="col-md-3">
                                <div class="text-muted small text-end" id="promptModelDesc">Optimized for Cursor .cursorrules and Composer chat.</div>
                            </div>
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
<!-- Prompt Preview Modal -->
<div class="modal fade" id="promptPreviewModal" tabindex="-1" aria-labelledby="promptPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-warning bg-opacity-10 border-warning">
                <h5 class="modal-title fw-bold" id="promptPreviewModalLabel">
                    <i class="bi bi-stars me-2"></i>Prompt Pack Preview
                    <span class="badge bg-dark ms-2 fw-normal" id="modalModelBadge">Cursor AI</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="promptPreviewBody">
                <div class="d-flex align-items-center justify-content-center py-5">
                    <div class="spinner-border text-warning" role="status"></div>
                    <span class="ms-3 text-muted">Generating prompts...</span>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <div class="text-muted small">
                    <i class="bi bi-info-circle me-1"></i> Edit any prompt content above. Changes will be bundled into your ZIP download.
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-warning fw-bold" onclick="applyCustomPrompts()">
                        <i class="bi bi-check-lg me-1"></i> Apply to ZIP Package
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
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

    // ── Prompt Customizer Functions ──

    // Update description text when model selection changes
    document.getElementById('promptModelSelect').addEventListener('change', function() {
        const descriptions = {
            cursor: 'Optimized for Cursor .cursorrules and Composer chat.',
            claude: 'XML-tagged system prompts for Claude precision.',
            gemini: 'Structured instructions for Gemini large context windows.',
            copilot: 'Inline-comment style for GitHub Copilot Workspace.'
        };
        document.getElementById('promptModelDesc').textContent = descriptions[this.value] || '';
    });

    // Cached prompt data for customization
    let cachedPrompts = {};

    async function previewPrompts() {
        const state = getProjectState();
        const modelTarget = document.getElementById('promptModelSelect').value;
        const modal = new bootstrap.Modal(document.getElementById('promptPreviewModal'));
        const body = document.getElementById('promptPreviewBody');

        // Show loading state
        body.innerHTML = `
            <div class="d-flex align-items-center justify-content-center py-5">
                <div class="spinner-border text-warning" role="status"></div>
                <span class="ms-3 text-muted">Generating prompts for ${modelTarget}...</span>
            </div>`;
        modal.show();

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const response = await fetch('/export/preview-prompts', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    project_state: JSON.stringify(state),
                    model_target: modelTarget
                })
            });

            const data = await response.json();

            if (!data.success) {
                body.innerHTML = `<div class="alert alert-danger m-4">${data.message || 'Failed to generate prompts.'}</div>`;
                return;
            }

            document.getElementById('modalModelBadge').textContent = data.model_label;
            cachedPrompts = data.prompts;

            // Build accordion UI with editable textareas
            let html = '<div class="accordion" id="promptAccordion">';
            let index = 0;
            for (const [filename, content] of Object.entries(data.prompts)) {
                const isFirst = index === 0;
                const cleanName = filename.replace('.md', '').replace(/_/g, ' ');
                html += `
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button ${isFirst ? '' : 'collapsed'} fw-semibold" type="button"
                                    data-bs-toggle="collapse" data-bs-target="#promptCollapse${index}">
                                <i class="bi bi-file-earmark-code me-2 text-warning"></i>
                                <span class="font-monospace small">${filename}</span>
                                <span class="ms-2 text-muted small fw-normal">— ${cleanName}</span>
                            </button>
                        </h2>
                        <div id="promptCollapse${index}" class="accordion-collapse collapse ${isFirst ? 'show' : ''}"
                             data-bs-parent="#promptAccordion">
                            <div class="accordion-body p-2">
                                <textarea class="form-control font-monospace"
                                          id="promptEdit_${filename}"
                                          rows="14"
                                          style="font-size: 0.8rem; background: #1e1e2e; color: #cdd6f4; border: 1px solid #45475a; resize: vertical;"
                                >${content.replace(/</g, '&lt;').replace(/>/g, '&gt;')}</textarea>
                            </div>
                        </div>
                    </div>`;
                index++;
            }
            html += '</div>';
            body.innerHTML = html;

        } catch (err) {
            body.innerHTML = `<div class="alert alert-danger m-4">Network error: ${err.message}</div>`;
        }
    }

    function applyCustomPrompts() {
        // Read edited values from textareas and store in project state
        const state = getProjectState();
        const customized = {};

        for (const filename of Object.keys(cachedPrompts)) {
            const textarea = document.getElementById(`promptEdit_${filename}`);
            if (textarea) {
                customized[filename] = textarea.value;
            } else {
                customized[filename] = cachedPrompts[filename];
            }
        }

        // Store customized prompts and model target in state
        state.promptPacks = { files: customized };
        state.promptModelTarget = document.getElementById('promptModelSelect').value;
        saveProjectState(state);

        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('promptPreviewModal')).hide();

        // Show success toast if available
        if (typeof showToast === 'function') {
            showToast('Prompt packs customized and attached to ZIP package.', 'success');
        }
    }
</script>
@endsection
