@extends('base')

@section('content')
<!-- Post title and content -->
<div class='flex items-center space-x-2'>
    <h1 id='title'>
        
    </h1>
</div>
<div id="edit-delete" hidden>
    <a class='clickable' href="{{ route('posts.update.ui', ['post' => $post->id]) }}">Edit</a>
    - <btn class="clickable" id='delete-button'>Delete</btn>
</div>
<p id='content'></p>

<a></a>
<!-- Comment list -->
<hr class='m-2'>
<div class='flex-col justify-start space-y-2'>
    <h2 class='font-semibold'>List of comments</h3>
    <ul id='comments'></ul>
    <!-- Comment submission -->
    <textarea cols='30' rows='2' id='add-comment'></textarea>
    <button class='block btn' id='submit-comment'>Submit</button>
    <p id='submit-error'></p>
</div>

@endsection

@section('script')
<script>
    const postId = '{{ $post->id }}';
    const submitComment = document.getElementById("submit-comment");
    const submitError = document.getElementById('submit-error');
    const deleteButton = document.getElementById('delete-button');
    
    document.addEventListener('DOMContentLoaded', async function (){
        loadPost(postId);
        loadComments(postId);
    });

    submitComment.addEventListener('click', function() {
        const comment = document.getElementById('add-comment').value;
        saveComment(comment, postId, submitError);
    })

    deleteButton.addEventListener('click', function() {
        if (confirm('Are you sure you want to delete this post?')){
            axios.delete(
                "{{ route('posts.destroy', ['post' => $post->id]) }}"
            ).then(
                () => location.assign("{{ route('posts.list.ui') }}")
            ).catch(
                () => alert('Failed to delete post')
            );
        };
    })

    async function loadPost(postId) {
        const postResponse = await fetch(`/api/posts/${postId}`);
        const title = document.getElementById('title');
        const content = document.getElementById('content');
        if (postResponse.ok) {
            const post = await postResponse.json();
            title.textContent = post.title;
            content.textContent = post.content;
            if (post.is_own_post) {
                const editDelete = document.getElementById('edit-delete');
                editDelete.hidden = false;
            }
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
                const li = document.createElement('li');
                const commentHeader = document.createElement('p');
                const commentContent = document.createElement('p');
                const dateWithoutMS = comment.created_at.substring(0, 19);
                commentHeader.classList.add('italic')
                commentHeader.innerText = `${comment.user.name} - ${dateWithoutMS}`;
                commentContent.innerText = comment.content;
                li.appendChild(commentHeader);
                li.appendChild(commentContent);
                commentsList.appendChild(li);
            });
        }
        else{
            comments.textContent = 'Failed to load comments...';
        }
    }
    
    async function saveComment(comment, postId,  submitError) {
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
                })
            });
            if(response.status = 201) {
                const responseBody = await response.json();
                location.reload();
            }
            else if(response.status = 422) {
                const responseBody = await response.json();
                submitError.textContent = responseBody.message;
            }
            else {
                submitError.textContent = 'Error while saving comment.';
            }
        }
        else {
            submitError.textContent = 'Please add your comment before submission.';
        }
    }
</script>
@endsection