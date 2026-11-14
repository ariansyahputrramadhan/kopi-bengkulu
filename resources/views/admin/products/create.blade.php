@extends('layouts.admin')

@section('title', 'Tambah Produk')
@section('heading', 'Tambah Produk')

@section('content')
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data"
          class="bg-white rounded shadow p-6 max-w-2xl">
        @csrf
        @include('admin.products._form')
    </form>
@endsection