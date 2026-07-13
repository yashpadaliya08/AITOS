@extends('layouts.app')

@section('title', 'AITOS - Create Project Wizard')

@section('content')
<div class="card aitos-card border-0 shadow-sm mb-4">
    <div class="aitos-card-header bg-white d-flex justify-content-between align-items-center py-4">
        <div>
            <h4 class="fw-bold mb-1">Create Project Wizard</h4>
            <p class="text-muted small mb-0">Bootstrap your structured repository workspace context.</p>
        </div>
        <span class="badge bg-primary px-3 py-2">Version 2.0</span>
    </div>
    
    <div class="aitos-card-body p-4">
        <!-- Steps Indicator -->
        <div class="wizard-steps px-lg-5">
            <div class="wizard-progress-bar" id="wizardProgressBar"></div>
            <div class="wizard-step active" id="step-ind-1">
                <div class="step-number">1</div>
                <div class="step-label">Details</div>
            </div>
            <div class="wizard-step" id="step-ind-2">
                <div class="step-number">2</div>
                <div class="step-label">Statement</div>
            </div>
            <div class="wizard-step" id="step-ind-3">
                <div class="step-number">3</div>
                <div class="step-label">Team Planning</div>
            </div>
            <div class="wizard-step" id="step-ind-4">
                <div class="step-number">4</div>
                <div class="step-label">Tech Stack</div>
            </div>
            <div class="wizard-step" id="step-ind-5">
                <div class="step-number">5</div>
                <div class="step-label">Review</div>
            </div>
        </div>

        <form id="wizardForm" onsubmit="event.preventDefault();">
            <!-- PANE 1: Project Details -->
            <div class="wizard-pane active" id="pane-1">
                <h5 class="fw-bold mb-4"><i class="bi bi-info-circle text-primary me-2"></i> Step 1: Project Details</h5>
                
                <div class="mb-3">
                    <label for="projectNameInput" class="form-label fw-semibold">Project Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control form-control-lg border-light-subtle" id="projectNameInput" placeholder="e.g. AITOS Platform, SwiftRide" required>
                    <div class="form-text">Keep it short and descriptive. Used as the folder root directory.</div>
                </div>

                <div class="mb-3">
                    <label for="projectDescInput" class="form-label fw-semibold">Short Description <span class="text-danger">*</span></label>
                    <textarea class="form-control border-light-subtle" id="projectDescInput" rows="3" placeholder="Explain the project in 1-2 sentences..." required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold d-block">Project Goal / Environment</label>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="card p-3 border-light-subtle h-100 cursor-pointer d-flex flex-row align-items-center gap-3">
                                <input type="radio" name="projectGoal" value="Hackathon" checked class="form-check-input flex-shrink-0">
                                <div>
                                    <strong class="d-block text-dark">Hackathon</strong>
                                    <small class="text-muted">Fast layout, high AI velocity, 48-hour scope.</small>
                                </div>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label class="card p-3 border-light-subtle h-100 cursor-pointer d-flex flex-row align-items-center gap-3">
                                <input type="radio" name="projectGoal" value="Learning" class="form-check-input flex-shrink-0">
                                <div>
                                    <strong class="d-block text-dark">Learning</strong>
                                    <small class="text-muted">Strict syntax annotations, tutorials focus.</small>
                                </div>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label class="card p-3 border-light-subtle h-100 cursor-pointer d-flex flex-row align-items-center gap-3">
                                <input type="radio" name="projectGoal" value="Production" class="form-check-input flex-shrink-0">
                                <div>
                                    <strong class="d-block text-dark">Production</strong>
                                    <small class="text-muted">Rigorous testing, immutable blueprints.</small>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PANE 2: Problem Statement -->
            <div class="wizard-pane" id="pane-2">
                <h5 class="fw-bold mb-3"><i class="bi bi-file-earmark-text text-primary me-2"></i> Step 2: Problem Statement</h5>
                <p class="text-muted">Paste your full hackathon prompt, client design briefs, user requirements, or rough descriptions. AITOS parses this text block to extract structural boundaries.</p>
                
                <div class="mb-3">
                    <label for="problemStatementInput" class="form-label fw-semibold">Problem Statement / Raw Input <span class="text-danger">*</span></label>
                    <textarea class="form-control border-light-subtle font-monospace" id="problemStatementInput" rows="12" placeholder="Paste problem requirements here..." style="font-size: 0.9rem;" required></textarea>
                </div>
            </div>

            <!-- PANE 3: Team Members -->
            <div class="wizard-pane" id="pane-3">
                <h5 class="fw-bold mb-3"><i class="bi bi-people text-primary me-2"></i> Step 3: Team Members</h5>
                <p class="text-muted">Add developer credentials and active AI coding partners. AITOS structures assignment cards mapping technical scopes to team profiles.</p>

                <!-- Input form for member -->
                <div class="card bg-light border-0 p-3 mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" id="tempMemberName" class="form-control border-light-subtle" placeholder="Full Name">
                        </div>
                        <div class="col-md-3">
                            <input type="text" id="tempMemberRole" class="form-control border-light-subtle" placeholder="Role (e.g. Frontend Lead)">
                        </div>
                        <div class="col-md-3">
                            <input type="text" id="tempMemberGithub" class="form-control border-light-subtle" placeholder="GitHub Username">
                        </div>
                        <div class="col-md-2">
                            <select id="tempMemberAI" class="form-select border-light-subtle">
                                <option value="Cursor">Cursor</option>
                                <option value="Antigravity">Antigravity</option>
                                <option value="Claude">Claude</option>
                                <option value="Gemini">Gemini</option>
                                <option value="Copilot">Copilot</option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="button" onclick="addWizardMember()" class="btn btn-primary w-100"><i class="bi bi-plus-lg"></i></button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover border">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                                <th>GitHub</th>
                                <th>Preferred AI</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody id="wizardTeamTableBody">
                            <!-- Injected by script -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- PANE 4: Tech Stack -->
            <div class="wizard-pane" id="pane-4">
                <h5 class="fw-bold mb-4"><i class="bi bi-layers text-primary me-2"></i> Step 4: Technology Selection</h5>
                
                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="techFramework" class="form-label fw-semibold">Backend Framework</label>
                        <select id="techFramework" class="form-select border-light-subtle">
                            <option value="Laravel 11 (Recommended)">Laravel 11 (Recommended)</option>
                            <option value="Next.js App Router">Next.js App Router</option>
                            <option value="Django Python">Django Python</option>
                            <option value="Express Node.js">Express Node.js</option>
                            <option value="FastAPI Python">FastAPI Python</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="techDatabase" class="form-label fw-semibold">Database Schema</label>
                        <select id="techDatabase" class="form-select border-light-subtle">
                            <option value="SQLite (File-based local)">SQLite (File-based local)</option>
                            <option value="PostgreSQL">PostgreSQL</option>
                            <option value="MySQL">MySQL</option>
                            <option value="MongoDB (NoSQL)">MongoDB (NoSQL)</option>
                            <option value="None / Static JSON">None / Static JSON</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="techFrontend" class="form-label fw-semibold">Frontend Engine</label>
                        <select id="techFrontend" class="form-select border-light-subtle">
                            <option value="Blade Templates + Bootstrap 5">Blade Templates + Bootstrap 5</option>
                            <option value="React.js + TailwindCSS">React.js + TailwindCSS</option>
                            <option value="Vue.js + TailwindCSS">Vue.js + TailwindCSS</option>
                            <option value="HTML5 + Vanilla CSS">HTML5 + Vanilla CSS</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold d-block">Authorized AI Editors</label>
                        <div class="d-flex flex-wrap gap-3 mt-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="aiEditors" value="Cursor" id="aiEd1" checked>
                                <label class="form-check-label" for="aiEd1">Cursor</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="aiEditors" value="Antigravity" id="aiEd2" checked>
                                <label class="form-check-label" for="aiEd2">Antigravity</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="aiEditors" value="Claude Code" id="aiEd3" checked>
                                <label class="form-check-label" for="aiEd3">Claude Code</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="aiEditors" value="Gemini Code Assist" id="aiEd4">
                                <label class="form-check-label" for="aiEd4">Gemini Code Assist</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PANE 5: Review -->
            <div class="wizard-pane" id="pane-5">
                <h5 class="fw-bold mb-3"><i class="bi bi-file-earmark-check text-primary me-2"></i> Step 5: Summary & Review</h5>
                <p class="text-muted">Verify project properties. Completing the wizard compiles these parameters into draft requirements cards.</p>
                
                <div class="row g-4 mt-2">
                    <div class="col-lg-6">
                        <h6 class="fw-bold border-bottom pb-2">Project Properties</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="fw-semibold text-muted" style="width: 150px;">Project Name:</td>
                                <td id="summaryName">-</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted">Goal / Target:</td>
                                <td id="summaryGoal">-</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted">Framework:</td>
                                <td id="summaryFramework">-</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted">Database:</td>
                                <td id="summaryDatabase">-</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted">Frontend Layout:</td>
                                <td id="summaryFrontend">-</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted">AI Editors:</td>
                                <td id="summaryEditors">-</td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-lg-6">
                        <h6 class="fw-bold border-bottom pb-2">Development Team (Humans + AIs)</h6>
                        <ul class="list-group list-group-flush" id="summaryTeamList">
                            <!-- Injected by script -->
                        </ul>
                    </div>

                    <div class="col-12">
                        <div class="p-3 bg-light border rounded">
                            <strong class="d-block mb-1">Problem Statement Snippet:</strong>
                            <p class="text-muted font-monospace small mb-0 text-truncate" id="summaryProblemSnippet">-</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Wizard Nav Buttons -->
            <div class="d-flex justify-content-between mt-5 pt-3 border-top">
                <button type="button" id="wizardBackBtn" class="btn btn-outline-secondary px-4 py-2" onclick="wizardGoBack()" disabled>
                    <i class="bi bi-arrow-left me-1"></i> Back
                </button>
                <button type="button" id="wizardNextBtn" class="btn btn-primary px-4 py-2" onclick="wizardGoNext()">
                    Next <i class="bi bi-arrow-right ms-1"></i>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentStep = 1;
    const totalSteps = 5;
    let localTeam = [];

    document.addEventListener("DOMContentLoaded", () => {
        // Load existing state if wizard has data
        const state = getProjectState();
        document.getElementById("projectNameInput").value = state.projectName || "";
        document.getElementById("projectDescInput").value = state.projectDescription || "";
        document.getElementById("problemStatementInput").value = state.problemStatement || "";
        
        // Load goal radio value
        if (state.projectGoal) {
            const radios = document.getElementsByName("projectGoal");
            for (let r of radios) {
                if (r.value === state.projectGoal) r.checked = true;
            }
        }

        // Tech stack loader
        if (state.techStack) {
            document.getElementById("techFramework").value = state.techStack.framework || "Laravel 11 (Recommended)";
            document.getElementById("techDatabase").value = state.techStack.database || "SQLite (File-based local)";
            document.getElementById("techFrontend").value = state.techStack.frontend || "Blade Templates + Bootstrap 5";
            
            // AI checkbox values
            if (state.techStack.editors) {
                const checkBoxes = document.getElementsByName("aiEditors");
                checkBoxes.forEach(cb => {
                    cb.checked = state.techStack.editors.includes(cb.value);
                });
            }
        }

        // Team loader
        localTeam = state.teamMembers || [];
        renderTeamTable();
        updateStepsUI();
    });

    function renderTeamTable() {
        const tbody = document.getElementById("wizardTeamTableBody");
        tbody.innerHTML = "";
        localTeam.forEach((member, index) => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td><strong class="text-dark">${escapeHtml(member.name)}</strong></td>
                <td>${escapeHtml(member.role)}</td>
                <td><code class="text-secondary">@${escapeHtml(member.github)}</code></td>
                <td><span class="badge bg-light text-primary border">${escapeHtml(member.ai)}</span></td>
                <td class="text-end">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeWizardMember(${index})">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function addWizardMember() {
        const nameInput = document.getElementById("tempMemberName");
        const roleInput = document.getElementById("tempMemberRole");
        const githubInput = document.getElementById("tempMemberGithub");
        const aiSelect = document.getElementById("tempMemberAI");

        if (!nameInput.value || !roleInput.value || !githubInput.value) {
            alert("Please fill in all team member fields.");
            return;
        }

        localTeam.push({
            name: nameInput.value.trim(),
            role: roleInput.value.trim(),
            github: githubInput.value.trim(),
            ai: aiSelect.value
        });

        // Reset inputs
        nameInput.value = "";
        roleInput.value = "";
        githubInput.value = "";
        aiSelect.value = "Cursor";

        renderTeamTable();
    }

    function removeWizardMember(index) {
        localTeam.splice(index, 1);
        renderTeamTable();
    }

    function updateStepsUI() {
        // Hide all panes
        for (let i = 1; i <= totalSteps; i++) {
            document.getElementById(`pane-${i}`).classList.remove("active");
            document.getElementById(`step-ind-${i}`).classList.remove("active");
            document.getElementById(`step-ind-${i}`).classList.remove("completed");
            if (i < currentStep) {
                document.getElementById(`step-ind-${i}`).classList.add("completed");
            }
        }

        // Show active pane
        document.getElementById(`pane-${currentStep}`).classList.add("active");
        document.getElementById(`step-ind-${currentStep}`).classList.add("active");

        // Progress bar percentage
        const progressPct = ((currentStep - 1) / (totalSteps - 1)) * 100;
        document.getElementById("wizardProgressBar").style.width = `${progressPct}%`;

        // Buttons settings
        const backBtn = document.getElementById("wizardBackBtn");
        const nextBtn = document.getElementById("wizardNextBtn");

        backBtn.disabled = currentStep === 1;

        if (currentStep === totalSteps) {
            nextBtn.innerHTML = `Finalize Project <i class="bi bi-check-circle-fill ms-1"></i>`;
            nextBtn.classList.remove("btn-primary");
            nextBtn.classList.add("btn-success");
            compileReviewSummary();
        } else {
            nextBtn.innerHTML = `Next <i class="bi bi-arrow-right ms-1"></i>`;
            nextBtn.classList.remove("btn-success");
            nextBtn.classList.add("btn-primary");
        }
    }

    function wizardGoBack() {
        if (currentStep > 1) {
            currentStep--;
            updateStepsUI();
        }
    }

    function wizardGoNext() {
        // Validate inputs before moving
        if (currentStep === 1) {
            const name = document.getElementById("projectNameInput").value.trim();
            const desc = document.getElementById("projectDescInput").value.trim();
            if (!name || !desc) {
                document.getElementById("wizardForm").reportValidity();
                return;
            }
        } else if (currentStep === 2) {
            const statement = document.getElementById("problemStatementInput").value.trim();
            if (!statement) {
                document.getElementById("wizardForm").reportValidity();
                return;
            }
        }

        if (currentStep < totalSteps) {
            currentStep++;
            updateStepsUI();
        } else {
            // Save state and finalize
            finalizeProject();
        }
    }

    function compileReviewSummary() {
        // Name and details
        document.getElementById("summaryName").innerText = document.getElementById("projectNameInput").value.trim();
        
        const goalRadios = document.getElementsByName("projectGoal");
        let goalVal = "Hackathon";
        for (let r of goalRadios) {
            if (r.checked) goalVal = r.value;
        }
        document.getElementById("summaryGoal").innerText = goalVal;

        // Tech stack
        document.getElementById("summaryFramework").innerText = document.getElementById("techFramework").value;
        document.getElementById("summaryDatabase").innerText = document.getElementById("techDatabase").value;
        document.getElementById("summaryFrontend").innerText = document.getElementById("techFrontend").value;

        // AI editors selection
        const checkBoxes = document.getElementsByName("aiEditors");
        let selectedAIs = [];
        checkBoxes.forEach(cb => {
            if (cb.checked) selectedAIs.push(cb.value);
        });
        document.getElementById("summaryEditors").innerText = selectedAIs.length > 0 ? selectedAIs.join(", ") : "None";

        // Problem statement snippet
        const statement = document.getElementById("problemStatementInput").value.trim();
        document.getElementById("summaryProblemSnippet").innerText = statement;

        // Team list
        const teamListEl = document.getElementById("summaryTeamList");
        teamListEl.innerHTML = "";
        if (localTeam.length === 0) {
            teamListEl.innerHTML = `<li class="list-group-item text-muted">No team members added. Add members in Step 3.</li>`;
        } else {
            localTeam.forEach(member => {
                const li = document.createElement("li");
                li.className = "list-group-item d-flex justify-content-between align-items-center px-0";
                li.innerHTML = `
                    <div>
                        <strong class="text-dark">${escapeHtml(member.name)}</strong>
                        <span class="text-muted d-block small">${escapeHtml(member.role)} (@${escapeHtml(member.github)})</span>
                    </div>
                    <span class="badge bg-light text-primary border">${escapeHtml(member.ai)}</span>
                `;
                teamListEl.appendChild(li);
            });
        }
    }

    function finalizeProject() {
        const state = getProjectState();
        
        // Bind step inputs to state
        state.projectName = document.getElementById("projectNameInput").value.trim();
        state.projectDescription = document.getElementById("projectDescInput").value.trim();
        
        const goalRadios = document.getElementsByName("projectGoal");
        for (let r of goalRadios) {
            if (r.checked) state.projectGoal = r.value;
        }
        
        state.problemStatement = document.getElementById("problemStatementInput").value.trim();
        state.teamMembers = localTeam;
        
        // AI checkboxes
        const checkBoxes = document.getElementsByName("aiEditors");
        let selectedAIs = [];
        checkBoxes.forEach(cb => {
            if (cb.checked) selectedAIs.push(cb.value);
        });
        
        state.techStack = {
            framework: document.getElementById("techFramework").value,
            database: document.getElementById("techDatabase").value,
            frontend: document.getElementById("techFrontend").value,
            editors: selectedAIs
        };

        // Mark Wizard complete, unlock requirements
        state.wizardCompleted = true;
        
        // Clear subsequent approvals to enforce flow if editing existing project
        state.requirementsApproved = false;
        state.blueprintApproved = false;
        state.teamAssigned = false;
        state.contextCompiled = false;

        // Automatically update blueprint descriptions with user selected frameworks
        state.blueprints.business = `### Business Domain Map\n1. Target Audience: AI-assisted development teams working on **${state.projectName}**.\n2. Key Value Proposition: Eliminate context drift, standardize entity schemas, and synchronize multiple autonomous coding agents.\n3. Workflow Rules: Design phase precedes implementation. Decider approves change requests before coding.`;
        
        state.blueprints.database = `### Database Schema Outline\n- Framework database option: **${state.techStack.database}**\n- **projects**: id (uuid), name, goal, config (json)\n- **blueprints**: id (uuid), project_id, version, type, content (text), approved_by, approved_at\n- **team_members**: id (uuid), project_id, name, github_username, preferred_ai, role\n- **tasks**: id (uuid), member_id, description, status (pending/completed)`;
        
        state.blueprints.technical = `### Technical Blueprint & Architecture\n- Core Framework: **${state.techStack.framework}**\n- Frontend Layout: **${state.techStack.frontend}**\n- State Persistence: File-system JSON repository (.aitos/data)\n- Build Pipeline: Webhook-free file watchers checking current context`;
        
        state.blueprints.ui = `### User Interface Specifications\n- Grid Layout: Responsive container (sidebar navigation + flexible main content) matching **${state.techStack.frontend}** standard layout.\n- Palette: Professional light theme (#ffffff and #f8f9fa) with corporate blue (#0d6efd) accent colors\n- Components: Rounded Bootstrap cards, interactive progress steps, terminal viewports, VS Code file trees`;

        // Update decisions history
        state.decisions.push({
            date: new Date().toISOString().slice(0, 16).replace('T', ' '),
            title: `Wizard Finished (${state.projectName})`,
            desc: `Project created with Goal: ${state.projectGoal}, Stack: ${state.techStack.framework}/${state.techStack.database}.`
        });

        // Seed task list based on actual team members
        if (state.teamMembers.length > 0) {
            state.tasks = [];
            // Assign some dummy tasks to first three members
            const taskTitles = [
                "Create layout files & navigation views",
                "Configure local storage state controller",
                "Design ZIP package assembly controller",
                "Write core markdown compiler schemas",
                "Set up database schema models",
                "Optimize query indices for file search"
            ];
            taskTitles.forEach((t, i) => {
                const member = state.teamMembers[i % state.teamMembers.length];
                state.tasks.push({
                    id: `task-${i + 1}`,
                    text: t,
                    column: member.name.toLowerCase().split(' ')[0] // e.g. alex, sarah, dave
                });
            });
        }

        saveProjectState(state);
        window.location.href = "/requirements";
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
