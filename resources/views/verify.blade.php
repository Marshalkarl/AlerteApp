@extends('layout')
@section('c')
<h1>Confirmez votre inscription</h1>
@if($s->channel === 'telegram')
  <p class="mu">Ouvrez Telegram et appuyez sur « Démarrer » pour recevoir les alertes de {{ $s->quartier->name }}.</p>
  <a class="btn" href="https://t.me/{{ config('alerts.telegram_bot') }}?start={{ $s->token }}">Ouvrir Telegram</a>
@else
  <p class="mu">Saisissez le code reçu par SMS au {{ $s->phone }}.</p>
  <form method="post">@csrf
    <label>Code à 6 chiffres
      <input name="code" inputmode="numeric" maxlength="6" autocomplete="one-time-code" required>
    </label>
    @if($errors->any())<p class="err">{{ $errors->first() }}</p>@endif
    <button class="btn">Confirmer</button>
  </form>
@endif
@endsection
