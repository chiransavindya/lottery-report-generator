@extends('layouts.app')

@section('title', 'Create User')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>Create New User</h2>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">← Back to Users</a>
    </div>

    <div class="card" style="max-width: 600px; margin: 0 auto; border-top-color: var(--primary-color);">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div style="margin-bottom: 20px;">
                <label for="name" style="display: block; margin-bottom: 8px; font-weight: 600;">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                    style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 4px;">
                @error('name')
                    <span style="color: #c62828; font-size: 14px;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="email" style="display: block; margin-bottom: 8px; font-weight: 600;">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                    style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 4px;">
                @error('email')
                    <span style="color: #c62828; font-size: 14px;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="password" style="display: block; margin-bottom: 8px; font-weight: 600;">Password</label>
                <input type="password" id="password" name="password" required
                    style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 4px;">
                <p style="font-size: 12px; color: var(--text-light); margin-top: 5px;">Must be at least 8 characters long.
                </p>

                @error('password')
                    <span style="color: #c62828; font-size: 14px;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label for="password_confirmation" style="display: block; margin-bottom: 8px; font-weight: 600;">Confirm
                    Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                    style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 4px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label for="role" style="display: block; margin-bottom: 8px; font-weight: 600;">Role</label>
                <select id="role" name="role" required
                    style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 4px;">
                    <option value="">-- Select Role --</option>
                    <option value="operator" {{ old('role') === 'operator' ? 'selected' : '' }}>Operator</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                </select>
                @error('role')
                    <span style="color: #c62828; font-size: 14px;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 25px;">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        style="margin-right: 10px; width: 18px; height: 18px;">
                    <span style="font-weight: 600;">Active Account</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px;">Create User</button>
        </form>
    </div>
@endsection