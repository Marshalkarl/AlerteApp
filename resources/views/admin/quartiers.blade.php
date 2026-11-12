@extends('layouts.dashboard')
@section('title', 'Quartiers')

@section('nav')
  <a href="/admin">Tableau de bord</a>
  <a href="/admin/habitants">Habitants</a>
  <a href="/admin/quartiers" class="active">Quartiers</a>
  <form method="POST" action="/admin/deconnexion">@csrf<button class="link">Déconnexion</button></form>
@endsection

@section('content')
<h1>Quartiers</h1>

<section class="card">
  <h2>Ajouter un quartier</h2>
  <form method="POST" action="/admin/quartiers" class="form">
    @csrf
    <label>Nom du quartier
      <input name="name" maxlength="60" placeholder="Ex. Baguida" required>
    </label>
    <button class="btn">Ajouter</button>
  </form>
</section>

<section class="card">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Quartier</th><th>Habitants</th><th></th></tr></thead>
      <tbody>
      @foreach ($quartiers as $q)
        <tr>
          <td>
            <form method="POST" action="/admin/quartiers/{{ $q->id }}" style="display:flex;gap:8px">
              @csrf @method('PUT')
              <input name="name" value="{{ $q->name }}" maxlength="60" required>
              <button class="btn">Renommer</button>
            </form>
          </td>
          <td>{{ $q->subscribers_count }}</td>
          <td>
            <form method="POST" action="/admin/quartiers/{{ $q->id }}"
                  onsubmit="return confirm('Supprimer ce quartier ?')">
              @csrf @method('DELETE')
              <button class="link danger-text">Supprimer</button>
            </form>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</section>
@endsection