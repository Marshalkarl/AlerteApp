@extends('layout')
@section('c')
<h1>Espace administrateur</h1>
<form method="post" action="/admin/connexion">@csrf
  <label>Clé d’accès<input name="key" type="password" required></label>
  @if($errors->any())<p class="err">{{ $errors->first() }}</p>@endif
  <button class="btn">Se connecter</button>
</form>
@endsection
