@extends('base')

@section('content')
<!-- Post title and content -->
<h1 id='title'></h1>
<p id='content'></p>
<a></a>
<hr>
<!-- Comment list -->
<h2>Comment list</h3>
<ul id='comments'></ul>
<!-- Comment submission -->
<textarea cols="30" rows="2" id="add-comment"></textarea>
<button id="submit-comment">Submit</button>
<p id="submit-error"></p>
@endsection

@section('script')
<script>
    const postId = '{{ $id }}';
    const userId = "{{ Auth::user()->id }}";
    const submitComment = document.getElementById("submit-comment");
    const submitError = document.getElementById('submit-error');
    
    document.addEventListener('DOMContentLoaded', async function (){
        loadPost(postId);
        loadComments(postId);
    });

    submitComment.addEventListener('click', function() {
        const comment = document.getElementById('add-comment').value;
        saveComment(comment, postId, userId, submitError);
    })

    async function loadPost(postId) {
        const postResponse = await fetch(`/api/posts/${postId}`);
        const title = document.getElementById('title');
        const content = document.getElementById('content');
        if (postResponse.ok) {
            const post = await postResponse.json();
            title.textContent = post.title;
            content.textContent = post.content;
        }
        else {
            content.textContent = "Failed to load blog post..."
        }
    }

    async function loadComments(postId) {
        const commentsResponse = await fetch(`/api/comments/?post_id=${postId}`);
        const commentsList = document.getElementById('comments');
        if (commentsResponse.ok) {
            const comments = await commentsResponse.json();
            comments.forEach(comment => {
                const elem = document.createElement('li');
                const dateWithoutMs = comment.created_at.substring(0, 19);
                elem.innerText = `${comment.user.name} - ${dateWithoutMs}\r\n${comment.content}`;
                commentsList.appendChild(elem);
            });
        }
        else{
            comments.textContent = 'Failed to load comments...';
        }
    }
    
    async function saveComment(comment, postId, userId, submitError) {
        if(comment) {
            const response = await fetch("{{ route('comments.store') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: JSON.stringify({
                    content: comment,
                    post_id: postId,
                    user_id: userId
                })
            });
            if(response.status_code = 201) {
                location.reload();
            }
            else if(response.status_code = 422) {
                const responseBody = await response.json();
                submitError.textContent = responseBody.message;
            }
            else {
                submitError.textContent = 'Error while saving comment.';
            }
        }
        else {
            submitError.textContent = 'Please input your comment before submission.';
        }
    }
</script>
@endsection