<?php
session_start();
$host = 'localhost';
$db_name = 'dobu_martial_arts';
$username = 'root'; 
$password = ''; 

$db = new mysqli($host, $username, $password, $db_name);

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $db->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $first_name = $db->real_escape_string($_POST['first_name']);
    $last_name = $db->real_escape_string($_POST['last_name']);
    $email = $db->real_escape_string($_POST['email']);
    $phone_number = $db->real_escape_string($_POST['phone_number']);
    $membership_plan = $db->real_escape_string($_POST['membership_plan']);
    $selected_classes = isset($_POST['classes']) ? $_POST['classes'] : [];

    // Validate input
    if (strlen($username) < 5 || strlen($password) < 5 || strlen($first_name) < 5 || strlen($last_name) < 5) {
        $error_message = "Username, Password, First Name, and Last Name must be at least 5 characters long.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } elseif (!ctype_digit($phone_number)) {
        $error_message = "Phone Number must contain only numbers.";
    } elseif (empty($selected_classes)) {
        $error_message = "Please select at least one class.";
    } elseif ($membership_plan === 'Advanced' && count($selected_classes) !== 2) {
        $error_message = "Advanced plan requires selecting exactly 2 classes.";
    } else {
        // Check if username already exists
        $check_username = "SELECT * FROM users WHERE username = '$username'";
        $result = $db->query($check_username);
        
        if ($result->num_rows > 0) {
            $error_message = "Username has already been taken.";
        } else {
            // Check if email already exists
            $check_email = "SELECT * FROM users WHERE email = '$email'";
            $result = $db->query($check_email);
            
            if ($result->num_rows > 0) {
                $error_message = "Email address is already registered.";
            } else {
                // Replace the simple encoding with proper hashing
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Calculate membership expiry date (1 month from now)
                $membership_expiry = date('Y-m-d', strtotime('+1 month'));

                $sql = "INSERT INTO users (username, password, first_name, last_name, email, phone_number, membership_plan, membership_expiry) 
                        VALUES ('$username', '$hashed_password', '$first_name', '$last_name', '$email', '$phone_number', '$membership_plan', '$membership_expiry')";

                if ($db->query($sql) === TRUE) {
                    $user_id = $db->insert_id;
                    
                    // Insert selected classes
                    foreach ($selected_classes as $class_id) {
                        $class_sql = "INSERT INTO user_classes (user_id, class_id) VALUES ('$user_id', '$class_id')";
                        $db->query($class_sql);
                    }

                    $_SESSION['message'] = "Registration successful. Please log in.";
                    header("Location: login.php");
                    exit();
                } else {
                    $error_message = "Error: " . $db->error;
                }
            }
        }
    }
}

// Fetch available classes
$classes_sql = "SELECT id, name FROM classes";
$classes_result = $db->query($classes_sql);

$db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DoBu Martial Arts - Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../Style/style.css">
</head>
<body class="signup-page">
<style>
    body {
        padding-top: 60px;
    }
    .signup-container {
        margin-top: 2rem;
        margin-bottom: 2rem;
    }
</style>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <!-- Navigation content -->
    </nav>

    <div class="container signup-container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card signup-card">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">Sign Up for DoBu Martial Arts</h2>
                        <?php
                        if (!empty($error_message)) {
                            echo "<div class='alert alert-danger'>" . $error_message . "</div>";
                        }
                        ?>
                        <form action="signup.php" method="post" id="signupForm">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username (min 5 characters)</label>
                                <input type="text" class="form-control" id="username" name="username" required minlength="5">
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password (min 5 characters)</label>
                                <input type="password" class="form-control" id="password" name="password" required minlength="5">
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="5">
                            </div>
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name (min 5 characters)</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" required minlength="5">
                            </div>
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name (min 5 characters)</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" required minlength="5">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone_number" class="form-label">Phone Number (numbers only)</label>
                                <input type="tel" class="form-control" id="phone_number" name="phone_number" required pattern="[0-9]+">
                            </div>
                            <div class="mb-3">
                                <label for="membership" class="form-label">Training Plan</label>
                                <select class="form-select" id="membership" name="membership_plan" required>
                                    <option value="">Select a training plan</option>
                                    <option value="Basic">Basic (1 Martial Art - 2 sessions per week) - $25.00/month</option>
                                    <option value="Intermediate">Intermediate (1 Martial Art - 3 sessions per week) - $35.00/month</option>
                                    <option value="Advanced">Advanced (any 2 Martial Arts - 5 sessions per week) - $45.00/month</option>
                                    <option value="Elite">Elite (Unlimited Classes) - $60.00/month</option>
                                    <option value="Junior">Junior Membership (all-kinds Martial Art sessions) - $25.00/month</option>
                                </select>
                            </div>
                            <div class="mb-3" id="classSelection">
                                <label class="form-label">Select Classes (at least one required)</label>
                                <?php while ($class = $classes_result->fetch_assoc()): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="classes[]" value="<?php echo $class['id']; ?>" id="class_<?php echo $class['id']; ?>">
                                        <label class="form-check-label" for="class_<?php echo $class['id']; ?>">
                                            <?php echo $class['name']; ?>
                                        </label>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Sign Up</button>
                            </div>
                        </form>
                        <p class="text-center mt-3">
                            Already have an account? <a href="login.php">Login here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/main.js"></script>
</body>
</html>
