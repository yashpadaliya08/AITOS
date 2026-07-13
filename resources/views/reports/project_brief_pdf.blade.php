<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $projectName }} — Project Brief</title>
    <style>
        /* Clean, print-friendly PDF styling */
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11pt;
            color: #1e293b;
            line-height: 1.6;
            padding: 40px;
        }

        h1 { font-size: 22pt; font-weight: 700; color: #0f172a; margin-bottom: 6px; }
        h2 { font-size: 14pt; font-weight: 700; color: #1e3a5f; margin-top: 28px; margin-bottom: 10px; padding-bottom: 6px; border-bottom: 2px solid #cbd5e1; }
        h3 { font-size: 12pt; font-weight: 700; color: #334155; margin-top: 18px; margin-bottom: 8px; }
        h4 { font-size: 10pt; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 14px; margin-bottom: 6px; }

        p { margin-bottom: 8px; }
        .muted { color: #64748b; }
        .subtitle { font-size: 11pt; color: #64748b; margin-bottom: 24px; }

        /* Cover section */
        .cover {
            border-bottom: 3px solid #1e3a5f;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .cover-meta {
            margin-top: 16px;
            font-size: 9pt;
            color: #64748b;
        }

        .cover-meta span {
            margin-right: 24px;
        }

        /* Stats row */
        .stats-row {
            display: table;
            width: 100%;
            margin: 16px 0 20px;
        }

        .stat-box {
            display: table-cell;
            text-align: center;
            padding: 12px 8px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .stat-num {
            font-size: 18pt;
            font-weight: 800;
            color: #2563eb;
            display: block;
        }

        .stat-lbl {
            font-size: 7pt;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #64748b;
            font-weight: 600;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 10pt;
        }

        th {
            background: #f1f5f9;
            color: #475569;
            font-weight: 700;
            padding: 8px 10px;
            text-align: left;
            border-bottom: 2px solid #cbd5e1;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        td {
            padding: 7px 10px;
            border-bottom: 1px solid #e2e8f0;
            color: #1e293b;
            vertical-align: top;
        }

        tr:last-child td { border-bottom: none; }

        code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 9pt;
            background: #f1f5f9;
            padding: 1px 4px;
            border-radius: 3px;
            color: #2563eb;
        }

        /* Lists */
        .item-list {
            list-style: none;
            padding: 0;
        }

        .item-list li {
            padding: 6px 0 6px 16px;
            border-left: 3px solid #2563eb;
            margin-bottom: 6px;
            margin-left: 0;
            font-size: 10pt;
            color: #334155;
        }

        .item-list.danger li { border-left-color: #dc2626; }
        .item-list.warning li { border-left-color: #ca8a04; }
        .item-list.success li { border-left-color: #16a34a; }

        /* Chips / inline tags */
        .chip {
            display: inline-block;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 9pt;
            color: #334155;
            margin: 2px 2px 2px 0;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .b-pk { background: #dbeafe; color: #1d4ed8; }
        .b-fk { background: #ffedd5; color: #9a3412; }
        .b-uk { background: #ede9fe; color: #6d28d9; }
        .b-null { background: #f1f5f9; color: #64748b; }
        .b-auto { background: #dcfce7; color: #166534; }
        .b-get { background: #dbeafe; color: #1d4ed8; }
        .b-post { background: #dcfce7; color: #15803d; }
        .b-put { background: #fef9c3; color: #a16207; }
        .b-patch { background: #ede9fe; color: #6d28d9; }
        .b-delete { background: #fee2e2; color: #b91c1c; }

        /* Blueprint text */
        .bp-text {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px 16px;
            font-family: 'Courier New', monospace;
            font-size: 9pt;
            line-height: 1.7;
            color: #334155;
            white-space: pre-wrap;
            word-break: break-word;
            max-height: none;
        }

        /* Section divider */
        .section-divider {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 24px 0;
        }

        /* Page breaks */
        .page-break { page-break-before: always; }

        /* Footer */
        .footer {
            margin-top: 40px;
            padding-top: 16px;
            border-top: 2px solid #cbd5e1;
            font-size: 9pt;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

{{-- ================================================================
     COVER
================================================================ --}}
@php
    $fw = $techStack['framework'] ?? ($projectModel['tech_stack']['framework'] ?? null);
    $db = $techStack['database']  ?? ($projectModel['tech_stack']['database']  ?? null);
    $fe = $techStack['frontend']  ?? ($projectModel['tech_stack']['frontend']  ?? null);

    $entityCount = count($projectModel['entities']  ?? []);
    $moduleCount = count($projectModel['modules']   ?? []);
    $tableCount  = count($databaseSchema['tables']  ?? []);
    $apiCount    = collect($apiDesign['resources']   ?? [])->sum(fn($r) => count($r['endpoints'] ?? []));
    $phaseCount  = count($projectPlan['development_order'] ?? $projectModel['phases'] ?? []);
    $ruleCount   = count($projectModel['business_rules'] ?? []);
@endphp

<div class="cover">
    <p class="muted" style="font-size: 9pt; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600;">AITOS — Project Brief</p>
    <h1>{{ $projectName }}</h1>

    @if($projectGoal)
        <p class="subtitle">{{ $projectGoal }}</p>
    @elseif($projectDescription)
        <p class="subtitle">{{ Str::limit($projectDescription, 300) }}</p>
    @endif

    @if($fw || $db || $fe)
        <p>
            <strong>Tech Stack:</strong>
            @if($fw) <span class="chip">{{ ucfirst($fw) }}</span> @endif
            @if($db) <span class="chip">{{ ucfirst($db) }}</span> @endif
            @if($fe) <span class="chip">{{ ucfirst($fe) }}</span> @endif
        </p>
    @endif

    <div class="cover-meta">
        <span><strong>Generated:</strong> {{ $generatedAt }}</span>
        <span><strong>Tool:</strong> AITOS Context Compiler</span>
        <span><strong>Status:</strong> Architecture Draft</span>
    </div>
</div>

{{-- Stats --}}
<div class="stats-row">
    <div class="stat-box"><span class="stat-num">{{ $entityCount }}</span><span class="stat-lbl">Entities</span></div>
    <div class="stat-box"><span class="stat-num">{{ $moduleCount }}</span><span class="stat-lbl">Modules</span></div>
    <div class="stat-box"><span class="stat-num">{{ $tableCount }}</span><span class="stat-lbl">DB Tables</span></div>
    <div class="stat-box"><span class="stat-num">{{ $apiCount }}</span><span class="stat-lbl">Endpoints</span></div>
    <div class="stat-box"><span class="stat-num">{{ $phaseCount }}</span><span class="stat-lbl">Phases</span></div>
    <div class="stat-box"><span class="stat-num">{{ $ruleCount }}</span><span class="stat-lbl">Rules</span></div>
</div>


{{-- ================================================================
     SECTION 1 — EXECUTIVE SUMMARY
================================================================ --}}
@php $projectSummary = $requirements['projectSummary'] ?? $projectDescription ?? ''; @endphp

<h2>1. Executive Summary</h2>

@if($projectSummary)
    <p>{{ $projectSummary }}</p>
@endif

@if(!empty($projectModel['roles']))
    <h4>User Roles</h4>
    <p>
        @foreach($projectModel['roles'] as $role)
            <span class="chip">{{ $role }}</span>
        @endforeach
    </p>
@endif

@if(!empty($projectModel['modules']))
    <h4>Core Modules</h4>
    <p>
        @foreach($projectModel['modules'] as $mod)
            <span class="chip">{{ $mod }}</span>
        @endforeach
    </p>
@endif


{{-- ================================================================
     SECTION 2 — ENTITIES
================================================================ --}}
@if(!empty($projectModel['entities']))
<h2>2. Entities Overview</h2>

<table>
    <thead>
        <tr><th>Entity</th><th>Key Attributes</th></tr>
    </thead>
    <tbody>
        @foreach($projectModel['entities'] as $entity)
        @php
            $eName  = is_array($entity) ? ($entity['name'] ?? '—') : $entity;
            $eAttrs = is_array($entity) ? ($entity['attributes'] ?? []) : [];
        @endphp
        <tr>
            <td><strong>{{ $eName }}</strong></td>
            <td>
                @forelse(array_slice($eAttrs, 0, 8) as $attr)
                    <code>{{ $attr }}</code>{{ !$loop->last ? ', ' : '' }}
                @empty
                    <span class="muted">—</span>
                @endforelse
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif


{{-- ================================================================
     SECTION 3 — DATABASE SCHEMA
================================================================ --}}
@if(!empty($databaseSchema['tables']))
<h2>3. Database Schema</h2>

@foreach($databaseSchema['tables'] as $table)
<h3>{{ strtoupper($table['name']) }}</h3>
<table>
    <thead>
        <tr><th>Column</th><th>Type</th><th>Constraints</th></tr>
    </thead>
    <tbody>
        @foreach($table['columns'] as $col)
        @php
            $flags = [];
            if ($col['primary_key']    ?? false) $flags[] = '<span class="badge b-pk">PK</span>';
            if ($col['auto_increment'] ?? false) $flags[] = '<span class="badge b-auto">AUTO</span>';
            if (($col['unique'] ?? false) && !($col['primary_key'] ?? false)) $flags[] = '<span class="badge b-uk">UNIQUE</span>';
            if ($col['nullable'] ?? false) $flags[] = '<span class="badge b-null">NULL</span>';
            if (isset($col['foreign_key'])) {
                $on  = $col['foreign_key']['on'] ?? '?';
                $ref = $col['foreign_key']['references'] ?? 'id';
                $flags[] = "<span class=\"badge b-fk\">FK → {$on}.{$ref}</span>";
            }
        @endphp
        <tr>
            <td><code>{{ $col['name'] }}</code></td>
            <td><span class="muted" style="font-family: 'Courier New', monospace; font-size: 9pt;">{{ $col['type'] }}</span></td>
            <td>{!! implode(' ', $flags) !!}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endforeach
@endif


{{-- ================================================================
     SECTION 4 — API DESIGN
================================================================ --}}
@if(!empty($apiDesign['resources']))
<div class="page-break"></div>
<h2>4. API Endpoints</h2>

@foreach($apiDesign['resources'] as $resource)
<h3>{{ $resource['name'] ?? 'Resource' }}</h3>
<table>
    <thead>
        <tr><th>Method</th><th>Endpoint</th><th>Description</th></tr>
    </thead>
    <tbody>
        @foreach($resource['endpoints'] ?? [] as $ep)
        @php
            $method = strtoupper($ep['method'] ?? 'GET');
            $badgeClass = match($method) {
                'POST'   => 'b-post',
                'PUT'    => 'b-put',
                'PATCH'  => 'b-patch',
                'DELETE' => 'b-delete',
                default  => 'b-get',
            };
        @endphp
        <tr>
            <td><span class="badge {{ $badgeClass }}">{{ $method }}</span></td>
            <td><code>{{ $ep['path'] ?? '' }}</code></td>
            <td>{{ $ep['description'] ?? '' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endforeach
@endif


{{-- ================================================================
     SECTION 5 — BUSINESS RULES
================================================================ --}}
@if(!empty($projectModel['business_rules']))
<h2>5. Business Rules</h2>
<ul class="item-list">
    @foreach($projectModel['business_rules'] as $rule)
        <li>{{ $rule }}</li>
    @endforeach
</ul>
@endif


{{-- ================================================================
     SECTION 6 — FUNCTIONAL REQUIREMENTS
================================================================ --}}
@if(!empty($projectModel['functional_requirements']))
<h2>6. Functional Requirements</h2>
<ul class="item-list">
    @foreach($projectModel['functional_requirements'] as $req)
        <li>{{ $req }}</li>
    @endforeach
</ul>
@endif

@if(!empty($projectModel['non_functional_requirements']))
<h3>Non-Functional Requirements</h3>
<ul class="item-list">
    @foreach($projectModel['non_functional_requirements'] as $req)
        <li>{{ $req }}</li>
    @endforeach
</ul>
@endif


{{-- ================================================================
     SECTION 7 — USER STORIES
================================================================ --}}
@if(!empty($projectModel['user_stories']))
<h2>7. User Stories</h2>
<ul class="item-list">
    @foreach($projectModel['user_stories'] as $story)
        <li>{{ $story }}</li>
    @endforeach
</ul>
@endif


{{-- ================================================================
     SECTION 8 — DEVELOPMENT PHASES / PLAN
================================================================ --}}
@php $devOrder = $projectPlan['development_order'] ?? $projectModel['phases'] ?? []; @endphp
@if(!empty($devOrder))
<div class="page-break"></div>
<h2>8. Development Phases</h2>
<table>
    <thead>
        <tr><th>#</th><th>Phase</th></tr>
    </thead>
    <tbody>
        @foreach($devOrder as $idx => $phase)
        <tr>
            <td><strong>{{ $idx + 1 }}</strong></td>
            <td>{{ is_array($phase) ? ($phase['task'] ?? json_encode($phase)) : $phase }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif


{{-- ================================================================
     SECTION 9 — RISKS & ASSUMPTIONS
================================================================ --}}
@if(!empty($projectModel['risks']) || !empty($projectModel['assumptions']))
<h2>9. Risks & Assumptions</h2>

@if(!empty($projectModel['risks']))
<h4>Risks</h4>
<ul class="item-list danger">
    @foreach($projectModel['risks'] as $risk)
        <li>{{ $risk }}</li>
    @endforeach
</ul>
@endif

@if(!empty($projectModel['assumptions']))
<h4>Assumptions</h4>
<ul class="item-list warning">
    @foreach($projectModel['assumptions'] as $assumption)
        <li>{{ $assumption }}</li>
    @endforeach
</ul>
@endif
@endif


{{-- ================================================================
     SECTION 10 — BLUEPRINTS (raw text)
================================================================ --}}
@php $bpTexts = []; @endphp
@foreach(['business' => 'Business Blueprint', 'database' => 'Database Blueprint', 'technical' => 'Technical Blueprint', 'ui' => 'UI Blueprint'] as $bpKey => $bpLabel)
    @php $bpVal = $blueprints[$bpKey] ?? ''; @endphp
    @if(!empty($bpVal))
        @php $bpTexts[$bpLabel] = is_array($bpVal) ? json_encode($bpVal, JSON_PRETTY_PRINT) : $bpVal; @endphp
    @endif
@endforeach

@if(!empty($bpTexts))
<div class="page-break"></div>
<h2>10. Blueprint Documents</h2>

@foreach($bpTexts as $label => $text)
<h3>{{ $label }}</h3>
<div class="bp-text">{{ Str::limit($text, 3000) }}</div>
@endforeach
@endif


{{-- ================================================================
     FOOTER
================================================================ --}}
<div class="footer">
    <strong>{{ $projectName }}</strong> — Project Brief &nbsp;|&nbsp;
    Generated by AITOS Context Compiler on {{ $generatedAt }} &nbsp;|&nbsp;
    Page numbers apply when printed
</div>

</body>
</html>
