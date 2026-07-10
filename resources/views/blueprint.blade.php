@extends('layouts.app')

@section('title', 'AITOS - Blueprint Review')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1">Architecture Blueprint Review</h3>
        <p class="text-muted mb-0">Inspect the structured system schematics generated for your AI team.</p>
    </div>
    <div class="d-flex align-items-center gap-3">
        <div class="text-end d-none d-sm-block">
            <small class="text-muted d-block uppercase font-monospace" style="font-size: 0.65rem;">Active Blueprint Version</small>
            <span class="fw-bold fs-5 text-dark" id="blueprintVersionBadge">v1.0.0-Draft</span>
        </div>
        <span class="badge bg-secondary px-3 py-2">Phase 3: Blueprint</span>
    </div>
</div>

<div class="row">
    <div class="col-lg-9 mb-4">
        <div class="card aitos-card border-light-subtle shadow-sm">
            <div class="card-header bg-white border-bottom p-0">
                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs border-0" id="blueprintTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active py-3 px-4 fw-semibold border-0 rounded-0" id="business-tab" data-bs-toggle="tab" data-bs-target="#business" type="button" role="tab" aria-controls="business" aria-selected="true">
                            <i class="bi bi-briefcase me-2"></i>Business
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 px-4 fw-semibold border-0 rounded-0" id="database-tab" data-bs-toggle="tab" data-bs-target="#database" type="button" role="tab" aria-controls="database" aria-selected="false">
                            <i class="bi bi-hdd-network me-2"></i>Database
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 px-4 fw-semibold border-0 rounded-0" id="technical-tab" data-bs-toggle="tab" data-bs-target="#technical" type="button" role="tab" aria-controls="technical" aria-selected="false">
                            <i class="bi bi-code-square me-2"></i>Technical
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-3 px-4 fw-semibold border-0 rounded-0" id="ui-tab" data-bs-toggle="tab" data-bs-target="#ui" type="button" role="tab" aria-controls="ui" aria-selected="false">
                            <i class="bi bi-window-sidebar me-2"></i>UI Spec
                        </button>
                    </li>
                </ul>
            </div>
            
            <div class="aitos-card-body p-4">
                <div class="tab-content" id="blueprintTabContent">
                    <!-- Business Blueprint Tab -->
                    <div class="tab-pane fade show active" id="business" role="tabpanel" aria-labelledby="business-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-dark mb-0">Business Blueprint</h5>
                            <button type="button" id="edit-btn-business" class="btn btn-sm btn-outline-secondary" onclick="editBlueprint('business')"><i class="bi bi-pencil-square me-1"></i> Edit Blueprint</button>
                        </div>
                        <div class="p-3 bg-light rounded border border-light-subtle font-monospace" style="font-size: 0.9rem; white-space: pre-wrap;" id="view-bp-business"></div>
                        <div class="d-none" id="edit-box-business">
                            <textarea id="input-bp-business" class="form-control font-monospace border-light-subtle" rows="12" style="font-size: 0.85rem;"></textarea>
                            <div class="d-flex gap-2 justify-content-end mt-3">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="cancelEditBp('business')">Cancel</button>
                                <button type="button" class="btn btn-sm btn-primary" onclick="saveBlueprint('business')">Save Suggestion</button>
                            </div>
                        </div>
                    </div>

                    <!-- Database Blueprint Tab -->
                    <div class="tab-pane fade" id="database" role="tabpanel" aria-labelledby="database-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-dark mb-0">Database Blueprint</h5>
                            <button type="button" id="edit-btn-database" class="btn btn-sm btn-outline-secondary" onclick="editBlueprint('database')"><i class="bi bi-pencil-square me-1"></i> Edit Blueprint</button>
                        </div>
                        <div class="p-3 bg-light rounded border border-light-subtle font-monospace" style="font-size: 0.9rem; white-space: pre-wrap;" id="view-bp-database"></div>
                        <div class="d-none" id="edit-box-database">
                            <textarea id="input-bp-database" class="form-control font-monospace border-light-subtle" rows="12" style="font-size: 0.85rem;"></textarea>
                            <div class="d-flex gap-2 justify-content-end mt-3">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="cancelEditBp('database')">Cancel</button>
                                <button type="button" class="btn btn-sm btn-primary" onclick="saveBlueprint('database')">Save Suggestion</button>
                            </div>
                        </div>
                    </div>

                    <!-- Technical Blueprint Tab -->
                    <div class="tab-pane fade" id="technical" role="tabpanel" aria-labelledby="technical-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-dark mb-0">Technical Blueprint</h5>
                            <button type="button" id="edit-btn-technical" class="btn btn-sm btn-outline-secondary" onclick="editBlueprint('technical')"><i class="bi bi-pencil-square me-1"></i> Edit Blueprint</button>
                        </div>
                        <div class="p-3 bg-light rounded border border-light-subtle font-monospace" style="font-size: 0.9rem; white-space: pre-wrap;" id="view-bp-technical"></div>
                        <div class="d-none" id="edit-box-technical">
                            <textarea id="input-bp-technical" class="form-control font-monospace border-light-subtle" rows="12" style="font-size: 0.85rem;"></textarea>
                            <div class="d-flex gap-2 justify-content-end mt-3">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="cancelEditBp('technical')">Cancel</button>
                                <button type="button" class="btn btn-sm btn-primary" onclick="saveBlueprint('technical')">Save Suggestion</button>
                            </div>
                        </div>
                    </div>

                    <!-- UI Blueprint Tab -->
                    <div class="tab-pane fade" id="ui" role="tabpanel" aria-labelledby="ui-tab">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-dark mb-0">UI Spec Blueprint</h5>
                            <button type="button" id="edit-btn-ui" class="btn btn-sm btn-outline-secondary" onclick="editBlueprint('ui')"><i class="bi bi-pencil-square me-1"></i> Edit Blueprint</button>
                        </div>
                        <div class="p-3 bg-light rounded border border-light-subtle font-monospace" style="font-size: 0.9rem; white-space: pre-wrap;" id="view-bp-ui"></div>
                        <div class="d-none" id="edit-box-ui">
                            <textarea id="input-bp-ui" class="form-control font-monospace border-light-subtle" rows="12" style="font-size: 0.85rem;"></textarea>
                            <div class="d-flex gap-2 justify-content-end mt-3">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="cancelEditBp('ui')">Cancel</button>
                                <button type="button" class="btn btn-sm btn-primary" onclick="saveBlueprint('ui')">Save Suggestion</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Sidebar Metadata -->
    <div class="col-lg-3">
        <div class="card aitos-card border-light-subtle shadow-sm mb-4">
            <div class="aitos-card-header bg-white">
                <span class="fw-bold"><i class="bi bi-info-circle me-1"></i> Blueprint Registry</span>
            </div>
            <div class="aitos-card-body py-3">
                <div class="mb-3">
                    <small class="text-muted d-block">Status</small>
                    <span class="badge bg-warning text-dark font-monospace mt-1" id="bpStatusText">Draft Pending</span>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Release Date</small>
                    <strong class="text-dark d-block mt-1" id="bpDateText">-</strong>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block">Approved By</small>
                    <strong class="text-dark d-block mt-1">Human Decider</strong>
                </div>
                <hr>
                <div class="alert alert-info py-2 px-3 small border-0 mb-0">
                    <i class="bi bi-lightbulb-fill me-1"></i> Once approved, the version <strong>v1.0.0</strong> becomes locked. Future changes increment draft editions.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex flex-wrap justify-content-between align-items-center border-top pt-4 mb-4 gap-3">
    <a href="/requirements" class="btn btn-outline-secondary px-4 py-2"><i class="bi bi-arrow-left me-1"></i> Back to Analysis</a>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-danger px-4 py-2" data-bs-toggle="modal" data-bs-target="#changeRequestModal">
            <i class="bi bi-x-circle me-1"></i> Request Changes
        </button>
        <button type="button" class="btn btn-success btn-lg px-5 py-3 fw-bold shadow-sm" onclick="approveBlueprint()">
            Approve Blueprint & Lock v1.0.0 <i class="bi bi-check-circle-fill ms-1"></i>
        </button>
    </div>
