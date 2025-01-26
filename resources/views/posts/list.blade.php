@extends('base')

@section('content')
<a href="{{ route('posts.create.ui') }}">Create post</a>
<table id='table-posts'>
    <tr>
        <th>Title</th>
        <th>Content</th>
        <th>User</th>
    </tr>
</table>
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
                addCell(postRow, post, 'user_id');
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
        if (href) {
            cell.innerHTML = `<a href="${href}">${post[attr]}</a>`
        }
        else{
            cell.innerHTML = post[attr];
        }
        postRow.appendChild(cell);
    }
</script>
@endsection