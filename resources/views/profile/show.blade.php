{{-- File: resources/views/profile/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('content')
    <h1>Profil Pengguna</h1>

    <table class="info" style="max-width: 600px;">
        <tr>
            <th style="width: 160px; background: #f3f4f6;">Nama</th>
            <td>{{ $user->name }}</td>
        </tr>
        <tr>
            <th style="background: #f3f4f6;">Email</th>
            <td>{{ $user->email }}</td>
        </tr>
        <tr>
            <th style="background: #f3f4f6;">Role</th>
            <td><span class="badge badge-{{ $user->role === 'admin' ? 'dikembalikan' : 'dipinjam' }}">{{ ucfirst($user->role) }}</span></td>
        </tr>
    </table>

    <h2 style="margin-top: 36px;">Ganti Password</h2>
    <div style="max-width: 500px;">
        <form action="{{ route('profile.password') }}" method="POST">
            @csrf
            @method('PUT')

            <label for="password_lama">Password Lama</label>
            <input type="password" name="password_lama" id="password_lama">
            @error('password_lama')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="password">Password Baru</label>
            <input type="password" name="password" id="password">
            @error('password')
                <div class="error">{{ $message }}</div>
            @enderror

            <label for="password_confirmation">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" id="password_confirmation">
            @error('password_confirmation')
                <div class="error">{{ $message }}</div>
            @enderror

            <button type="submit" class="btn" style="margin-top: 20px;">Perbarui Password</button>
        </form>
    </div>
@endsection
