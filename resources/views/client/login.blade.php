@extends('layouts.dashboard')
@section('title', 'Connexion')

@section('nav')<a href="/">Accueil</a>@endsection

@section('content')
<div class="auth card">
  <h1>Mon espace</h1>
  <p class="muted">Entrez le numéro avec lequel vous êtes inscrit. Nous vous envoyons un code de connexion par SMS ou Telegram.</p>
  <form method="POST" action="/connexion" class="form">
    @csrf
    <label>Numéro de téléphone
      <input name="phone" type="tel" inputmode="tel" placeholder="90 00 00 00" value="{{ old('phone') }}" required autofocus>
    </label>
    <button class="btn">Recevoir mon code</button>
  </form>
  <p class="muted">Pas encore inscrit ? <a href="/">Recevoir les alertes</a></p>
</div>
@endsection