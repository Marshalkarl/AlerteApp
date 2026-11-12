@extends('layouts.dashboard')
@section('title', 'Code de connexion')

@section('nav')<a href="/connexion">Changer de numéro</a>@endsection

@section('content')
<div class="auth card">
  <h1>Entrez votre code</h1>
  <p class="muted">Le code à 6 chiffres est valable 10 minutes.</p>
  <form method="POST" action="/connexion/code" class="form">
    @csrf
    <label>Code
      <input name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="6" pattern="[0-9]{6}"
             placeholder="000000" required autofocus>
    </label>
    <button class="btn">Me connecter</button>
  </form>
</div>
@endsection