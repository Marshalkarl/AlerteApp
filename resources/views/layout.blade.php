<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="theme-color" content="#0f6f8f">
<link rel="manifest" href="/manifest.webmanifest">
<link rel="icon" href="/icon-192.png">
<title>Alerte Inondations Lomé</title>
<style>
:root{--bg:#e9f1f3;--card:#fbfdfd;--ink:#0c2330;--mu:#4f6472;--bd:#c9d8de;--ac:#0f6f8f;--on:#fff;--s1:#f2b134;--s2:#ee7b30;--s3:#d62839}
@media(prefers-color-scheme:dark){:root{--bg:#0a1a24;--card:#112431;--ink:#e6f0f4;--mu:#93aab7;--bd:#223a4a;--ac:#4cc3e6;--on:#06202b}}
*{box-sizing:border-box}
body{margin:0;background:var(--bg);color:var(--ink);font:16px/1.5 system-ui,sans-serif;padding-bottom:env(safe-area-inset-bottom)}
header{display:flex;justify-content:space-between;align-items:center;padding:calc(14px + env(safe-area-inset-top)) 18px 10px;max-width:560px;margin:auto}
header a{color:inherit;text-decoration:none}
main{max-width:560px;margin:auto;padding:0 18px 36px}
h1{font-size:1.7rem;line-height:1.1;margin:8px 0}
h2{font-size:1.1rem;margin:28px 0 8px}
.mu{color:var(--mu)}
form{background:var(--card);border:1px solid var(--bd);border-radius:14px;padding:16px}
label{display:block;font-weight:600;margin-bottom:12px}
input,select,textarea{display:block;width:100%;font:inherit;color:var(--ink);background:var(--bg);border:1px solid var(--bd);border-radius:10px;padding:12px;min-height:48px;margin-top:5px}
fieldset{border:0;padding:0;margin:0 0 12px}
.opt{display:flex;gap:10px}
.opt label{flex:1;font-weight:400;border:1px solid var(--bd);border-radius:10px;padding:12px;text-align:center;margin:0;background:var(--bg)}
.opt input{display:inline;width:auto;min-height:0;margin:0 6px 0 0}
.chk{display:flex;gap:10px;font-weight:400}
.chk input{width:22px;min-height:22px;margin:2px 0 0}
.btn{display:block;width:100%;min-height:50px;border:0;border-radius:10px;background:var(--ac);color:var(--on);font:700 1rem system-ui;text-align:center;line-height:50px;text-decoration:none;cursor:pointer}
.err{color:var(--s3);font-weight:600}
.ok{background:var(--card);border-left:5px solid var(--ac);padding:10px 12px}
.al{border-left:5px solid var(--s2);padding:8px 0 8px 12px;border-bottom:1px solid var(--bd)}
.al p{margin:2px 0 0}
.tinond{border-left-color:var(--s3)}.teau{border-left-color:var(--ac)}.telec{border-left-color:var(--s1)}
</style>
</head>
<body>
<header><a href="/"><b>Alerte Inondations</b></a><span class="mu">Lomé</span></header>
<main>@yield('c')</main>
<script>if('serviceWorker' in navigator)navigator.serviceWorker.register('/sw.js')</script>
</body>
</html>
