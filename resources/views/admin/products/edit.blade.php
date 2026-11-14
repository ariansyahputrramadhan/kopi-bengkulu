@extends('layouts.admin')

@section('title', 'Edit Produk')
@section('heading', 'Edit Produk')

@section('content')
    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data"
          class="bg-white rounded shadow p-6 max-w-2xl">
        @csrf @method('PUT')
        @include('admin.products._form')
    </form>
@endsection