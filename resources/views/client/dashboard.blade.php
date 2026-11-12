@extends('layouts.dashboard')
@section('title', 'Mon espace')

@section('nav')
  <a href="/">Accueil</a>
  <form method="POST" action="/deconnexion">@csrf<button class="link">Déconnexion</button></form>
@endsection

@section('content')
<h1>Mon espace</h1>

@forelse ($actives as $a)
  <div class="banner {{ $a->type }}">
    <span class="ico">{{ \App\Support\Types::icon($a->type) }}</span>
    <div>
      <strong>{{ \App\Support\Types::label($a->type) }} · {{ $s->quartier->name }}</strong><br>
      {{ $a->message }}<br>
      <small>{{ $a->created_at->format('d/m/Y à H:i') }}</small>
    </div>
  </div>
@empty
  <div class="calm">✅ Aucune alerte en cours à {{ $s->quartier->name }}.</div>
@endforelse

<section class="stats">
  <div class="stat"><span>Mon quartier</span><strong>{{ $s->quartier->name }}</strong></div>
  <div class="stat"><span>Mon numéro</span><strong>{{ $s->phone }}</strong></div>
  <div class="stat"><span>Je reçois les alertes par</span><strong>{{ $s->channel === 'telegram' ? 'Telegram' : 'SMS' }}</strong></div>
</section>

<section class="card">
  <h2>Historique des alertes</h2>
  @forelse ($historique as $a)
    <div class="hist">
      <span class="ico">{{ \App\Support\Types::icon($a->type) }}</span>
      <div>
        <strong>{{ \App\Support\Types::label($a->type) }}</strong>
        <small class="muted"> · {{ $a->created_at->format('d/m/Y H:i') }}</small><br>
        {{ $a->message }}
      </div>
    </div>
  @empty
    <p class="muted">Aucune alerte reçue pour le moment.</p>
  @endforelse

  <div class="pager">
    @if ($historique->onFirstPage()) <span>← Plus récentes</span>
    @else <a href="{{ $historique->previousPageUrl() }}">← Plus récentes</a> @endif
    @if ($historique->hasMorePages()) <a href="{{ $historique->nextPageUrl() }}">Plus anciennes →</a>
    @else <span>Plus anciennes →</span> @endif
  </div>
</section>

<div class="grid2">
  <section class="card">
    <h2>Changer de quartier</h2>
    <form method="POST" action="/mon-espace/quartier" class="form">
      @csrf @method('PUT')
      <select name="quartier_id" required>
        @foreach ($quartiers as $q)
          <option value="{{ $q->id }}" @selected($q->id === $s->quartier_id)>{{ $q->name }}</option>
        @endforeach
      </select>
      <button class="btn">Enregistrer</button>
    </form>
  </section>

  <section class="card">
    <h2>Me désinscrire</h2>
    <p class="muted">Vous ne recevrez plus aucune alerte et votre numéro sera supprimé.</p>
    <form method="POST" action="/mon-espace" onsubmit="return confirm('Vous désinscrire définitivement ?')">
      @csrf @method('DELETE')
      <button class="btn danger">Me désinscrire</button>
    </form>
  </section>
</div>
@endsection