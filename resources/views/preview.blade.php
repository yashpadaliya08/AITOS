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
                    
                    <!-- Framework Starter Scaffolding Tree -->
                    <div class="tree-node directory" onclick="toggleFolder('scaffold-folder')">
                        <i class="bi bi-folder-fill" id="scaffold-folder-icon"></i> <span id="scaffold-folder-name">Scaffold (Framework)</span>
                    </div>
                    <div id="scaffold-folder-content" class="tree-sub-folder collapsed">
                        <!-- Skeletons injected dynamically by JS -->
                    </div>

                    <!-- .aitos/ -->
                    <div class="tree-node directory" onclick="toggleFolder('aitos-folder')">
                        <i class="bi bi-folder-fill" id="aitos-folder-icon"></i> .aitos
                    </div>
                    
                    <div id="aitos-folder-content" class="tree-sub-folder">
                        <!-- manifest.json -->
                        <div class="tree-node file" id="node-manifestjson" onclick="selectFile('manifest.json')">
                            <i class="bi bi-filetype-json"></i> manifest.json
                        </div>

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
                            <div class="tree-node file" id="node-codegenjson" onclick="selectFile('data/code_generation_model.json')">
                                <i class="bi bi-filetype-json"></i> code_generation_model.json
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
                            <div class="tree-node file" id="node-ctxworkspacesummary" onclick="selectFile('context/workspace_summary.md')">
                                <i class="bi bi-filetype-md"></i> workspace_summary.md
                            </div>
                            <div class="tree-node file" id="node-ctxcurrentstate" onclick="selectFile('context/current_state.md')">
                                <i class="bi bi-filetype-md"></i> current_state.md
                            </div>
                            <div class="tree-node file" id="node-ctxnextsteps" onclick="selectFile('context/next_steps.md')">
                                <i class="bi bi-filetype-md"></i> next_steps.md
                            </div>
                            <div class="tree-node file" id="node-ctxteamnotes" onclick="selectFile('context/team_notes.md')">
                                <i class="bi bi-filetype-md"></i> team_notes.md
                            </div>
                            <div class="tree-node file" id="node-ctxarchsummary" onclick="selectFile('context/architecture_summary.md')">
                                <i class="bi bi-filetype-md"></i> architecture_summary.md
                            </div>
                        </div>

                        <!-- prompts/ -->
                        <div class="tree-node directory" onclick="toggleFolder('prompts-folder')">
                            <i class="bi bi-folder-fill" id="prompts-folder-icon"></i> prompts
                        </div>
                        <div id="prompts-folder-content" class="tree-sub-folder collapsed">
                            <div class="tree-node file" id="node-pbackend" onclick="selectFile('prompts/backend.md')">
                                <i class="bi bi-filetype-md"></i> backend.md
                            </div>
                            <div class="tree-node file" id="node-pfrontend" onclick="selectFile('prompts/frontend.md')">
                                <i class="bi bi-filetype-md"></i> frontend.md
                            </div>
                            <div class="tree-node file" id="node-pdatabase" onclick="selectFile('prompts/database.md')">
                                <i class="bi bi-filetype-md"></i> database.md
                            </div>
                            <div class="tree-node file" id="node-papi" onclick="selectFile('prompts/api.md')">
                                <i class="bi bi-filetype-md"></i> api.md
                            </div>
                            <div class="tree-node file" id="node-ptesting" onclick="selectFile('prompts/testing.md')">
                                <i class="bi bi-filetype-md"></i> testing.md
                            </div>
                            <div class="tree-node file" id="node-preview" onclick="selectFile('prompts/review.md')">
                                <i class="bi bi-filetype-md"></i> review.md
                            </div>
                            <div class="tree-node file" id="node-pbugfix" onclick="selectFile('prompts/bugfix.md')">
                                <i class="bi bi-filetype-md"></i> bugfix.md
                            </div>
                            <div class="tree-node file" id="node-parchitecture" onclick="selectFile('prompts/architecture.md')">
                                <i class="bi bi-filetype-md"></i> architecture.md
                            </div>
                        </div>

                        <!-- reports/ -->
                        <div class="tree-node directory" onclick="toggleFolder('reports-folder')">
                            <i class="bi bi-folder-fill" id="reports-folder-icon"></i> reports
                        </div>
                        <div id="reports-folder-content" class="tree-sub-folder collapsed">
                            <div class="tree-node file" id="node-genlogjson" onclick="selectFile('reports/generation_log.json')">
                                <i class="bi bi-filetype-json"></i> generation_log.json
                            </div>
                            <div class="tree-node file" id="node-piperepjson" onclick="selectFile('reports/pipeline_report.json')">
                                <i class="bi bi-filetype-json"></i> pipeline_report.json
                            </div>
                            <div class="tree-node file" id="node-archrevjson" onclick="selectFile('reports/architecture_review.json')">
                                <i class="bi bi-filetype-json"></i> architecture_review.json
                            </div>
                        </div>

                        <!-- workspace/ -->
                        <div class="tree-node directory" onclick="toggleFolder('workspace-folder')">
                            <i class="bi bi-folder-fill" id="workspace-folder-icon"></i> workspace
                        </div>
                        <div id="workspace-folder-content" class="tree-sub-folder collapsed">
                            <div class="tree-node file" id="node-workspacejson" onclick="selectFile('workspace/workspace.json')">
                                <i class="bi bi-filetype-json"></i> workspace.json
                            </div>
                            <div class="tree-node file" id="node-taskcontextjson" onclick="selectFile('workspace/task_context.json')">
                                <i class="bi bi-filetype-json"></i> task_context.json
                            </div>
                        </div>

                        <!-- handoff/ -->
                        <div class="tree-node directory" onclick="toggleFolder('handoff-folder')">
                            <i class="bi bi-folder-fill" id="handoff-folder-icon"></i> handoff
                        </div>
                        <div id="handoff-folder-content" class="tree-sub-folder collapsed">
                            <div class="tree-node file" id="node-handoffmd" onclick="selectFile('handoff/handoff.md')">
                                <i class="bi bi-filetype-md"></i> handoff.md
                            </div>
                        </div>

                        <!-- framework/ -->
                        <div class="tree-node directory" onclick="toggleFolder('framework-folder')">
                            <i class="bi bi-folder-fill" id="framework-folder-icon"></i> framework
                        </div>
                        <div id="framework-folder-content" class="tree-sub-folder collapsed">
                            <div class="tree-node file" id="node-fwconfig" onclick="selectFile('framework/config.copy')">
                                <i class="bi bi-file-earmark-code"></i> config templates copies
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

                        <!-- ai_sessions/ -->
                        <div class="tree-node directory" onclick="toggleFolder('aisessions-folder')">
                            <i class="bi bi-folder-fill" id="aisessions-folder-icon"></i> ai_sessions
                        </div>
                        <div id="aisessions-folder-content" class="tree-sub-folder collapsed">
                            <div class="tree-node file" id="node-sessionkeep" onclick="selectFile('ai_sessions/.gitkeep')">
                                <i class="bi bi-file-earmark-lock"></i> .gitkeep
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

    function selectFile(filePath, customNodeId, customIcon) {
        // Remove active class from all files
        document.querySelectorAll(".tree-node.file").forEach(node => {
            node.classList.remove("active");
        });

        // Add active to selected
        let nodeId = 'node-readme';
        let tabIcon = 'bi-filetype-md';
        
        if (customNodeId) {
            nodeId = customNodeId;
            tabIcon = customIcon || 'bi-file-earmark-code';
        } else {
            if (filePath === 'README.md') nodeId = 'node-readme';
            else if (filePath === 'START_HERE.md') nodeId = 'node-starthere';
            else if (filePath === 'PROJECT_SUMMARY.md') nodeId = 'node-summary';
            else if (filePath === 'manifest.json') { nodeId = 'node-manifestjson'; tabIcon = 'bi-filetype-json'; }
            else if (filePath === 'data/project.json') { nodeId = 'node-projjson'; tabIcon = 'bi-filetype-json'; }
            else if (filePath === 'data/business_blueprint.json') { nodeId = 'node-bpbusiness'; tabIcon = 'bi-filetype-json'; }
            else if (filePath === 'data/database_blueprint.json') { nodeId = 'node-bpdatabase'; tabIcon = 'bi-filetype-json'; }
            else if (filePath === 'data/technical_blueprint.json') { nodeId = 'node-bptechnical'; tabIcon = 'bi-filetype-json'; }
            else if (filePath === 'data/ui_blueprint.json') { nodeId = 'node-bpui'; tabIcon = 'bi-filetype-json'; }
            else if (filePath === 'data/team.json') { nodeId = 'node-teamjson'; tabIcon = 'bi-filetype-json'; }
            else if (filePath === 'data/tasks.json') { nodeId = 'node-tasksjson'; tabIcon = 'bi-filetype-json'; }
            else if (filePath === 'data/rules.json') { nodeId = 'node-rulesjson'; tabIcon = 'bi-filetype-json'; }
            else if (filePath === 'data/decisions.json') { nodeId = 'node-decisionsjson'; tabIcon = 'bi-filetype-json'; }
            else if (filePath === 'data/code_generation_model.json') { nodeId = 'node-codegenjson'; tabIcon = 'bi-filetype-json'; }
            else if (filePath === 'context/CURRENT_CONTEXT.md') nodeId = 'node-ctxcurrent';
            else if (filePath === 'context/BACKEND_CONTEXT.md') nodeId = 'node-ctxbackend';
            else if (filePath === 'context/FRONTEND_CONTEXT.md') nodeId = 'node-ctxfrontend';
            else if (filePath === 'context/DATABASE_CONTEXT.md') nodeId = 'node-ctxdatabase';
            else if (filePath === 'context/GENERIC_CONTEXT.md') nodeId = 'node-ctxgeneric';
            else if (filePath === 'context/workspace_summary.md') nodeId = 'node-ctxworkspacesummary';
            else if (filePath === 'context/current_state.md') nodeId = 'node-ctxcurrentstate';
            else if (filePath === 'context/next_steps.md') nodeId = 'node-ctxnextsteps';
            else if (filePath === 'context/team_notes.md') nodeId = 'node-ctxteamnotes';
            else if (filePath === 'context/architecture_summary.md') nodeId = 'node-ctxarchsummary';
            else if (filePath === 'prompts/backend.md') nodeId = 'node-pbackend';
            else if (filePath === 'prompts/frontend.md') nodeId = 'node-pfrontend';
            else if (filePath === 'prompts/database.md') nodeId = 'node-pdatabase';
            else if (filePath === 'prompts/api.md') nodeId = 'node-papi';
            else if (filePath === 'prompts/review.md') nodeId = 'node-preview';
            else if (filePath === 'prompts/testing.md') nodeId = 'node-ptesting';
            else if (filePath === 'prompts/bugfix.md') nodeId = 'node-pbugfix';
            else if (filePath === 'prompts/architecture.md') nodeId = 'node-parchitecture';
            else if (filePath === 'workspace/workspace.json') { nodeId = 'node-workspacejson'; tabIcon = 'bi-filetype-json'; }
            else if (filePath === 'workspace/task_context.json') { nodeId = 'node-taskcontextjson'; tabIcon = 'bi-filetype-json'; }
            else if (filePath === 'handoff/handoff.md') nodeId = 'node-handoffmd';
            else if (filePath === 'reports/generation_log.json') { nodeId = 'node-genlogjson'; tabIcon = 'bi-filetype-json'; }
            else if (filePath === 'reports/pipeline_report.json') { nodeId = 'node-piperepjson'; tabIcon = 'bi-filetype-json'; }
            else if (filePath === 'reports/architecture_review.json') { nodeId = 'node-archrevjson'; tabIcon = 'bi-filetype-json'; }
            else if (filePath === 'framework/config.copy') { nodeId = 'node-fwconfig'; tabIcon = 'bi-file-earmark-code'; }
            else if (filePath === 'config/config.json') { nodeId = 'node-configjson'; tabIcon = 'bi-filetype-json'; }
            else if (filePath === 'snapshots/v1.0.0-snapshot.json') { nodeId = 'node-snapshotjson'; tabIcon = 'bi-filetype-json'; }
            else if (filePath === 'ai_sessions/.gitkeep') { nodeId = 'node-sessionkeep'; tabIcon = 'bi-file-earmark-lock'; }
        }

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

        const framework = state.techStack?.framework || 'laravel';

        const mappingRows = [];
        const tables = (state.databaseSchema && state.databaseSchema.tables) || [];
        tables.forEach(t => {
            const singular = t.name.replace(/s$/, '');
            const camel = singular.charAt(0).toUpperCase() + singular.slice(1);
            mappingRows.push({
                entity: camel,
                model: camel,
                controller: camel + "Controller",
                route: t.name.toLowerCase(),
                migration: "create_" + t.name.toLowerCase() + "_table",
                service: camel + "Service",
                repository: camel + "Repository"
            });
        });

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

            'manifest.json': JSON.stringify({
                projectName: state.projectName,
                framework: framework,
                blueprintVersion: "1.0.0"
            }, null, 4),

            'data/project.json': JSON.stringify(schemaProj, null, 4),
            'data/business_blueprint.json': JSON.stringify({ version: "1.0.0", blueprint: state.blueprints.business }, null, 4),
            'data/database_blueprint.json': JSON.stringify({ version: "1.0.0", blueprint: state.blueprints.database }, null, 4),
            'data/technical_blueprint.json': JSON.stringify({ version: "1.0.0", blueprint: state.blueprints.technical }, null, 4),
            'data/ui_blueprint.json': JSON.stringify({ version: "1.0.0", blueprint: state.blueprints.ui }, null, 4),
            
            'data/team.json': JSON.stringify(schemaTeam, null, 4),
            'data/tasks.json': JSON.stringify(schemaTasks, null, 4),
            'data/rules.json': JSON.stringify(schemaRules, null, 4),
            'data/decisions.json': JSON.stringify(schemaDecisions, null, 4),
            'data/code_generation_model.json': JSON.stringify({ mappings: mappingRows }, null, 4),

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

            'context/workspace_summary.md': `# Workspace Summary\n\nActive developer environment for project ${state.projectName}.\nFramework: ${framework.toUpperCase()}`,
            'context/current_state.md': `# Current State\n\nActive Sprint Tasks: ${state.tasks ? state.tasks.length : 0}\nCompleted Tasks: 0`,
            'context/next_steps.md': `# Next Steps\n\n1. Complete active models scaffolding.\n2. Bind API routing definitions.`,
            'context/team_notes.md': `# Team Collaboration Notes\n\nActive Developer counts: ${state.teamMembers ? state.teamMembers.length : 0} members.`,
            'context/architecture_summary.md': `# Architecture Summary\n\nTarget Stack: ${framework.toUpperCase()}\nEntity counts: ${tables.length} models mapped.`,

            'prompts/backend.md': `# Backend AI Prompt Pack\nImplement the API services, repository layers, Eloquent models, or SQLAlchemy entities mapped inside code_generation_model.json.`,
            'prompts/frontend.md': `# Frontend AI Prompt Pack\nImplement client layout pages, responsive components, CSS views, or Next.js layout setups.`,
            'prompts/database.md': `# Database Schema AI Prompt Pack\nConfigure database migrations, seed data scripts, relations keys, and table indexing configurations.`,
            'prompts/api.md': `# API Design AI Prompt Pack\nConfigure REST route endpoints, payload validators, JSON structures, status codes, and error handlers.`,
            'prompts/review.md': `# Architecture Review AI Prompt Pack\nInspect pull requests, analyze naming structures alignment, check permissions gates and handle errors.`,
            'prompts/testing.md': `# Testing AI Prompt Pack\nWrite PHPUnit tests or FastAPI test cases targeting controllers, database seeds, and mock dependencies.`,
            'prompts/bugfix.md': `# Bugfix AI Prompt Pack\nAnalyze application logs, diagnose database exceptions, and edit models safely.`,
            'prompts/architecture.md': `# Architecture AI Prompt Pack\nExamine modular designs, verify design patterns boundaries, layout interfaces, and maintain clean separation of concerns.`,

            'workspace/workspace.json': JSON.stringify({
                members: state.teamMembers,
                current_tasks: state.tasks,
                current_sprint: "Sprint 1",
                current_phase: "Development Phase"
            }, null, 4),
            'workspace/task_context.json': JSON.stringify(state.tasks ? state.tasks.map(t => ({
                task: t.text,
                description: 'Implementation task: ' + t.text,
                dependencies: [],
                required_files: []
            })) : [], null, 4),
            'handoff/handoff.md': `# AI Development Handoff Report\n\nAllow another AI tool or teammate to continue working immediately on project **${state.projectName}**.\n\n## Next Steps\n- Read CURRENT_CONTEXT.md\n- Inspect task_context.json`,

            'reports/generation_log.json': JSON.stringify({
                engines: ["ProjectIntelligenceEngine", "BlueprintGenerator", "KnowledgeGraphEngine", "DatabaseGenerator", "ApiGenerator", "CodeGenerationEngine", "UiGenerator", "PlanningGenerator", "DocumentationGenerator", "ContextCompiler", "PromptPackGenerator", "WorkspaceEngine", "RepositoryGenerator"],
                duration: "0.32s",
                warnings: []
            }, null, 4),
            'reports/pipeline_report.json': JSON.stringify({ success: true, completionPercentage: 100 }, null, 4),
            'reports/architecture_review.json': JSON.stringify({ status: "approved", reviewer: "AITOS Gate", readiness: "100%" }, null, 4),

            'framework/config.copy': `Autoload configs / package settings for framework: ${framework}`,

            'config/config.json': JSON.stringify({
                aitos_version: "1.5.0",
                last_compile_date: new Date().toISOString(),
                git_sync_enabled: true
            }, null, 4),

            'snapshots/v1.0.0-snapshot.json': JSON.stringify({
                snapshot_id: "snap-v1.0.0",
                timestamp: new Date().toISOString(),
                blueprint_version: "1.0.0",
                project_name: state.projectName,
                md5: "8b7a4d6ef2e987c2b4510001bc9f88c3"
            }, null, 4),

            'ai_sessions/.gitkeep': ""
        };

        document.getElementById("scaffold-folder-name").innerText = `Scaffold (${framework.toUpperCase()})`;
        
        const scaffoldFolder = document.getElementById("scaffold-folder-content");
        scaffoldFolder.innerHTML = "";
        
        if (framework === 'laravel') {
            tables.forEach(t => {
                const tableName = t.name;
                const camel = tableName.charAt(0).toUpperCase() + tableName.slice(1).replace(/s$/, '');
                if (camel === 'User') return;
                
                addVirtualFile("app/Models/" + camel + ".php", `<?php\n\nnamespace App\\Models;\n\nuse Illuminate\\Database\\Eloquent\\Model;\n\nclass ${camel} extends Model\n{\n    protected \$guarded = [];\n}\n`, "bi-filetype-php");
                addVirtualFile("app/Http/Controllers/" + camel + "Controller.php", `<?php\n\nnamespace App\\Http\\Controllers;\n\nuse App\\Models\\${camel};\nuse Illuminate\\Http\\Request;\n\nclass ${camel}Controller extends Controller\n{\n    public function index() { return response()->json(${camel}::all()); }\n}\n`, "bi-filetype-php");
                addVirtualFile("app/Services/" + camel + "Service.php", `<?php\n\nnamespace App\\Services;\n\nclass ${camel}Service {\n    public function handle() {}\n}\n`, "bi-filetype-php");
                addVirtualFile("app/Repositories/" + camel + "Repository.php", `<?php\n\nnamespace App\\Repositories;\n\nclass ${camel}Repository {\n    public function all() {}\n}\n`, "bi-filetype-php");
            });
            addVirtualFile("routes/api.php", `<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n\nRoute::middleware('api')->group(function () {\n` + tables.map(t => `    Route::apiResource('${t.name}', \\App\\Http\\Controllers\\${t.name.charAt(0).toUpperCase() + t.name.slice(1).replace(/s$/, '')}Controller::class);`).join('\n') + `\n});`, "bi-filetype-php");
            addVirtualFile("composer.json", `{\n    "name": "laravel/laravel",\n    "require": {\n        "php": "^8.2"\n    }\n}`, "bi-filetype-json");
        } else if (framework === 'fastapi') {
            tables.forEach(t => {
                const tableName = t.name;
                const singular = tableName.replace(/s$/, '');
                const camel = singular.charAt(0).toUpperCase() + singular.slice(1);
                
                addVirtualFile("app/models/" + singular + ".py", `from sqlalchemy import Column, Integer, String\nfrom app.database import Base\n\nclass ${camel}(Base):\n    __tablename__ = "${tableName}"\n    id = Column(Integer, primary_key=True)\n`, "bi-filetype-py");
                addVirtualFile("app/schemas/" + singular + ".py", `from pydantic import BaseModel\n\nclass ${camel}Base(BaseModel):\n    pass\n`, "bi-filetype-py");
            });
            addVirtualFile("main.py", `from fastapi import FastAPI\napp = FastAPI()\n`, "bi-filetype-py");
            addVirtualFile("requirements.txt", `fastapi>=0.100.0\nuvicorn>=0.22.0\n`, "bi-file-earmark-code");
        } else if (framework === 'react') {
            tables.forEach(t => {
                const camel = t.name.charAt(0).toUpperCase() + t.name.slice(1).replace(/s$/, '');
                addVirtualFile("src/pages/" + camel + "Page.jsx", `import React from 'react';\nexport default function ${camel}Page() { return <div>${camel} Page</div>; }`, "bi-filetype-jsx");
            });
            addVirtualFile("package.json", `{\n  "dependencies": { "react": "^18.2.0" }\n}`, "bi-filetype-json");
        } else if (framework === 'next') {
            tables.forEach(t => {
                const camel = t.name.charAt(0).toUpperCase() + t.name.slice(1).replace(/s$/, '');
                addVirtualFile("src/app/" + t.name + "/page.tsx", `import React from 'react';\nexport default function page() { return <div>${camel} page</div>; }`, "bi-file-earmark-code");
            });
            addVirtualFile("package.json", `{\n  "dependencies": { "next": "latest" }\n}`, "bi-filetype-json");
        } else if (framework === 'node') {
            tables.forEach(t => {
                const singular = t.name.replace(/s$/, '');
                addVirtualFile("src/controllers/" + singular + ".js", `exports.getAll = async (req, res) => { res.json([]); };`, "bi-filetype-js");
            });
            addVirtualFile("package.json", `{\n  "dependencies": { "express": "^4.18.2" }\n}`, "bi-filetype-json");
        } else if (framework === 'odoo') {
            tables.forEach(t => {
                const singular = t.name.replace(/s$/, '');
                const camel = singular.charAt(0).toUpperCase() + singular.slice(1);
                addVirtualFile("models/" + singular + ".py", `from odoo import models, fields\nclass ${camel}(models.Model):\n    _name = "aitos.${singular}"\n`, "bi-filetype-py");
            });
            addVirtualFile("__manifest__.py", `{\n  "name": "AITOS Odoo",\n  "depends": ["base"]\n}`, "bi-filetype-py");
        }

        function addVirtualFile(path, content, iconClass) {
            projectFiles[path] = content;
            
            const node = document.createElement("div");
            node.className = "tree-node file";
            const nodeId = "node-" + path.replace(/[\/\.\-\$]/g, '');
            node.setAttribute("id", nodeId);
            node.setAttribute("onclick", `selectFile('${path}', '${nodeId}', '${iconClass}')`);
            node.innerHTML = `<i class="bi ${iconClass}"></i> ${path}`;
            scaffoldFolder.appendChild(node);
        }
    }
</script>
@endsection
