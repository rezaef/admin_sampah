@extends('admin.layouts.app')
@section('title', 'Tambah Reward')
@section('heading', 'Tambah Reward')
@section('subheading', 'Buat item reward baru untuk ditampilkan di aplikasi.')
@section('content')
<div class="card stack"><form method="POST" action="{{ route('admin.rewards.store') }}" class="stack">@csrf @include('admin.rewards._form')</form></div>
@endsection
