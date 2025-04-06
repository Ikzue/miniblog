@extends('base')

@section('content')

@session('success') 
<p>{{ $value }}</p>
@endsession

<div class="flex items-center justify-between mt-2">
<h2 class="text-xl">Users</h2>
</div>

<table class="min-w-full divide-y-2">
    <thead>
    <tr>
        <th>Username</th>
        <th>Email</th>
        <th>Role</th>
        <th>Is email public</th>
    </tr>
    </thead>
    <tbody id='table-users'></tbody>
</table>
@endsection


@section('script')

<script type="module">
    // Populate users table
    const usersTable = document.getElementById('table-users');
    document.addEventListener('DOMContentLoaded', async function () {
        axios.get(
            `/api/users`,
        ).then(
            (response) => {
                const users = response.data;
                users.forEach(user => {
                    const userRow = document.createElement('tr');
                    addTableData(userRow, user, 'name', `/users/details/${user.id}`);
                    addTableData(userRow, user, 'email');
                    addTableData(userRow, user, 'role');
                    addTableData(userRow, user, 'is_email_public');
                    usersTable.appendChild(userRow);
                })

            }
        ).catch(
            () => alert('Failed to fetch users')
        )
    })
</script>
@endsection