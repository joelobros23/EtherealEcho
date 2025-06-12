// assets/js/auth.js

document.addEventListener('DOMContentLoaded', () => {

    // Registration
    const registerForm = document.querySelector('#registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const username = document.querySelector('#registerUsername').value;
            const email = document.querySelector('#registerEmail').value;
            const password = document.querySelector('#registerPassword').value;

            try {
                const response = await fetch('api/register.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ username, email, password })
                });

                const data = await response.json();

                if (response.ok) {
                    alert(data.message);
                    window.location.href = 'login.html'; // Redirect to login page
                } else {
                    alert(data.error);
                }
            } catch (error) {
                console.error('Registration error:', error);
                alert('An error occurred during registration.');
            }
        });
    }

    // Login
    const loginForm = document.querySelector('#loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const username = document.querySelector('#loginUsername').value;
            const password = document.querySelector('#loginPassword').value;

            try {
                const response = await fetch('api/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ username, password })
                });

                const data = await response.json();

                if (response.ok) {
                    localStorage.setItem('token', data.token); // Store the token
                    localStorage.setItem('userId', data.userId); // Store the user ID
                    alert(data.message);
                    window.location.href = 'home.html'; // Redirect to home page
                } else {
                    alert(data.error);
                }
            } catch (error) {
                console.error('Login error:', error);
                alert('An error occurred during login.');
            }
        });
    }

    // Logout
    const logoutButton = document.querySelector('#logoutButton'); // Assuming there's a logout button with this ID
    if (logoutButton) {
        logoutButton.addEventListener('click', async (e) => {
            e.preventDefault();

            try {
                const response = await fetch('api/logout.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + localStorage.getItem('token') // Send token for validation
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    localStorage.removeItem('token'); // Remove the token
                    localStorage.removeItem('userId'); // Remove user ID
                    alert(data.message);
                    window.location.href = 'index.html'; // Redirect to index page
                } else {
                    alert(data.error);
                }
            } catch (error) {
                console.error('Logout error:', error);
                alert('An error occurred during logout.');
            }
        });
    }
});