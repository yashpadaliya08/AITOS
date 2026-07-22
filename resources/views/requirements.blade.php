@extends('layouts.app')

@section('title', 'AITOS - Requirement Analysis')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Requirement Analysis</h3>
        <p class="text-muted mb-0">Review and refine AI-extracted structures from your problem statement.</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <button type="button" class="btn btn-outline-primary fw-semibold px-3 py-2" onclick="reAnalyzeWithAI()">
            <i class="bi bi-arrow-repeat me-1"></i> Re-analyze with AI
        </button>
        <span class="badge bg-secondary px-3 py-2">Phase 2: Analysis</span>
    </div>
</div>

<!-- Outdated Problem Statement Warning Banner -->
<div id="outdatedBanner" class="alert alert-warning border-warning border-opacity-25 d-none justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-2">
        <i class="bi bi-exclamation-triangle-fill text-warning fs-5"></i>
        <div>
            <strong class="text-dark">Project configuration has changed.</strong>
            <span class="text-muted d-block small">The current Requirement Analysis is now outdated because you edited the project details or problem statement.</span>
        </div>
    </div>
    <button type="button" class="btn btn-warning btn-sm fw-semibold" onclick="reAnalyzeWithAI()">
        <i class="bi bi-arrow-repeat me-1"></i> Re-analyze with AI
    </button>
</div>

<!-- API Key Configuration Missing Banner -->
<div id="apiKeyMissingBanner" class="alert alert-danger border-danger border-opacity-25 d-none justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-2">
        <i class="bi bi-shield-slash-fill text-danger fs-5"></i>
        <div>
            <strong class="text-dark">AI API Key Configuration Required</strong>
            <span class="text-muted d-block small">No API key is configured for your default AI provider. Please set your credentials before continuing.</span>
        </div>
    </div>
    <a href="/settings" class="btn btn-danger btn-sm fw-semibold">
        <i class="bi bi-gear-fill me-1"></i> Go to Settings
    </a>
</div>

<!-- Full Card deck Loading View -->
<div id="requirementsLoadingOverlay" class="d-none flex-column align-items-center justify-content-center py-5 my-5 text-center">
    <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <h5 class="fw-bold mb-1">AITOS AI Engine is Analyzing...</h5>
    <p class="text-muted small" id="loadingOverlaySubtitle">Sending project context to selected provider. This may take 10-15 seconds...</p>
</div>

