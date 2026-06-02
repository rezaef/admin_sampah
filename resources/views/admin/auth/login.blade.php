<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Admin · Sampah Detector</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --green-400: #4ade80;
            --green-500: #22c55e;
            --green-600: #16a34a;
            --cyan-500: #06b6d4;
            --sky-500: #0ea5e9;
            --bg-dark: #0b0f19;
            --bg-card: rgba(255, 255, 255, 0.03);
            --border-subtle: rgba(255, 255, 255, 0.08);
            --text-primary: #ffffff;
            --text-secondary: rgba(255, 255, 255, 0.6);
            --text-muted: rgba(255, 255, 255, 0.35);
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-dark);
            font-family: 'Inter', system-ui, sans-serif;
            -webkit-font-smoothing: antialiased;
            overflow: hidden;
            position: relative;
            padding: 20px;
        }

        /* ═══════════════════════════════════════════
           Animated Grid Background (React-style)
           ═══════════════════════════════════════════ */
        .grid-bg {
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 64px 64px;
            mask-image: radial-gradient(ellipse 70% 60% at 50% 50%, black 30%, transparent 100%);
            -webkit-mask-image: radial-gradient(ellipse 70% 60% at 50% 50%, black 30%, transparent 100%);
            z-index: 0;
            animation: grid-fade-in 2s ease-out;
        }

        @keyframes grid-fade-in {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* ═══════════════════════════════════════════
           Floating Ambient Orbs
           ═══════════════════════════════════════════ */
        .orb {
            position: fixed;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0;
            pointer-events: none;
            z-index: 0;
            animation: orb-appear 2s ease-out forwards;
        }
        .orb-1 {
            width: 500px; height: 500px;
            background: var(--green-600);
            top: -5%; left: -5%;
            animation-delay: 0.2s;
        }
        .orb-1 { animation: orb-appear 2s 0.2s ease-out forwards, orb-drift-1 25s 2.2s infinite alternate ease-in-out; }
        .orb-2 {
            width: 400px; height: 400px;
            background: var(--sky-500);
            bottom: -10%; right: -5%;
        }
        .orb-2 { animation: orb-appear 2s 0.5s ease-out forwards, orb-drift-2 22s 2.5s infinite alternate ease-in-out; }
        .orb-3 {
            width: 300px; height: 300px;
            background: var(--cyan-500);
            top: 50%; left: 60%;
            transform: translate(-50%, -50%);
        }
        .orb-3 { animation: orb-appear 2s 0.8s ease-out forwards, orb-drift-3 28s 2.8s infinite alternate ease-in-out; }

        @keyframes orb-appear {
            from { opacity: 0; transform: scale(0.5); }
            to { opacity: 0.25; transform: scale(1); }
        }
        @keyframes orb-drift-1 {
            0% { transform: translate(0, 0); }
            100% { transform: translate(120px, 80px); }
        }
        @keyframes orb-drift-2 {
            0% { transform: translate(0, 0); }
            100% { transform: translate(-100px, -60px); }
        }
        @keyframes orb-drift-3 {
            0% { transform: translate(-50%, -50%); }
            100% { transform: translate(calc(-50% + 60px), calc(-50% - 90px)); }
        }

        /* ═══════════════════════════════════════════
           Floating Emoji Particles System
           ═══════════════════════════════════════════ */
        .particles-container {
            position: fixed;
            inset: 0;
            z-index: 1;
            pointer-events: none;
            overflow: hidden;
            contain: layout style;
        }
        .particle-emoji {
            position: absolute;
            left: 0;
            top: 0;
            pointer-events: none;
            line-height: 1;
            filter: grayscale(0.2) brightness(0.85);
            user-select: none;
            will-change: transform, opacity;
            transition: filter 0.3s ease;
            contain: layout style;
        }
        .particle-emoji.near-cursor {
            filter: grayscale(0) brightness(1.2) drop-shadow(0 0 8px rgba(74, 222, 128, 0.5));
        }

        /* ═══════════════════════════════════════════
           Interactive Cursor Glow
           ═══════════════════════════════════════════ */
        .interactive-glow {
            position: fixed;
            width: 700px;
            height: 700px;
            background: radial-gradient(circle, rgba(74, 222, 128, 0.1) 0%, rgba(14, 165, 233, 0.06) 40%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
            z-index: 1;
            left: 0;
            top: 0;
            transform: translate3d(-350px, -350px, 0);
            opacity: 0;
            transition: opacity 0.8s cubic-bezier(0.23, 1, 0.32, 1);
            will-change: transform;
            contain: layout style;
        }
        body:hover .interactive-glow {
            opacity: 1;
        }

        /* ═══════════════════════════════════════════
           Login Box — Entry Animation
           ═══════════════════════════════════════════ */
        .login-box {
            width: 100%;
            max-width: 420px;
            background: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 24px;
            padding: 44px 36px 36px;
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            box-shadow:
                0 24px 80px rgba(0, 0, 0, 0.5),
                0 0 0 1px rgba(255,255,255,0.04),
                inset 0 1px 1px rgba(255, 255, 255, 0.08);
            position: relative;
            z-index: 10;

            /* Entry animation */
            opacity: 0;
            transform: translateY(30px) scale(0.96);
            animation: box-entrance 0.9s cubic-bezier(0.23, 1, 0.32, 1) 0.3s forwards;
        }

        /* Subtle border glow on hover */
        .login-box::before {
            content: '';
            position: absolute;
            inset: -1px;
            border-radius: 25px;
            padding: 1px;
            background: linear-gradient(135deg, rgba(74,222,128,0.2), transparent 50%, rgba(14,165,233,0.2));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 0;
            transition: opacity 0.5s ease;
            pointer-events: none;
        }
        .login-box:hover::before {
            opacity: 1;
        }

        @keyframes box-entrance {
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* ── Staggered Children Animations ── */
        .anim-child {
            opacity: 0;
            transform: translateY(18px);
            animation: child-slide-in 0.7s cubic-bezier(0.23, 1, 0.32, 1) forwards;
        }
        .anim-child:nth-child(1) { animation-delay: 0.6s; }
        .anim-child:nth-child(2) { animation-delay: 0.75s; }
        .anim-child:nth-child(3) { animation-delay: 0.9s; }
        .anim-child:nth-child(4) { animation-delay: 1.0s; }
        .anim-child:nth-child(5) { animation-delay: 1.1s; }
        .anim-child:nth-child(6) { animation-delay: 1.2s; }

        @keyframes child-slide-in {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ═══════════════════════════════════════════
           Brand
           ═══════════════════════════════════════════ */
        .login-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin-bottom: 28px;
        }
        .brand-icon {
            width: 46px;
            height: 46px;
            background: linear-gradient(135deg, var(--green-600), var(--sky-500));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: 0 4px 18px rgba(22, 163, 74, 0.35);
            transition: transform 0.4s cubic-bezier(0.23, 1, 0.32, 1), box-shadow 0.4s ease;
        }
        .login-brand:hover .brand-icon {
            transform: rotate(-8deg) scale(1.08);
            box-shadow: 0 6px 24px rgba(22, 163, 74, 0.45);
        }
        .brand-text {
            display: flex;
            flex-direction: column;
            text-align: left;
        }
        .brand-name {
            font-size: 17px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.3px;
        }
        .brand-sub {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 500;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }

        .login-header {
            margin-bottom: 30px;
            text-align: center;
        }
        .login-header h1 {
            font-size: 26px;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.5px;
            line-height: 1.3;
        }
        .login-header p {
            color: var(--text-secondary);
            margin-top: 6px;
            font-size: 13.5px;
        }

        /* ═══════════════════════════════════════════
           Form Fields — with animated focus states
           ═══════════════════════════════════════════ */
        .field {
            display: grid;
            gap: 7px;
            margin-bottom: 20px;
            position: relative;
        }
        .field label {
            font-size: 12.5px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.7);
            transition: color 0.3s ease;
        }
        .field:focus-within label {
            color: var(--green-400);
        }
        .input-wrap {
            position: relative;
        }
        .input-wrap .icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
            color: rgba(255, 255, 255, 0.4);
            transition: color 0.3s ease, transform 0.3s cubic-bezier(0.23, 1, 0.32, 1);
            z-index: 2;
        }
        .field:focus-within .input-wrap .icon {
            color: var(--green-400);
            transform: translateY(-50%) scale(1.15);
        }

        .field input {
            width: 100%;
            padding: 14px 16px 14px 44px;
            border-radius: 14px;
            border: 1.5px solid rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.03);
            font-family: inherit;
            font-size: 14px;
            color: #fff;
            transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
            outline: none;
            position: relative;
        }
        .field input::placeholder {
            color: rgba(255, 255, 255, 0.25);
            transition: color 0.3s ease;
        }
        .field input:focus {
            border-color: var(--green-400);
            background: rgba(255, 255, 255, 0.06);
            box-shadow:
                0 0 0 3px rgba(74, 222, 128, 0.1),
                0 0 20px rgba(74, 222, 128, 0.05);
        }
        .field input:focus::placeholder {
            color: rgba(255, 255, 255, 0.15);
        }

        /* Input focus line animation */
        .input-focus-line {
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--green-400), var(--cyan-500));
            border-radius: 0 0 14px 14px;
            transition: width 0.4s cubic-bezier(0.23, 1, 0.32, 1), left 0.4s cubic-bezier(0.23, 1, 0.32, 1);
            z-index: 2;
        }
        .field:focus-within .input-focus-line {
            width: 100%;
            left: 0;
        }

        /* ═══════════════════════════════════════════
           Login Button — with shimmer + ripple
           ═══════════════════════════════════════════ */
        .btn-login {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--green-600) 0%, #10b981 50%, var(--green-600) 100%);
            background-size: 200% 100%;
            color: #fff;
            font-family: inherit;
            font-size: 14.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.35s cubic-bezier(0.23, 1, 0.32, 1);
            box-shadow: 0 8px 30px rgba(22, 163, 74, 0.3);
            margin-top: 8px;
            position: relative;
            overflow: hidden;
            letter-spacing: 0.2px;
        }

        /* Shimmer sweep */
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
            transition: none;
        }
        .btn-login:hover::before {
            animation: btn-shimmer 0.8s ease-out;
        }
        @keyframes btn-shimmer {
            to { left: 100%; }
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 40px rgba(22, 163, 74, 0.4);
            background-position: 100% 0;
        }
        .btn-login:active {
            transform: translateY(0) scale(0.98);
            box-shadow: 0 6px 20px rgba(22, 163, 74, 0.25);
        }

        /* Ripple effect container */
        .btn-login .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: scale(0);
            animation: ripple-expand 0.6s ease-out;
            pointer-events: none;
        }
        @keyframes ripple-expand {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        /* ═══════════════════════════════════════════
           Error Box
           ═══════════════════════════════════════════ */
        .error-box {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 12px;
            background: rgba(239, 68, 68, 0.12);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.2);
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 22px;
            animation: shake-in 0.5s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
        }
        @keyframes shake-in {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70% { transform: translateX(-4px); }
            20%, 40%, 60% { transform: translateX(4px); }
            80% { transform: translateX(2px); }
            90% { transform: translateX(-2px); }
        }

        /* ═══════════════════════════════════════════
           Footer
           ═══════════════════════════════════════════ */
        .login-footer {
            text-align: center;
            margin-top: 24px;
            color: var(--text-muted);
            font-size: 12px;
            letter-spacing: 0.3px;
        }

        /* ═══════════════════════════════════════════
           Responsive
           ═══════════════════════════════════════════ */
        @media (max-width: 480px) {
            .login-box {
                padding: 32px 24px 28px;
                border-radius: 20px;
            }
            .login-header h1 { font-size: 22px; }
        }
    </style>
