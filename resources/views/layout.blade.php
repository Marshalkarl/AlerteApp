<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="theme-color" content="#050b14">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="Alertes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<link rel="manifest" href="/manifest.webmanifest">
<link rel="icon" href="/icon-192.png">
<link rel="apple-touch-icon" href="/icons/apple-touch-icon.png">
<title>Alerte Inondations Lomé</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/dashboard.css?v=2">
</head>
<body>
<header class="topbar">
  <a class="brand" href="/">🌊 Alerte Inondations</a>
  <span class="muted city">Lomé</span>
</header>
<main class="wrap pub">@yield('c')</main>
<script>if('serviceWorker' in navigator)navigator.serviceWorker.register('/sw.js')</script>
</body>
</html>