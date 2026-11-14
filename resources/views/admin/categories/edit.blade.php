@extends('layouts.admin')

@section('title', 'Edit Kategori')
@section('heading', 'Edit Kategori')

@section('content')
    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="bg-white rounded shadow p-6 max-w-lg">
        @csrf @method('PUT')
        @include('admin.categories._form')
    </form>
@endsection