</div>

<!-- Request Changes Modal -->
<div class="modal fade" id="changeRequestModal" tabindex="-1" aria-labelledby="changeRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="changeRequestModalLabel">Request Architecture Changes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">Briefly outline what architectural changes are required. This suggestion will be recorded in the Decision Log, and the Blueprint draft version will be incremented.</p>
                <div class="mb-3">
                    <label for="changeRequestFeedback" class="form-label fw-semibold">Architectural Feedback</label>
                    <textarea class="form-control border-light-subtle" id="changeRequestFeedback" rows="5" placeholder="e.g. Please optimize database connection pool size, or change API path specifications..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitChangeRequest()">Submit Request</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let draftSuffix = 0; // Simulated minor version increments on edit

    document.addEventListener("DOMContentLoaded", () => {
        loadBlueprintsData();
        updateRegistryInfo();
    });

    function loadBlueprintsData() {
        const state = getProjectState();
        
        const bpTypes = ['business', 'database', 'technical', 'ui'];
        bpTypes.forEach(bp => {
            const content = state.blueprints[bp] || "";
            document.getElementById(`view-bp-${bp}`).innerText = content;
            document.getElementById(`input-bp-${bp}`).value = content;
        });

        // Version badge setup
        document.getElementById("blueprintVersionBadge").innerText = `v${state.blueprints.version || "1.0.0"}-Draft.${draftSuffix}`;
    }

    function updateRegistryInfo() {
        const state = getProjectState();
        document.getElementById("bpDateText").innerText = new Date().toLocaleDateString(undefined, {
            year: 'numeric', month: 'long', day: 'numeric'
        });
        
        if (state.blueprintApproved) {
            document.getElementById("bpStatusText").className = "badge bg-success font-monospace mt-1";
            document.getElementById("bpStatusText").innerText = "Locked & Approved";
            document.getElementById("blueprintVersionBadge").innerText = `v${state.blueprints.version || "1.0.0"} (Locked)`;
        } else {
            document.getElementById("bpStatusText").className = "badge bg-warning text-dark font-monospace mt-1";
            document.getElementById("bpStatusText").innerText = "Draft Pending";
        }
    }

    function editBlueprint(bp) {
        const viewEl = document.getElementById(`view-bp-${bp}`);
        const editEl = document.getElementById(`edit-box-${bp}`);
        const btnEdit = document.getElementById(`edit-btn-${bp}`);

        viewEl.classList.add("d-none");
        editEl.classList.remove("d-none");
        btnEdit.classList.add("d-none");
        
        document.getElementById(`input-bp-${bp}`).focus();
    }

    function cancelEditBp(bp) {
        const viewEl = document.getElementById(`view-bp-${bp}`);
        const editEl = document.getElementById(`edit-box-${bp}`);
        const btnEdit = document.getElementById(`edit-btn-${bp}`);
        const textInput = document.getElementById(`input-bp-${bp}`);

        textInput.value = viewEl.innerText;

        viewEl.classList.remove("d-none");
        editEl.classList.add("d-none");
        btnEdit.classList.remove("d-none");
    }

    function saveBlueprint(bp) {
        const viewEl = document.getElementById(`view-bp-${bp}`);
        const editEl = document.getElementById(`edit-box-${bp}`);
        const btnEdit = document.getElementById(`edit-btn-${bp}`);
        const textInput = document.getElementById(`input-bp-${bp}`);

        const textVal = textInput.value.trim();
        viewEl.innerText = textVal;

        // Save in state
        const state = getProjectState();
        state.blueprints[bp] = textVal;
        
        // Increment minor version since "blueprint modification automatically creates a Decision entry and edits immutable files"
        draftSuffix++;
        state.blueprints.version = "1.0.0";
        
        state.decisions.push({
            date: new Date().toISOString().slice(0, 16).replace('T', ' '),
            title: `Blueprint Revised: ${bp.toUpperCase()}`,
            desc: `Modified ${bp} blueprint markdown sheet. Incrementing draft signature to v1.0.0-Draft.${draftSuffix}.`
        });

        saveProjectState(state);
        loadBlueprintsData();

        viewEl.classList.remove("d-none");
        editEl.classList.add("d-none");
        btnEdit.classList.remove("d-none");
    }

    function submitChangeRequest() {
        const feedback = document.getElementById("changeRequestFeedback").value.trim();
        if (!feedback) {
            alert("Feedback description cannot be empty.");
            return;
        }

        const state = getProjectState();
        draftSuffix++;
        
        state.decisions.push({
            date: new Date().toISOString().slice(0, 16).replace('T', ' '),
            title: "Architecture Change Requested",
            desc: `Feedback: "${feedback}". Incrementing draft identifier to v1.0.0-Draft.${draftSuffix}.`
        });

        saveProjectState(state);
        
        // Hide modal
        const modalEl = document.getElementById("changeRequestModal");
        const modal = bootstrap.Modal.getInstance(modalEl);
        modal.hide();

        document.getElementById("changeRequestFeedback").value = "";
        loadBlueprintsData();
        
        alert("Feedback submitted successfully. The draft blueprint version has been updated.");
    }

    function approveBlueprint() {
        const state = getProjectState();
        state.blueprintApproved = true;
        state.blueprints.status = "Locked";
        state.blueprints.version = "1.0.0";
        
        // Reset subsequent progress to enforce workflow sequence
        state.teamAssigned = false;
        state.contextCompiled = false;

        state.decisions.push({
            date: new Date().toISOString().slice(0, 16).replace('T', ' '),
            title: "Blueprint Approved",
            desc: "Architecture blueprints fully locked and approved (Version 1.0.0). Unlocking Team Planning phase."
        });

        saveProjectState(state);
        updateRegistryInfo();
        
        setTimeout(() => {
            window.location.href = "/team";
        }, 800);
    }
</script>
@endsection