</head>
<body>
    <!-- Background Layers -->
    <div class="grid-bg"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
    <div class="particles-container" id="particles"></div>
    <div class="interactive-glow" id="interactive-glow"></div>

    <!-- Login Box -->
    <div class="login-box">
        <!-- Logo Brand -->
        <div class="login-brand anim-child">
            <div class="brand-icon">♻️</div>
            <div class="brand-text">
                <span class="brand-name">Sampah Detector</span>
                <span class="brand-sub">Admin Panel</span>
            </div>
        </div>

        <div class="login-header anim-child">
            <h1>Selamat datang 👋</h1>
            <p>Masuk ke panel admin Sampah Detector</p>
        </div>

        @if($errors->any())
            <div class="error-box anim-child">❌ {{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf
            <div class="field anim-child">
                <label for="identifier">Username atau Email</label>
                <div class="input-wrap">
                    <span class="icon">👤</span>
                    <input
                        id="identifier"
                        type="text"
                        name="identifier"
                        value="{{ old('identifier') }}"
                        placeholder="admin@sampahdetector.app"
                        required
                        autofocus
                    >
                    <div class="input-focus-line"></div>
                </div>
            </div>
            <div class="field anim-child">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <span class="icon">🔒</span>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="••••••••••"
                        required
                    >
                    <div class="input-focus-line"></div>
                </div>
            </div>
            <button type="submit" class="btn-login anim-child" id="btn-login">Masuk ke Dashboard →</button>
        </form>

        <div class="login-footer anim-child">
            &copy; {{ date('Y') }} Sampah Detector &middot; All rights reserved
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // ─── Shared Mouse State ───
            let mouseX = window.innerWidth * 0.5;
            let mouseY = window.innerHeight * 0.5;

            // Single passive mousemove listener for all systems
            document.body.addEventListener('mousemove', (e) => {
                mouseX = e.clientX;
                mouseY = e.clientY;
            }, { passive: true });

            // ─── Glow State ───
            const glow = document.getElementById('interactive-glow');
            let glowX = mouseX;
            let glowY = mouseY;

            // ─── Physics-Based Emoji Particle System ───
            const particlesContainer = document.getElementById('particles');
            const PARTICLE_COUNT = 28;
            const trashEmojis = ['🗑️', '♻️', '🥫', '🍌', '🧃', '📦', '🥤', '🍂', '🧴', '🛒', '💚', '🌿', '🍃', '🧹', '🗞️'];
            const CURSOR_RADIUS = 120;
            const CURSOR_RADIUS_SQ = CURSOR_RADIUS * CURSOR_RADIUS; // Pre-computed to avoid sqrt
            const REPULSION_FORCE = 3.5;
            const FRICTION = 0.94;
            const particles = [];

            function initParticle(p, randomY) {
                const w = window.innerWidth;
                const h = window.innerHeight;
                p.x = Math.random() * w;
                p.y = randomY ? (Math.random() * h) : (h + 40 + Math.random() * 100);
                p.vx = (Math.random() - 0.5) * 0.3;
                p.vy = -(0.4 + Math.random() * 0.8);
                p.size = 18 + Math.random() * 16;
                p.rotation = Math.random() * 360;
                p.rotationSpeed = (Math.random() - 0.5) * 1.2;
                p.wobblePhase = Math.random() * Math.PI * 2;
                p.wobbleSpeed = 0.01 + Math.random() * 0.02;
                p.wobbleAmp = 0.3 + Math.random() * 0.5;
                p.baseOpacity = 0.25 + Math.random() * 0.3;
                p.opacity = randomY ? p.baseOpacity : 0;
                p.fadeIn = !randomY;
                p.scale = randomY ? 1 : 0.5;
                p.nearCursor = false;
                // Set fontSize once — it never changes after init
                p.el.style.fontSize = `${p.size}px`;
            }

            function createParticleObj() {
                const el = document.createElement('div');
                el.classList.add('particle-emoji');
                el.textContent = trashEmojis[Math.floor(Math.random() * trashEmojis.length)];
                particlesContainer.appendChild(el);
                const p = { el };
                initParticle(p, true);
                return p;
            }

            for (let i = 0; i < PARTICLE_COUNT; i++) {
                particles.push(createParticleObj());
            }

            // ─── Single Master Animation Loop (glow + particles) ───
            function masterLoop() {
                // ── Update Glow (lerp toward cursor) ──
                glowX += (mouseX - glowX) * 0.08;
                glowY += (mouseY - glowY) * 0.08;
                glow.style.transform = `translate3d(${glowX - 350}px, ${glowY - 350}px, 0)`;

                // ── Update Particles ──
                const w = window.innerWidth;
                const h = window.innerHeight;
                const fadeZone = h * 0.15;
                const mx = mouseX;
                const my = mouseY;

                for (let i = 0, len = particles.length; i < len; i++) {
                    const p = particles[i];

                    // Fade in new particles
                    if (p.fadeIn) {
                        p.opacity = Math.min(p.opacity + 0.005, p.baseOpacity);
                        p.scale = Math.min(p.scale + 0.008, 1);
                        if (p.opacity >= p.baseOpacity) p.fadeIn = false;
                    }

                    // Wobble (organic sway)
                    p.wobblePhase += p.wobbleSpeed;
                    p.vx += Math.sin(p.wobblePhase) * p.wobbleAmp * 0.02;

                    // Cursor interaction: use squared distance (skip sqrt)
                    const dx = p.x - mx;
                    const dy = p.y - my;
                    const distSq = dx * dx + dy * dy;
                    const wasNear = p.nearCursor;

                    if (distSq < CURSOR_RADIUS_SQ && distSq > 0) {
                        const dist = Math.sqrt(distSq); // sqrt only when needed
                        const force = (1 - dist / CURSOR_RADIUS) * REPULSION_FORCE;
                        const invDist = 1 / dist; // single division
                        p.vx += dx * invDist * force;
                        p.vy += dy * invDist * force;
                        p.rotationSpeed += (Math.random() - 0.5) * 2;
                        p.nearCursor = true;
                    } else {
                        p.nearCursor = false;
                    }

                    // Toggle glow class only on state change
                    if (p.nearCursor !== wasNear) {
                        p.el.classList.toggle('near-cursor', p.nearCursor);
                    }

                    // Apply velocity with friction
                    p.vx *= FRICTION;
                    p.vy *= FRICTION;
                    p.vy -= 0.01; // gentle upward float

                    p.x += p.vx;
                    p.y += p.vy;
                    p.rotation += p.rotationSpeed;
                    p.rotationSpeed *= 0.995;

                    // Recycle off-screen particles
                    if (p.y < -60 || p.x < -80 || p.x > w + 80) {
                        initParticle(p, false);
                        p.el.textContent = trashEmojis[Math.floor(Math.random() * trashEmojis.length)];
                    }

                    // Fade out near top
                    if (p.y < fadeZone && !p.fadeIn) {
                        p.opacity = p.baseOpacity * (p.y / fadeZone);
                    }

                    // GPU-accelerated transform via translate3d
                    p.el.style.transform = `translate3d(${p.x}px,${p.y}px,0) rotate(${p.rotation}deg) scale(${p.scale})`;
                    p.el.style.opacity = p.opacity > 0 ? p.opacity : 0;
                }

                requestAnimationFrame(masterLoop);
            }
            requestAnimationFrame(masterLoop);

            // ─── Button Ripple Effect ───
            const btn = document.getElementById('btn-login');
            btn.addEventListener('click', function(e) {
                const rect = this.getBoundingClientRect();
                const ripple = document.createElement('span');
                ripple.classList.add('ripple');
                const size = Math.max(rect.width, rect.height);
                ripple.style.width = ripple.style.height = `${size}px`;
                ripple.style.left = `${e.clientX - rect.left - size / 2}px`;
                ripple.style.top = `${e.clientY - rect.top - size / 2}px`;
                this.appendChild(ripple);
                ripple.addEventListener('animationend', () => ripple.remove());
            });

            // ─── Tilt Effect on Login Box ───
            const loginBox = document.querySelector('.login-box');
            loginBox.addEventListener('mousemove', (e) => {
                const rect = loginBox.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                const centerX = rect.width * 0.5;
                const centerY = rect.height * 0.5;

                const rotateX = ((y - centerY) / centerY) * -3;
                const rotateY = ((x - centerX) / centerX) * 3;

                loginBox.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.01)`;
            }, { passive: true });

            loginBox.addEventListener('mouseleave', () => {
                loginBox.style.transition = 'transform 0.5s cubic-bezier(0.23, 1, 0.32, 1)';
                loginBox.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) scale(1)';
                setTimeout(() => {
                    loginBox.style.transition = '';
                }, 500);
            });
        });
    </script>
</body>
</html>
