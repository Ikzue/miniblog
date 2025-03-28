@extends('base')

@section('content')

<table class="min-w-full divide-y-2">
    <thead>
    <tr>
        <th>Username</th>
        <th>Email</th>
        <th>Role</th>
        <th>Is email public</th>
    </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->role }}</td>
            <td>{{ $user->is_email_public ? 'true' : 'false' }}</td>
        </tr>
        @endforeach
    </tbody>
    <tbody id='table-posts'></tbody>
</table>
@endsection
