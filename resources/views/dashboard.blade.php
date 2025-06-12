@extends('layouts.authLayout')

@section('content')
    <p>Selamat datang, {{ auth()->user()->name }}!</p>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">Logout</button>
    </form>
@endsection