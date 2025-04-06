
@extends('base')

@section('content')
<h1>Edit user -  {{ $user->name }}</h1>
<form class="flex flex-col" id="edit-form">
    <label class="font-medium" for="name">Username</label>
    <input class="border-gray-400 rounded" type="text" id="name" name="name"  value="{{ $user->name }}">

    <label class="font-medium" for="email">Email</label>
    <input class="border-gray-400 rounded" type="text" id="email" name="email"  value="{{ $user->email }}">

    <label class="font-medium" for="role">Role</label>
    <select name="role" id="role">
        @foreach ($roles as $role)
        <option value="{{ $role->value }}" @selected($user->role === $role->value)>{{ $role->value }}</option>
        @endforeach
    </select>

    <label class="font-medium" for="is_email_public">Public email</label>
    <select name="is_email_public" id="is_email_public">
        <option value="1" @selected($user->is_email_public == true)>Yes</option>
        <option value="0" @selected($user->is_email_public != true)>No</option>
    </select>
    <button class="btn mt-4 max-w-20">Save</button>
</form>
@endsection

@section('script')
<script>
    const editForm = document.getElementById('edit-form');

editForm.addEventListener('submit', e => {
    e.preventDefault();

    const formData = new FormData(editForm);
    formData.append('_method', 'PUT');  // Need method spoofing

    axios.post(
        "{{ route('users.update', ['user' => $user->id]) }}", formData
    ).then(function (response) {
        location.assign("{{ route('users.details.ui', ['user' => $user->id]) }}")
    }).catch(function (error) {
        console.log(error.response.data);
        if(error.response && typeof(error.response.data.errors) === 'object') {
            const errorList = document.getElementById('error-list');
            errorList.innerHTML = "";
            for (const [key, value] of Object.entries(error.response.data.errors)) {
                errorItem = document.createElement('li');
                errorItem.textContent = value;
                errorList.appendChild(errorItem);
            }
        }
    });
});
</script>
@endsection