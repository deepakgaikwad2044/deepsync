@extends('layouts.layouts')

@section('content')

<style>
:root {
    --error-brand: #8e44ad;
    --error-dark: #6c3483;
    --error-light: #bb8fce;
}

body {
    margin: 0;
    background: #08060d;
    font-family: system-ui, sans-serif;
}

.error429-wrapper {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 25px;
    overflow: hidden;
    position: relative;
    background:
        radial-gradient(circle at 15% 20%, rgba(142,68,173,.20), transparent 35%),
        radial-gradient(circle at 85% 80%, rgba(108,52,131,.18), transparent 35%),
        #08060d;
}

.error429-wrapper::before,
.error429-wrapper::after {
    content: "";
    position: absolute;
    width: 260px;
    height: 260px;
    border: 1px solid rgba(187,143,207,.12);
    border-radius: 50%;
    animation: orbit 12s linear infinite;
}

.error429-wrapper::after {
    width: 380px;
    height: 380px;
    animation-direction: reverse;
}

@keyframes orbit {
    to { transform: rotate(360deg); }
}

.error429-card {
    width: 100%;
    max-width: 540px;
    padding: 45px 30px;
    text-align: center;
    position: relative;
    z-index: 1;
    border: 1px solid rgba(187,143,207,.2);
    border-radius: 26px;
    background: rgba(20,15,27,.88);
    backdrop-filter: blur(18px);
    box-shadow: 0 25px 80px rgba(0,0,0,.55);
    animation: appear .7s ease;
}

@keyframes appear {
    from {
        opacity: 0;
        transform: translateY(25px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.error429-icon {
    width: 78px;
    height: 78px;
    margin: 0 auto 22px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 22px;
    color: var(--error-light);
    font-size: 38px;
    background: rgba(142,68,173,.15);
    border: 1px solid rgba(187,143,207,.25);
    box-shadow: 0 0 35px rgba(142,68,173,.18);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    50% {
        box-shadow: 0 0 45px rgba(142,68,173,.38);
        transform: scale(1.04);
    }
}

.error429-code {
    font-size: clamp(5rem, 18vw, 8rem);
    font-weight: 900;
    line-height: 1;
    letter-spacing: -5px;
    background: linear-gradient(135deg, #fff, #bb8fce, #8e44ad);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin: 10px 0 18px;
    text-shadow: 0 10px 35px rgba(142,68,173,.12);
}

.error429-title {
    color: #fff;
    font-size: clamp(1.4rem, 5vw, 2rem);
    font-weight: 750;
    margin-bottom: 12px;
}

.error429-message {
    color: #b8adbf;
    font-size: 1rem;
    line-height: 1.7;
    max-width: 390px;
    margin: 0 auto;
}

.error429-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(187,143,207,.35), transparent);
    margin: 28px 0;
}

.error429-info {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 15px;
    border-radius: 30px;
    color: #d9b8e9;
    background: rgba(142,68,173,.12);
    border: 1px solid rgba(187,143,207,.18);
    font-size: .82rem;
}

.error429-info span {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #bb8fce;
    box-shadow: 0 0 10px #bb8fce;
}

.error429-actions {
    margin-top: 30px;
}

.error429-home {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 13px 25px;
    border-radius: 12px;
    color: #fff;
    text-decoration: none;
    font-weight: 650;
    background: linear-gradient(135deg, #8e44ad, #6c3483);
    box-shadow: 0 10px 30px rgba(142,68,173,.25);
    transition: .3s ease;
}

.error429-home:hover {
    transform: translateY(-3px);
    box-shadow: 0 14px 35px rgba(142,68,173,.4);
    color: #fff;
}

.error429-footer {
    color: #756b7c;
    font-size: .78rem;
    margin-top: 28px;
}

@media(max-width:480px) {
    .error429-card {
        padding: 38px 20px;
        border-radius: 20px;
    }

    .error429-wrapper {
        padding: 15px;
    }

    .error429-divider {
        margin: 23px 0;
    }
}
</style>

<div class="error429-wrapper">

    <div class="error429-card">

        <div class="error429-icon">⚡</div>

        <div class="error429-code">429</div>

        <div class="error429-title">
            Too Many Requests
        </div>

        <p class="error429-message">
            {{ $message ?? "Whoa! You've reached the request limit. Please slow down and try again shortly." }}
        </p>

        <div class="error429-divider"></div>

        <div class="error429-info">
            <span></span>
            Request limit protection is active
        </div>

        <div class="error429-actions">
    <a href="{{ route('home') }}"
       class="error429-home"
       onclick="if (window.history.length > 1) { event.preventDefault(); window.history.back(); }">
        <span>←</span>
        Go Back
    </a>
</div>

        <div class="error429-footer">
            Protected by Deep Sync Framework
        </div>

    </div>

</div>

@endsection
