// dashboard.js
document.addEventListener('DOMContentLoaded', function() {
    const employeeId = localStorage.getItem('employeeId');

    if (employeeId) {
        fetch(`http://localhost/hr_backend/employee.php?id=${employeeId}`)
            .then(response => response.json())
            .then(data => {
                if (data) {
                    document.getElementById('employeeName').innerText = data.name;
                    document.getElementById('employeeEmail').innerText = data.email;
                    document.getElementById('employeeDepartment').innerText = data.department_name;
                    document.getElementById('employeePhone').innerText = data.phone;
                    document.getElementById('employeeSalary').innerText = data.basic_salary;
                } else {
                    console.error('Employee data is undefined or null');
                }
            })
            .catch(error => console.error('Error:', error));
    } else {
        console.error('Employee ID not found in local storage');
        window.location.href = 'index.html';
    }

    // Logout button event listener
    document.getElementById('logoutButton').addEventListener('click', function() {
        logout();
    });
});

function logout() {
    localStorage.removeItem('employeeId');
    window.location.href = 'index.html';
}
