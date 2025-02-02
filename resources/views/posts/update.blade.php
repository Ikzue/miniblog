@extends('base')

@section('content')

<ul id="error-list">

</ul>

<form class="flex flex-col" id="edit-form">
    @csrf
    <label class="font-medium" for="title">Title </label>
    <input class="border-gray-400 rounded" type="text" id="title" name="title"  value="{{ $post->title }}">
    <label class="font-medium" for="content">Content </label>
    <textarea class="border-gray-400 rounded" rows=4 type="text" id="content" name="content">{{ $post->content }}</textarea>
    <button class="btn mt-4 max-w-20">Save</button>
</form>
@endsection

@section('script')
<script>
    const editForm = document.getElementById('edit-form');

    editForm.addEventListener('submit', e => {
        e.preventDefault();

        const formData = new FormData(editForm);
        console.log("{{ route('posts.update.ui', ['post' => $post->id]) }}");
        formData.append('_method', 'PUT');  // Need method spoofing

        axios.post(
            "{{ route('posts.update', ['post' => $post->id]) }}", formData
        ).then(function (response) {
            location.assign("{{ route('posts.details.ui', ['post' => $post->id]) }}")
        }).catch(function (error) {
            if(error.response && typeof(error.response.data.errors) === 'object') {
                const errorList = document.getElementById('error-list');
                errorList.innerHTML = "";
                for (const [key, value] of Object.entries(error.response.data.errors)) {
                    errorItem = document.createElement('li');
                    errorItem.textContent = value;
                    errorList.appendChild(errorItem);
                    console.log(`${key}: ${value}`);
                }
            }
        });
    });
</script>
@endsection