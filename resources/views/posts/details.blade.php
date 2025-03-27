@extends('base')

@section('content')
<!-- Post title -->
<div class='flex items-center space-x-2'>
    <h1 id='title'>
    </h1>
</div>

<!-- Edit / Delete -->
<div class="flex gap-2">
    <a id='edit-link' class='clickable' href="{{ route('posts.update.ui', ['post' => $post->id]) }}" hidden>Edit</a>
    <div id='edit-disabled' class="warning" hidden>Can't edit</div>
    <span> | </span> 
    <btn id='delete-link' class="clickable" hidden>Delete</btn>
    <div id='delete-disabled' class="warning" hidden>Can't delete</div>
</div>

<!-- Post content -->
<p id='content'></p>

<!-- Comment list -->
<hr class='m-2'>
<div id='comments-container' class='flex-col justify-start space-y-2'>
    <h2 class='font-semibold'>List of comments</h3>
    <ul id='comments-list'></ul>

    <!-- Comment form -->
    <div id='comment-form' hidden>
        <textarea cols='30' rows='2' id='add-comment'></textarea>
        <button class='block btn' id='submit-comment'>Submit</button>
        <p id='submit-error'></p>
    </div>
    <p id='comment-disabled' class='warning' hidden>Can't comment</p>
</div>

@endsection

@section('script')
<script>
    const postId = '{{ $post->id }}';
    const commentsContainer = document.getElementById('comments-container');
    const submitComment = document.getElementById("submit-comment");
    const submitError = document.getElementById('submit-error');
    const deleteLink = document.getElementById('delete-link');
    
    // Load post and contents
    document.addEventListener('DOMContentLoaded', async function (){
        loadPost(postId);
        loadComments(postId);
    });

    // Comment edition and delete
    commentsContainer.addEventListener("click", (event) => {
        const classList = event.target.classList;
        if(classList.contains('comment-edit')) {
            const newComment = prompt('Edit comment');
            if(newComment) {
                const commentId = event.target.value;
                axios.put(
                    `/api/comments/${commentId}`,
                    {content: newComment}
                ).then(
                    () => location.reload()
                ).catch(
                    () => alert('Failed to edit comment')
                );
            }
        }
        else if(classList.contains('comment-delete')) {
            if (confirm('Are you sure you want to delete this comment?')){
                const commentId = event.target.value;
                axios.delete(
                    `/api/comments/${commentId}`
                ).then(
                    () => location.reload()
                ).catch(
                    () => alert('Failed to delete comment')
                );
            };
        }
    })

    // Submit comment
    submitComment.addEventListener('click', function() {
        const comment = document.getElementById('add-comment').value;
        saveComment(comment, postId, submitError);
    })

    // Delete post
    deleteLink.addEventListener('click', function() {
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
        function toggleVisibility(condition, elementToShow, elementToHide) {
            elementToShow.hidden = !condition;
            elementToHide.hidden = condition;
        }
        const postResponse = await fetch(`/api/posts/${postId}`);
        const title = document.getElementById('title');
        const content = document.getElementById('content');

        if (postResponse.ok) {
            const post = await postResponse.json();
            title.textContent = post.title;
            content.textContent = post.content;

            // Check if user can edit a post, delete a post, or create a comment and show/hide elements accordingly
            const editLink = document.getElementById('edit-link');
            const editDisabled = document.getElementById('edit-disabled');
            const deleteLink = document.getElementById('delete-link');
            const deleteDisabled = document.getElementById('delete-disabled');
            const commentForm = document.getElementById('comment-form');
            const commentDisabled = document.getElementById('comment-disabled');

            toggleVisibility(post.can_update, editLink, editDisabled);
            toggleVisibility(post.can_delete, deleteLink, deleteDisabled);
            toggleVisibility(post.can_comment, commentForm, commentDisabled);
        }
        else {
            content.textContent = "Failed to load blog post..."
        }
    }

    async function loadComments(postId) {
        /**
         * Add a 'delete' or 'edit' anchor action to the comment header along with separator
         * @param {string} action - 'delete' or 'edit'
         * @param {HTMLElement} commentHeader - Elem where we append the anchor
         */
        function addActionToComment(action, commentHeader, commentId)
        {
            const separator = document.createElement('span');
            separator.textContent = ' - ';
            commentHeader.appendChild(separator);

            const commentAction = document.createElement('a');
            commentAction.classList.add(`comment-${action}`);
            commentAction.classList.add('clickable');
            commentAction.textContent = action[0].toUpperCase() + action.slice(1);
            commentAction.value = commentId;
            commentHeader.appendChild(commentAction);
        }
        const commentsResponse = await fetch(`/api/comments/?post_id=${postId}`);
        const commentsList = document.getElementById('comments-list');
        if (commentsResponse.ok) {
            const comments = await commentsResponse.json();
            comments.forEach(comment => {
                const li = document.createElement('li');
                const commentHeader = document.createElement('p');
                const commentContent = document.createElement('p');
                commentHeader.classList.add('italic')
                commentHeader.innerText = `${comment.user.display} - ${comment.created_at}`;

                if (comment.can_update) {
                    addActionToComment('edit', commentHeader, comment.id);
                }
                if (comment.can_delete) {
                    addActionToComment('delete', commentHeader, comment.id);
                }
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
            if(response.status === 201) {
                const responseBody = await response.json();
                location.reload();
            }
            else if(response.status === 422 || response.status === 403) {
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