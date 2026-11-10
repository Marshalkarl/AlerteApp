@extends('layout')
@section('c')
<h1>Restez prévenu dans votre quartier</h1>
<p class="mu">Recevez par SMS ou Telegram les alertes d’inondation et de coupure d’eau ou d’électricité.</p>
@if(session('ok'))<p class="ok">{{ session('ok') }}</p>@endif
<form method="post" action="/inscription">@csrf
  <label>Numéro de téléphone
    <input name="phone" type="tel" inputmode="tel" autocomplete="tel" placeholder="Ex. : 90 00 00 00" value="{{ old('phone') }}" required>
  </label>
  <label>Quartier
    <select name="quartier_id">@foreach($quartiers as $q)<option value="{{ $q->id }}" @selected(old('quartier_id') == $q->id)>{{ $q->name }}</option>@endforeach</select>
  </label>
  <fieldset><div class="opt">
    <label><input type="radio" name="channel" value="sms" checked>SMS</label>
    <label><input type="radio" name="channel" value="telegram">Telegram</label>
  </div></fieldset>
  <label class="chk"><input type="checkbox" name="consent" value="1"> J’accepte de recevoir des alertes pour ce quartier.</label>
  @if($errors->any())<p class="err">{{ $errors->first() }}</p>@endif
  <button class="btn">S’inscrire</button>
</form>
<h2>Alertes des dernières 24 h</h2>
@forelse($alerts as $a)
  <div class="al t{{ $a->type }}"><b>{{ $a->quartier->name }}</b> <span class="mu">{{ $a->created_at->diffForHumans() }}</span><p>{{ $a->message }}</p></div>
@empty
  <p class="mu">Aucune alerte pour le moment.</p>
@endforelse
@endsection
