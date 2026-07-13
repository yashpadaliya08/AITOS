<!-- Sidebar Navigation Component -->
<div class="aitos-sidebar">
    <div class="sidebar-brand">
        <!-- Text logo with AI theme styling -->
        <span class="fs-4 fw-bold text-primary d-flex align-items-center gap-2">
            <i class="bi bi-cpu-fill"></i>
            AITOS
        </span>
        <span class="badge bg-light text-secondary border font-monospace" style="font-size: 0.65rem;">V2.0</span>
    </div>

    <div class="sidebar-menu">
        <div class="menu-label">Workspace</div>
        <a href="/dashboard" class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>
        <a href="/" class="menu-item {{ request()->is('/') ? 'active' : '' }}">
            <i class="bi bi-folder-fill"></i> Projects
        </a>

        <div class="menu-label mt-4">Development Lifecycle</div>
        
        <a href="/requirements" id="nav-requirements" data-icon="bi-card-checklist" class="menu-item {{ request()->is('requirements') ? 'active' : '' }} disabled">
            <i class="bi bi-lock-fill"></i> Requirements
        </a>
        
        <a href="/blueprint" id="nav-blueprint" data-icon="bi-journal-code" class="menu-item {{ request()->is('blueprint') ? 'active' : '' }} disabled">
            <i class="bi bi-lock-fill"></i> Blueprints
        </a>
        
        <a href="/team" id="nav-team" data-icon="bi-people-fill" class="menu-item {{ request()->is('team') ? 'active' : '' }} disabled">
            <i class="bi bi-lock-fill"></i> Team Planning
        </a>
        
        <a href="/compiler" id="nav-compiler" data-icon="bi-terminal-fill" class="menu-item {{ request()->is('compiler') ? 'active' : '' }} disabled">
            <i class="bi bi-lock-fill"></i> Context Compiler
        </a>
        
        <a href="/export" id="nav-export" data-icon="bi-download" class="menu-item {{ request()->is('export') || request()->is('preview') ? 'active' : '' }} disabled">
            <i class="bi bi-lock-fill"></i> Export Center
        </a>

        <div class="menu-label mt-4">Settings</div>
        <a href="/settings" class="menu-item {{ request()->is('settings') ? 'active' : '' }}">
            <i class="bi bi-gear-fill"></i> Settings
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.85rem; font-weight: 700;">
                HD
            </div>
            <div>
                <div class="fw-semibold text-truncate" style="max-width: 140px; font-size: 0.85rem;">Human Decider</div>
                <div class="text-muted" style="font-size: 0.7rem;">Local Environment</div>
            </div>
        </div>
    </div>
</div>