<div class="row g-4 mb-5" id="requirementsDeckContainer">
    <!-- Entities Card -->
    <div class="col-md-6 col-lg-4">
        <div class="card aitos-card h-100 border-light-subtle">
            <div class="aitos-card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-database text-primary me-2"></i> Entities</span>
                <button type="button" class="btn btn-sm btn-outline-primary px-3 py-1" onclick="toggleEditCard('entities')">
                    <span id="btn-text-entities">Edit</span>
                </button>
            </div>
            <div class="aitos-card-body">
                <div id="view-entities" class="whitespace-pre-wrap text-secondary" style="font-size: 0.9rem;"></div>
                <div id="edit-container-entities" class="d-none">
                    <textarea id="edit-input-entities" class="form-control font-monospace border-light-subtle" rows="8" style="font-size: 0.85rem;"></textarea>
                    <div class="d-flex gap-2 mt-3 justify-content-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1" onclick="cancelEditCard('entities')">Cancel</button>
                        <button type="button" class="btn btn-sm btn-primary px-3 py-1" onclick="saveEditCard('entities')">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Relationships Card -->
    <div class="col-md-6 col-lg-4">
        <div class="card aitos-card h-100 border-light-subtle">
            <div class="aitos-card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-diagram-3 text-primary me-2"></i> Entity Relationships</span>
                <button type="button" class="btn btn-sm btn-outline-primary px-3 py-1" onclick="toggleEditCard('relationships')">
                    <span id="btn-text-relationships">Edit</span>
                </button>
            </div>
            <div class="aitos-card-body">
                <div id="view-relationships" class="whitespace-pre-wrap text-secondary" style="font-size: 0.9rem;"></div>
                <div id="edit-container-relationships" class="d-none">
                    <textarea id="edit-input-relationships" class="form-control font-monospace border-light-subtle" rows="8" style="font-size: 0.85rem;"></textarea>
                    <div class="d-flex gap-2 mt-3 justify-content-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1" onclick="cancelEditCard('relationships')">Cancel</button>
                        <button type="button" class="btn btn-sm btn-primary px-3 py-1" onclick="saveEditCard('relationships')">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modules Card -->
    <div class="col-md-6 col-lg-4">
        <div class="card aitos-card h-100 border-light-subtle">
            <div class="aitos-card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-boxes text-primary me-2"></i> Modules</span>
                <button type="button" class="btn btn-sm btn-outline-primary px-3 py-1" onclick="toggleEditCard('modules')">
                    <span id="btn-text-modules">Edit</span>
                </button>
            </div>
            <div class="aitos-card-body">
                <div id="view-modules" class="whitespace-pre-wrap text-secondary" style="font-size: 0.9rem;"></div>
                <div id="edit-container-modules" class="d-none">
                    <textarea id="edit-input-modules" class="form-control font-monospace border-light-subtle" rows="8" style="font-size: 0.85rem;"></textarea>
                    <div class="d-flex gap-2 mt-3 justify-content-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1" onclick="cancelEditCard('modules')">Cancel</button>
                        <button type="button" class="btn btn-sm btn-primary px-3 py-1" onclick="saveEditCard('modules')">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Roles Card -->
    <div class="col-md-6 col-lg-4">
        <div class="card aitos-card h-100 border-light-subtle">
            <div class="aitos-card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-person-badge text-primary me-2"></i> User Roles</span>
                <button type="button" class="btn btn-sm btn-outline-primary px-3 py-1" onclick="toggleEditCard('roles')">
                    <span id="btn-text-roles">Edit</span>
                </button>
            </div>
            <div class="aitos-card-body">
                <div id="view-roles" class="whitespace-pre-wrap text-secondary" style="font-size: 0.9rem;"></div>
                <div id="edit-container-roles" class="d-none">
                    <textarea id="edit-input-roles" class="form-control font-monospace border-light-subtle" rows="8" style="font-size: 0.85rem;"></textarea>
                    <div class="d-flex gap-2 mt-3 justify-content-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1" onclick="cancelEditCard('roles')">Cancel</button>
                        <button type="button" class="btn btn-sm btn-primary px-3 py-1" onclick="saveEditCard('roles')">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Business Rules Card -->
    <div class="col-md-6 col-lg-4">
        <div class="card aitos-card h-100 border-light-subtle">
            <div class="aitos-card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-shield-slash text-primary me-2"></i> Business Rules</span>
                <button type="button" class="btn btn-sm btn-outline-primary px-3 py-1" onclick="toggleEditCard('businessRules')">
                    <span id="btn-text-businessRules">Edit</span>
                </button>
            </div>
            <div class="aitos-card-body">
                <div id="view-businessRules" class="whitespace-pre-wrap text-secondary" style="font-size: 0.9rem;"></div>
                <div id="edit-container-businessRules" class="d-none">
                    <textarea id="edit-input-businessRules" class="form-control font-monospace border-light-subtle" rows="8" style="font-size: 0.85rem;"></textarea>
                    <div class="d-flex gap-2 mt-3 justify-content-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1" onclick="cancelEditCard('businessRules')">Cancel</button>
                        <button type="button" class="btn btn-sm btn-primary px-3 py-1" onclick="saveEditCard('businessRules')">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Requirements Card -->
    <div class="col-md-6 col-lg-4">
        <div class="card aitos-card h-100 border-light-subtle">
            <div class="aitos-card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-card-checklist text-primary me-2"></i> Functional Specs</span>
                <button type="button" class="btn btn-sm btn-outline-primary px-3 py-1" onclick="toggleEditCard('requirements')">
                    <span id="btn-text-requirements">Edit</span>
                </button>
            </div>
            <div class="aitos-card-body">
                <div id="view-requirements" class="whitespace-pre-wrap text-secondary" style="font-size: 0.9rem;"></div>
                <div id="edit-container-requirements" class="d-none">
                    <textarea id="edit-input-requirements" class="form-control font-monospace border-light-subtle" rows="8" style="font-size: 0.85rem;"></textarea>
                    <div class="d-flex gap-2 mt-3 justify-content-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1" onclick="cancelEditCard('requirements')">Cancel</button>
                        <button type="button" class="btn btn-sm btn-primary px-3 py-1" onclick="saveEditCard('requirements')">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assumptions Card -->
    <div class="col-md-6 col-lg-4">
        <div class="card aitos-card h-100 border-light-subtle">
            <div class="aitos-card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-question-circle text-primary me-2"></i> Assumptions</span>
                <button type="button" class="btn btn-sm btn-outline-primary px-3 py-1" onclick="toggleEditCard('assumptions')">
                    <span id="btn-text-assumptions">Edit</span>
                </button>
            </div>
            <div class="aitos-card-body">
                <div id="view-assumptions" class="whitespace-pre-wrap text-secondary" style="font-size: 0.9rem;"></div>
                <div id="edit-container-assumptions" class="d-none">
                    <textarea id="edit-input-assumptions" class="form-control font-monospace border-light-subtle" rows="8" style="font-size: 0.85rem;"></textarea>
                    <div class="d-flex gap-2 mt-3 justify-content-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1" onclick="cancelEditCard('assumptions')">Cancel</button>
                        <button type="button" class="btn btn-sm btn-primary px-3 py-1" onclick="saveEditCard('assumptions')">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Risks Card -->
    <div class="col-md-6 col-lg-4">
        <div class="card aitos-card h-100 border-light-subtle">
            <div class="aitos-card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold text-danger"><i class="bi bi-exclamation-triangle text-danger me-2"></i> Risks</span>
                <button type="button" class="btn btn-sm btn-outline-danger px-3 py-1" onclick="toggleEditCard('risks')">
                    <span id="btn-text-risks">Edit</span>
                </button>
            </div>
            <div class="aitos-card-body">
                <div id="view-risks" class="whitespace-pre-wrap text-secondary" style="font-size: 0.9rem;"></div>
                <div id="edit-container-risks" class="d-none">
                    <textarea id="edit-input-risks" class="form-control font-monospace border-light-subtle" rows="8" style="font-size: 0.85rem;"></textarea>
                    <div class="d-flex gap-2 mt-3 justify-content-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1" onclick="cancelEditCard('risks')">Cancel</button>
                        <button type="button" class="btn btn-sm btn-danger px-3 py-1" onclick="saveEditCard('risks')">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Stories Card -->
    <div class="col-md-6 col-lg-4">
        <div class="card aitos-card h-100 border-light-subtle">
            <div class="aitos-card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-journal-text text-primary me-2"></i> User Stories</span>
                <button type="button" class="btn btn-sm btn-outline-primary px-3 py-1" onclick="toggleEditCard('userStories')">
                    <span id="btn-text-userStories">Edit</span>
                </button>
            </div>
            <div class="aitos-card-body">
                <div id="view-userStories" class="whitespace-pre-wrap text-secondary" style="font-size: 0.9rem;"></div>
                <div id="edit-container-userStories" class="d-none">
                    <textarea id="edit-input-userStories" class="form-control font-monospace border-light-subtle" rows="8" style="font-size: 0.85rem;"></textarea>
                    <div class="d-flex gap-2 mt-3 justify-content-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1" onclick="cancelEditCard('userStories')">Cancel</button>
                        <button type="button" class="btn btn-sm btn-primary px-3 py-1" onclick="saveEditCard('userStories')">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Non-functional Specs Card -->
    <div class="col-md-6 col-lg-4">
        <div class="card aitos-card h-100 border-light-subtle">
            <div class="aitos-card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-speedometer2 text-primary me-2"></i> Non-functional Specs</span>
                <button type="button" class="btn btn-sm btn-outline-primary px-3 py-1" onclick="toggleEditCard('nonFunctionalRequirements')">
                    <span id="btn-text-nonFunctionalRequirements">Edit</span>
                </button>
            </div>
            <div class="aitos-card-body">
                <div id="view-nonFunctionalRequirements" class="whitespace-pre-wrap text-secondary" style="font-size: 0.9rem;"></div>
                <div id="edit-container-nonFunctionalRequirements" class="d-none">
                    <textarea id="edit-input-nonFunctionalRequirements" class="form-control font-monospace border-light-subtle" rows="8" style="font-size: 0.85rem;"></textarea>
                    <div class="d-flex gap-2 mt-3 justify-content-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1" onclick="cancelEditCard('nonFunctionalRequirements')">Cancel</button>
                        <button type="button" class="btn btn-sm btn-primary px-3 py-1" onclick="saveEditCard('nonFunctionalRequirements')">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Suggested Folder Structure Card -->
    <div class="col-md-6 col-lg-4">
        <div class="card aitos-card h-100 border-light-subtle">
            <div class="aitos-card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-folder-symlink text-primary me-2"></i> File Directory Map</span>
                <button type="button" class="btn btn-sm btn-outline-primary px-3 py-1" onclick="toggleEditCard('suggestedFolderStructure')">
                    <span id="btn-text-suggestedFolderStructure">Edit</span>
                </button>
            </div>
            <div class="aitos-card-body">
                <div id="view-suggestedFolderStructure" class="whitespace-pre-wrap text-secondary" style="font-size: 0.9rem;"></div>
                <div id="edit-container-suggestedFolderStructure" class="d-none">
                    <textarea id="edit-input-suggestedFolderStructure" class="form-control font-monospace border-light-subtle" rows="8" style="font-size: 0.85rem;"></textarea>
                    <div class="d-flex gap-2 mt-3 justify-content-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1" onclick="cancelEditCard('suggestedFolderStructure')">Cancel</button>
                        <button type="button" class="btn btn-sm btn-primary px-3 py-1" onclick="saveEditCard('suggestedFolderStructure')">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Implementation Phases Card -->
    <div class="col-md-6 col-lg-4">
        <div class="card aitos-card h-100 border-light-subtle">
            <div class="aitos-card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-kanban text-primary me-2"></i> Action Phases</span>
                <button type="button" class="btn btn-sm btn-outline-primary px-3 py-1" onclick="toggleEditCard('implementationPhases')">
                    <span id="btn-text-implementationPhases">Edit</span>
                </button>
            </div>
            <div class="aitos-card-body">
                <div id="view-implementationPhases" class="whitespace-pre-wrap text-secondary" style="font-size: 0.9rem;"></div>
                <div id="edit-container-implementationPhases" class="d-none">
                    <textarea id="edit-input-implementationPhases" class="form-control font-monospace border-light-subtle" rows="8" style="font-size: 0.85rem;"></textarea>
                    <div class="d-flex gap-2 mt-3 justify-content-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1" onclick="cancelEditCard('implementationPhases')">Cancel</button>
                        <button type="button" class="btn btn-sm btn-primary px-3 py-1" onclick="saveEditCard('implementationPhases')">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Notes Card -->
    <div class="col-md-6 col-lg-4">
        <div class="card aitos-card h-100 border-light-subtle">
            <div class="aitos-card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark"><i class="bi bi-sticky text-primary me-2"></i> AI Architecture Notes</span>
                <button type="button" class="btn btn-sm btn-outline-primary px-3 py-1" onclick="toggleEditCard('aiNotes')">
                    <span id="btn-text-aiNotes">Edit</span>
                </button>
            </div>
            <div class="aitos-card-body">
                <div id="view-aiNotes" class="whitespace-pre-wrap text-secondary" style="font-size: 0.9rem;"></div>
                <div id="edit-container-aiNotes" class="d-none">
                    <textarea id="edit-input-aiNotes" class="form-control font-monospace border-light-subtle" rows="8" style="font-size: 0.85rem;"></textarea>
                    <div class="d-flex gap-2 mt-3 justify-content-end">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1" onclick="cancelEditCard('aiNotes')">Cancel</button>
                        <button type="button" class="btn btn-sm btn-primary px-3 py-1" onclick="saveEditCard('aiNotes')">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center border-top pt-4 mb-4">
    <a href="/wizard" class="btn btn-outline-secondary px-4 py-2"><i class="bi bi-arrow-left me-1"></i> Back to Wizard</a>
    <button type="button" class="btn btn-success btn-lg px-5 py-3 fw-bold shadow-sm" onclick="approveRequirements()">
        Approve Analysis & Unlock Blueprints <i class="bi bi-shield-check ms-1"></i>
    </button>
