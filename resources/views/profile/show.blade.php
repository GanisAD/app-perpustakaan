@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
    <h1>Profil Saya</h1>

    {{-- Info Profil --}}
    <div style="background: #f8fafc; padding: 16px; border-radius: 6px; margin-bottom: 24px; border: 1px solid #e2e8f0;">
        <p style="margin: 4px 0;"><strong>Nama:</strong> {{ $user->name }}</p>
        <p style="margin: 4px 0;"><strong>Email:</strong> {{ $user->email }}</p>
        <p style="margin: 4px 0;"><strong>Role:</strong> {{ ucfirst($user->role) }}</p>
    </div>

    <h2>Ganti Password</h2>

    <form action="{{ route('profile.password.update') }}" method="POST" style="max-width: 400px;">
        @csrf
        @method('PUT')

        <label for="current_password">Password Lama</label>
        <input type="password" name="current_password" id="current_password">
        @error('current_password')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="password">Password Baru</label>
        <input type="password" name="password" id="password">
        @error('password')
            <div class="error">{{ $message }}</div>
        @enderror

        <label for="password_confirmation">Konfirmasi Password Baru</label>
        <input type="password" name="password_confirmation" id="password_confirmation">

        <button type="submit" class="btn">Simpan Password Baru</button>
    </form>
@endsection