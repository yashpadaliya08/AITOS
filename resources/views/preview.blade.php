@extends('layouts.app')

@section('title', 'AITOS - Repository Preview')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">AITOS Repository Preview</h3>
        <p class="text-muted mb-0">Browse the generated AI context structure and repository meta files before downloading the package.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="/compiler" class="btn btn-outline-secondary px-3"><i class="bi bi-arrow-left me-1"></i> Compiler</a>
        <a href="/export" class="btn btn-success px-4 py-2 fw-semibold shadow-sm">Proceed to Export <i class="bi bi-download ms-1"></i></a>
    </div>
</div>

<!-- VS Code Style Editor Container -->
<div class="card border-0 shadow-lg mb-4">
    <div class="code-explorer">
        <!-- Sidebar File Tree -->
        <div class="explorer-sidebar">
            <div class="explorer-sidebar-title d-flex justify-content-between align-items-center">
                <span>Explorer</span>
                <span class="badge bg-dark text-muted font-monospace" style="font-size: 0.65rem;" id="expProjName">Project</span>
            </div>
            
            <div class="explorer-tree">
                <!-- Root Folder -->
                <div class="tree-node directory active-root" onclick="toggleFolder('root-folder')">
                    <i class="bi bi-folder2-open" id="root-folder-icon"></i> <span id="treeRootName">ProjectName</span>
                </div>
                
                <div id="root-folder-content" class="tree-sub-folder">
                    <!-- README.md -->
                    <div class="tree-node file" id="node-readme" onclick="selectFile('README.md')">
                        <i class="bi bi-filetype-md"></i> README.md
                    </div>
                    <!-- START_HERE.md -->
                    <div class="tree-node file" id="node-starthere" onclick="selectFile('START_HERE.md')">
                        <i class="bi bi-filetype-md"></i> START_HERE.md
                    </div>
                    <!-- PROJECT_SUMMARY.md -->
                    <div class="tree-node file" id="node-summary" onclick="selectFile('PROJECT_SUMMARY.md')">
                        <i class="bi bi-filetype-md"></i> PROJECT_SUMMARY.md
                    </div>
                    
                    <!-- .aitos/ -->
                    <div class="tree-node directory" onclick="toggleFolder('aitos-folder')">
                        <i class="bi bi-folder-fill" id="aitos-folder-icon"></i> .aitos
                    </div>
                    
                    <div id="aitos-folder-content" class="tree-sub-folder">
                        <!-- data/ -->
                        <div class="tree-node directory" onclick="toggleFolder('data-folder')">
                            <i class="bi bi-folder-fill" id="data-folder-icon"></i> data
                        </div>
                        <div id="data-folder-content" class="tree-sub-folder collapsed">
                            <div class="tree-node file" id="node-projjson" onclick="selectFile('data/project.json')">
                                <i class="bi bi-filetype-json"></i> project.json
                            </div>
                            <div class="tree-node file" id="node-bpbusiness" onclick="selectFile('data/business_blueprint.json')">
                                <i class="bi bi-filetype-json"></i> business_blueprint.json
                            </div>
                            <div class="tree-node file" id="node-bpdatabase" onclick="selectFile('data/database_blueprint.json')">
                                <i class="bi bi-filetype-json"></i> database_blueprint.json
                            </div>
                            <div class="tree-node file" id="node-bptechnical" onclick="selectFile('data/technical_blueprint.json')">
                                <i class="bi bi-filetype-json"></i> technical_blueprint.json
                            </div>
                            <div class="tree-node file" id="node-bpui" onclick="selectFile('data/ui_blueprint.json')">
                                <i class="bi bi-filetype-json"></i> ui_blueprint.json
                            </div>
                            <div class="tree-node file" id="node-teamjson" onclick="selectFile('data/team.json')">
                                <i class="bi bi-filetype-json"></i> team.json
                            </div>
                            <div class="tree-node file" id="node-tasksjson" onclick="selectFile('data/tasks.json')">
                                <i class="bi bi-filetype-json"></i> tasks.json
                            </div>
                            <div class="tree-node file" id="node-rulesjson" onclick="selectFile('data/rules.json')">
                                <i class="bi bi-filetype-json"></i> rules.json
                            </div>
                            <div class="tree-node file" id="node-decisionsjson" onclick="selectFile('data/decisions.json')">
                                <i class="bi bi-filetype-json"></i> decisions.json
                            </div>
                        </div>

                        <!-- context/ -->
                        <div class="tree-node directory" onclick="toggleFolder('context-folder')">
                            <i class="bi bi-folder-fill" id="context-folder-icon"></i> context
                        </div>
                        <div id="context-folder-content" class="tree-sub-folder collapsed">
                            <div class="tree-node file" id="node-ctxcurrent" onclick="selectFile('context/CURRENT_CONTEXT.md')">
                                <i class="bi bi-filetype-md"></i> CURRENT_CONTEXT.md
                            </div>
                            <div class="tree-node file" id="node-ctxbackend" onclick="selectFile('context/BACKEND_CONTEXT.md')">
                                <i class="bi bi-filetype-md"></i> BACKEND_CONTEXT.md
                            </div>
                            <div class="tree-node file" id="node-ctxfrontend" onclick="selectFile('context/FRONTEND_CONTEXT.md')">
                                <i class="bi bi-filetype-md"></i> FRONTEND_CONTEXT.md
                            </div>
                            <div class="tree-node file" id="node-ctxdatabase" onclick="selectFile('context/DATABASE_CONTEXT.md')">
                                <i class="bi bi-filetype-md"></i> DATABASE_CONTEXT.md
                            </div>
                            <div class="tree-node file" id="node-ctxgeneric" onclick="selectFile('context/GENERIC_CONTEXT.md')">
                                <i class="bi bi-filetype-md"></i> GENERIC_CONTEXT.md
                            </div>
                        </div>

                        <!-- config/ -->
                        <div class="tree-node directory" onclick="toggleFolder('config-folder')">
                            <i class="bi bi-folder-fill" id="config-folder-icon"></i> config
                        </div>
                        <div id="config-folder-content" class="tree-sub-folder collapsed">
                            <div class="tree-node file" id="node-configjson" onclick="selectFile('config/config.json')">
                                <i class="bi bi-filetype-json"></i> config.json
                            </div>
                        </div>

                        <!-- snapshots/ -->
                        <div class="tree-node directory" onclick="toggleFolder('snapshots-folder')">
                            <i class="bi bi-folder-fill" id="snapshots-folder-icon"></i> snapshots
                        </div>
                        <div id="snapshots-folder-content" class="tree-sub-folder collapsed">
                            <div class="tree-node file" id="node-snapshotjson" onclick="selectFile('snapshots/v1.0.0-snapshot.json')">
                                <i class="bi bi-filetype-json"></i> v1.0.0-snapshot.json
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Editor Viewport -->
        <div class="explorer-editor">
            <div class="editor-tabs">
                <div class="editor-tab active" id="activeEditorTab">
                    <i class="bi bi-file-earmark-code"></i> <span id="activeTabName">README.md</span>
                </div>
            </div>
            <div class="editor-content" id="editorContentBody">
                <!-- Content loaded by JS -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let projectFiles = {};

    document.addEventListener("DOMContentLoaded", () => {
        generateProjectVirtualFiles();
        loadTreeHeaders();
        // Open README by default
        selectFile('README.md');
    });

    function loadTreeHeaders() {
        const state = getProjectState();
        const rootName = state.projectName || "AITOS_Project";
        document.getElementById("treeRootName").innerText = rootName;
        document.getElementById("expProjName").innerText = rootName.toUpperCase().slice(0,10);
    }

    function toggleFolder(folderId) {
        const subFolder = document.getElementById(`${folderId}-content`);
        const icon = document.getElementById(`${folderId}-icon`);
        
        if (subFolder.classList.contains("collapsed")) {
            subFolder.classList.remove("collapsed");
            icon.className = "bi bi-folder2-open";
        } else {
            subFolder.classList.add("collapsed");
            icon.className = "bi bi-folder-fill";
        }
    }

    function selectFile(filePath) {
        // Remove active class from all files
        document.querySelectorAll(".tree-node.file").forEach(node => {
            node.classList.remove("active");
        });

        // Add active to selected
        let nodeId = 'node-readme';
        let tabIcon = 'bi-filetype-md';
        
        if (filePath === 'README.md') nodeId = 'node-readme';
        else if (filePath === 'START_HERE.md') nodeId = 'node-starthere';
        else if (filePath === 'PROJECT_SUMMARY.md') nodeId = 'node-summary';
        else if (filePath === 'data/project.json') { nodeId = 'node-projjson'; tabIcon = 'bi-filetype-json'; }
        else if (filePath === 'data/business_blueprint.json') { nodeId = 'node-bpbusiness'; tabIcon = 'bi-filetype-json'; }
        else if (filePath === 'data/database_blueprint.json') { nodeId = 'node-bpdatabase'; tabIcon = 'bi-filetype-json'; }
        else if (filePath === 'data/technical_blueprint.json') { nodeId = 'node-bptechnical'; tabIcon = 'bi-filetype-json'; }
        else if (filePath === 'data/ui_blueprint.json') { nodeId = 'node-bpui'; tabIcon = 'bi-filetype-json'; }
        else if (filePath === 'data/team.json') { nodeId = 'node-teamjson'; tabIcon = 'bi-filetype-json'; }
        else if (filePath === 'data/tasks.json') { nodeId = 'node-tasksjson'; tabIcon = 'bi-filetype-json'; }
        else if (filePath === 'data/rules.json') { nodeId = 'node-rulesjson'; tabIcon = 'bi-filetype-json'; }
        else if (filePath === 'data/decisions.json') { nodeId = 'node-decisionsjson'; tabIcon = 'bi-filetype-json'; }
        else if (filePath === 'context/CURRENT_CONTEXT.md') nodeId = 'node-ctxcurrent';
        else if (filePath === 'context/BACKEND_CONTEXT.md') nodeId = 'node-ctxbackend';
        else if (filePath === 'context/FRONTEND_CONTEXT.md') nodeId = 'node-ctxfrontend';
        else if (filePath === 'context/DATABASE_CONTEXT.md') nodeId = 'node-ctxdatabase';
        else if (filePath === 'context/GENERIC_CONTEXT.md') nodeId = 'node-ctxgeneric';
        else if (filePath === 'config/config.json') { nodeId = 'node-configjson'; tabIcon = 'bi-filetype-json'; }
        else if (filePath === 'snapshots/v1.0.0-snapshot.json') { nodeId = 'node-snapshotjson'; tabIcon = 'bi-filetype-json'; }

        const selectedNode = document.getElementById(nodeId);
        if (selectedNode) selectedNode.classList.add("active");

        // Update editor headers
        const basename = filePath.includes('/') ? filePath.split('/').pop() : filePath;
        document.getElementById("activeTabName").innerText = basename;
        document.getElementById("activeEditorTab").querySelector("i").className = `bi ${tabIcon}`;

        // Populate editor contents
        const content = projectFiles[filePath] || "File empty or missing.";
        document.getElementById("editorContentBody").innerText = content;
    }

    function generateProjectVirtualFiles() {
        const state = getProjectState();
        const rootDir = state.projectName || "AITOS_Project";

        // Helper schemas formatting
        const schemaProj = {
            name: state.projectName,
            description: state.projectDescription,
            goal: state.projectGoal,
            version: "1.0.0",
            created_at: new Date().toISOString()
        };

        const schemaTeam = state.teamMembers;
        const schemaTasks = state.tasks;
        const schemaRules = state.requirements.businessRules.split('\n').map(l => l.trim().replace(/^•\s*/, ''));
        const schemaDecisions = state.decisions;

        // Virtual files collection
        projectFiles = {
            'README.md': `# ${state.projectName || "AITOS Project"}

${state.projectDescription || "No description provided."}

## Development Stack
- **Framework:** ${state.techStack.framework}
- **Database:** ${state.techStack.database}
- **Frontend:** ${state.techStack.frontend}

## AITOS Context System
This repository is pre-configured with AITOS (AI Team Operating System) context layers.
The \`.aitos/\` directory holds structured project requirements, blueprints, planning data, and optimized markdown summaries for AI-assisted development tools.

Please review \`START_HERE.md\` to trigger AI workspace alignments.
`,

            'START_HERE.md': `# AITOS Workspace Alignment

> **AITOS Philosophy:** Humans Decide. AI Builds. AITOS Remembers.

Welcome, AI Developer Agent. You have been opened in this workspace. Before writing code or proposing edits, synchronize your parameters with this repository context.

### Synchronization Instructions
1. Load \`PROJECT_SUMMARY.md\` to understand the product vision.
2. Read files inside \`.aitos/context/\` directory to align core technology scopes:
   - \`CURRENT_CONTEXT.md\` for your active task list and feature assignments.
   - \`BACKEND_CONTEXT.md\` for entities, models, and databases.
   - \`FRONTEND_CONTEXT.md\` for CSS, bootstrap grids, and layouts.
3. Consult \`.aitos/data/rules.json\` to verify business logic constraints.

Do not create files or change folder routing outside approved specifications.
`,

            'PROJECT_SUMMARY.md': `# Project Summary: ${state.projectName}

## Problem Statement
\`\`\`text
${state.problemStatement}
\`\`\`

## System Requirements Overview
${state.requirements.requirements}

## Risks & Assumptions
### Assumptions
${state.requirements.assumptions}

### Risks & Mitigations
${state.requirements.risks}
`,

            'data/project.json': JSON.stringify(schemaProj, null, 4),
            'data/business_blueprint.json': JSON.stringify({ version: "1.0.0", blueprint: state.blueprints.business }, null, 4),
            'data/database_blueprint.json': JSON.stringify({ version: "1.0.0", blueprint: state.blueprints.database }, null, 4),
            'data/technical_blueprint.json': JSON.stringify({ version: "1.0.0", blueprint: state.blueprints.technical }, null, 4),
            'data/ui_blueprint.json': JSON.stringify({ version: "1.0.0", blueprint: state.blueprints.ui }, null, 4),
            
            'data/team.json': JSON.stringify(schemaTeam, null, 4),
            'data/tasks.json': JSON.stringify(schemaTasks, null, 4),
            'data/rules.json': JSON.stringify(schemaRules, null, 4),
            'data/decisions.json': JSON.stringify(schemaDecisions, null, 4),

            'context/CURRENT_CONTEXT.md': `# Current Alignment Context

This file details the assigned development boundaries.

## Active Sprint Tasks
${state.tasks.map(t => `- [ ] **${t.column.toUpperCase()}**: ${t.text}`).join('\n')}

## Decision History Summary
${state.decisions.map(d => `- **${d.date}** *${d.title}*: ${d.desc}`).join('\n')}
`,

            'context/BACKEND_CONTEXT.md': `# Backend Context Layer

Contains model logic, API routes, and schema structures.

## Technology Stack
- Core: ${state.techStack.framework}
- Database: ${state.techStack.database}

## Entities Map
${state.requirements.entities}

## Modules Map
${state.requirements.modules}

## Database Specifications
${state.blueprints.database}
`,

            'context/FRONTEND_CONTEXT.md': `# Frontend Context Layer

Contains layout structures, CSS frameworks, templates, and styling.

## Design Frameworks
- Layouts: ${state.techStack.frontend}
- Icons: Bootstrap Icons (CDN)

## User Interface Specifications
${state.blueprints.ui}
`,

            'context/DATABASE_CONTEXT.md': `# Database Schema Context

## Design Schema
${state.blueprints.database}
`,

            'context/GENERIC_CONTEXT.md': `# Generic Development Guidelines

1. **Strict Coding Standards:** Maintain standard formatting rules matching the primary framework convention.
2. **Commit Policy:** Commit regularly. Each commit message must start with a context marker matching the active module (e.g. \`feat(auth)\`).
3. **No Overwrites:** Do not rewrite files or delete sections unless specified. Write modular additions.
`,

            'config/config.json': JSON.stringify({
                aitos_version: "1.0.0",
                last_compile_date: new Date().toISOString(),
                git_sync_enabled: true
            }, null, 4),

            'snapshots/v1.0.0-snapshot.json': JSON.stringify({
                snapshot_id: "snap-v1.0.0",
                timestamp: new Date().toISOString(),
                blueprint_version: "1.0.0",
                project_name: state.projectName,
                md5: "8b7a4d6ef2e987c2b4510001bc9f88c3"
            }, null, 4)
        };
    }
</script>
@endsection
