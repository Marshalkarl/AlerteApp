@extends('layout')
@section('c')
<h1>Envoyer une alerte</h1>
@if(session('ok'))<p class="ok">{{ session('ok') }}</p>@endif
<form method="post" action="/admin/alerte">@csrf
  <label>Quartier
    <select name="quartier_id">@foreach($quartiers as $q)<option value="{{ $q->id }}">{{ $q->name }} ({{ $q->subscribers_count }} inscrits)</option>@endforeach</select>
  </label>
  <label>Type
    <select name="type"><option value="inond">Inondation</option><option value="eau">Coupure d’eau</option><option value="elec">Coupure d’électricité</option></select>
  </label>
  <label>Message (140 caractères max)
    <textarea name="message" id="m" maxlength="140" required placeholder="Ex. : Coupure d’électricité programmée de 9 h à 13 h."></textarea>
  </label>
  <p class="mu" id="cnt">0 / 140</p>
  @if($errors->any())<p class="err">{{ $errors->first() }}</p>@endif
  <button class="btn" onclick="return confirm('Envoyer cette alerte à tous les inscrits du quartier ?')">Envoyer l’alerte</button>
</form>
<h2>Derniers envois</h2>
@foreach($alerts as $a)
  <div class="al t{{ $a->type }}"><b>{{ $a->quartier->name }}</b> <span class="mu">{{ $a->created_at->diffForHumans() }}, {{ $a->sent_count }} envoyés</span><p>{{ $a->message }}</p></div>
@endforeach
<script>m.oninput=()=>cnt.textContent=m.value.length+' / 140'</script>
@endsection
