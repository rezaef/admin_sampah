@extends('admin.layouts.app')
@section('title', 'Edit Reward')
@section('heading', 'Edit Reward')
@section('subheading', 'Perbarui data reward yang sudah tersedia.')
@section('content')
<div class="card stack"><form method="POST" action="{{ route('admin.rewards.update', $reward) }}" class="stack">@csrf @method('PUT') @include('admin.rewards._form')</form></div>
@endsection
