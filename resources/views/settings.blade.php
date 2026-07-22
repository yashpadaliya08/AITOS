@extends('layouts.app')

@section('title', 'AITOS - Settings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">System Settings</h3>
        <p class="text-muted mb-0">Customize compiler parameters, theme options, and AI alignment preferences.</p>
    </div>
    <span class="badge bg-secondary px-3 py-2">System Config</span>
</div>

<div class="row g-4">
    <!-- Left column form settings -->
    <div class="col-lg-8 mb-4">
        <form id="settingsForm" onsubmit="event.preventDefault(); saveSettings();">
            <!-- Project Meta Section -->
            <div class="card aitos-card border-light-subtle shadow-sm mb-4">
                <div class="aitos-card-header bg-white">
                    <span class="fw-bold"><i class="bi bi-info-circle text-primary me-1"></i> Core Project Details</span>
                </div>
                <div class="aitos-card-body">
                    <div class="mb-3">
                        <label for="settingsName" class="form-label fw-semibold">Project Name</label>
                        <input type="text" class="form-control border-light-subtle" id="settingsName" placeholder="e.g. MyProject">
                    </div>
                    <div class="mb-3">
                        <label for="settingsDesc" class="form-label fw-semibold">Short Description</label>
                        <textarea class="form-control border-light-subtle" id="settingsDesc" rows="3" placeholder="e.g. A web platform."></textarea>
                    </div>
                </div>
            </div>

            <!-- Context Preferences Section -->
            <div class="card aitos-card border-light-subtle shadow-sm mb-4">
                <div class="aitos-card-header bg-white">
                    <span class="fw-bold"><i class="bi bi-cpu-fill text-primary me-1"></i> Compiler & Context Configuration</span>
                </div>
                <div class="aitos-card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold d-block">Generation Preference Rules</label>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="prefOptTokens" checked>
                            <label class="form-check-label" for="prefOptTokens">Optimize markdown token sizes (strips redundant formatting)</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="prefSnapshots" checked>
                            <label class="form-check-label" for="prefSnapshots">Create backup snapshot on every compiler run</label>
                        </div>
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="prefGitSync">
                            <label class="form-check-label" for="prefGitSync">Auto-generate GitHub workflows for context synchronizations</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="prefFormat" class="form-label fw-semibold">Primary Context Export Standard</label>
                        <select id="prefFormat" class="form-select border-light-subtle">
                            <option value="markdown">Standard Markdown (.md files - Default)</option>
                            <option value="xml">XML Tags (Optimized for Anthropic Claude)</option>
                            <option value="json">Raw JSON (For API schema pipelines)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- AI Engine Configuration -->
            <div class="card aitos-card border-light-subtle shadow-sm mb-4">
                <div class="aitos-card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="bi bi-cpu-fill text-primary me-1"></i> AI Engine Configuration</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 fw-medium"><i class="bi bi-check-circle-fill me-1"></i> Locked & Active</span>
                </div>
                <div class="aitos-card-body">
                    <div class="alert alert-success border border-success-subtle bg-success-subtle text-dark small mb-4 d-flex align-items-center gap-2">
                        <i class="bi bi-nvidia text-success fs-5"></i>
                        <div>
                            <strong>NVIDIA Nemotron 3 Ultra (free)</strong> is set as your default AI engine via OpenRouter.
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="keyOpenAI" class="form-label fw-semibold">OpenRouter API Key</label>
                            <input type="password" class="form-control border-light-subtle" id="keyOpenAI" placeholder="sk-or-v1-...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Default AI Provider</label>
                            <input type="text" class="form-control border-light-subtle bg-light fw-medium" value="OpenAI / OpenRouter" readonly>
                            <select id="keyDefaultProvider" class="d-none">
                                <option value="openai" selected>OpenAI / OpenRouter</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Model Name</label>
                            <input type="text" class="form-control border-light-subtle bg-light fw-medium" value="NVIDIA: Nemotron 3 Ultra (free)" readonly>
                            <select id="keyModel" class="d-none">
                                <option value="nvidia/nemotron-3-ultra-550b-a55b:free" selected>Nemotron 3 Ultra (free)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">OpenRouter Model Slug</label>
                            <input type="text" class="form-control border-light-subtle bg-light font-monospace small" value="nvidia/nemotron-3-ultra-550b-a55b:free" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-primary px-5 py-3 fw-bold shadow-sm">Save Preferences</button>
            </div>
        </form>
    </div>

    <!-- Right column layout settings -->
    <div class="col-lg-4">
        <!-- Platform Plugins Card -->
        <div class="card aitos-card border-light-subtle shadow-sm mb-4">
            <div class="aitos-card-header bg-white py-3">
                <span class="fw-bold text-dark"><i class="bi bi-plugin text-primary me-1"></i> Platform Plugins</span>
            </div>
            <div class="aitos-card-body" style="max-height: 400px; overflow-y: auto;">
                @php
                    $plugins = \App\Services\Architect\Registry\PluginRegistry::scan();
                @endphp
                @if(empty($plugins))
                    <p class="text-muted small mb-0">No plugins found under `plugins/` directory.</p>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($plugins as $key => $manifest)
                            <div class="list-group-item px-0 py-2 border-light-subtle d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="text-dark small d-block">{{ $manifest['name'] }}</strong>
                                    <span class="text-muted" style="font-size: 0.75rem;">v{{ $manifest['version'] }} by {{ $manifest['author'] }}</span>
                                </div>
                                <span class="badge {{ $manifest['enabled'] ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $manifest['enabled'] ? 'Active' : 'Disabled' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="card aitos-card border-light-subtle shadow-sm mb-4">
            <div class="aitos-card-header bg-white">
                <span class="fw-bold"><i class="bi bi-palette text-primary me-1"></i> Theme & Display</span>
            </div>
            <div class="aitos-card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold d-block">App Color Scheme</label>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary active text-start py-2.5 px-3 d-flex align-items-center justify-content-between">
                            <span><i class="bi bi-sun-fill me-2"></i> Modern White (SaaS Minimal)</span>
                            <i class="bi bi-check-circle-fill"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary text-start py-2.5 px-3 opacity-50" disabled>
                            <span><i class="bi bi-moon-stars-fill me-2"></i> Dark Mode (Coming Soon)</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const MODEL_PRESETS = {
        gemini: [
            { value: "gemini-3.5-flash", label: "Gemini 3.5 Flash (Fast - Default)" },
            { value: "gemini-1.5-flash", label: "Gemini 1.5 Flash" },
            { value: "gemini-1.5-pro", label: "Gemini 1.5 Pro" },
            { value: "custom", label: "Custom Model ID..." }
        ],
        openai: [
            { value: "google/gemma-4-31b-it:free", label: "Gemma 4 31B - Free (Fast 2s)" },
            { value: "cohere/north-mini-code:free", label: "Cohere North Code - Free (Fast 3s)" },
            { value: "qwen/qwen-2.5-coder-32b-instruct", label: "Qwen 2.5 Coder (OpenRouter - 1.4s)" },
            { value: "qwen/qwen-3-coder-32b-instruct", label: "Qwen 3 Coder (OpenRouter)" },
            { value: "nvidia/llama-3.1-nemotron-70b-instruct", label: "Nemotron 70B (OpenRouter)" },
            { value: "gpt-4o-mini", label: "GPT-4o Mini (OpenAI)" },
            { value: "custom", label: "Custom Model ID..." }
        ],
        anthropic: [
            { value: "claude-3-haiku-20240307", label: "Claude 3 Haiku (Default)" },
            { value: "claude-3-5-sonnet-20240620", label: "Claude 3.5 Sonnet" },
            { value: "custom", label: "Custom Model ID..." }
        ]
    };

    document.addEventListener("DOMContentLoaded", () => {
        loadSettingsFromState();
    });

    function populateModels(provider, selectedModel) {
        const select = document.getElementById("keyModel");
        select.innerHTML = "";
        
        const presets = MODEL_PRESETS[provider] || [];
        let isPreset = false;
        
        presets.forEach(p => {
            const opt = document.createElement("option");
            opt.value = p.value;
            opt.textContent = p.label;
            select.appendChild(opt);
            if (p.value === selectedModel) {
                isPreset = true;
            }
        });

        const customGroup = document.getElementById("customModelGroup");
        const customInput = document.getElementById("keyCustomModel");

        if (selectedModel && !isPreset) {
            select.value = "custom";
            customInput.value = selectedModel;
            customGroup.classList.remove("d-none");
        } else {
            select.value = selectedModel || (presets[0] ? presets[0].value : "");
            customInput.value = "";
            customGroup.classList.add("d-none");
        }
    }

    function onProviderChange() {
        const provider = document.getElementById("keyDefaultProvider").value;
        const state = getProjectState();
        const model = getModelForProvider(provider, state);
        populateModels(provider, model);
    }

    function onModelChange() {
        const selectVal = document.getElementById("keyModel").value;
        const customGroup = document.getElementById("customModelGroup");
        if (selectVal === "custom") {
            customGroup.classList.remove("d-none");
            document.getElementById("keyCustomModel").value = "";
            document.getElementById("keyCustomModel").focus();
        } else {
            customGroup.classList.add("d-none");
        }
    }

    function loadSettingsFromState() {
        const state = getProjectState();

        document.getElementById("settingsName").value = state.projectName || "";
        document.getElementById("settingsDesc").value = state.projectDescription || "";
        
        // Settings triggers
        if (state.config) {
            document.getElementById("prefGitSync").checked = state.config.git_sync_enabled || false;
        }

        // Lock to OpenRouter Nemotron 3 Ultra free
        state.apiKeys = state.apiKeys || {};
        state.apiKeys.defaultProvider = "openai";
        state.apiKeys.openaiModel = "nvidia/nemotron-3-ultra-550b-a55b:free";
        saveProjectState(state);
    }

    function saveSettings() {
        const state = getProjectState();

        state.projectName = document.getElementById("settingsName").value.trim();
        state.projectDescription = document.getElementById("settingsDesc").value.trim();

        // Update config variables
        state.config = {
            git_sync_enabled: document.getElementById("prefGitSync").checked,
            last_compile_date: new Date().toISOString()
        };

        // Enforce active provider & model
        state.apiKeys = state.apiKeys || {};
        if (document.getElementById("keyOpenAI").value.trim() !== "") {
            state.apiKeys.openai = document.getElementById("keyOpenAI").value.trim();
        }
        state.apiKeys.defaultProvider = "openai";
        state.apiKeys.openaiModel = "nvidia/nemotron-3-ultra-550b-a55b:free";

        state.decisions.push({
            date: new Date().toISOString().slice(0, 16).replace('T', ' '),
            title: "Settings Saved",
            desc: "Updated core project metadata and AI engine integration keys."
        });

        saveProjectState(state);
        
        // Show premium toast notification
        showToast('Settings saved. Engine set to NVIDIA Nemotron 3 Ultra (free).', 'success');
    }
</script>
@endsection
