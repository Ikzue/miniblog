@extends('base')

@section('content')

@session('success') 
<p>{{ $value }}</p>
@endsession

<div class="flex items-center justify-between mt-2">
    <h2 class="text-xl">Posts</h2>
    @can('create', App\Models\Post::class)
        <a class="btn text-black" href="{{ route('posts.create.ui') }}">Create post</a>
    @else
        <p class="text-red-500">Can't post</p>
    @endcan
</div>
<div>
<table class="min-w-full divide-y-2">
    <thead>
    <tr>
        <th>Title</th>
        <th>Content</th>
        <th>User</th>
    </tr>
    </thead>
    <tbody id='table-posts'></tbody>
</table>
</div>

@endsection


@section('script')
<script>
    const postsTable = document.getElementById('table-posts');
    // Populate posts table
    document.addEventListener('DOMContentLoaded', async function () {
        let response = await fetch('/api/posts');
        if (response.ok) {
            let posts = await response.json();
            posts.forEach(post => {
                const postRow = document.createElement('tr');
                let $id = post.id;
                addCell(postRow, post, 'title', `/posts/details/${post.id}`);
                addCell(postRow, post, 'content');
                addCell(postRow, post, 'user.name');
                postsTable.appendChild(postRow);
            })
        }
        else {
            let error = document.createElement('p');
            error.textContent = "Couldn't fetch posts.";
            document.body.after(error);
        }
    });

    function addCell(postRow, post, attr, href=""){
        const cell = document.createElement('td');
        const attrVal = attr.split('.').reduce((props, key)=>props&&props[key]||null, post)
        if (href) {
            cell.innerHTML = `<a class="clickable" href="${href}">${attrVal}</a>`
        }
        else{
            cell.innerHTML = attrVal;
        }

        postRow.appendChild(cell);
    }
</script>
@endsection