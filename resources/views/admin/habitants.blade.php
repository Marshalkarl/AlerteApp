@extends('layouts.dashboard')
@section('title', 'Habitants')

@section('nav')
  <a href="/admin">Tableau de bord</a>
  <a href="/admin/habitants" class="active">Habitants</a>
  <form method="POST" action="/admin/deconnexion">@csrf<button class="link">Déconnexion</button></form>
@endsection

@section('content')
<h1>Habitants</h1>

<form method="GET" class="card filters">
  <input type="search" name="q" value="{{ request('q') }}" placeholder="Rechercher un numéro">
  <select name="quartier">
    <option value="">Tous les quartiers</option>
    @foreach ($quartiers as $q)
      <option value="{{ $q->id }}" @selected(request('quartier') == $q->id)>{{ $q->name }}</option>
    @endforeach
  </select>
  <select name="statut">
    <option value="">Tous les statuts</option>
    <option value="verifie" @selected(request('statut') === 'verifie')>Confirmés</option>
    <option value="attente" @selected(request('statut') === 'attente')>En attente</option>
  </select>
  <button class="btn">Filtrer</button>
</form>

<section class="card">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Numéro</th><th>Quartier</th><th>Canal</th><th>Statut</th><th>Inscrit le</th><th></th></tr></thead>
      <tbody>
      @forelse ($subscribers as $s)
        <tr>
          <td>{{ $s->phone }}</td>
          <td>{{ $s->quartier->name }}</td>
          <td>{{ $s->channel === 'telegram' ? 'Telegram' : 'SMS' }}</td>
          <td>
            @if ($s->verified_at) <span class="badge ok">Confirmé</span>
            @else <span class="badge wait">En attente</span> @endif
          </td>
          <td>{{ $s->created_at->format('d/m/Y') }}</td>
          <td>
            <form method="POST" action="/admin/habitants/{{ $s->id }}"
                  onsubmit="return confirm('Supprimer cet habitant ?')">
              @csrf @method('DELETE')
              <button class="link danger-text">Supprimer</button>
            </form>
          </td>
        </tr>
      @empty
        <tr><td colspan="6" class="muted">Aucun habitant trouvé.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>

  <div class="pager">
    @if ($subscribers->onFirstPage()) <span>← Précédent</span>
    @else <a href="{{ $subscribers->previousPageUrl() }}">← Précédent</a> @endif
    <span>Page {{ $subscribers->currentPage() }}</span>
    @if ($subscribers->hasMorePages()) <a href="{{ $subscribers->nextPageUrl() }}">Suivant →</a>
    @else <span>Suivant →</span> @endif
  </div>
</section>
@endsection