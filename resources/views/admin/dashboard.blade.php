@extends('layouts.dashboard')
@section('title', 'Administration')

@section('nav')
  <a href="/admin" class="active">Tableau de bord</a>
  <a href="/admin/habitants">Habitants</a>
  <form method="POST" action="/admin/deconnexion">@csrf<button class="link">Déconnexion</button></form>
@endsection

@section('content')
<h1>Tableau de bord</h1>

<section class="stats">
  <div class="stat"><span>Habitants inscrits</span><strong>{{ $stats['inscrits'] }}</strong>
    <small>{{ $stats['en_attente'] }} en attente de confirmation</small></div>
  <div class="stat"><span>Alertes (30 jours)</span><strong>{{ $stats['alertes_30j'] }}</strong></div>
  <div class="stat"><span>Messages envoyés (30 jours)</span><strong>{{ $stats['messages_30j'] }}</strong></div>
  <div class="stat"><span>Canaux</span><strong>{{ $canaux['sms'] ?? 0 }} SMS · {{ $canaux['telegram'] ?? 0 }} Telegram</strong></div>
</section>

<div class="grid2">
  <section class="card">
    <h2>Inscrits par quartier</h2>
    @php $max = max(1, (int) $quartiers->max('subscribers_count')); @endphp
    @foreach ($quartiers as $q)
      <div class="bar-row">
        <span>{{ $q->name }}</span>
        <div class="bar"><i style="width: {{ round($q->subscribers_count / $max * 100) }}%"></i></div>
        <b>{{ $q->subscribers_count }}</b>
      </div>
    @endforeach
  </section>

  <section class="card">
    <h2>Alertes envoyées (14 derniers jours)</h2>
    @php $maxJ = max(1, (int) $jours->max('n')); @endphp
    <div class="cols">
      @foreach ($jours as $j)
        <div class="col" title="{{ $j['label'] }} : {{ $j['n'] }}">
          <i style="height: {{ round($j['n'] / $maxJ * 100) }}%"></i>
          <small>{{ $loop->index % 2 === 0 ? $j['label'] : '' }}</small>
        </div>
      @endforeach
    </div>
  </section>
</div>

<section class="card">
  <h2>Envoyer une alerte</h2>
  <form method="POST" action="/admin/alerte" class="form"
        onsubmit="return confirm('Envoyer cette alerte à tous les habitants du quartier ?')">
    @csrf
    <div class="grid2">
      <label>Quartier
        <select name="quartier_id" required>
          @foreach ($quartiers as $q)
            <option value="{{ $q->id }}">{{ $q->name }} ({{ $q->subscribers_count }})</option>
          @endforeach
        </select>
      </label>
      <label>Type
        <select name="type" required>
          @foreach (\App\Support\Types::LABELS as $k => $v)
            <option value="{{ $k }}">{{ \App\Support\Types::icon($k) }} {{ $v }}</option>
          @endforeach
        </select>
      </label>
    </div>
    <label>Message
      <textarea name="message" maxlength="140" rows="3" required
                oninput="this.nextElementSibling.textContent = this.value.length + '/140'"></textarea>
      <small class="muted">0/140</small>
    </label>
    <button class="btn">Envoyer l’alerte</button>
  </form>
</section>

<section class="card">
  <h2>Dernières alertes</h2>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Date</th><th>Quartier</th><th>Type</th><th>Message</th><th>Envoyés</th></tr></thead>
      <tbody>
      @forelse ($alerts as $a)
        <tr>
          <td>{{ $a->created_at->format('d/m/Y H:i') }}</td>
          <td>{{ $a->quartier->name }}</td>
          <td>{{ \App\Support\Types::icon($a->type) }} {{ \App\Support\Types::label($a->type) }}</td>
          <td>{{ $a->message }}</td>
          <td>{{ $a->sent_count }}</td>
        </tr>
      @empty
        <tr><td colspan="5" class="muted">Aucune alerte envoyée pour le moment.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
</section>
@endsection