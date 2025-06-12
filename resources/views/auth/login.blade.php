@extends('layouts.authLayout')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        font-family: 'Segoe UI', sans-serif;
    }

    .login-container {
        background: #fff;
        max-width: 400px;
        margin: 60px auto;
        padding: 40px 30px 30px 30px;
        border-radius: 16px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2);
    }

    .login-title {
        text-align: center;
        font-size: 2rem;
        font-weight: 700;
        color: #667eea;
        margin-bottom: 24px;
        letter-spacing: 1px;
    }

    .form-group {
        margin-bottom: 22px;
    }

    label {
        font-weight: 600;
        color: #333;
    }

    input[type="email"],
    input[type="password"] {
        width: 100%;
        padding: 12px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        margin-top: 6px;
        font-size: 1rem;
        transition: border 0.2s;
        box-sizing: border-box;
    }

    input[type="email"]:focus,
    input[type="password"]:focus {
        border-color: #667eea;
        outline: none;
    }

    .form-group input[type="checkbox"] {
        margin-right: 8px;
    }

    .error {
        background: #ffe5e5;
        color: #d90429;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 18px;
        text-align: center;
        font-weight: 500;
    }

    button[type="submit"] {
        width: 100%;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        color: #fff;
        border: none;
        padding: 14px 0;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 700;
        cursor: pointer;
        transition: background 0.2s;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.15);
    }

    button[type="submit"]:hover {
        background: linear-gradient(90deg, #764ba2 0%, #667eea 100%);
    }
</style>

<div class="login-container">
    <div class="login-title">Login</div>
    @if ($errors->any())
    <div class="error">
        {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <div class="form-group" style="display: flex; align-items: center;">
            <input type="checkbox" name="remember" id="remember">
            <label for="remember" style="margin: 0; font-weight: 400;">Remember me</label>
        </div>

        <button type="submit">Login</button>
    </form>
</div>
@endsection