<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AITOS - AI Team Operating System')</title>
    
    <!-- Meta tags for SEO -->
    <meta name="description" content="AITOS - AI-first project planning and context management platform for hackathons and AI-assisted software teams. Keep your AI developers in sync.">
    <meta name="author" content="AITOS V2">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom Style -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    @yield('styles')
</head>
<body>

    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Toast Notification Container -->
    <div class="aitos-toast-container" id="toastContainer"></div>

    <div class="app-wrapper">
        <!-- Sidebar Navigation -->
        @include('layouts.sidebar')

        <!-- Main Content Wrapper -->
        <div class="main-container">
            <!-- Top Header -->
            @include('layouts.header')

            <!-- Main Content Grid -->
            <main class="flex-grow-1 position-relative">
                <!-- Lock Screen Overlay (handled by JS state check) -->
                <div id="phaseLockOverlay" class="position-absolute top-0 start-0 w-100 h-100 d-none justify-content-center align-items-center" style="background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); z-index: 1000; min-height: 400px; transition: var(--transition);">
                    <div class="text-center p-5 rounded-4 bg-white border border-light-subtle shadow-lg" style="max-width: 500px; animation: modalZoom 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);">
                        <div class="mb-4">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-danger-subtle text-danger" style="width: 76px; height: 76px; font-size: 2.25rem;">
                                <i class="bi bi-lock-fill"></i>
                            </span>
                        </div>
                        <h4 class="fw-bold mb-2">Phase is Locked</h4>
                        <p class="text-muted mb-4" id="phaseLockMessage">Please complete the required previous workflow stages before accessing this component.</p>
                        <a href="#" id="phaseLockActionBtn" class="btn btn-primary px-4 py-2.5 fw-semibold shadow-sm">
                            <i class="bi bi-arrow-right-circle me-1"></i> Go to Current Stage
                        </a>
                    </div>
                </div>

                <!-- Actual View Content -->
                <div class="page-content">
                    @yield('content')
                </div>
            </main>

            <!-- Bottom Footer -->
            @include('layouts.footer')
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Global Application State Control Script -->
    <script>
        // Default template data for a new project
        const DEFAULT_PROJECT_STATE = {
            wizardCompleted: false,
            requirementsApproved: false,
            blueprintApproved: false,
            teamAssigned: false,
            contextCompiled: false,
            
            // AI Configuration & Cache Hash
            apiKeys: {
                gemini: "",
                openai: "",
                anthropic: "",
                defaultProvider: "openai",
                openaiModel: "nvidia/nemotron-3-ultra-550b-a55b:free",
                geminiModel: "gemini-3.5-flash",
                anthropicModel: "claude-3-haiku-20240307"
            },
            analysisHash: "",
            
            // Step 1: Details
            projectName: "",
            projectDescription: "",
            projectGoal: "Hackathon",
            
            // Step 2: Statement
            problemStatement: "",
            
            // Step 3: Team
            teamMembers: [
                { name: "Alex Chen", role: "Frontend Lead", github: "alexchen-dev", ai: "Cursor" },
                { name: "Sarah Connor", role: "Backend Architect", github: "sconnor-codes", ai: "Antigravity" },
                { name: "Dave Miller", role: "Database Engineer", github: "davemiller", ai: "Claude" }
            ],
            
            // Step 4: Tech Stack
            techStack: {
                framework: "Laravel 11",
                database: "SQLite",
                frontend: "Blade + Bootstrap 5",
                editors: ["Cursor", "Antigravity"]
            },

            // Requirements cards
            requirements: {
                entities: "Project: id, name, goal, config\nBlueprint: id, project_id, version, content\nTeamMember: id, name, github_username, preferred_ai, role\nTask: id, description, status",
                relationships: "Blueprint belongs_to Project\nTeamMember belongs_to Project\nTask belongs_to TeamMember",
                modules: "• Wizard Manager: Handles multi-step form collection\n• Requirements Synthesizer: Suggests editable modules from prompt\n• Versioned Blueprint Engine: Maintains immutable structural blueprint logs\n• Context Compiler: Compiles details to markdown\n• Export Package Packager: Packages zip files",
                roles: "• Product Owner / Decider (Human Developer)\n• Build Partner (AI Coding Assistants: Cursor, Claude, Antigravity, etc.)\n• AITOS Supervisor: Context maintainer and compiler",
                businessRules: "• Humans decide and approve, AI suggests and builds\n• Approved blueprints are fully immutable; edits spawn a new version\n• Every blueprint revision automatically generates a Decision history record\n• Compilation outputs cannot be modified manually",
                requirements: "• Generate structured project package containing .aitos directory\n• Maintain editable cards for requirements categorization\n• Provide tabbed interfaces for four types of architecture blueprints\n• Enable task drag-and-drop workflow simulation\n• Build file-explorer preview system for code validation",
                assumptions: "• Development occurs in local, git-managed environment\n• All AI agents reading the repository package support markdown-based instructions\n• Developers will perform synchronization using Git/GitHub",
                risks: "• Context drift if developers fail to update blueprints\n• Multi-agent race conditions if two AI assistants edit the same entity without committing"
            },

            // Blueprint Versioning
            blueprints: {
                version: "1.0.0",
                status: "Pending",
                aiGenerated: false,
                business: "### Business Domain Map\n1. Target Audience: AI-assisted development teams.\n2. Key Value Proposition: Eliminate context drift, standardize entity schemas, and synchronize multiple autonomous coding agents.\n3. Workflow Rules: Design phase precedes implementation. Decider approves change requests before coding.",
                database: "### Database Schema Outline\n- **projects**: id (uuid), name, goal, config (json)\n- **blueprints**: id (uuid), project_id, version, type, content (text), approved_by, approved_at\n- **team_members**: id (uuid), project_id, name, github_username, preferred_ai, role\n- **tasks**: id (uuid), member_id, description, status (pending/completed)",
                technical: "### Technical Blueprint & Architecture\n- Framework: Laravel 11.x (PHP 8.2+)\n- Frontend: Blade Template Engine + Bootstrap 5.3 CSS\n- State Persistence: File-system JSON repository (.aitos/data)\n- Build Pipeline: Webhook-free file watchers checking current context",
                ui: "### User Interface Specifications\n- Grid Layout: Responsive container (sidebar navigation + flexible main content)\n- Palette: Professional light theme (#ffffff and #f8f9fa) with corporate blue (#0d6efd) accent colors\n- Components: Rounded Bootstrap cards, interactive progress steps, terminal viewports, VS Code file trees"
            },

            // Tasks list
            tasks: [
                { id: "task-1", text: "Create layout files & navigation views", column: "alex" },
                { id: "task-2", text: "Configure local storage state controller", column: "alex" },
                { id: "task-3", text: "Design ZIP package assembly controller", column: "sarah" },
                { id: "task-4", text: "Write core markdown compiler schemas", column: "sarah" },
                { id: "task-5", text: "Set up SQLite tables (future DB prep)", column: "dave" },
                { id: "task-6", text: "Optimize query indices for file search", column: "dave" }
            ],

            // Decisions History
            decisions: [
                { date: "2026-07-10 14:02", title: "Project Initialized", desc: "Bootstrapped project layout matching V2 specification." }
            ]
        };

        // Initialize state or load from local storage
        function getProjectState() {
            let stateStr = localStorage.getItem("aitos_project_state");
            if (!stateStr) {
                localStorage.setItem("aitos_project_state", JSON.stringify(DEFAULT_PROJECT_STATE));
                return JSON.parse(JSON.stringify(DEFAULT_PROJECT_STATE));
            }
            try {
                let state = JSON.parse(stateStr);
                // Ensure apiKeys exists
                if (!state.apiKeys) {
                    state.apiKeys = JSON.parse(JSON.stringify(DEFAULT_PROJECT_STATE.apiKeys));
                }
                // Enforce OpenRouter Nemotron 3 Ultra free
                state.apiKeys.defaultProvider = "openai";
                state.apiKeys.openaiModel = "nvidia/nemotron-3-ultra-550b-a55b:free";
                localStorage.setItem("aitos_project_state", JSON.stringify(state));
                return state;
            } catch (e) {
                localStorage.setItem("aitos_project_state", JSON.stringify(DEFAULT_PROJECT_STATE));
                return JSON.parse(JSON.stringify(DEFAULT_PROJECT_STATE));
            }
        }

        /**
         * Always returns the correct model for the given provider.
         */
        function getModelForProvider(provider, state) {
            return "nvidia/nemotron-3-ultra-550b-a55b:free";
        }

        // Save current project state
        function saveProjectState(state) {
            localStorage.setItem("aitos_project_state", JSON.stringify(state));
            updateLayoutFromState();
        }

        // Reset to initial settings
        function resetProjectState() {
            localStorage.setItem("aitos_project_state", JSON.stringify(DEFAULT_PROJECT_STATE));
            updateLayoutFromState();
        }

        // Update top header and sidebar based on state
        function updateLayoutFromState() {
            const state = getProjectState();
            
            // 1. Update project title and information in headers
            const headerProjName = document.querySelectorAll(".header-project-name-text");
            const headerProjGoal = document.querySelectorAll(".header-project-goal-text");
            const headerProjPhase = document.querySelectorAll(".header-project-phase-text");
            const headerProgressBar = document.querySelector(".header-progress-bar-el");
            const headerProgressText = document.querySelector(".header-progress-text-el");

            let displayName = state.projectName || "AITOS (No Project)";
            headerProjName.forEach(el => el.innerText = displayName);
            
            let displayGoal = state.projectGoal ? `(${state.projectGoal})` : "";
            headerProjGoal.forEach(el => el.innerText = displayGoal);

            // Determine Phase & Progress Value
            let phase = "Landing Page";
            let progressPct = 0;
            if (state.wizardCompleted) {
                phase = "Requirement Analysis";
                progressPct = 20;
            }
            if (state.requirementsApproved) {
                phase = "Blueprint Review";
                progressPct = 40;
            }
            if (state.blueprintApproved) {
                phase = "Team Planning";
                progressPct = 60;
            }
            if (state.teamAssigned) {
                phase = "Context Compiler";
                progressPct = 80;
            }
            if (state.contextCompiled) {
                phase = "Export Center";
                progressPct = 100;
            }

            headerProjPhase.forEach(el => el.innerText = phase);
            if (headerProgressBar) {
                headerProgressBar.style.width = `${progressPct}%`;
                headerProgressBar.setAttribute("aria-valuenow", progressPct);
            }
            if (headerProgressText) {
                headerProgressText.innerText = `${progressPct}% Complete`;
            }

            // 2. Lock / Unlock sidebar links based on phase requirements
            // Users cannot skip ahead. Enforce steps sequentially.
            // Dashboard & Settings & Landing are always accessible.
            const navRequirements = document.getElementById("nav-requirements");
            const navBlueprint = document.getElementById("nav-blueprint");
            const navTeam = document.getElementById("nav-team");
            const navCompiler = document.getElementById("nav-compiler");
            const navExport = document.getElementById("nav-export");

            // Requirements requires Wizard
            toggleNavLink(navRequirements, state.wizardCompleted);
            // Blueprint requires Requirements approved
            toggleNavLink(navBlueprint, state.requirementsApproved);
            // Team requires Blueprint approved
            toggleNavLink(navTeam, state.blueprintApproved);
            // Compiler requires Team assigned
            toggleNavLink(navCompiler, state.teamAssigned);
            // Export / Preview requires Context compiled
            toggleNavLink(navExport, state.contextCompiled);

            // Determine if the current page should be locked
            checkPageAccess(state, phase);
        }

        function toggleNavLink(el, enabled) {
            if (!el) return;
            if (enabled) {
                el.classList.remove("disabled");
                el.querySelector("i").classList.remove("bi-lock-fill");
                el.querySelector("i").classList.add(el.dataset.icon);
                el.setAttribute("title", "");
            } else {
                el.classList.add("disabled");
                el.querySelector("i").classList.remove(el.dataset.icon);
                el.querySelector("i").classList.add("bi-lock-fill");
                el.setAttribute("title", "Complete previous steps to unlock");
            }
        }

        function checkPageAccess(state, currentPhase) {
            const path = window.location.pathname;
            const overlay = document.getElementById("phaseLockOverlay");
            const overlayMessage = document.getElementById("phaseLockMessage");
            const overlayActionBtn = document.getElementById("phaseLockActionBtn");

            if (!overlay) return;

            let isLocked = false;
            let lockedMsg = "";
            let redirectUrl = "/";

            if (path.includes("/requirements") && !state.wizardCompleted) {
                isLocked = true;
                lockedMsg = "You must complete the Create Project Wizard before analyzing requirements.";
                redirectUrl = "/wizard";
            } else if (path.includes("/blueprint") && !state.requirementsApproved) {
                isLocked = true;
                lockedMsg = "Requirements must be reviewed and approved before starting Blueprint reviews.";
                redirectUrl = "/requirements";
            } else if (path.includes("/team") && !state.blueprintApproved) {
                isLocked = true;
                lockedMsg = "The Blueprints must be approved and locked before assigning modules and tasks to the team.";
                redirectUrl = "/blueprint";
            } else if (path.includes("/compiler") && !state.teamAssigned) {
                isLocked = true;
                lockedMsg = "Team Planning must be confirmed before running the Context Compiler.";
                redirectUrl = "/team";
            } else if (path.includes("/export") && !state.contextCompiled) {
                isLocked = true;
                lockedMsg = "You must compile the project context to generate the repository package before downloading it.";
                redirectUrl = "/compiler";
            } else if (path.includes("/preview") && !state.contextCompiled) {
                isLocked = true;
                lockedMsg = "You must compile the project context to generate the repository preview.";
                redirectUrl = "/compiler";
            }

            if (isLocked) {
                overlay.classList.remove("d-none");
                overlay.classList.add("d-flex");
                overlayMessage.innerText = lockedMsg;
                overlayActionBtn.href = redirectUrl;
                overlayActionBtn.innerText = "Navigate to Required Step";
            } else {
                overlay.classList.add("d-none");
                overlay.classList.remove("d-flex");
            }
        }

        // Initialize display
        document.addEventListener("DOMContentLoaded", () => {
            updateLayoutFromState();

            // Mobile Sidebar Toggle
            const sidebar = document.getElementById('aitosSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const hamburger = document.getElementById('hamburgerToggle');
            const closeBtn = document.getElementById('sidebarCloseBtn');

            function openSidebar() {
                sidebar.classList.add('mobile-open');
                overlay.classList.add('active');
            }
            function closeSidebar() {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
            }

            if (hamburger) hamburger.addEventListener('click', openSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (overlay) overlay.addEventListener('click', closeSidebar);
        });

        /**
         * Global Toast Notification System
         * Usage: showToast('Settings saved!', 'success')
         * Types: 'success', 'error', 'warning', 'info'
         */
        function showToast(message, type = 'success', duration = 4000) {
            const container = document.getElementById('toastContainer');
            if (!container) return;

            const icons = {
                success: 'bi-check-circle-fill',
                error: 'bi-x-circle-fill',
                warning: 'bi-exclamation-triangle-fill',
                info: 'bi-info-circle-fill'
            };
            const titles = {
                success: 'Success',
                error: 'Error',
                warning: 'Warning',
                info: 'Info'
            };

            const toast = document.createElement('div');
            toast.className = `aitos-toast toast-${type}`;
            toast.innerHTML = `
                <i class="bi ${icons[type] || icons.info} toast-icon"></i>
                <div class="toast-body">
                    <div class="toast-title">${titles[type] || 'Notification'}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="this.closest('.aitos-toast').remove()">
                    <i class="bi bi-x"></i>
                </button>
            `;

            container.appendChild(toast);

            // Auto-dismiss
            setTimeout(() => {
                toast.classList.add('toast-exit');
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }
    </script>
    
    @yield('scripts')
</body>
</html>
