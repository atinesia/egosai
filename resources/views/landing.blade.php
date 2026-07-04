<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EGOS AI — CS & Sales AI untuk WhatsApp Bisnis</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="icon" type="image/svg+xml" href="{{ asset('faveicon.svg') }}">
    <link rel="alternate icon" type="image/png" href="{{ asset('faveicon.svg') }}">
    <link
        href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --bg: #050a16;
            --bg-alt: #0a1226;
            --panel: #0d1731;
            --panel-soft: #0b142b;
            --border: rgba(112, 201, 255, 0.14);
            --cyan: #3fe8f5;
            --blue: #2b8fe8;
            --blue-deep: #1451d6;
            --navy: #0a2c6b;
            --text: #f4f9ff;
            --muted: #8fa3c8;
            --muted-soft: #5f7296;
            --gradient: linear-gradient(135deg, #3fe8f5 0%, #2b8fe8 52%, #1451d6 100%);
            --gradient-soft: linear-gradient(135deg, rgba(63, 232, 245, 0.15), rgba(20, 81, 214, 0.15));
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            line-height: 1.6;
        }

        h1,
        h2,
        h3,
        h4 {
            font-family: 'Sora', sans-serif;
            letter-spacing: -0.02em;
        }

        .mono {
            font-family: 'JetBrains Mono', monospace;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        ul {
            list-style: none;
        }

        img {
            max-width: 100%;
            display: block;
        }

        .wrap {
            max-width: 1180px;
            margin: 0 auto;
            padding: 0 24px;
        }

        .grad-text {
            background: var(--gradient);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12.5px;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--cyan);
            background: rgba(63, 232, 245, 0.08);
            border: 1px solid var(--border);
            padding: 7px 14px;
            border-radius: 100px;
        }

        .eyebrow::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--cyan);
            box-shadow: 0 0 8px var(--cyan);
        }

        /* ---------- Background ambience ---------- */
        .bg-glow {
            position: fixed;
            inset: 0;
            z-index: -2;
            background:
                radial-gradient(700px 500px at 15% 8%, rgba(43, 143, 232, 0.16), transparent 60%),
                radial-gradient(800px 600px at 90% 15%, rgba(63, 232, 245, 0.10), transparent 60%),
                radial-gradient(900px 700px at 50% 100%, rgba(20, 81, 214, 0.14), transparent 60%),
                var(--bg);
        }

        .bg-grid {
            position: fixed;
            inset: 0;
            z-index: -1;
            opacity: 0.35;
            background-image: linear-gradient(rgba(112, 201, 255, 0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(112, 201, 255, 0.05) 1px, transparent 1px);
            background-size: 64px 64px;
            mask-image: radial-gradient(ellipse 80% 60% at 50% 0%, black 40%, transparent 90%);
        }

        /* ---------- Nav ---------- */
        nav {
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(14px);
            background: rgba(5, 10, 22, 0.72);
            border-bottom: 1px solid var(--border);
        }

        nav .wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 76px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand img {
            height: 38px;
            width: auto;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 36px;
        }

        .nav-links a {
            font-size: 14.5px;
            font-weight: 500;
            color: var(--muted);
            transition: color .2s;
        }

        .nav-links a:hover {
            color: var(--text);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 13px 26px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14.5px;
            cursor: pointer;
            border: none;
            transition: transform .25s ease, box-shadow .25s ease, opacity .2s;
            white-space: nowrap;
        }

        .btn-primary {
            background: var(--gradient);
            color: #04101f;
            box-shadow: 0 10px 30px -8px rgba(43, 143, 232, 0.55);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 36px -6px rgba(63, 232, 245, 0.55);
        }

        .btn-ghost {
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text);
        }

        .btn-ghost:hover {
            border-color: rgba(112, 201, 255, 0.4);
            background: rgba(112, 201, 255, 0.06);
        }

        .btn-sm {
            padding: 10px 20px;
            font-size: 13.5px;
            border-radius: 10px;
        }

        .nav-cta {
            display: none;
        }

        @media(min-width:900px) {
            .nav-cta {
                display: inline-flex;
            }
        }

        /* ---------- Hero ---------- */
        .hero {
            padding: 88px 0 60px;
            position: relative;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 56px;
            align-items: center;
        }

        @media(min-width:980px) {
            .hero-grid {
                grid-template-columns: 1.05fr 0.95fr;
                gap: 40px;
            }
        }

        .hero-copy .eyebrow {
            margin-bottom: 22px;
        }

        .hero h1 {
            font-size: clamp(34px, 5.2vw, 58px);
            font-weight: 700;
            line-height: 1.08;
            margin-bottom: 22px;
        }

        .hero p.lede {
            font-size: 17.5px;
            color: var(--muted);
            max-width: 520px;
            margin-bottom: 34px;
        }

        .hero-cta {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            align-items: center;
            margin-bottom: 22px;
        }

        .hero-note {
            font-size: 13px;
            color: var(--muted-soft);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .hero-note svg {
            flex-shrink: 0;
        }

        /* phone mockup */
        .phone-stage {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 480px;
        }

        .orbit-ring {
            position: absolute;
            width: 430px;
            height: 430px;
            border-radius: 50%;
            border: 1.5px solid transparent;
            background: conic-gradient(from 0deg, transparent 0%, rgba(63, 232, 245, 0.55) 18%, rgba(43, 143, 232, 0.4) 30%, transparent 42%) border-box;
            -webkit-mask: linear-gradient(#000 0 0) padding-box, linear-gradient(#000 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            animation: spin 16s linear infinite;
        }

        .orbit-ring.r2 {
            width: 500px;
            height: 500px;
            opacity: 0.5;
            animation-duration: 24s;
            animation-direction: reverse;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .sparkle {
            position: absolute;
            top: 18%;
            right: 20%;
            width: 22px;
            height: 22px;
            animation: twinkle 2.6s ease-in-out infinite;
        }

        @keyframes twinkle {

            0%,
            100% {
                opacity: 0.3;
                transform: scale(0.8) rotate(0deg);
            }

            50% {
                opacity: 1;
                transform: scale(1.15) rotate(20deg);
            }
        }

        .phone {
            position: relative;
            width: 270px;
            background: linear-gradient(180deg, #0d1731, #080f22);
            border-radius: 34px;
            border: 1px solid var(--border);
            box-shadow: 0 40px 80px -20px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255, 255, 255, 0.02) inset;
            padding: 14px;
        }

        .phone-notch {
            width: 60px;
            height: 6px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            margin: 0 auto 12px;
        }

        .phone-screen {
            background: var(--bg-alt);
            border-radius: 20px;
            padding: 14px 12px;
            height: 400px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            overflow: hidden;
        }

        .chat-header {
            display: flex;
            align-items: center;
            gap: 8px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 4px;
        }

        .chat-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--gradient);
            flex-shrink: 0;
        }

        .chat-header .name {
            font-size: 12.5px;
            font-weight: 600;
        }

        .chat-header .status {
            font-size: 10px;
            color: var(--cyan);
        }

        .bubble {
            max-width: 80%;
            padding: 9px 12px;
            border-radius: 14px;
            font-size: 12px;
            opacity: 0;
            animation: rise .5s ease forwards;
        }

        .bubble.in {
            align-self: flex-start;
            background: var(--panel);
            border: 1px solid var(--border);
            border-bottom-left-radius: 4px;
        }

        .bubble.out {
            align-self: flex-end;
            background: var(--gradient);
            color: #04101f;
            font-weight: 500;
            border-bottom-right-radius: 4px;
        }

        .bubble b1 {
            animation-delay: .3s;
        }

        @keyframes rise {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .typing-tag {
            align-self: flex-start;
            font-size: 10px;
            color: var(--muted-soft);
            display: flex;
            align-items: center;
            gap: 6px;
            opacity: 0;
            animation: rise .5s ease forwards;
            animation-delay: 1.8s;
        }

        .typing-dot {
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: var(--cyan);
            animation: blink 1s infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: .3
            }

            50% {
                opacity: 1
            }
        }

        /* ---------- Stats strip ---------- */
        .stats-strip {
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            padding: 34px 0;
            background: rgba(255, 255, 255, 0.012);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            text-align: center;
        }

        .stats-grid .stat b {
            display: block;
            font-family: 'JetBrains Mono', monospace;
            font-size: clamp(20px, 3vw, 30px);
            color: var(--cyan);
        }

        .stats-grid .stat span {
            font-size: 13px;
            color: var(--muted);
        }

        /* ---------- Section shared ---------- */
        section {
            padding: 110px 0;
        }

        .section-head {
            max-width: 680px;
            margin-bottom: 60px;
        }

        .section-head.center {
            margin-left: auto;
            margin-right: auto;
            text-align: center;
        }

        .section-head .eyebrow {
            margin-bottom: 18px;
        }

        .section-head h2 {
            font-size: clamp(28px, 3.6vw, 42px);
            font-weight: 700;
            margin-bottom: 16px;
            line-height: 1.15;
        }

        .section-head p {
            color: var(--muted);
            font-size: 16px;
        }

        .reveal {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity .7s ease, transform .7s ease;
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ---------- Problem/solution ---------- */
        .ps-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }

        @media(min-width:860px) {
            .ps-grid {
                grid-template-columns: 1fr 1fr;
                gap: 20px;
            }
        }

        .ps-col {
            background: var(--panel-soft);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 32px;
        }

        .ps-col h3 {
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: 22px;
            font-family: 'JetBrains Mono', monospace;
        }

        .ps-col.before h3 {
            color: #ff8a8a;
        }

        .ps-col.after h3 {
            color: var(--cyan);
        }

        .ps-item {
            display: flex;
            gap: 12px;
            padding: 14px 0;
            border-bottom: 1px solid var(--border);
            font-size: 14.5px;
            color: var(--muted);
        }

        .ps-item:last-child {
            border-bottom: none;
        }

        .ps-item svg {
            flex-shrink: 0;
            margin-top: 2px;
        }

        .ps-col.after .ps-item {
            color: var(--text);
        }

        /* ---------- How it works ---------- */
        .steps {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0;
            counter-reset: step;
        }

        @media(min-width:860px) {
            .steps {
                grid-template-columns: repeat(3, 1fr);
                gap: 28px;
            }
        }

        .step {
            position: relative;
            padding: 32px 28px 28px;
            border: 1px solid var(--border);
            border-radius: 20px;
            background: var(--panel-soft);
        }

        .step .num {
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            color: var(--cyan);
            display: inline-block;
            margin-bottom: 18px;
            letter-spacing: .05em;
        }

        .step h3 {
            font-size: 19px;
            margin-bottom: 10px;
        }

        .step p {
            color: var(--muted);
            font-size: 14.5px;
        }

        .step-connector {
            display: none;
        }

        @media(min-width:860px) {
            .steps {
                position: relative;
            }
        }

        /* ---------- Features ---------- */
        .feat-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 18px;
        }

        @media(min-width:640px) {
            .feat-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media(min-width:980px) {
            .feat-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .feat-card {
            background: var(--panel-soft);
            border: 1px solid var(--border);
            border-radius: 18px;
            padding: 28px;
            transition: transform .3s ease, border-color .3s ease, background .3s ease;
        }

        .feat-card:hover {
            transform: translateY(-4px);
            border-color: rgba(63, 232, 245, 0.35);
            background: var(--panel);
        }

        .feat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--gradient-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 18px;
            border: 1px solid var(--border);
        }

        .feat-card h3 {
            font-size: 17px;
            margin-bottom: 8px;
        }

        .feat-card p {
            font-size: 14px;
            color: var(--muted);
        }

        /* ---------- Rotator ---------- */
        .rotator-intro {
            background: var(--panel-soft);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 28px 32px;
            margin-bottom: 36px;
            font-size: 15px;
            color: var(--muted);
        }

        .rotator-intro b {
            color: var(--text);
        }

        .rotator-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        @media(min-width:860px) {
            .rotator-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .rotator-card {
            border-radius: 22px;
            padding: 34px;
            position: relative;
            overflow: hidden;
            border: 1px solid var(--border);
            background: var(--panel-soft);
        }

        .rotator-card.priority {
            border-color: rgba(63, 232, 245, 0.4);
            background: linear-gradient(160deg, rgba(63, 232, 245, 0.06), rgba(20, 81, 214, 0.08));
        }

        .rotator-tag {
            display: inline-block;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11.5px;
            padding: 5px 12px;
            border-radius: 100px;
            background: rgba(63, 232, 245, 0.1);
            color: var(--cyan);
            margin-bottom: 18px;
            letter-spacing: .05em;
        }

        .rotator-card h3 {
            font-size: 22px;
            margin-bottom: 12px;
        }

        .rotator-card p {
            color: var(--muted);
            font-size: 14.5px;
            margin-bottom: 20px;
        }

        .rotator-visual {
            display: flex;
            gap: 8px;
            margin-bottom: 22px;
        }

        .rotator-node {
            flex: 1;
            height: 8px;
            border-radius: 6px;
            background: rgba(112, 201, 255, 0.12);
            position: relative;
            overflow: hidden;
        }

        .rotator-node.active::after {
            content: '';
            position: absolute;
            inset: 0;
            background: var(--gradient);
            animation: pulseFill 2.4s ease-in-out infinite;
        }

        .rotator-node:nth-child(1).active::after {
            animation-delay: 0s;
        }

        .rotator-node:nth-child(2).active::after {
            animation-delay: .4s;
        }

        .rotator-node:nth-child(3).active::after {
            animation-delay: .8s;
        }

        @keyframes pulseFill {

            0%,
            100% {
                opacity: .25;
            }

            50% {
                opacity: 1;
            }
        }

        .rotator-card ul li {
            display: flex;
            gap: 10px;
            font-size: 13.5px;
            color: var(--text);
            padding: 6px 0;
        }

        /* ---------- Pricing ---------- */
        .pricing-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 22px;
        }

        @media(min-width:900px) {
            .pricing-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .price-card {
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 34px 30px;
            background: var(--panel-soft);
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .price-card.featured {
            border-color: rgba(63, 232, 245, 0.5);
            background: linear-gradient(160deg, rgba(63, 232, 245, 0.08), rgba(20, 81, 214, 0.1));
            transform: scale(1.02);
            box-shadow: 0 30px 60px -20px rgba(43, 143, 232, 0.35);
        }

        .badge-pop {
            position: absolute;
            top: -13px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--gradient);
            color: #04101f;
            font-size: 11.5px;
            font-weight: 700;
            padding: 6px 16px;
            border-radius: 100px;
            font-family: 'JetBrains Mono', monospace;
            letter-spacing: .04em;
        }

        .price-name {
            font-size: 14px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .08em;
            font-family: 'JetBrains Mono', monospace;
            margin-bottom: 14px;
        }

        .price-amount {
            font-size: 38px;
            font-weight: 700;
            font-family: 'Sora', sans-serif;
            margin-bottom: 2px;
        }

        .price-amount span {
            font-size: 14px;
            font-weight: 500;
            color: var(--muted);
            font-family: 'Inter', sans-serif;
        }

        .price-trial {
            font-size: 12.5px;
            color: var(--cyan);
            margin-bottom: 24px;
            font-weight: 600;
        }

        .price-card ul {
            margin-bottom: 28px;
            flex-grow: 1;
        }

        .price-card li {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            font-size: 14px;
            padding: 9px 0;
            border-bottom: 1px solid var(--border);
            color: var(--muted);
        }

        .price-card li:last-child {
            border-bottom: none;
        }

        .price-card li svg {
            flex-shrink: 0;
            margin-top: 3px;
        }

        .price-card .btn {
            width: 100%;
        }

        /* ---------- FAQ ---------- */
        .faq-list {
            border-top: 1px solid var(--border);
        }

        .faq-item {
            border-bottom: 1px solid var(--border);
        }

        .faq-q {
            width: 100%;
            text-align: left;
            background: none;
            border: none;
            color: var(--text);
            padding: 24px 4px;
            font-size: 16px;
            font-weight: 600;
            font-family: 'Sora', sans-serif;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            gap: 20px;
        }

        .faq-q .plus {
            flex-shrink: 0;
            transition: transform .3s ease;
            color: var(--cyan);
        }

        .faq-item.open .plus {
            transform: rotate(45deg);
        }

        .faq-a {
            max-height: 0;
            overflow: hidden;
            transition: max-height .35s ease;
        }

        .faq-a p {
            padding: 0 4px 24px;
            color: var(--muted);
            font-size: 14.5px;
            max-width: 640px;
        }

        /* ---------- Final CTA ---------- */
        .cta-banner {
            border-radius: 28px;
            padding: 70px 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
            background: linear-gradient(160deg, rgba(63, 232, 245, 0.1), rgba(10, 44, 107, 0.4));
            border: 1px solid rgba(63, 232, 245, 0.25);
        }

        .cta-banner h2 {
            font-size: clamp(26px, 4vw, 42px);
            margin-bottom: 18px;
        }

        .cta-banner p {
            color: var(--muted);
            max-width: 520px;
            margin: 0 auto 32px;
            font-size: 16px;
        }

        .cta-buttons {
            display: flex;
            gap: 14px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 18px;
        }

        .cta-fine {
            font-size: 12.5px;
            color: var(--muted-soft);
        }

        /* ---------- Footer ---------- */
        footer {
            border-top: 1px solid var(--border);
            padding: 50px 0 30px;
        }

        .footer-top {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 30px;
            margin-bottom: 34px;
        }

        .footer-brand img {
            height: 32px;
            margin-bottom: 12px;
        }

        .footer-brand p {
            color: var(--muted);
            font-size: 13.5px;
            max-width: 280px;
        }

        .footer-cols {
            display: flex;
            gap: 60px;
            flex-wrap: wrap;
        }

        .footer-col h4 {
            font-size: 12.5px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--muted-soft);
            margin-bottom: 14px;
            font-family: 'JetBrains Mono', monospace;
        }

        .footer-col a {
            display: block;
            font-size: 14px;
            color: var(--muted);
            padding: 5px 0;
            transition: color .2s;
        }

        .footer-col a:hover {
            color: var(--text);
        }

        .footer-bottom {
            border-top: 1px solid var(--border);
            padding-top: 24px;
            font-size: 12.5px;
            color: var(--muted-soft);
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }

        ::selection {
            background: rgba(63, 232, 245, 0.3);
        }
    </style>
</head>

<body>

    <div class="bg-glow"></div>
    <div class="bg-grid"></div>

    <nav>
        <div class="wrap">
            <div class="brand">
                <img src="logo-egos.png" alt="EGOS AI">
            </div>
            <div class="nav-links">
                <a href="#fitur">Fitur</a>
                <a href="#cara-kerja">Cara Kerja</a>
                <a href="#rotator">Rotator</a>
                <a href="#harga">Harga</a>
                <a href="#faq">FAQ</a>
            </div>
            <a href="#harga" class="btn btn-primary btn-sm nav-cta">Coba 7 Hari Gratis</a>
        </div>
    </nav>

    <!-- HERO -->
    <header class="hero">
        <div class="wrap hero-grid">
            <div class="hero-copy">
                <span class="eyebrow">CS &amp; Sales AI untuk WhatsApp Bisnis</span>
                <h1>Chat pelanggan nggak pernah tidur.<br>CS Anda juga <span class="grad-text">nggak perlu.</span></h1>
                <p class="lede">EGOS AI membalas chat WhatsApp pelanggan secara otomatis 24/7, follow-up calon pembeli
                    sampai closing, dan mengatur semua tim CS Anda dari satu dashboard — tanpa pelanggan menunggu satu
                    detik pun.</p>
                <div class="hero-cta">
                    <a href="#harga" class="btn btn-primary">Mulai 7 Hari Gratis →</a>
                    <a href="#cara-kerja" class="btn btn-ghost">Lihat Cara Kerja</a>
                </div>
                <div class="hero-note">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M20 6L9 17l-5-5" stroke="#3fe8f5" stroke-width="2.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    Tanpa kartu kredit · Setup kurang dari 10 menit · Batal kapan saja
                </div>
            </div>

            <div class="phone-stage">
                <div class="orbit-ring r2"></div>
                <div class="orbit-ring"></div>
                <svg class="sparkle" viewBox="0 0 24 24" fill="#3fe8f5">
                    <path d="M12 0l1.8 8.2L22 10l-8.2 1.8L12 20l-1.8-8.2L2 10l8.2-1.8z" />
                </svg>
                <div class="phone">
                    <div class="phone-notch"></div>
                    <div class="phone-screen">
                        <div class="chat-header">
                            <div class="chat-avatar"></div>
                            <div>
                                <div class="name">Toko Baju Aulia</div>
                                <div class="status">● EGOS AI aktif</div>
                            </div>
                        </div>
                        <div class="bubble in" style="animation-delay:.2s">Halo kak, baju yang warna dusty pink masih
                            ada size M ga?</div>
                        <div class="bubble out" style="animation-delay:.9s">Halo kak Rani 👋 Untuk dusty pink size M
                            masih ready, stok tinggal 3 pcs. Mau saya siapkan sekalian kak?</div>
                        <div class="bubble in" style="animation-delay:1.6s">Mau kak, gimana caranya?</div>
                        <div class="typing-tag"><span class="typing-dot"></span> AI sedang membalas…</div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- STATS -->
    <div class="stats-strip">
        <div class="wrap stats-grid">
            <div class="stat"><b>&lt; 5 detik</b><span>Rata-rata waktu respon AI</span></div>
            <div class="stat"><b>24/7</b><span>Aktif tanpa jeda, tanpa lembur</span></div>
            <div class="stat"><b>1–5</b><span>Nomor WhatsApp dalam 1 dashboard</span></div>
        </div>
    </div>

    <!-- PROBLEM / SOLUTION -->
    <section>
        <div class="wrap">
            <div class="section-head center">
                <span class="eyebrow">Masalah yang sering terjadi</span>
                <h2>CS kewalahan, chat numpuk, closing kescape.</h2>
                <p>Setiap chat yang telat dibalas adalah calon pembeli yang pindah ke toko sebelah.</p>
            </div>
            <div class="ps-grid">
                <div class="ps-col before reveal">
                    <h3>Tanpa EGOS AI</h3>
                    <div class="ps-item"><svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" stroke="#ff8a8a" stroke-width="2" />
                            <path d="M15 9l-6 6M9 9l6 6" stroke="#ff8a8a" stroke-width="2" stroke-linecap="round" />
                        </svg>Pelanggan chat malam hari, baru dibalas besok siang — sudah keburu beli di tempat lain.
                    </div>
                    <div class="ps-item"><svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" stroke="#ff8a8a" stroke-width="2" />
                            <path d="M15 9l-6 6M9 9l6 6" stroke="#ff8a8a" stroke-width="2" stroke-linecap="round" />
                        </svg>1 CS pegang 1 nomor WA, chat menumpuk saat promo atau jam ramai.</div>
                    <div class="ps-item"><svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" stroke="#ff8a8a" stroke-width="2" />
                            <path d="M15 9l-6 6M9 9l6 6" stroke="#ff8a8a" stroke-width="2" stroke-linecap="round" />
                        </svg>Broadcast promo dari 1 nomor saja, berisiko nomor kena banned WhatsApp.</div>
                    <div class="ps-item"><svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" stroke="#ff8a8a" stroke-width="2" />
                            <path d="M15 9l-6 6M9 9l6 6" stroke="#ff8a8a" stroke-width="2" stroke-linecap="round" />
                        </svg>Calon pembeli yang tanya-tanya lalu diam, tidak pernah di-follow up lagi.</div>
                </div>
                <div class="ps-col after reveal">
                    <h3>Dengan EGOS AI</h3>
                    <div class="ps-item"><svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <path d="M20 6L9 17l-5-5" stroke="#3fe8f5" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>Chat dibalas otomatis dalam hitungan detik, kapan pun pelanggan bertanya.</div>
                    <div class="ps-item"><svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <path d="M20 6L9 17l-5-5" stroke="#3fe8f5" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>Tim CS bisa handle banyak chat sekaligus lewat dashboard multi-agent.</div>
                    <div class="ps-item"><svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <path d="M20 6L9 17l-5-5" stroke="#3fe8f5" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>Blast promo terbagi rapi lewat Smart &amp; Priority Rotator, nomor tetap aman.</div>
                    <div class="ps-item"><svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <path d="M20 6L9 17l-5-5" stroke="#3fe8f5" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>AI otomatis follow-up calon pembeli sampai mereka memutuskan checkout.</div>
                </div>
            </div>
        </div>
    </section>

    <!-- HOW IT WORKS -->
    <section id="cara-kerja">
        <div class="wrap">
            <div class="section-head">
                <span class="eyebrow">Cara Kerja</span>
                <h2>Aktif dalam 3 langkah, tanpa tim teknis.</h2>
                <p>Tidak perlu instal aplikasi tambahan atau ganti nomor WhatsApp yang sudah dipakai pelanggan Anda.</p>
            </div>
            <div class="steps">
                <div class="step reveal">
                    <span class="num">01 / Hubungkan</span>
                    <h3>Scan QR, nomor langsung aktif</h3>
                    <p>Hubungkan 1 sampai 5 nomor WhatsApp bisnis Anda ke dashboard EGOS AI hanya dengan scan QR code,
                        seperti membuka WhatsApp Web.</p>
                </div>
                <div class="step reveal">
                    <span class="num">02 / Latih AI</span>
                    <h3>AI mempelajari bisnis Anda</h3>
                    <p>Masukkan FAQ, katalog produk, harga, dan gaya bahasa brand Anda. AI akan menjawab persis seperti
                        CS terbaik Anda.</p>
                </div>
                <div class="step reveal">
                    <span class="num">03 / Otomatis Jalan</span>
                    <h3>AI balas &amp; follow-up sendiri</h3>
                    <p>Chat masuk langsung direspons, calon pembeli di-follow up otomatis, dan tim CS mendapat
                        notifikasi real-time untuk closing.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES -->
    <section id="fitur">
        <div class="wrap">
            <div class="section-head">
                <span class="eyebrow">Fitur Utama</span>
                <h2>Semua yang tim CS &amp; Sales Anda butuhkan.</h2>
                <p>Dirancang khusus untuk UMKM dan bisnis yang berjualan lewat WhatsApp.</p>
            </div>
            <div class="feat-grid">
                <div class="feat-card reveal">
                    <div class="feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                            <path d="M12 3a9 9 0 100 18 9 9 0 000-18z" stroke="#3fe8f5" stroke-width="1.8" />
                            <path d="M12 7v5l3 3" stroke="#3fe8f5" stroke-width="1.8" stroke-linecap="round" />
                        </svg></div>
                    <h3>Balas Otomatis 24/7</h3>
                    <p>AI Engine merespons pertanyaan pelanggan dalam hitungan detik, siang maupun malam, tanpa jeda
                        libur.</p>
                </div>
                <div class="feat-card reveal">
                    <div class="feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                            <rect x="3" y="4" width="18" height="14" rx="2" stroke="#3fe8f5"
                                stroke-width="1.8" />
                            <path d="M8 10h8M8 14h5" stroke="#3fe8f5" stroke-width="1.8" stroke-linecap="round" />
                        </svg></div>
                    <h3>Dashboard Multi-Agent</h3>
                    <p>Beberapa CS bisa menangani chat secara bersamaan dari satu dashboard, dengan notifikasi
                        real-time.</p>
                </div>
                <div class="feat-card reveal">
                    <div class="feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                            <path d="M4 12a8 8 0 0114-5.3M20 12a8 8 0 01-14 5.3" stroke="#3fe8f5" stroke-width="1.8"
                                stroke-linecap="round" />
                            <path d="M18 4v4h-4M6 20v-4h4" stroke="#3fe8f5" stroke-width="1.8" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg></div>
                    <h3>Broadcast &amp; Blast Cerdas</h3>
                    <p>Kirim promo ke ribuan kontak dengan Smart &amp; Priority Rotator, menjaga nomor tetap aman dari
                        banned.</p>
                </div>
                <div class="feat-card reveal">
                    <div class="feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                            <rect x="5" y="2" width="14" height="20" rx="3" stroke="#3fe8f5"
                                stroke-width="1.8" />
                            <path d="M9 18h6" stroke="#3fe8f5" stroke-width="1.8" stroke-linecap="round" />
                        </svg></div>
                    <h3>Multi Nomor WhatsApp</h3>
                    <p>Kelola sampai 5 nomor WhatsApp bisnis dalam satu dashboard yang sama, tanpa perlu ganti
                        perangkat.</p>
                </div>
                <div class="feat-card reveal">
                    <div class="feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                            <path d="M12 2l7 4v6c0 5-3.4 8.7-7 10-3.6-1.3-7-5-7-10V6l7-4z" stroke="#3fe8f5"
                                stroke-width="1.8" stroke-linejoin="round" />
                        </svg></div>
                    <h3>Privasi Nomor Terjaga</h3>
                    <p>Penanganan identitas WhatsApp yang aman, menjaga data kontak pelanggan Anda tetap terlindungi.
                    </p>
                </div>
                <div class="feat-card reveal">
                    <div class="feat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                            <path d="M13 2L4 14h6l-1 8 9-12h-6l1-8z" stroke="#3fe8f5" stroke-width="1.8"
                                stroke-linejoin="round" />
                        </svg></div>
                    <h3>Notifikasi Real-time</h3>
                    <p>Setiap chat baru langsung muncul ke tim CS secara instan, tidak ada lagi pesan yang terlewat.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ROTATOR -->
    <section id="rotator">
        <div class="wrap">
            <div class="section-head">
                <span class="eyebrow">Biar Nomor WA Anda Tetap Aman</span>
                <h2>Apa itu Smart Rotator &amp; Priority Rotator?</h2>
            </div>
            <div class="rotator-intro reveal">
                WhatsApp membatasi jumlah pesan yang boleh dikirim dari satu nomor dalam sehari. Kirim blast dalam
                jumlah besar dari <b>satu nomor saja sangat berisiko diblokir atau di-banned</b>. Rotator adalah sistem
                EGOS AI yang otomatis membagi pengiriman pesan ke beberapa nomor WhatsApp Anda, sehingga blast tetap
                sampai ke banyak pelanggan tanpa membuat satu nomor pun terlihat mencurigakan di mata WhatsApp.
            </div>
            <div class="rotator-grid">
                <div class="rotator-card reveal">
                    <span class="rotator-tag">PAKET PRO</span>
                    <h3>Smart Rotator</h3>
                    <p>Setiap pesan blast dibagi secara <b style="color:var(--text)">bergantian dan merata</b> ke
                        seluruh nomor WhatsApp yang Anda hubungkan. Dengan begitu, tidak ada satu nomor yang mengirim
                        terlalu banyak pesan sekaligus — pola pengiriman tetap terlihat wajar dan aman.</p>
                    <div class="rotator-visual">
                        <div class="rotator-node active"></div>
                        <div class="rotator-node active"></div>
                        <div class="rotator-node active"></div>
                    </div>
                    <ul>
                        <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M20 6L9 17l-5-5" stroke="#3fe8f5" stroke-width="2.5"
                                    stroke-linecap="round" />
                            </svg>Rotasi merata ke semua nomor terhubung</li>
                        <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M20 6L9 17l-5-5" stroke="#3fe8f5" stroke-width="2.5"
                                    stroke-linecap="round" />
                            </svg>Menurunkan risiko nomor kena blokir</li>
                        <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M20 6L9 17l-5-5" stroke="#3fe8f5" stroke-width="2.5"
                                    stroke-linecap="round" />
                            </svg>Cocok untuk blast hingga 10.000/bulan</li>
                    </ul>
                </div>
                <div class="rotator-card priority reveal">
                    <span class="rotator-tag">PAKET ENTERPRISE</span>
                    <h3>Priority Rotator</h3>
                    <p>Selangkah lebih pintar: sistem otomatis mengecek <b style="color:var(--text)">kesehatan tiap
                            nomor</b> (reputasi &amp; sisa kuota amannya), lalu memprioritaskan nomor paling sehat untuk
                        mengirim lebih dulu. Hasilnya, pengiriman lebih cepat dan tetap aman meski volumenya unlimited.
                    </p>
                    <div class="rotator-visual">
                        <div class="rotator-node active" style="flex:2"></div>
                        <div class="rotator-node active" style="flex:1"></div>
                        <div class="rotator-node" style="flex:0.6"></div>
                    </div>
                    <ul>
                        <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M20 6L9 17l-5-5" stroke="#3fe8f5" stroke-width="2.5"
                                    stroke-linecap="round" />
                            </svg>Prioritaskan nomor dengan reputasi terbaik</li>
                        <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M20 6L9 17l-5-5" stroke="#3fe8f5" stroke-width="2.5"
                                    stroke-linecap="round" />
                            </svg>Kecepatan kirim lebih tinggi dari Smart Rotator</li>
                        <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M20 6L9 17l-5-5" stroke="#3fe8f5" stroke-width="2.5"
                                    stroke-linecap="round" />
                            </svg>Didesain untuk blast tanpa batas (unlimited)</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- PRICING -->
    <section id="harga">
        <div class="wrap">
            <div class="section-head center">
                <span class="eyebrow">Coba 7 Hari Gratis di Semua Paket</span>
                <h2>Harga simpel, tanpa biaya tersembunyi.</h2>
                <p>Mulai gratis 7 hari dulu, upgrade kapan saja saat bisnis Anda berkembang.</p>
            </div>
            <div class="pricing-grid">
                <div class="price-card reveal">
                    <div class="price-name">Starter</div>
                    <div class="price-amount">Rp 150rb<span>/bulan</span></div>
                    <div class="price-trial">✓ Coba 7 hari gratis</div>
                    <ul>
                        <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M20 6L9 17l-5-5" stroke="#3fe8f5" stroke-width="2.5"
                                    stroke-linecap="round" />
                            </svg>Maksimal 1 Nomor WhatsApp</li>
                        <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M20 6L9 17l-5-5" stroke="#3fe8f5" stroke-width="2.5"
                                    stroke-linecap="round" />
                            </svg>Limit Blast: 1.000 / bulan</li>
                        <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M20 6L9 17l-5-5" stroke="#3fe8f5" stroke-width="2.5"
                                    stroke-linecap="round" />
                            </svg>AI Engine: Standard AI (8B)</li>
                        <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M20 6L9 17l-5-5" stroke="#3fe8f5" stroke-width="2.5"
                                    stroke-linecap="round" />
                            </svg>Akses Agen CS: 1 User</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn btn-ghost">Mulai Gratis 7 Hari</a>
                </div>

                <div class="price-card featured reveal">
                    <span class="badge-pop">PALING POPULER</span>
                    <div class="price-name">Pro</div>
                    <div class="price-amount">Rp 300rb<span>/bulan</span></div>
                    <div class="price-trial">✓ Coba 7 hari gratis</div>
                    <ul>
                        <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M20 6L9 17l-5-5" stroke="#3fe8f5" stroke-width="2.5"
                                    stroke-linecap="round" />
                            </svg>Maksimal 3 Nomor WhatsApp</li>
                        <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M20 6L9 17l-5-5" stroke="#3fe8f5" stroke-width="2.5"
                                    stroke-linecap="round" />
                            </svg>Limit Blast: 10.000/bln + Smart Rotator</li>
                        <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M20 6L9 17l-5-5" stroke="#3fe8f5" stroke-width="2.5"
                                    stroke-linecap="round" />
                            </svg>AI Engine: Ultra Smart AI (70B)</li>
                        <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M20 6L9 17l-5-5" stroke="#3fe8f5" stroke-width="2.5"
                                    stroke-linecap="round" />
                            </svg>Akses Agen CS: Maks 5 CS</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn btn-primary">Mulai Gratis 7 Hari</a>
                </div>

                <div class="price-card reveal">
                    <div class="price-name">Enterprise</div>
                    <div class="price-amount">Rp 750rb<span>/bulan</span></div>
                    <div class="price-trial">✓ Coba 7 hari gratis</div>
                    <ul>
                        <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M20 6L9 17l-5-5" stroke="#3fe8f5" stroke-width="2.5"
                                    stroke-linecap="round" />
                            </svg>Maksimal 5 Nomor WhatsApp</li>
                        <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M20 6L9 17l-5-5" stroke="#3fe8f5" stroke-width="2.5"
                                    stroke-linecap="round" />
                            </svg>Limit Blast: UNLIMITED + Priority Rotator</li>
                        <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M20 6L9 17l-5-5" stroke="#3fe8f5" stroke-width="2.5"
                                    stroke-linecap="round" />
                            </svg>AI Engine: Ultra Smart AI (70B)</li>
                        <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M20 6L9 17l-5-5" stroke="#3fe8f5" stroke-width="2.5"
                                    stroke-linecap="round" />
                            </svg>Akses Agen CS: Unlimited CS</li>
                    </ul>
                    <a href="{{ route('register') }}" class="btn btn-ghost">Mulai Gratis 7 Hari</a>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section id="faq">
        <div class="wrap">
            <div class="section-head">
                <span class="eyebrow">FAQ</span>
                <h2>Pertanyaan yang sering ditanyakan.</h2>
            </div>
            <div class="faq-list">
                <div class="faq-item">
                    <button class="faq-q">Apakah perlu instal aplikasi tambahan di HP?<span class="plus"><svg
                                width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5v14M5 12h14" stroke="#3fe8f5" stroke-width="2"
                                    stroke-linecap="round" />
                            </svg></span></button>
                    <div class="faq-a">
                        <p>Tidak. Anda cukup scan QR code sekali di dashboard EGOS AI, mirip seperti membuka WhatsApp
                            Web. Nomor WhatsApp yang sudah Anda pakai sehari-hari bisa langsung dihubungkan.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-q">Apakah nomor WhatsApp saya aman, tidak akan diblokir?<span
                            class="plus"><svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5v14M5 12h14" stroke="#3fe8f5" stroke-width="2"
                                    stroke-linecap="round" />
                            </svg></span></button>
                    <div class="faq-a">
                        <p>Untuk pengiriman blast dalam jumlah besar, paket Pro dan Enterprise dilengkapi Smart Rotator
                            dan Priority Rotator yang membagi pengiriman ke beberapa nomor secara otomatis, sehingga
                            pola pengiriman tetap wajar dan risiko blokir jauh lebih rendah.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-q">Bagaimana AI belajar tentang bisnis saya?<span class="plus"><svg
                                width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5v14M5 12h14" stroke="#3fe8f5" stroke-width="2"
                                    stroke-linecap="round" />
                            </svg></span></button>
                    <div class="faq-a">
                        <p>Anda cukup memasukkan daftar FAQ, katalog produk, harga, dan gaya bahasa brand Anda ke
                            dashboard. AI akan menggunakan informasi tersebut untuk membalas pelanggan sesuai karakter
                            bisnis Anda.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-q">Bisa ganti paket kapan saja?<span class="plus"><svg width="18"
                                height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M12 5v14M5 12h14" stroke="#3fe8f5" stroke-width="2"
                                    stroke-linecap="round" />
                            </svg></span></button>
                    <div class="faq-a">
                        <p>Bisa. Anda dapat naik atau turun paket kapan saja sesuai kebutuhan bisnis, tanpa kontrak
                            jangka panjang.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FINAL CTA -->
    <section>
        <div class="wrap">
            <div class="cta-banner reveal">
                <h2>Waktunya chat pelanggan <span class="grad-text">dijawab AI</span>, bukan ditumpuk.</h2>
                <p>Coba EGOS AI gratis selama 7 hari. Tanpa kartu kredit, tanpa ribet — hanya butuh 10 menit untuk
                    mulai.</p>
                <div class="cta-buttons">
                    <a href="#harga" class="btn btn-primary">Mulai 7 Hari Gratis Sekarang →</a>
                    <a href="#cara-kerja" class="btn btn-ghost">Pelajari Cara Kerjanya</a>
                </div>
                <div class="cta-fine">Tanpa kartu kredit · Aktif dalam 10 menit · Batal kapan saja</div>
            </div>
        </div>
    </section>

    <footer>
        <div class="wrap">
            <div class="footer-top">
                <div class="footer-brand">
                    <img src="logo-egos.png" alt="EGOS AI">
                    <p>CS &amp; Sales AI untuk WhatsApp bisnis Anda. Dibuat untuk UMKM Indonesia yang ingin closing
                        lebih cepat.</p>
                </div>
                <div class="footer-cols">
                    <div class="footer-col">
                        <h4>Produk</h4>
                        <a href="#fitur">Fitur</a>
                        <a href="#rotator">Rotator</a>
                        <a href="#harga">Harga</a>
                    </div>
                    <div class="footer-col">
                        <h4>Perusahaan</h4>
                        <a href="#">Tentang Kami</a>
                        <a href="#">Hubungi Kami</a>
                        <a href="#faq">FAQ</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <span>© 2026 EGOS AI. Seluruh hak cipta dilindungi.</span>
                <span>Dibuat dengan ♥ untuk UMKM Indonesia</span>
            </div>
        </div>
    </footer>

    <script>
        // Scroll reveal
        const revealEls = document.querySelectorAll('.reveal');
        const io = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('visible');
                    io.unobserve(e.target);
                }
            });
        }, {
            threshold: 0.15
        });
        revealEls.forEach(el => io.observe(el));

        // FAQ accordion
        document.querySelectorAll('.faq-item').forEach(item => {
            const q = item.querySelector('.faq-q');
            const a = item.querySelector('.faq-a');
            q.addEventListener('click', () => {
                const isOpen = item.classList.contains('open');
                document.querySelectorAll('.faq-item.open').forEach(other => {
                    other.classList.remove('open');
                    other.querySelector('.faq-a').style.maxHeight = null;
                });
                if (!isOpen) {
                    item.classList.add('open');
                    a.style.maxHeight = a.scrollHeight + 'px';
                }
            });
        });
    </script>

</body>

</html>
