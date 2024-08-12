document.addEventListener('DOMContentLoaded', function() {
    const employeeId = localStorage.getItem('employeeId'); 

    if (employeeId) {
        // Fetch and display employee's vacations
        fetch(`http://localhost/hr_backend/vacation.php?employee_id=${employeeId}`)
            .then(response => response.json())
            .then(vacations => {
                const vacationList = document.getElementById('vacationList');
                if (vacations.length > 0) {
                    vacations.forEach(vacation => {
                        const vacationItem = document.createElement('p');
                        vacationItem.innerText = `${vacation.type.charAt(0).toUpperCase() + vacation.type.slice(1)}: ${vacation.start_date} to ${vacation.end_date}`;
                        vacationList.appendChild(vacationItem);
                    });
                } else {
                    vacationList.innerText = 'No vacations applied.';
                }
            })
            .catch(error => console.error('Error:', error));
    } else {
        console.error('Employee ID not found in local storage');
        window.location.href = 'index.html';
    }

    // Handle vacation form submission
    document.getElementById('vacationForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const vacationType = document.getElementById('vacationType').value;
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;

        fetch('http://localhost/hr_backend/vacation.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                employee_id: employeeId,  // Use the logged-in employee ID
                start_date: startDate,
                end_date: endDate,
                type: vacationType,
                is_paid: vacationType === 'annual' ? 1 : 0
            })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.status);
            // Clear the form fields after successful submission
            document.getElementById('vacationForm').reset();
            // Refresh the list of vacations
            window.location.reload();
        })
        .catch(error => console.error('Error:', error));
    });

    // Logout button event listener
    document.getElementById('logoutButton').addEventListener('click', function() {
        logout();
    });
});

function logout() {
    localStorage.removeItem('employeeId');
    window.location.href = 'index.html';
}
