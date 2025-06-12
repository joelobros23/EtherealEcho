document.addEventListener('DOMContentLoaded', function() {
    const profilePictureElement = document.getElementById('profile-picture');
    const usernameElement = document.getElementById('username');
    const bioElement = document.getElementById('bio');
    const editProfileButton = document.getElementById('edit-profile-button');
    const saveProfileButton = document.getElementById('save-profile-button');
    const bioTextarea = document.getElementById('bio-textarea');
    const profileDetails = document.getElementById('profile-details');

    // Function to fetch and display user profile data
    function fetchUserProfile() {
        const userId = getLoggedInUserId(); // Implement this function in auth.js to get the logged-in user's ID
        if (!userId) {
            // Redirect to login if not logged in
            window.location.href = 'login.html';
            return;
        }
        
        fetch(`api/get_user_data.php?id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    usernameElement.textContent = data.username;
                    bioElement.textContent = data.bio || 'No bio yet.';
                    bioTextarea.value = data.bio || ''; // Set textarea value for editing
                    if (data.profile_picture) {
                        profilePictureElement.src = data.profile_picture;
                    } else {
                        profilePictureElement.src = 'assets/img/default.png'; // Use a default profile picture
                    }
                } else {
                    console.error('Error fetching profile:', data.message);
                    // Handle error appropriately (e.g., display an error message)
                }
            })
            .catch(error => {
                console.error('Error fetching profile:', error);
                // Handle error appropriately
            });
    }

    // Function to handle editing the profile
    function enableEditMode() {
        profileDetails.style.display = 'none';
        bioTextarea.style.display = 'block';
        saveProfileButton.style.display = 'inline-block';
        editProfileButton.style.display = 'none';
    }

    // Function to handle saving the profile
    function saveProfile() {
        const userId = getLoggedInUserId(); // Ensure this function exists
        if (!userId) {
            window.location.href = 'login.html';
            return;
        }

        const newBio = bioTextarea.value;
        const formData = new FormData();
        formData.append('bio', newBio);

        fetch('api/update_profile.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update displayed bio and switch back to view mode
                bioElement.textContent = newBio;
                profileDetails.style.display = 'block';
                bioTextarea.style.display = 'none';
                saveProfileButton.style.display = 'none';
                editProfileButton.style.display = 'inline-block';
                // Optionally display a success message
            } else {
                console.error('Error updating profile:', data.message);
                // Handle error appropriately (e.g., display an error message)
            }
        })
        .catch(error => {
            console.error('Error updating profile:', error);
            // Handle error appropriately
        });
    }

    // Event listeners
    if (editProfileButton) {
        editProfileButton.addEventListener('click', enableEditMode);
    }

    if (saveProfileButton) {
        saveProfileButton.addEventListener('click', saveProfile);
    }

    // Initial profile fetch
    fetchUserProfile();

    // Helper function to get logged-in user ID (assuming it's stored in localStorage)
    function getLoggedInUserId() {
        return localStorage.getItem('user_id');
    }
});