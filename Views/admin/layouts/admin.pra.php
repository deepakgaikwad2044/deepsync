<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Deep Sync Admin</title>
<style>
:root {
    color-scheme: dark;
    --bg: #000;
    --surface: #0d0d0d;
    --border: #2a2a2a;
    --text: #fff;
    --muted: #aaa;
}
* { box-sizing: border-box; }
body {
    margin: 0;
    min-height: 100vh;
    background: linear-gradient(145deg,#000,#171717);
    color: var(--text);
    font-family: Arial, sans-serif;
}
a { color: inherit; text-decoration: none; }
.admin-shell {
    width: min(1100px,calc(100% - 32px));
    margin: auto;
    padding: 20px 0;
}
.admin-card {
    width: min(440px,100%);
    margin: 55px auto;
    padding: 28px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 14px;
}
.admin-muted { color: var(--muted); }
.admin-field { margin-top: 20px; }
.admin-field label {
    display: block;
    margin-bottom: 8px;
}
.admin-field input {
    width: 100%;
    padding: 13px;
    color: #fff;
    background: #050505;
    border: 1px solid var(--border);
    border-radius: 8px;
}
.admin-button {
    display: block;
    width: 100%;
    margin-top: 22px;
    padding: 13px;
    color: #000;
    background: #fff;
    border: 0;
    border-radius: 8px;
    font-weight: bold;
    text-align: center;
    cursor: pointer;
}
.admin-error { margin-top: 8px; color: #ff7777; }
.admin-alert {
    margin: 18px 0;
    padding: 12px;
    background: #151515;
    border: 1px solid var(--border);
    border-radius: 8px;
}
.admin-topbar {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    padding: 16px 0;
    border-bottom: 1px solid var(--border);
}
.admin-footer {
    margin-top: 50px;
    padding: 18px 0;
    color: var(--muted);
    border-top: 1px solid var(--border);
}
@media(max-width:520px) {
    .admin-card { margin: 30px auto; padding: 22px; }
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
