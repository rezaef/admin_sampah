@extends('admin.layouts.app')
@section('title', 'Tambah Challenge')
@section('heading', 'Tambah Challenge')
@section('subheading', 'Buat challenge baru untuk periode tertentu.')
@section('content')
<div class="card stack"><form method="POST" action="{{ route('admin.challenges.store') }}" class="stack">@csrf @include('admin.challenges._form')</form></div>
@endsection
