@extends('base')

@section('content')
<h2 class="text-xl mt-4">Comments</h2>

<table class="min-w-full divide-y-2">
    <thead>
        <tr>
            <th>Comment</th>
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
        let response = await fetch('/api/comments/?my_comments=true');
        if (response.ok) {
            let comments = await response.json();
            comments.forEach(comment => {
                const commentRow = document.createElement('tr');
                console.log(comment);
                addCell(commentRow, comment, 'content');
                addCell(commentRow, comment, 'post.title', `/posts/details/${comment.post.id}`);
                commentsTable.appendChild(commentRow)
            })
        }
        else{
            let error = document.createElement('p');
            error.textContent = "Couldn't fetch comments.";
            document.body.after(error);
        }
    })

    function addCell(commentRow, post, attr, href=""){
        const cell = document.createElement('td');
        const attrVal = attr.split('.').reduce((props, key)=>props&&props[key]||null, post);
        if (href) {
            cell.innerHTML = `<a class="clickable" href="${href}">${attrVal}</a>`
        }
        else{
            cell.innerHTML = attrVal;
        }
        commentRow.appendChild(cell);
    }
</script>
@endsection