</div>
@endsection

@section('scripts')
<script>
    let activeEdits = {};

    document.addEventListener("DOMContentLoaded", () => {
        checkAnalysisCache();
    });

    async function checkAnalysisCache() {
        const state = getProjectState();
        
        // Calculate SHA256 of current context properties
        const currentText = (state.projectName || '') + '|' + (state.projectGoal || '') + '|' + (state.problemStatement || '');
        const currentHash = await calculateHash(currentText);

        const defaultProv = (state.apiKeys && state.apiKeys.defaultProvider) || "openai";
        const activeKey = state.apiKeys ? (state.apiKeys[defaultProv] || '') : '';
        const activeModel = getModelForProvider(defaultProv, state);

        // If hash exists but differs from cached hash, show outdated banner
        if (state.analysisHash && state.analysisHash !== currentHash) {
            document.getElementById("outdatedBanner").classList.remove("d-none");
            document.getElementById("outdatedBanner").classList.add("d-flex");
        } else {
            document.getElementById("outdatedBanner").classList.add("d-none");
            document.getElementById("outdatedBanner").classList.remove("d-flex");
        }

        // If no cached analysis exists (first time wizard entry)
        if (!state.analysisHash) {
            // Always try running, the backend will check .env if activeKey is empty!
            runAIAnalysis(currentHash, activeKey, defaultProv);
        } else {
            // Render from local storage cache
            document.getElementById("requirementsDeckContainer").classList.remove("d-none");
            loadRequirementsData();
        }
    }

    async function calculateHash(text) {
        const msgBuffer = new TextEncoder().encode(text);
        const hashBuffer = await crypto.subtle.digest('SHA-256', msgBuffer);
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
    }

    function runAIAnalysis(targetHash, apiKey, provider) {
        const state = getProjectState();
        
        // Setup overlay loaders
        document.getElementById("apiKeyMissingBanner").classList.add("d-none");
        document.getElementById("outdatedBanner").classList.add("d-none");
        document.getElementById("requirementsDeckContainer").classList.add("d-none");
        
        const loader = document.getElementById("requirementsLoadingOverlay");
        loader.classList.remove("d-none");
        loader.classList.add("d-flex");
        
        const activeModel = getModelForProvider(provider, state);
        const providersText = {
            gemini: `Gemini (${activeModel})`,
            openai: `OpenRouter (${activeModel})`,
            anthropic: `Anthropic (${activeModel})`
        };
        document.getElementById("loadingOverlaySubtitle").innerText = `Sending project context to ${providersText[provider] || 'AI engine'}. This may take 10-15 seconds...`;

        // POST request to local Laravel backend
        fetch('/api/analyze', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                project_name: state.projectName,
                project_description: state.projectDescription,
                project_goal: state.projectGoal,
                problem_statement: state.problemStatement,
                preferred_stack: state.techStack,
                provider: provider,
                api_key: apiKey,
                model: getModelForProvider(provider, state)
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw new Error(err.message || "Failed to contact engine."); });
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.analysis) {
                // Map the structured JSON result to requirements state
                const res = data.analysis;
                
                let entitiesStr = "";
                if (Array.isArray(res.entities)) {
                    entitiesStr = res.entities.map(e => {
                        if (typeof e === 'object' && e !== null) {
                            return `${e.name || ''}: ${(e.attributes || []).join(', ')}`;
                        }
                        return String(e);
                    }).join('\n');
                } else {
                    entitiesStr = res.entities || "";
                }

                let relationshipsStr = "";
                if (Array.isArray(res.relationships)) {
                    relationshipsStr = res.relationships.map(r => {
                        if (typeof r === 'object' && r !== null) {
                            return `${r.from || ''} ${r.type || 'belongs_to'} ${r.to || ''}`;
                        }
                        return String(r);
                    }).join('\n');
                } else {
                    relationshipsStr = res.relationships || "";
                }

                state.requirements = {
                    entities: entitiesStr,
                    relationships: relationshipsStr,
                    modules: Array.isArray(res.modules) ? res.modules.join('\n') : (res.modules || ''),
                    roles: Array.isArray(res.roles) ? res.roles.join('\n') : (res.roles || ''),
                    businessRules: Array.isArray(res.businessRules) ? res.businessRules.join('\n') : (res.businessRules || ''),
                    requirements: Array.isArray(res.functionalRequirements) ? res.functionalRequirements.join('\n') : (res.functionalRequirements || ''),
                    nonFunctionalRequirements: Array.isArray(res.nonFunctionalRequirements) ? res.nonFunctionalRequirements.join('\n') : (res.nonFunctionalRequirements || ''),
                    assumptions: Array.isArray(res.assumptions) ? res.assumptions.join('\n') : (res.assumptions || ''),
                    risks: Array.isArray(res.risks) ? res.risks.join('\n') : (res.risks || ''),
                    userStories: Array.isArray(res.userStories) ? res.userStories.join('\n') : (res.userStories || ''),
                    suggestedFolderStructure: Array.isArray(res.suggestedFolderStructure) ? res.suggestedFolderStructure.join('\n') : (res.suggestedFolderStructure || ''),
                    implementationPhases: Array.isArray(res.implementationPhases) ? res.implementationPhases.join('\n') : (res.implementationPhases || ''),
                    aiNotes: Array.isArray(res.aiNotes) ? res.aiNotes.join('\n') : (res.aiNotes || '')
                };

                // Sync technology stack suggestions if returned
                if (res.suggestedTechnologyStack) {
                    state.techStack.framework = res.suggestedTechnologyStack.framework || state.techStack.framework;
                    state.techStack.database = res.suggestedTechnologyStack.database || state.techStack.database;
                    state.techStack.frontend = res.suggestedTechnologyStack.frontend || state.techStack.frontend;
                }

                // Cache hash
                state.analysisHash = data.hash;

                state.decisions.push({
                    date: new Date().toISOString().slice(0, 16).replace('T', ' '),
                    title: `Context Analyzed (${provider.toUpperCase()})`,
                    desc: `Automatically analyzed project context. Hash cached successfully.`
                });

                saveProjectState(state);
                
                // Hide loaders
                loader.classList.add("d-none");
                document.getElementById("requirementsDeckContainer").classList.remove("d-none");
                loadRequirementsData();
            } else {
                throw new Error("Invalid response JSON schema.");
            }
        })
        .catch(err => {
            loader.classList.add("d-none");
            
            // If the key is indeed missing on both frontend and backend config
            if (err.message.includes("API key") || err.message.includes("API Key") || err.message.includes("missing")) {
                document.getElementById("apiKeyMissingBanner").classList.remove("d-none");
                document.getElementById("apiKeyMissingBanner").classList.add("d-flex");
                document.getElementById("requirementsDeckContainer").classList.add("d-none");
            } else {
                alert("AITOS AI Analysis Error: " + err.message + "\n\nPress OK to proceed using local default mockup schemas.");
                
                // Mock hash to allow continuation if offline/API fails during test
                state.analysisHash = targetHash;
                saveProjectState(state);
                document.getElementById("requirementsDeckContainer").classList.remove("d-none");
                loadRequirementsData();
            }
        });
    }

    async function reAnalyzeWithAI() {
        const state = getProjectState();
        const currentText = (state.projectName || '') + '|' + (state.projectGoal || '') + '|' + (state.problemStatement || '');
        const currentHash = await calculateHash(currentText);

        const defaultProv = (state.apiKeys && state.apiKeys.defaultProvider) || "openai";
        const activeKey = state.apiKeys ? (state.apiKeys[defaultProv] || '') : '';

        if (confirm("Re-analyzing will overwrite current cards with fresh AI findings. Proceed?")) {
            runAIAnalysis(currentHash, activeKey, defaultProv);
        }
    }

    function loadRequirementsData() {
        const state = getProjectState();
        
        const fields = [
            'entities', 'relationships', 'modules', 'roles', 'businessRules', 'requirements', 
            'nonFunctionalRequirements', 'assumptions', 'risks', 'userStories', 
            'suggestedFolderStructure', 'implementationPhases', 'aiNotes'
        ];
        
        fields.forEach(field => {
            const content = state.requirements[field] || "";
            const viewEl = document.getElementById(`view-${field}`);
            const inputEl = document.getElementById(`edit-input-${field}`);
            if (viewEl) viewEl.innerText = content;
            if (inputEl) inputEl.value = content;
        });
    }

    function toggleEditCard(field) {
        const viewEl = document.getElementById(`view-${field}`);
        const editEl = document.getElementById(`edit-container-${field}`);
        const btnText = document.getElementById(`btn-text-${field}`);

        if (editEl.classList.contains("d-none")) {
            viewEl.classList.add("d-none");
            editEl.classList.remove("d-none");
            btnText.innerText = "Editing...";
            document.getElementById(`edit-input-${field}`).focus();
        } else {
            cancelEditCard(field);
        }
    }

    function cancelEditCard(field) {
        const viewEl = document.getElementById(`view-${field}`);
        const editEl = document.getElementById(`edit-container-${field}`);
        const btnText = document.getElementById(`btn-text-${field}`);
        const textInput = document.getElementById(`edit-input-${field}`);

        textInput.value = viewEl.innerText;

        viewEl.classList.remove("d-none");
        editEl.classList.add("d-none");
        btnText.innerText = "Edit";
    }

    function saveEditCard(field) {
        const viewEl = document.getElementById(`view-${field}`);
        const editEl = document.getElementById(`edit-container-${field}`);
        const btnText = document.getElementById(`btn-text-${field}`);
        const textInput = document.getElementById(`edit-input-${field}`);

        const textValue = textInput.value.trim();
        viewEl.innerText = textValue;

        const state = getProjectState();
        state.requirements[field] = textValue;
        
        state.decisions.push({
            date: new Date().toISOString().slice(0, 16).replace('T', ' '),
            title: `Requirements Edited: ${field.charAt(0).toUpperCase() + field.slice(1)}`,
            desc: `User updated requirement analysis parameters.`
        });
        
        saveProjectState(state);

        viewEl.classList.remove("d-none");
        editEl.classList.add("d-none");
        btnText.innerText = "Edit";
    }

    function approveRequirements() {
        const state = getProjectState();
        state.requirementsApproved = true;
        
        state.blueprintApproved = false;
        state.teamAssigned = false;
        state.contextCompiled = false;

        state.decisions.push({
            date: new Date().toISOString().slice(0, 16).replace('T', ' '),
            title: "Requirements Approved",
            desc: "Requirement boundaries and rules locked by Decider. Proceeding to Blueprints review."
        });

        saveProjectState(state);
        window.location.href = "/blueprint";
    }
</script>
@endsection
