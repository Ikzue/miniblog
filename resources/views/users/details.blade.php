
@extends('base')

@section('content')

<div>
    <h1>User details -  {{ $user->name }}</h1>
    <!-- Edit / Delete -->
    <div class="flex gap-2">
        <a id='edit-link' class='clickable' href="{{ route('users.update.ui', ['user' => $user->id]) }}">Edit</a>
        <span> | </span> 
        <btn id='delete-link' class="clickable">Delete</btn>
    </div>
    <p>Username: {{ $user->name }}</p>
    <p>Email: {{ $user->email }}</p>
    <p>Created at: {{ $user->created_at }}</p>
    <p>Updated at: {{ $user->updated_at }}</p>
    <p>Role: {{ $user->role }}</p>
    <p>Is email public: {{ $user->is_email_public ? 'Yes' : 'No' }}</p>
</div>

<!-- Post list -->
<hr class='m-2'>
<div class='flex-col justify-start space-y-2'>
    <h2 class='font-semibold'>List of posts</h2>
    <ul id='posts-list' class="pl-5 list-disc"></ul>
<div>

<!-- Comment list -->
<hr class='m-2'>
<div class='flex-col justify-start space-y-2'>
    <h2 class='font-semibold'>List of comments</h2>
    <ul id='comments-list' class="pl-5 list-disc"></ul>
<div>

</table>
@endsection

@section('script')
<script>
    const userId = '{{ $user->id }}';
    const deleteLink = document.getElementById('delete-link');

    // Delete post
    deleteLink.addEventListener('click', function() {
        if (confirm('Are you sure you want to delete this post?')){
            axios.delete(
                "{{ route('users.destroy', ['user' => $user->id]) }}"
            ).then(
                () => location.assign("{{ route('users.list.ui') }}")
            ).catch(
                () => alert('Failed to delete post')
            );
        };
    })

    document.addEventListener('DOMContentLoaded', async function (){
        loadPosts(userId);
        loadComments(userId);
    });

    // Load all user's posts
    function loadPosts(userId) {
        axios.get(
            `/api/posts/getUserPosts/${userId}`,
        ).then(
            (response) => {
                const postsList = document.getElementById('posts-list');
                const postsData = response.data;
                postsData.forEach(post => {
                    addAnchorToList(postsList, post, `/posts/details/${post.id}`, post.title);
                })
                }
        ).catch(
            () => alert('Failed to fetch posts')
        )
    }

    // Load all user's comments
    function loadComments(id) {
        console.log(userId);
        axios.get(
            `/api/comments/getUserComments/${userId}`,
        ).then(
            (response) => {
                const commentsList = document.getElementById('comments-list');
                const commentsData = response.data;
                commentsData.forEach(comment => {
                    addAnchorToList(commentsList, comment, `/posts/details/${comment.post.id}`, comment.content);
                })
                }
        ).catch(
            () => alert('Failed to fetch comments')
        )
    }

    function addAnchorToList(list, data, href, textContent){
        const item = document.createElement('li');
        const anchor = document.createElement('a');
        anchor.href = href;
        anchor.textContent = textContent;
        anchor.classList.add('clickable');
        item.appendChild(anchor);
        list.appendChild(item);
    }
</script>
@endsection