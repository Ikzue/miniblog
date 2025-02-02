@extends('base')

@section('content')
<h2 class="text-xl mt-4">Comments</h2>

<table class="min-w-full divide-y-2">
    <thead>
        <tr>
            <th>Comment</th>
            <th>User</th>
            <th>Post</th>
        </tr>
    </thead>
    <tbody id='table-comments'>

    </tbody>

</table>
@endsection

@section('script')
<script>
    const commentsTable = document.getElementById('table-comments');
    // Populate comments table
    document.addEventListener('DOMContentLoaded', async function (){
        let response = await fetch('/api/comments');
        if (response.ok) {
            let comments = await response.json();
            comments.forEach(comment => {
                const commentRow = document.createElement('tr');
                addCell(commentRow, comment, 'content');
                addCell(commentRow, comment, 'user_id');
                addCell(commentRow, comment, 'post_id');
                commentsTable.appendChild(commentRow)
            })
        }
        else{
            let error = document.createElement('p');
            error.textContent = "Couldn't fetch comments.";
            document.body.after(error);
        }
    })

    function addCell(commentRow, post, attr){
        const cell = document.createElement('td');
        cell.innerHTML = post[attr];
        commentRow.appendChild(cell);
    }
</script>
@endsection
