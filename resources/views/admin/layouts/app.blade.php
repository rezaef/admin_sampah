<!DOCTYPE html>
<html lang="id" data-theme="dark">
<head>
    <script>
        (function() {
            const savedTheme = localStorage.getItem('admin-theme') || 'dark';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') · Sampah Detector</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0c1222;
            --bg-surface: rgba(15, 23, 42, 0.6);
            --card: rgba(30, 41, 59, 0.5);
            --card-solid: #1e293b;
            --card-hover: rgba(30, 41, 59, 0.7);
            --text: #f1f5f9;
            --text-2: #94a3b8;
            --text-3: #64748b;
            --line: rgba(148, 163, 184, 0.1);
            --line-strong: rgba(148, 163, 184, 0.18);
            --primary: #22c55e;
            --primary-light: rgba(34, 197, 94, 0.1);
            --primary-glow: rgba(34, 197, 94, 0.15);
            --primary-dark: #16a34a;
            --accent: #0ea5e9;
            --accent-light: rgba(14, 165, 233, 0.1);
            --danger: #ef4444;
            --danger-light: rgba(239, 68, 68, 0.1);
            --warning: #f59e0b;
            --warning-light: rgba(245, 158, 11, 0.1);
            --success: #22c55e;
            --success-light: rgba(34, 197, 94, 0.1);
            --sidebar-bg: rgba(2, 6, 23, 0.85);
            --sidebar-text: rgba(248,250,252,.75);
            --sidebar-active-bg: rgba(34, 197, 94, 0.12);
            --sidebar-active-text: #4ade80;
            --sidebar-hover-bg: rgba(248,250,252,.05);
            --radius: 16px;
            --radius-sm: 10px;
            --shadow: 0 1px 3px rgba(0,0,0,.2), 0 1px 2px rgba(0,0,0,.15);
            --shadow-md: 0 8px 32px rgba(0,0,0,.25);
            --shadow-glow: 0 0 20px rgba(34, 197, 94, 0.08);
            --glass-blur: blur(20px);
            --glass-border: 1px solid rgba(148, 163, 184, 0.08);
            --grid-color: rgba(148, 163, 184, 0.03);
            --orb-color: rgba(34, 197, 94, 0.06);
            --input-bg: rgba(15, 23, 42, 0.5);
            --input-focus-bg: rgba(15, 23, 42, 0.7);
        }

        [data-theme="light"] {
            --bg: #f8fafc;
            --bg-surface: rgba(255, 255, 255, 0.6);
            --card: rgba(255, 255, 255, 0.7);
            --card-solid: #ffffff;
            --card-hover: rgba(255, 255, 255, 0.95);
            --text: #0f172a;
            --text-2: #475569;
            --text-3: #94a3b8;
            --line: rgba(148, 163, 184, 0.2);
            --line-strong: rgba(148, 163, 184, 0.35);
            --sidebar-bg: rgba(255, 255, 255, 0.9);
            --sidebar-text: #334155;
            --sidebar-active-bg: rgba(34, 197, 94, 0.1);
            --sidebar-active-text: #16a34a;
            --sidebar-hover-bg: rgba(148, 163, 184, 0.08);
            --glass-border: 1px solid rgba(148, 163, 184, 0.15);
            --shadow: 0 1px 3px rgba(15,23,42,0.08), 0 1px 2px rgba(15,23,42,0.04);
            --shadow-md: 0 10px 30px rgba(15,23,42,0.08);
            --grid-color: rgba(15, 23, 42, 0.03);
            --orb-color: rgba(34, 197, 94, 0.05);
            --input-bg: #ffffff;
            --input-focus-bg: #ffffff;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text);
            font-size: 14px;
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        a { color: inherit; text-decoration: none; }
        input, textarea, select, button { font-family: inherit; }

        /* ── Custom Scrollbar ── */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(148,163,184,0.2); border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(148,163,184,0.35); }

        /* ── Background Grid ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(var(--grid-color) 1px, transparent 1px),
                linear-gradient(90deg, var(--grid-color) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
            z-index: 0;
        }
        /* Ambient gradient orbs */
        body::after {
            content: '';
            position: fixed;
            top: -20%; left: -10%;
            width: 60vw; height: 60vh;
            background: radial-gradient(ellipse, var(--orb-color) 0%, transparent 60%);
            pointer-events: none;
            z-index: 0;
        }

        /* ── Layout Shell ── */
        .shell {
            display: grid;
            grid-template-columns: 260px 1fr;
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }

        /* ═══════════════════════════════════════════
           Sidebar — Frosted Dark Glass
           ═══════════════════════════════════════════ */
        .sidebar {
            background: var(--sidebar-bg);
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            border-right: 1px solid rgba(148,163,184,0.06);
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 50;
        }
        .sidebar-inner { padding: 24px 16px; display: flex; flex-direction: column; gap: 32px; flex: 1; }
        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 4px 8px;
        }
        .brand-icon {
            width: 42px; height: 42px;
            background: linear-gradient(135deg, #16a34a, #0ea5e9);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
            box-shadow: 0 4px 14px rgba(22,163,74,0.3);
            transition: transform 0.3s cubic-bezier(0.23,1,0.32,1), box-shadow 0.3s;
        }
        .brand:hover .brand-icon {
            transform: rotate(-6deg) scale(1.05);
            box-shadow: 0 6px 20px rgba(22,163,74,0.4);
        }
        .brand-text { display: flex; flex-direction: column; }
        .brand-name { font-size: 15px; font-weight: 800; color: var(--text); letter-spacing: -.3px; }
        .brand-sub { font-size: 10px; color: var(--text-3); font-weight: 600; letter-spacing: 1.2px; text-transform: uppercase; }

        .nav-section-label {
            font-size: 10px;
            font-weight: 700;
            color: var(--text-3);
            opacity: 0.8;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            padding: 0 8px;
            margin-bottom: 6px;
        }
        .nav { display: flex; flex-direction: column; gap: 2px; }
        .nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            color: var(--sidebar-text);
            font-weight: 500;
            font-size: 13.5px;
            transition: all 0.2s cubic-bezier(0.23,1,0.32,1);
            position: relative;
        }
        .nav a .nav-icon { font-size: 17px; width: 22px; text-align: center; flex-shrink: 0; transition: transform 0.2s; }
        .nav a:hover {
            background: var(--sidebar-hover-bg);
            color: var(--text);
            transform: translateX(3px);
        }
        .nav a:hover .nav-icon { transform: scale(1.15); }
        .nav a.active {
            background: var(--sidebar-active-bg);
            color: var(--sidebar-active-text);
            font-weight: 700;
            box-shadow: inset 3px 0 0 var(--primary);
        }
        .nav a.active .nav-icon { filter: none; }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid rgba(248,250,252,.06);
            margin-top: auto;
        }
        .sidebar-footer .admin-info { display: flex; align-items: center; gap: 10px; }
        .admin-avatar {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, #16a34a, #0ea5e9);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 13px; color: #fff;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(22,163,74,0.25);
        }
        .admin-meta { flex: 1; min-width: 0; }
        .admin-name { font-size: 13px; font-weight: 700; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .admin-role { font-size: 11px; color: var(--text-3); }

        /* ═══════════════════════════════════════════
           Main Content Area
           ═══════════════════════════════════════════ */
        .main { min-width: 0; display: flex; flex-direction: column; }

        /* ── Topbar — Frosted Glass ── */
        .topbar {
            background: var(--bg-surface);
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            border-bottom: 1px solid var(--line);
            padding: 16px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            position: sticky;
            top: 0;
            z-index: 40;
        }
        .page-title h1 { font-size: 22px; font-weight: 800; letter-spacing: -.4px; color: var(--text); }
        .page-title p { font-size: 13px; color: var(--text-2); margin-top: 2px; }

        .topbar-right { display: flex; align-items: center; gap: 12px; }
        .live-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            background: var(--success-light);
            color: var(--success);
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            border: 1px solid rgba(34,197,94,0.15);
        }
        .live-dot {
            width: 7px; height: 7px;
            background: var(--success);
            border-radius: 50%;
            animation: pulse-dot 2s ease-in-out infinite;
            box-shadow: 0 0 6px var(--success);
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: .4; transform: scale(.7); }
        }

        /* ═══════════════════════════════════════════
           Content Area — Page Entrance Animation
           ═══════════════════════════════════════════ */
        .content {
            padding: 24px 28px;
            flex: 1;
            animation: content-enter 0.5s cubic-bezier(0.23,1,0.32,1) both;
        }
        @keyframes content-enter {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .section { display: grid; gap: 20px; }

        /* ═══════════════════════════════════════════
           Cards — Dark Glassmorphism
           ═══════════════════════════════════════════ */
        .card {
            background: var(--card);
            border: var(--glass-border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 20px;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        .card:hover {
            border-color: rgba(148,163,184,0.14);
            box-shadow: var(--shadow-md);
        }
        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
            gap: 12px;
        }
        .card-title { font-size: 15px; font-weight: 700; color: var(--text); }
        .card-sub { font-size: 12px; color: var(--text-2); margin-top: 2px; }

        /* ═══════════════════════════════════════════
           Stat Cards — Animated Gradient Top Bar
           ═══════════════════════════════════════════ */
        .grid-4 { display: grid; gap: 16px; grid-template-columns: repeat(4, minmax(0, 1fr)); }
        .grid-3 { display: grid; gap: 16px; grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .grid-2 { display: grid; gap: 16px; grid-template-columns: repeat(2, minmax(0, 1fr)); }

        .stat-card {
            background: var(--card);
            border: var(--glass-border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            transition: transform 0.3s cubic-bezier(0.23,1,0.32,1), box-shadow 0.3s, border-color 0.3s;
            /* Staggered entrance */
            opacity: 0;
            animation: stat-enter 0.6s cubic-bezier(0.23,1,0.32,1) both;
        }
        .stat-card:nth-child(1) { animation-delay: 0.05s; }
        .stat-card:nth-child(2) { animation-delay: 0.1s; }
        .stat-card:nth-child(3) { animation-delay: 0.15s; }
        .stat-card:nth-child(4) { animation-delay: 0.2s; }
        .stat-card:nth-child(5) { animation-delay: 0.25s; }
        .stat-card:nth-child(6) { animation-delay: 0.3s; }
        .stat-card:nth-child(7) { animation-delay: 0.35s; }
        .stat-card:nth-child(8) { animation-delay: 0.4s; }

        @keyframes stat-enter {
            from {
                opacity: 0;
                transform: translateY(16px) scale(0.97);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
            border-color: rgba(148,163,184,0.14);
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
        }
        .stat-card.green::before { background: linear-gradient(90deg, #16a34a, #4ade80); }
        .stat-card.blue::before { background: linear-gradient(90deg, #0ea5e9, #38bdf8); }
        .stat-card.orange::before { background: linear-gradient(90deg, #f97316, #fb923c); }
        .stat-card.purple::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }
        .stat-card.red::before { background: linear-gradient(90deg, #dc2626, #f87171); }
        .stat-card.teal::before { background: linear-gradient(90deg, #0d9488, #2dd4bf); }
        .stat-card.yellow::before { background: linear-gradient(90deg, #ca8a04, #facc15); }
        .stat-card.indigo::before { background: linear-gradient(90deg, #4f46e5, #818cf8); }

        /* Hover glow per color */
        .stat-card.green:hover { box-shadow: var(--shadow-md), 0 0 24px rgba(34,197,94,0.08); }
        .stat-card.blue:hover { box-shadow: var(--shadow-md), 0 0 24px rgba(14,165,233,0.08); }
        .stat-card.orange:hover { box-shadow: var(--shadow-md), 0 0 24px rgba(249,115,22,0.08); }
        .stat-card.purple:hover { box-shadow: var(--shadow-md), 0 0 24px rgba(139,92,246,0.08); }
        .stat-card.red:hover { box-shadow: var(--shadow-md), 0 0 24px rgba(239,68,68,0.08); }
        .stat-card.teal:hover { box-shadow: var(--shadow-md), 0 0 24px rgba(13,148,136,0.08); }
        .stat-card.yellow:hover { box-shadow: var(--shadow-md), 0 0 24px rgba(202,138,4,0.08); }
        .stat-card.indigo:hover { box-shadow: var(--shadow-md), 0 0 24px rgba(79,70,229,0.08); }

        .stat-icon {
            width: 44px; height: 44px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            transition: transform 0.3s cubic-bezier(0.23,1,0.32,1);
        }
        .stat-card:hover .stat-icon { transform: scale(1.12) rotate(-4deg); }
        .stat-icon.green { background: rgba(34,197,94,0.12); }
        .stat-icon.blue { background: rgba(14,165,233,0.12); }
        .stat-icon.orange { background: rgba(249,115,22,0.12); }
        .stat-icon.purple { background: rgba(139,92,246,0.12); }
        .stat-icon.red { background: rgba(239,68,68,0.12); }
        .stat-icon.teal { background: rgba(13,148,136,0.12); }
        .stat-icon.yellow { background: rgba(202,138,4,0.12); }
        .stat-icon.indigo { background: rgba(79,70,229,0.12); }

        .stat-label { font-size: 12px; font-weight: 600; color: var(--text-2); text-transform: uppercase; letter-spacing: .5px; }
        .stat-value { font-size: 30px; font-weight: 900; letter-spacing: -1px; line-height: 1; color: var(--text); }
        .stat-value[data-counter] { transition: none; }

        /* ═══════════════════════════════════════════
           Tables — Dark Premium
           ═══════════════════════════════════════════ */
        table { width: 100%; border-collapse: collapse; }
        thead tr { border-bottom: 1px solid var(--line-strong); }
        th {
            font-size: 11px;
            font-weight: 700;
            color: var(--text-3);
            text-transform: uppercase;
            letter-spacing: .7px;
            padding: 12px 14px;
            text-align: left;
            white-space: nowrap;
        }
        td { padding: 14px; border-bottom: 1px solid var(--line); vertical-align: middle; color: var(--text); }
        tbody tr { transition: background 0.2s ease, box-shadow 0.2s ease; }
        tbody tr:hover { background: rgba(148,163,184,0.04); }
        tbody tr:last-child td { border-bottom: none; }

        /* ── Scrollable Table Wrapper ── */
        .tbl-scroll { overflow-x: auto; -webkit-overflow-scrolling: touch; }

        /* ═══════════════════════════════════════════
           Badges — with subtle glow
           ═══════════════════════════════════════════ */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11.5px;
            font-weight: 700;
            white-space: nowrap;
            transition: box-shadow 0.2s ease;
        }
        .badge-success { color: #4ade80; background: rgba(34,197,94,0.12); border: 1px solid rgba(34,197,94,0.15); }
        .badge-warning { color: #fbbf24; background: rgba(245,158,11,0.12); border: 1px solid rgba(245,158,11,0.15); }
        .badge-danger { color: #f87171; background: rgba(239,68,68,0.12); border: 1px solid rgba(239,68,68,0.15); }
        .badge-neutral { color: var(--text-2); background: rgba(148,163,184,0.1); border: 1px solid rgba(148,163,184,0.1); }
        .badge-blue { color: #60a5fa; background: rgba(59,130,246,0.12); border: 1px solid rgba(59,130,246,0.15); }
        .badge-purple { color: #c084fc; background: rgba(168,85,247,0.12); border: 1px solid rgba(168,85,247,0.15); }

        /* ═══════════════════════════════════════════
           Buttons — Shimmer & Glass
           ═══════════════════════════════════════════ */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 16px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--line-strong);
            background: var(--card);
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            transition: all 0.25s cubic-bezier(0.23,1,0.32,1);
            white-space: nowrap;
            position: relative;
            overflow: hidden;
        }
        .btn:hover {
            background: var(--card-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            border-color: rgba(148,163,184,0.2);
        }
        .btn:active { transform: translateY(0) scale(0.98); }
        /* Shimmer sweep on hover */
        .btn::after {
            content: '';
            position: absolute;
            top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.06), transparent);
            pointer-events: none;
        }
        .btn:hover::after { animation: btn-sweep 0.6s ease-out; }
        @keyframes btn-sweep { to { left: 100%; } }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            color: #fff;
            border-color: rgba(34,197,94,0.3);
            box-shadow: 0 4px 14px rgba(34,197,94,0.2);
        }
        .btn-primary:hover {
            box-shadow: 0 6px 20px rgba(34,197,94,0.3);
            border-color: rgba(34,197,94,0.4);
        }
        .btn-danger {
            background: rgba(239,68,68,0.12);
            color: #f87171;
            border-color: rgba(239,68,68,0.2);
        }
        .btn-danger:hover {
            background: rgba(239,68,68,0.2);
            box-shadow: 0 4px 12px rgba(239,68,68,0.15);
        }
        .btn-sm { padding: 6px 12px; font-size: 12px; }

        /* ═══════════════════════════════════════════
           Form Elements — Enhanced Focus
           ═══════════════════════════════════════════ */
        .field { display: grid; gap: 6px; }
        .field label { font-size: 13px; font-weight: 600; color: var(--text-2); transition: color 0.2s; }
        .field:focus-within label { color: var(--primary); }
        .field input,
        .field textarea,
        .field select,
        select.inline-select {
            width: 100%;
            padding: 11px 14px;
            border-radius: var(--radius-sm);
            border: 1.5px solid var(--line-strong);
            background: var(--input-bg);
            font-size: 13px;
            color: var(--text);
            transition: all 0.25s cubic-bezier(0.23,1,0.32,1);
            outline: none;
        }
        .field input::placeholder,
        .field textarea::placeholder { color: var(--text-3); }
        .field input:focus,
        .field textarea:focus,
        .field select:focus,
        select.inline-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-glow), 0 0 16px rgba(34,197,94,0.06);
            background: var(--input-focus-bg);
        }
        .field textarea { min-height: 120px; resize: vertical; }

        /* Checkbox modernization */
        .field input[type="checkbox"] {
            width: 18px; height: 18px;
            accent-color: var(--primary);
            cursor: pointer;
        }

        /* ═══════════════════════════════════════════
           Flash Messages — Slide In + Auto-Dismiss
           ═══════════════════════════════════════════ */
        .flash {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 14px 18px;
            border-radius: var(--radius-sm);
            font-size: 13.5px;
            font-weight: 600;
            margin-bottom: 16px;
            animation: flash-slide-in 0.5s cubic-bezier(0.23,1,0.32,1) both;
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }
        .flash::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0;
            height: 3px;
            background: currentColor;
            opacity: 0.3;
            animation: flash-timer 5s linear forwards;
        }
        @keyframes flash-slide-in {
            from { opacity: 0; transform: translateY(-10px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes flash-timer {
            from { width: 100%; }
            to { width: 0%; }
        }
        .flash-success {
            background: rgba(34,197,94,0.1);
            color: #4ade80;
            border: 1px solid rgba(34,197,94,0.15);
        }
        .flash-error {
            background: rgba(239,68,68,0.1);
            color: #f87171;
            border: 1px solid rgba(239,68,68,0.15);
        }

        /* ── Toolbar ── */
        .toolbar { display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; }

        /* ── Actions ── */
        .actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

        /* ═══════════════════════════════════════════
           Pagination — Dark Theme
           ═══════════════════════════════════════════ */
        .pagination { margin-top: 16px; }
        .pagination nav { display: flex; justify-content: flex-end; }
        .pagination .page-item .page-link {
            padding: 7px 13px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid var(--line);
            color: var(--text-2);
            background: transparent;
            transition: all 0.2s;
        }
        .pagination .page-item.active .page-link {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
            box-shadow: 0 2px 8px rgba(34,197,94,0.25);
        }
        .pagination .page-item .page-link:hover { background: rgba(148,163,184,0.08); }

        /* ── Stack (vertical gap) ── */
        .stack { display: grid; gap: 16px; }

        /* ── Utilities ── */
        .muted { color: var(--text-2); }
        .text-sm { font-size: 12px; }
        .font-bold { font-weight: 700; }
        .truncate { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 220px; }

        /* ═══════════════════════════════════════════
           Sidebar Overlay & Toggles
           ═══════════════════════════════════════════ */
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text);
            padding: 8px;
            border-radius: var(--radius-sm);
            align-items: center;
            justify-content: center;
            transition: background 0.15s;
        }
        .menu-toggle:hover {
            background: rgba(148,163,184,0.1);
        }
        .sidebar-close {
            display: none;
            background: none;
            border: none;
            color: var(--sidebar-text);
            cursor: pointer;
            padding: 8px;
            border-radius: var(--radius-sm);
            align-items: center;
            justify-content: center;
            margin-left: auto;
            transition: background 0.15s, color 0.15s;
        }
        .sidebar-close:hover {
            background: var(--sidebar-hover-bg);
            color: #f8fafc;
        }
        .sidebar-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(2, 6, 23, 0.6);
            backdrop-filter: blur(6px);
            z-index: 45;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease;
        }
        .sidebar-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        /* ═══════════════════════════════════════════
           Responsive
           ═══════════════════════════════════════════ */
        @media (max-width: 1200px) {
            .grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 900px) {
            .shell { grid-template-columns: 1fr; }
            .sidebar { position: fixed; width: 260px; transform: translateX(-100%); transition: transform .25s cubic-bezier(0.23,1,0.32,1); }
            .sidebar.open { transform: translateX(0); }
            .grid-4, .grid-3, .grid-2 { grid-template-columns: 1fr; }
            .content { padding: 16px; }
            .topbar { padding: 14px 16px; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
            .menu-toggle { display: flex; }
            .sidebar-close { display: flex; }
            .topbar-left { display: flex; align-items: center; gap: 12px; }
        }

        /* ── Smooth row highlight for realtime updates ── */
        @keyframes highlight-row {
            0% { background: rgba(34,197,94,0.08); }
            100% { background: transparent; }
        }
        .row-new { animation: highlight-row 2s ease-out forwards; }

        /* Logout button style */
        .btn-logout {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--sidebar-text);
            opacity: 0.6;
            font-size: 18px;
            padding: 4px;
            transition: color .2s, opacity .2s;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-logout:hover {
            color: var(--danger);
            opacity: 1;
        }

        /* ── Toast Container & Toast Notification ── */
        #toast-container {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 10000;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 380px;
            width: calc(100vw - 48px);
            pointer-events: none;
        }
        .toast-notif {
            pointer-events: auto;
            background: var(--card-solid);
            border: var(--glass-border);
            border-left: 5px solid var(--primary);
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            border-radius: var(--radius-sm);
            padding: 14px 16px;
            color: var(--text);
            box-shadow: var(--shadow-md);
            display: flex;
            align-items: flex-start;
            gap: 12px;
            transform: translateY(20px);
            opacity: 0;
            transition: transform 0.35s cubic-bezier(0.23, 1, 0.32, 1), opacity 0.35s ease;
        }
        .toast-notif.show {
            transform: translateY(0);
            opacity: 1;
        }
        .toast-notif-icon {
            width: 32px; height: 32px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; flex-shrink: 0;
            background: rgba(148,163,184,0.08);
        }
        .toast-notif.tinggi { border-left-color: var(--danger); }
        .toast-notif.tinggi .toast-notif-icon { background: var(--danger-light); }
        .toast-notif.sedang { border-left-color: var(--warning); }
        .toast-notif.sedang .toast-notif-icon { background: var(--warning-light); }
        .toast-notif.rendah { border-left-color: var(--success); }
        .toast-notif.rendah .toast-notif-icon { background: var(--success-light); }
        
        .toast-notif-body { flex: 1; min-width: 0; }
        .toast-notif-title { font-weight: 800; font-size: 13px; color: var(--text); margin-bottom: 2px; }
        .toast-notif-msg { font-size: 12px; color: var(--text-2); line-height: 1.4; word-break: break-word; }
        .toast-notif-close { cursor: pointer; font-size: 16px; color: var(--text-3); border: none; background: none; line-height: 1; padding: 0 4px; }
        .toast-notif-close:hover { color: var(--text); }

        /* ── Global Lightbox Modal ── */
        .lightbox {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,.85);
            z-index: 999999;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .lightbox.open { display: flex; }
        .lightbox-content {
            position: relative;
            display: inline-block;
            max-width: 90vw;
            max-height: 85vh;
        }
        .lightbox img {
            display: block;
            max-width: 90vw;
            max-height: 85vh;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,.5);
        }
        .lightbox-close {
            position: absolute;
            top: -16px;
            right: -16px;
            width: 34px;
            height: 34px;
            background: var(--card-solid);
            color: var(--text);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            cursor: pointer;
            border: 1px solid var(--glass-border);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            transition: transform 0.15s, background-color 0.15s, border-color 0.15s;
            z-index: 1000001;
            line-height: 1;
        }
        .lightbox-close:hover {
            transform: scale(1.1);
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="shell">
    <div class="sidebar-overlay" id="sidebar-overlay"></div>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-inner">
            <div class="brand" style="width: 100%;">
                <div class="brand-icon" style="padding: 0; overflow: hidden; background: none;">
                    <img src="{{ asset('logo.png') }}" alt="Logo" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div class="brand-text">
                    <span class="brand-name">Sampah Detector</span>
                    <span class="brand-sub">Admin Panel</span>
                </div>
                <button type="button" class="sidebar-close" id="sidebar-close" aria-label="Tutup Menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            <div>
                <p class="nav-section-label">Menu Utama</p>
                <nav class="nav">
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span class="nav-icon">🏠</span> Dashboard
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <span class="nav-icon">👥</span> Pengguna
                    </a>
                    <a href="{{ route('admin.classifications.index') }}" class="{{ request()->routeIs('admin.classifications.*') ? 'active' : '' }}">
                        <span class="nav-icon">🔬</span> Klasifikasi
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <span class="nav-icon">📋</span> Laporan
                        @php $pendingCount = \App\Models\EnvironmentalReport::where('status','Menunggu verifikasi')->count(); @endphp
                        @if($pendingCount > 0)
                            <span class="badge badge-danger" style="margin-left:auto;padding:2px 7px;font-size:10px;">{{ $pendingCount }}</span>
                        @endif
                    </a>
                </nav>
            </div>

            <div>
                <p class="nav-section-label">Manajemen</p>
                <nav class="nav">
                    <a href="{{ route('admin.rewards.index') }}" class="{{ request()->routeIs('admin.rewards.*') ? 'active' : '' }}">
                        <span class="nav-icon">🎁</span> Reward
                    </a>
                    <a href="{{ route('admin.redemptions.index') }}" class="{{ request()->routeIs('admin.redemptions.*') ? 'active' : '' }}">
                        <span class="nav-icon">💸</span> Penukaran
                        @php $pendingRedeemCount = \App\Models\RewardRedemption::where('status','Menunggu proses')->count(); @endphp
                        @if($pendingRedeemCount > 0)
                            <span class="badge badge-danger" style="margin-left:auto;padding:2px 7px;font-size:10px;">{{ $pendingRedeemCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.challenges.index') }}" class="{{ request()->routeIs('admin.challenges.*') ? 'active' : '' }}">
                        <span class="nav-icon">🏆</span> Challenge
                    </a>
                </nav>
            </div>
        </div>

        <div class="sidebar-footer">
            <div class="admin-info">
                <div class="admin-avatar">{{ strtoupper(substr(auth()->user()->display_name ?? 'A', 0, 1)) }}</div>
                <div class="admin-meta">
                    <div class="admin-name">{{ auth()->user()->display_name ?? 'Admin' }}</div>
                    <div class="admin-role">Administrator</div>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}" style="flex-shrink:0">
                    @csrf
                    <button type="submit" class="btn-logout" title="Logout">⏻</button>
                </form>
            </div>
        </div>
    </aside>

    <main class="main">
        <div class="topbar">
            <div class="topbar-left">
                <button type="button" class="menu-toggle" id="menu-toggle" aria-label="Buka Menu">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                </button>
                <div class="page-title">
                    <h1>@yield('heading', 'Dashboard')</h1>
                    <p>@yield('subheading', 'Manajemen aplikasi Sampah Detector')</p>
                </div>
            </div>
            <div class="topbar-right">
                <button type="button" class="btn btn-sm btn-theme-toggle" id="theme-toggle" aria-label="Ganti Tema" style="padding: 7px 10px; font-size: 15px; border-radius: 99px;">
                    <span id="theme-toggle-icon">☀️</span>
                </button>
                <div class="live-badge" id="live-badge">
                    <span class="live-dot"></span>
                    <span id="live-status">Live</span>
                </div>
                <div style="display:flex;align-items:center;gap:5px;color:var(--text-2);font-size:13px;font-weight:600;font-variant-numeric:tabular-nums;">
                    <span style="font-size:15px">🕐</span>
                    <span id="last-updated" style="letter-spacing:.3px;min-width:52px"></span>
                </div>
            </div>
        </div>

        <div class="content">
            @if(session('success'))
                <div class="flash flash-success" id="flash-msg">✅ {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="flash flash-error" id="flash-msg">❌ {{ session('error') }}</div>
            @endif

            <div class="section">
                @yield('content')
            </div>
        </div>
    </main>
</div>

<div id="toast-container"></div>

<div class="lightbox" id="lightbox" onclick="closeLightbox(event)">
    <div class="lightbox-content" onclick="event.stopPropagation()">
        <span class="lightbox-close" onclick="closeLightbox(event)">&times;</span>
        <img id="lightbox-img" src="" alt="Foto laporan">
    </div>
</div>

<script>
// ── Global Realtime Polling ──────────────────────────────────────
(function() {
    const POLL_INTERVAL = 5000; // 5 detik (percepat untuk respons real-time)

    // Request permission for push notifications
    if (typeof Notification !== 'undefined' && Notification.permission === 'default') {
        Notification.requestPermission();
    }

    // Play double beep sound
    window.playNotificationSound = function() {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const audioCtx = new AudioCtx();
            
            const playBeep = (delay, freq, duration) => {
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.frequency.value = freq;
                osc.type = 'sine';
                
                gain.gain.setValueAtTime(0, audioCtx.currentTime + delay);
                gain.gain.linearRampToValueAtTime(0.12, audioCtx.currentTime + delay + 0.02);
                gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + delay + duration);
                
                osc.start(audioCtx.currentTime + delay);
                osc.stop(audioCtx.currentTime + delay + duration);
            };
            
            playBeep(0, 880, 0.1);
            playBeep(0.12, 1100, 0.18);
        } catch (e) {
            console.warn('Web Audio beep error:', e);
        }
    };

    // Show custom toast notification
    window.showToastNotification = function(title, msg, urgency = 'sedang', actionUrl = '#') {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast-notif ${urgency.toLowerCase()}`;
        
        let icon = '🔔';
        if (urgency.toLowerCase() === 'tinggi') icon = '🔴';
        else if (urgency.toLowerCase() === 'sedang') icon = '🟡';
        else if (urgency.toLowerCase() === 'rendah') icon = '🟢';

        toast.innerHTML = `
            <div class="toast-notif-icon">${icon}</div>
            <div class="toast-notif-body" onclick="window.location.href='${actionUrl}'" style="cursor:pointer">
                <div class="toast-notif-title">${title}</div>
                <div class="toast-notif-msg">${msg}</div>
            </div>
            <button class="toast-notif-close">&times;</button>
        `;

        container.appendChild(toast);

        // Slide in
        setTimeout(() => toast.classList.add('show'), 50);

        // Click to close
        toast.querySelector('.toast-notif-close').addEventListener('click', (e) => {
            e.stopPropagation();
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 400);
        });

        // Auto remove
        setTimeout(() => {
            if (toast.parentNode) {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 400);
            }
        }, 7000);
    };

    // ── Global Lightbox ──
    window.openLightbox = function(src) {
        const lb = document.getElementById('lightbox');
        const img = document.getElementById('lightbox-img');
        if (lb && img) {
            img.src = src;
            lb.classList.add('open');
            document.body.style.overflow = 'hidden';
        }
    };
    window.closeLightbox = function(e) {
        const lb = document.getElementById('lightbox');
        if (lb) {
            if (e === undefined || e.target === lb || e.target.classList.contains('lightbox-close')) {
                lb.classList.remove('open');
                document.body.style.overflow = '';
            }
        }
    };
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') window.closeLightbox();
    });

    // ── Live clock — update setiap detik ──
    function tickClock() {
        const el = document.getElementById('last-updated');
        if (!el) return;
        const now = new Date();
        const hh = now.getHours().toString().padStart(2,'0');
        const mm = now.getMinutes().toString().padStart(2,'0');
        const ss = now.getSeconds().toString().padStart(2,'0');
        el.textContent = `${hh}:${mm}:${ss}`;
    }
    tickClock();
    setInterval(tickClock, 1000);

    // ── Polling data ──
    window.registerPollCallback = function(fn) {
        window._pollCallbacks = window._pollCallbacks || [];
        window._pollCallbacks.push(fn);
    };

    async function poll() {
        try {
            const cbs = window._pollCallbacks || [];
            for (const cb of cbs) { await cb(); }
        } catch(e) {
            console.warn('Poll error:', e);
        }
    }

    setInterval(poll, POLL_INTERVAL);

    // Animate counter numbers
    function animateCounter(el, target) {
        const start = parseInt(el.textContent) || 0;
        if (start === target) return;
        const diff = target - start;
        const steps = 20;
        let step = 0;
        const timer = setInterval(() => {
            step++;
            el.textContent = Math.round(start + diff * (step / steps));
            if (step >= steps) { el.textContent = target; clearInterval(timer); }
        }, 16);
    }
    window.animateCounter = animateCounter;

    // Auto-dismiss flash messages after 5s
    const flash = document.getElementById('flash-msg');
    if (flash) {
        setTimeout(() => {
            flash.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            flash.style.opacity = '0';
            flash.style.transform = 'translateY(-8px)';
            setTimeout(() => flash.remove(), 400);
        }, 5000);
    }

    // Responsive Sidebar Hamburger Menu Toggle
    const menuToggle = document.getElementById('menu-toggle');
    const sidebarClose = document.getElementById('sidebar-close');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    const sidebar = document.getElementById('sidebar');

    function openSidebar() {
        sidebar.classList.add('open');
        sidebarOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    // Theme Toggle Handler
    const themeToggleBtn = document.getElementById('theme-toggle');
    const themeToggleIcon = document.getElementById('theme-toggle-icon');

    function updateToggleIcon(theme) {
        if (!themeToggleIcon) return;
        themeToggleIcon.textContent = theme === 'light' ? '🌙' : '☀️';
    }

    // Initialize icon based on current theme on load
    const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
    updateToggleIcon(currentTheme);

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const current = document.documentElement.getAttribute('data-theme') || 'dark';
            const next = current === 'dark' ? 'light' : 'dark';
            
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('admin-theme', next);
            updateToggleIcon(next);
            
            // Dispatch dynamic window event for child scripts (e.g. donut chart) to respond
            window.dispatchEvent(new Event('theme-changed'));
        });
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        sidebarOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    if (menuToggle) menuToggle.addEventListener('click', openSidebar);
    if (sidebarClose) sidebarClose.addEventListener('click', closeSidebar);
    if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);
})();
</script>
@stack('scripts')
</body>
</html>
