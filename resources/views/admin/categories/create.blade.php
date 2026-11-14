@extends('layouts.admin')

@section('title', 'Tambah Kategori')
@section('heading', 'Tambah Kategori')

@section('content')
    <form method="POST" action="{{ route('admin.categories.store') }}" class="bg-white rounded shadow p-6 max-w-lg">
        @csrf
        @include('admin.categories._form')
    </form>
@endsection