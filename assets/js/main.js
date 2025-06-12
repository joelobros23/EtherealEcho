/* Content for assets/js/main.js */

document.addEventListener('DOMContentLoaded', function() {
    const postForm = document.getElementById('post-form');
    const postsContainer = document.getElementById('posts-container');

    if (postForm) {
        postForm.addEventListener('submit', function(event) {
            event.preventDefault();

            const content = document.getElementById('post-content').value;

            fetch('api/create_post.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'content=' + encodeURIComponent(content)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('post-content').value = ''; // Clear the textarea
                    loadPosts(); // Refresh the posts
                } else {
                    alert('Error creating post: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while creating the post.');
            });
        });
    }

    function loadPosts() {
        if (!postsContainer) {
            return; // If the container doesn't exist, exit the function.
        }

        fetch('api/get_posts.php')
        .then(response => response.json())
        .then(posts => {
            postsContainer.innerHTML = ''; // Clear existing posts

            if (posts && posts.length > 0) {
                posts.forEach(post => {
                    const postDiv = document.createElement('div');
                    postDiv.classList.add('post');
                    postDiv.innerHTML = `
                        <div class="post-header">
                            <img src="${post.profile_picture}" alt="Profile Picture" class="profile-picture">
                            <span class="username">${post.username}</span>
                            <span class="post-date">${post.created_at}</span>
                        </div>
                        <div class="post-content">
                            ${post.content}
                        </div>
                        <div class="post-footer">
                            <button class="comment-button" data-post-id="${post.id}">Comment</button>
                            <div class="comments-section" id="comments-${post.id}">
                                <!-- Comments will be loaded here -->
                            </div>
                            <div class="comment-form" id="comment-form-${post.id}" style="display: none;">
                                <textarea id="comment-text-${post.id}" placeholder="Write a comment"></textarea>
                                <button onclick="submitComment(${post.id})">Submit Comment</button>
                            </div>
                        </div>
                    `;
                    postsContainer.appendChild(postDiv);

                    // Add event listener to the comment button
                    const commentButton = postDiv.querySelector('.comment-button');
                    commentButton.addEventListener('click', () => {
                        toggleCommentForm(post.id);
                        loadComments(post.id); // Load comments when comment section is opened
                    });
                });
            } else {
                postsContainer.innerHTML = '<p>No posts to display.</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            postsContainer.innerHTML = '<p>Failed to load posts.</p>';
        });
    }

    // Function to toggle the comment form visibility
    function toggleCommentForm(postId) {
        const commentForm = document.getElementById(`comment-form-${postId}`);
        if (commentForm) {
            commentForm.style.display = commentForm.style.display === 'none' ? 'block' : 'none';
        }
    }


    // Function to submit a comment
    window.submitComment = function(postId) {
        const content = document.getElementById(`comment-text-${postId}`).value;

        fetch('api/comment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'post_id=' + postId + '&content=' + encodeURIComponent(content)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`comment-text-${postId}`).value = ''; // Clear the textarea
                loadComments(postId); // Refresh the comments
            } else {
                alert('Error creating comment: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while creating the comment.');
        });
    };

    // Function to load comments for a specific post
    function loadComments(postId) {
        const commentsSection = document.getElementById(`comments-${postId}`);

        fetch(`api/get_comments.php?post_id=${postId}`)
        .then(response => response.json())
        .then(comments => {
            commentsSection.innerHTML = ''; // Clear existing comments

            if (comments && comments.length > 0) {
                comments.forEach(comment => {
                    const commentDiv = document.createElement('div');
                    commentDiv.classList.add('comment');
                    commentDiv.innerHTML = `
                        <div class="comment-header">
                            <span class="comment-username">${comment.username}</span>
                            <span class="comment-date">${comment.created_at}</span>
                        </div>
                        <div class="comment-content">
                            ${comment.content}
                        </div>
                    `;
                    commentsSection.appendChild(commentDiv);
                });
            } else {
                commentsSection.innerHTML = '<p>No comments yet.</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            commentsSection.innerHTML = '<p>Failed to load comments.</p>';
        });
    }



    // Initial load of posts when the page loads
    loadPosts();
});