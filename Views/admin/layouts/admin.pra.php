<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Deep Sync Admin</title>

<style>
:root {
    color-scheme: dark;

    --bg: #000000;
    --surface: #0d0d0d;
    --surface-hover: #151019;
    --border: #2a2a2a;

    --text: #ffffff;
    --muted: #aaaaaa;

    --primary: #8e44ad;
    --primary-dark: #6c3483;
    --primary-light: #bb8fce;
    --primary-soft: rgba(142, 68, 173, 0.14);
    --primary-border: rgba(142, 68, 173, 0.45);
}

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    min-height: 100vh;
    background: linear-gradient(145deg, #000000, #100b14);
    color: var(--text);
    font-family: Arial, sans-serif;
}

a {
    color: var(--primary-light);
    text-decoration: none;
    transition: color .2s ease;
}

a:hover {
    color: #ffffff;
}

button,
input {
    font: inherit;
}

.admin-shell {
    width: min(1100px, calc(100% - 32px));
    margin: auto;
    padding: 20px 0;
}

.admin-card {
    width: min(440px, 100%);
    margin: 55px auto;
    padding: 28px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-top: 2px solid var(--primary);
    border-radius: 14px;
    box-shadow: 0 12px 35px rgba(0, 0, 0, .35);
}

.admin-card h1 {
    margin-top: 0;
}

.admin-muted {
    color: var(--muted);
}

.admin-field {
    margin-top: 20px;
}

.admin-field label {
    display: block;
    margin-bottom: 8px;
}

.admin-field input {
    width: 100%;
    padding: 13px;
    color: #ffffff;
    background: #050505;
    border: 1px solid var(--border);
    border-radius: 8px;
    outline: none;
    transition: border-color .2s ease, box-shadow .2s ease;
}

.admin-field input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px var(--primary-soft);
}

.admin-button {
    display: block;
    width: 100%;
    margin-top: 22px;
    padding: 13px;
    color: #ffffff;
    background: var(--primary);
    border: 1px solid var(--primary);
    border-radius: 8px;
    font-weight: bold;
    text-align: center;
    cursor: pointer;
    transition: background .2s ease, border-color .2s ease, transform .2s ease;
}

.admin-button:hover {
    color: #ffffff;
    background: var(--primary-dark);
    border-color: var(--primary-dark);
}

.admin-button:focus-visible {
    outline: 2px solid var(--primary-light);
    outline-offset: 3px;
}

.admin-button:active {
    transform: translateY(1px);
}

.admin-error {
    margin-top: 8px;
    color: #ff7777;
}

.admin-alert {
    margin: 18px 0;
    padding: 12px;
    background: var(--primary-soft);
    border: 1px solid var(--primary-border);
    border-left: 3px solid var(--primary);
    border-radius: 8px;
    overflow-wrap: anywhere;
}

.admin-topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
    padding: 16px 0;
    border-bottom: 1px solid var(--primary-border);
}

.admin-footer {
    margin-top: 50px;
    padding: 18px 0;
    color: var(--muted);
    border-top: 1px solid var(--border);
}

::selection {
    color: #ffffff;
    background: var(--primary-dark);
}

@media(max-width: 520px) {
    .admin-card {
        margin: 30px auto;
        padding: 22px;
    }

    .admin-topbar {
        align-items: flex-start;
        flex-direction: column;
        gap: 6px;
    }
}
</style>
</head>

<body>
@include("admin.layouts.header")

<main class="admin-shell">
@yield("content")
</main>

@include("admin.layouts.footer")
@yield("scripts")
</body>
</html>
