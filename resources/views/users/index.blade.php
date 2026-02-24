@extends('layouts.app')

@section('title', 'User Management')

@section('content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2>User Management</h2>
        <a href="{{ route('users.create') }}" class="btn btn-primary">+ Create New User</a>
    </div>

    @if($users->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>#{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span
                                class="badge badge-{{ $user->role === 'super_admin' ? 'completed' : ($user->role === 'admin' ? 'processing' : 'pending') }}">
                                {{ ucwords(str_replace('_', ' ', $user->role)) }}
                            </span>
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge badge-completed">Active</span>
                            @else
                                <span class="badge badge-failed">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : 'Never' }}</td>
                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-primary"
                                style="padding: 6px 12px; font-size: 12px;">Edit</a>

                            @if($user->id !== auth()->id())
                                <form action="{{ route('users.destroy', $user) }}" method="POST" style="display: inline;"
                                    onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                        style="padding: 6px 12px; font-size: 12px;">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $users->links() }}
        </div>
    @else
        <p style="color: #6b7280; text-align: center; padding: 40px;">No users found.</p>
    @endif
@endsection