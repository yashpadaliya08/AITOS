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
                <div class="aitos-card-header bg-white">
                    <span class="fw-bold"><i class="bi bi-cpu-fill text-primary me-1"></i> AI Engine Configuration</span>
                </div>
                <div class="aitos-card-body">
                    <div class="alert alert-light border small text-muted mb-4">
                        <i class="bi bi-shield-lock-fill text-secondary me-1"></i> API keys are stored strictly in your local browser storage and are sent only to the local backend during analysis requests.
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="keyGemini" class="form-label fw-semibold">Gemini API Key</label>
                            <input type="password" class="form-control border-light-subtle" id="keyGemini" placeholder="AIzaSy...">
                        </div>
                        <div class="col-md-6">
                            <label for="keyOpenAI" class="form-label fw-semibold">OpenAI API Key</label>
                            <input type="password" class="form-control border-light-subtle" id="keyOpenAI" placeholder="sk-proj-...">
                        </div>
                        <div class="col-md-6">
                            <label for="keyAnthropic" class="form-label fw-semibold">Anthropic API Key</label>
                            <input type="password" class="form-control border-light-subtle" id="keyAnthropic" placeholder="sk-ant-...">
                        </div>
                        <div class="col-md-6">
                            <label for="keyDefaultProvider" class="form-label fw-semibold">Default AI Provider</label>
                            <select id="keyDefaultProvider" class="form-select border-light-subtle">
                                <option value="gemini">Gemini (Default / Recommended)</option>
                                <option value="openai">OpenAI (gpt-4o-mini)</option>
                                <option value="anthropic">Anthropic (Claude 3)</option>
                            </select>
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
    document.addEventListener("DOMContentLoaded", () => {
        loadSettingsFromState();
    });

    function loadSettingsFromState() {
        const state = getProjectState();

        document.getElementById("settingsName").value = state.projectName || "";
        document.getElementById("settingsDesc").value = state.projectDescription || "";
        
        // Settings triggers
        if (state.config) {
            document.getElementById("prefGitSync").checked = state.config.git_sync_enabled || false;
        }

        // Load keys
        if (state.apiKeys) {
            document.getElementById("keyGemini").value = state.apiKeys.gemini || "";
            document.getElementById("keyOpenAI").value = state.apiKeys.openai || "";
            document.getElementById("keyAnthropic").value = state.apiKeys.anthropic || "";
            document.getElementById("keyDefaultProvider").value = state.apiKeys.defaultProvider || "openai";
        }
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

        // Update API keys
        state.apiKeys = {
            gemini: document.getElementById("keyGemini").value.trim(),
            openai: document.getElementById("keyOpenAI").value.trim(),
            anthropic: document.getElementById("keyAnthropic").value.trim(),
            defaultProvider: document.getElementById("keyDefaultProvider").value
        };

        state.decisions.push({
            date: new Date().toISOString().slice(0, 16).replace('T', ' '),
            title: "Settings Saved",
            desc: "Updated core project metadata and AI engine integration keys."
        });

        saveProjectState(state);
        
        // Show success alert
        alert("Settings saved successfully. Layout elements have been re-rendered.");
    }
</script>
@endsection
