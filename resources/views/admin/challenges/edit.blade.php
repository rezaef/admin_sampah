@extends('admin.layouts.app')
@section('title', 'Edit Challenge')
@section('heading', 'Edit Challenge')
@section('subheading', 'Perbarui challenge yang sudah tersedia.')
@section('content')
<div class="card stack"><form method="POST" action="{{ route('admin.challenges.update', $challenge) }}" class="stack">@csrf @method('PUT') @include('admin.challenges._form')</form></div>
@endsection
