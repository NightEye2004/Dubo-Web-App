document.addEventListener('DOMContentLoaded', function() {
    // Change Password Form
    const changePasswordForm = document.getElementById('changePasswordForm');
    if (changePasswordForm) {
        changePasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('change_password.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert('Error: ' + data.error);
                } else if (data.success) {
                    alert(data.success);
                    $('#changePasswordModal').modal('hide');
                    this.reset();
                } else {
                    alert('An unexpected error occurred. Please try again.');
                }
            });
        });
    }

    // Private Tuition Booking
    const daySelect = document.getElementById('tuition_day');
    const timeSelect = document.getElementById('tuition_time');
    if (daySelect && timeSelect) {
        const availableTimes = {
            'Monday': ['10:30 - 12:00'],
            'Tuesday': ['08:00 - 10:00', '10:30 - 12:00'],
            'Wednesday': ['08:00 - 10:00', '10:30 - 12:00'],
            'Thursday': ['08:00 - 10:00', '10:30 - 12:00'],
            'Friday': ['10:30 - 12:00', '19:00 - 21:00'],
            'Saturday': ['08:00 - 10:00'],
            'Sunday': ['08:00 - 10:00']
        };

        daySelect.addEventListener('change', function() {
            const selectedDay = this.value;
            timeSelect.innerHTML = '<option value="">Select a time</option>';
            if (selectedDay in availableTimes) {
                availableTimes[selectedDay].forEach(time => {
                    const option = document.createElement('option');
                    option.value = time;
                    option.textContent = time;
                    timeSelect.appendChild(option);
                });
            }
        });
    }

    // Upgrade Plan Form
    const planSelect = document.getElementById('new-plan');
    const classCheckboxes = document.querySelectorAll('input[name="classes[]"]');
    const upgradePlanForm = document.getElementById('upgradePlanForm');

    if (planSelect && upgradePlanForm) {
        function updateClassSelection() {
            const selectedPlan = planSelect.value;
            let maxClasses = 0;

            switch (selectedPlan) {
                case 'Basic':
                case 'Intermediate':
                    maxClasses = 1;
                    break;
                case 'Advanced':
                    maxClasses = 2;
                    break;
                case 'Elite':
                case 'Junior':
                    maxClasses = classCheckboxes.length;
                    break;
                default:
                    maxClasses = 0;
            }

            classCheckboxes.forEach(checkbox => {
                checkbox.disabled = false;
                checkbox.checked = false;
            });

            const classSelection = document.getElementById('classSelection');
            if (classSelection) {
                if (maxClasses > 0) {
                    classSelection.style.display = 'block';
                } else {
                    classSelection.style.display = 'none';
                }
            }

            let checkedCount = 0;
            classCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    checkedCount = document.querySelectorAll('input[name="classes[]"]:checked').length;
                    if (checkedCount >= maxClasses) {
                        classCheckboxes.forEach(cb => {
                            if (!cb.checked) {
                                cb.disabled = true;
                            }
                        });
                    } else {
                        classCheckboxes.forEach(cb => {
                            cb.disabled = false;
                        });
                    }
                });
            });
        }

        planSelect.addEventListener('change', updateClassSelection);
        updateClassSelection();

        upgradePlanForm.addEventListener('submit', function(event) {
            const checkedClasses = document.querySelectorAll('input[name="classes[]"]:checked');
            const selectedPlan = planSelect.value;

            if ((selectedPlan === 'Basic' || selectedPlan === 'Intermediate') && checkedClasses.length !== 1) {
                event.preventDefault();
                alert('Basic and Intermediate plans require selecting exactly 1 class.');
            } else if (selectedPlan === 'Advanced' && checkedClasses.length !== 2) {
                event.preventDefault();
                alert('Advanced plan requires selecting exactly 2 classes.');
            }
        });
    }

    // Delete Account Form
    const deleteAccountForm = document.getElementById('deleteAccountForm');
    if (deleteAccountForm) {
        deleteAccountForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const password = document.getElementById('deleteAccountPassword').value;
            
            // Verify password and delete account
            fetch('verify_password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'password=' + encodeURIComponent(password)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // If password is correct, proceed with account deletion
                    return fetch('delete_account.php', {
                        method: 'POST'
                    });
                } else {
                    throw new Error(data.message || 'Incorrect password');
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Your account has been successfully deleted.');
                    window.location.href = 'logout.php';
                } else {
                    throw new Error(data.message || 'Failed to delete account');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'An error occurred. Please try again.');
            });
        });
    }

    // Signup Form
    const signupForm = document.getElementById('signupForm');
    const membershipSelect = document.getElementById('membership');
    if (signupForm && membershipSelect) {
        function updateSignupClassSelection() {
            const selectedPlan = membershipSelect.value;
            let maxClasses = 0;

            switch (selectedPlan) {
                case 'Basic':
                case 'Intermediate':
                    maxClasses = 1;
                    break;
                case 'Advanced':
                    maxClasses = 2;
                    break;
                case 'Elite':
                case 'Junior':
                    maxClasses = classCheckboxes.length;
                    break;
                default:
                    maxClasses = 0;
            }

            classCheckboxes.forEach(checkbox => {
                checkbox.disabled = false;
                checkbox.checked = false;
            });

            if (maxClasses > 0) {
                document.getElementById('classSelection').style.display = 'block';
            } else {
                document.getElementById('classSelection').style.display = 'none';
            }

            let checkedCount = 0;
            classCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    checkedCount = document.querySelectorAll('input[name="classes[]"]:checked').length;
                    if (checkedCount >= maxClasses) {
                        classCheckboxes.forEach(cb => {
                            if (!cb.checked) {
                                cb.disabled = true;
                            }
                        });
                    } else {
                        classCheckboxes.forEach(cb => {
                            cb.disabled = false;
                        });
                    }
                });
            });
        }

        membershipSelect.addEventListener('change', updateSignupClassSelection);
        updateSignupClassSelection();

        signupForm.addEventListener('submit', function(event) {
            const checkedClasses = document.querySelectorAll('input[name="classes[]"]:checked');
            const selectedPlan = membershipSelect.value;

            if (checkedClasses.length === 0) {
                event.preventDefault();
                alert('Please select at least one class.');
            } else if (selectedPlan === 'Advanced' && checkedClasses.length !== 2) {
                event.preventDefault();
                alert('Advanced plan requires selecting exactly 2 classes.');
            }

            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                event.preventDefault();
                alert('Passwords do not match.');
            }
        });
    }
});
