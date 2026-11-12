<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
  <meta name="theme-color" content="#050b14">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-title" content="Alertes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
  <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
  <title>@yield('title') · Alerte Inondations</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>
  <header class="topbar">
    <a class="brand" href="/">🌊 Alerte Inondations</a>
    <nav>@yield('nav')</nav>
  </header>
  <main class="wrap">
    @if (session('ok')) <div class="notice ok">{{ session('ok') }}</div> @endif
    @if ($errors->any()) <div class="notice err">{{ $errors->first() }}</div> @endif
    @yield('content')
  </main>

  <script>
    // Compteurs animés sur les chiffres clés (uniquement les valeurs purement numériques)
    document.querySelectorAll('.stat strong').forEach(function (el) {
      var txt = el.textContent.trim();
      if (!/^\d+$/.test(txt)) return;
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
      var end = parseInt(txt, 10), start = null, dur = 1200;
      el.textContent = '0';
      function step(t) {
        if (!start) start = t;
        var p = Math.min((t - start) / dur, 1);
        el.textContent = Math.round(end * (1 - Math.pow(1 - p, 4)));
        if (p < 1) requestAnimationFrame(step);
      }
      setTimeout(function () { requestAnimationFrame(step); }, 250);
    });
  </script>
</body>
</html>