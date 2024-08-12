// login.js
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    fetch('http://localhost/hr_backend/login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ email: email, password: password })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Store employee ID in local storage
            localStorage.setItem('employeeId', data.employee_id);
            // Redirect to the employee dashboard
            window.location.href = 'dashboard.html';
        } else {
            document.getElementById('loginError').innerText = 'Invalid login credentials';
        }
    })
    .catch(error => console.error('Error:', error));
});
