<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../connect.php'; 

$user_id = $_SESSION['user_id'];

// Fetch user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle plan upgrade
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upgrade_plan'])) {
    $new_plan = $conn->real_escape_string($_POST['new_plan']);
    $selected_classes = isset($_POST['classes']) ? $_POST['classes'] : [];

    // Validate the selected plan and classes
    $valid_upgrade = true;
    $error_message = "";

    if ($new_plan === 'Advanced' && count($selected_classes) !== 2) {
        $valid_upgrade = false;
        $error_message = "Advanced plan requires selecting exactly 2 classes.";
    } elseif (($new_plan === 'Basic' || $new_plan === 'Intermediate') && count($selected_classes) !== 1) {
        $valid_upgrade = false;
        $error_message = $new_plan . " plan requires selecting exactly 1 class.";
    }

    if ($valid_upgrade) {
        // Update user's plan
        $update_plan_sql = "UPDATE users SET membership_plan = ? WHERE id = ?";
        $update_plan_stmt = $conn->prepare($update_plan_sql);
        $update_plan_stmt->bind_param("si", $new_plan, $user_id);
        $update_plan_stmt->execute();
        $update_plan_stmt->close();
        

        // Remove existing class enrollments
        $delete_classes_sql = "DELETE FROM user_classes WHERE user_id = ?";
        $delete_classes_stmt = $conn->prepare($delete_classes_sql);
        $delete_classes_stmt->bind_param("i", $user_id);
        $delete_classes_stmt->execute();
        $delete_classes_stmt->close();

        // Add new class enrollments
        $insert_class_sql = "INSERT INTO user_classes (user_id, class_id) VALUES (?, ?)";
        $insert_class_stmt = $conn->prepare($insert_class_sql);
        foreach ($selected_classes as $class_id) {
            $insert_class_stmt->bind_param("ii", $user_id, $class_id);
            $insert_class_stmt->execute();
        }
        $insert_class_stmt->close();
        

        $success_message = "Your plan has been successfully upgraded to " . $new_plan . ".";
        
        
        // Refresh user data
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
    
    }
}

// Fetch available classes
$classes_sql = "SELECT id, name FROM classes";
$classes_result = $conn->query($classes_sql);

// Fetch user's current classes
$user_classes_sql = "SELECT class_id FROM user_classes WHERE user_id = ?";
$user_classes_stmt = $conn->prepare($user_classes_sql);
$user_classes_stmt->bind_param("i", $user_id);
$user_classes_stmt->execute();
$user_classes_result = $user_classes_stmt->get_result();
$user_classes = [];
while ($row = $user_classes_result->fetch_assoc()) {
    $user_classes[] = $row['class_id'];
}
$user_classes_stmt->close();

// Fetch user's classes
$classes_sql = "SELECT c.name FROM user_classes uc JOIN classes c ON uc.class_id = c.id WHERE uc.user_id = ?";
$classes_stmt = $conn->prepare($classes_sql);
$classes_stmt->bind_param("i", $user_id);
$classes_stmt->execute();
$classes_result = $classes_stmt->get_result();

// Fetch user's schedule
$schedule_sql = "SELECT 'Regular' as type, s.day_of_week, s.start_time, s.end_time, c.name AS class_name 
                 FROM schedules s 
                 JOIN classes c ON s.class_id = c.id 
                 JOIN user_classes uc ON uc.class_id = c.id
                 WHERE uc.user_id = ?
                 UNION
                 SELECT 'Private' as type, pt.tuition_day as day_of_week, pt.tuition_time as start_time, 
                        ADDTIME(pt.tuition_time, SEC_TO_TIME(pt.duration * 3600)) as end_time, 
                        CONCAT('Private Tuition with ', i.name) as class_name
                 FROM private_tuitions pt
                 JOIN instructors i ON pt.instructor_id = i.id
                 WHERE pt.user_id = ?
                 UNION
                 SELECT 'Specialist' as type, 
                        CASE DAYOFWEEK(sb.start_date)
                            WHEN 1 THEN 'Sunday'
                            WHEN 2 THEN 'Monday'
                            WHEN 3 THEN 'Tuesday'
                            WHEN 4 THEN 'Wednesday'
                            WHEN 5 THEN 'Thursday'
                            WHEN 6 THEN 'Friday'
                            WHEN 7 THEN 'Saturday'
                        END as day_of_week,
                        CASE 
                            WHEN sb.booking_type = 'Self-Defence Course' THEN '18:00:00'
                            ELSE '09:00:00'
                        END as start_time,
                        CASE 
                            WHEN sb.booking_type = 'Self-Defence Course' THEN '20:00:00'
                            ELSE ADDTIME('09:00:00', SEC_TO_TIME(sb.duration * 3600))
                        END as end_time,
                        sb.booking_type as class_name
                 FROM specialist_bookings sb
                 WHERE sb.user_id = ?
                 ORDER BY FIELD(day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), start_time";
$schedule_stmt = $conn->prepare($schedule_sql);
$schedule_stmt->bind_param("iii", $user_id, $user_id, $user_id);
$schedule_stmt->execute();
$schedule_result = $schedule_stmt->get_result();

$schedule_array = [];
$regular_sessions = 0;
while ($row = $schedule_result->fetch_assoc()) {
    $schedule_array[] = $row;
    if ($row['type'] == 'Regular') {
        $regular_sessions++;
    }
}

// If the user doesn't have enough regular sessions, add more
if ($regular_sessions < $session_limit && $user['membership_plan'] != 'Elite' && $user['membership_plan'] != 'Junior') {
    $additional_sessions_needed = $session_limit - $regular_sessions;
    $available_classes_sql = "SELECT c.id, c.name, s.day_of_week, s.start_time, s.end_time 
                              FROM classes c 
                              JOIN schedules s ON c.id = s.class_id 
                              WHERE c.id NOT IN (SELECT class_id FROM user_classes WHERE user_id = ?)
                              LIMIT ?";
    $available_classes_stmt = $conn->prepare($available_classes_sql);
    $available_classes_stmt->bind_param("ii", $user_id, $additional_sessions_needed);
    $available_classes_stmt->execute();
    $available_classes_result = $available_classes_stmt->get_result();

    while ($class = $available_classes_result->fetch_assoc()) {
        $schedule_array[] = [
            'type' => 'Regular',
            'day_of_week' => $class['day_of_week'],
            'start_time' => $class['start_time'],
            'end_time' => $class['end_time'],
            'class_name' => $class['name']
        ];
        $regular_sessions++;

        // Add the class to user_classes
        $add_class_sql = "INSERT INTO user_classes (user_id, class_id) VALUES (?, ?)";
        $add_class_stmt = $conn->prepare($add_class_sql);
        $add_class_stmt->bind_param("ii", $user_id, $class['id']);
        $add_class_stmt->execute();

        if ($regular_sessions >= $session_limit) {
            break;
        }
    }
}

// Fetch instructors
$instructors_sql = "SELECT * FROM instructors";
$instructors_result = $conn->query($instructors_sql);

// Fetch all instructors for private tuition
$all_instructors_sql = "SELECT * FROM instructors";
$all_instructors_result = $conn->query($all_instructors_sql);

function get_membership_price($plan) {
    switch ($plan) {
        case 'Basic':
            return 25.00;
        case 'Intermediate':
            return 35.00;
        case 'Advanced':
            return 45.00;
        case 'Elite':
            return 60.00;
        case 'Junior':
            return 25.00;
        default:
            return 0.00;
    }
}

// Modify the getTotalPrivateTuitionHours function
function getTotalPrivateTuitionHours($user_id) {
    global $conn;
    $sql = "SELECT SUM(duration) as total_hours FROM private_tuitions WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total_hours'] ?? 0;
}

// Calculate total private tuition costs
$total_private_tuition_hours = getTotalPrivateTuitionHours($user_id);
$private_tuition_cost_per_hour = 9;
$total_private_tuition_cost = $total_private_tuition_hours * $private_tuition_cost_per_hour;

function bookPrivateTuition($user_id, $instructor_id, $day, $time, $duration = 2) {
    global $conn;
    $sql = "INSERT INTO private_tuitions (user_id, instructor_id, tuition_day, tuition_time, duration) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissi", $user_id, $instructor_id, $day, $time, $duration);
    return $stmt->execute();
}

// Handle private tuition booking
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_private_tuition'])) {
    $instructor_id = $_POST['instructor_id'];
    $day = $_POST['tuition_day'];
    $time = $_POST['tuition_time'];
    $duration = $_POST['duration'];
    
    if (bookPrivateTuition($user_id, $instructor_id, $day, $time, $duration)) {
        $success_message = "Private tuition booked successfully!";
        $total_private_tuition_hours += $duration;
        $total_private_tuition_cost = $total_private_tuition_hours * $private_tuition_cost_per_hour;
    } else {
        $error_message = "Error booking private tuition. Please try again.";
    }
}

// Get session limit based on membership plan
function get_session_limit($plan) {
    switch ($plan) {
        case 'Basic': return 2;
        case 'Intermediate': return 3;
        case 'Advanced': return 5;
        case 'Elite':
        case 'Junior': return PHP_INT_MAX;
        default: return 0;
    }
}

$session_limit = get_session_limit($user['membership_plan']);

// Constants for pricing
define('SELF_DEFENCE_COURSE_PRICE', 180.00);
define('FITNESS_TRAINING_PRICE_PER_HOUR', 6.00);
define('PERSONAL_FITNESS_TRAINING_PRICE_PER_HOUR', 35.00);

function bookSpecialistCourseOrTraining($user_id, $type, $start_date, $duration = null) {
    global $conn;
    $sql = "INSERT INTO specialist_bookings (user_id, booking_type, start_date, duration) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issi", $user_id, $type, $start_date, $duration);
    return $stmt->execute();
}

// Check if user can book Personal Fitness Course
function canBookPersonalFitnessCourse($user_id, $conn) {
    $sql = "SELECT COUNT(*) as count FROM specialist_bookings 
            WHERE user_id = ? 
            AND booking_type = 'Personal Fitness Training'
            AND start_date >= DATE_SUB(CURRENT_DATE, INTERVAL 2 MONTH)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['count'] == 0;
}

// Handle specialist bookings
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['book_specialist_course'])) {
        $start_date = $_POST['course_start_date'];
        if (bookSpecialistCourseOrTraining($user_id, 'Self-Defence Course', $start_date, 6)) {
            $success_message = "Self-Defence Course booked successfully!";
        } else {
            $error_message = "Error booking Self-Defence Course. Please try again.";
        }
    } elseif (isset($_POST['book_fitness_training'])) {
        $start_date = $_POST['training_date'];
        $duration = $_POST['training_duration'];
        $type = $_POST['training_type'];
        
        if ($type === 'Personal Fitness Training') {
            if (!canBookPersonalFitnessCourse($user_id, $conn)) {
                $error_message = "You can only book one Personal Fitness Training course every 2 months.";
            } else {
                if (bookSpecialistCourseOrTraining($user_id, $type, $start_date, $duration)) {
                    $success_message = "Personal Fitness Training booked successfully!";
                } else {
                    $error_message = "Error booking Personal Fitness Training. Please try again.";
                }
            }
        } else {
            if (bookSpecialistCourseOrTraining($user_id, $type, $start_date, $duration)) {
                $success_message = "Fitness Training booked successfully!";
            } else {
                $error_message = "Error booking Fitness Training. Please try again.";
            }
        }
    }
}

// Handle schedule updates
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_schedule'])) {
    $schedule_stmt = $conn->prepare($schedule_sql);
    $schedule_stmt->bind_param("iii", $user_id, $user_id, $user_id);
    $schedule_stmt->execute();
    $schedule_result = $schedule_stmt->get_result();
    
    $schedule_array = [];
    $regular_sessions = 0;
    while ($row = $schedule_result->fetch_assoc()) {
        $schedule_array[] = $row;
        if ($row['type'] == 'Regular') {
            $regular_sessions++;
        }
    }
    
    $success_message = "Schedule updated successfully!";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DoBu Martial Arts - User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../Style/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="../index.php"><img src="../Media/picture/DojoLogo.png" alt="DoBu Martial Arts" height="40"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="../index.php#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="classes.php">Classes</a></li>
                    <li class="nav-item"><a class="nav-link" href="schedule.php">Schedule</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                    <li class="nav-item"><a class="nav-link active" href="user-profile.php">Profile</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5 pt-5">
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div>
        <?php else: ?>
            <h1 class="mb-4">Welcome, <?php echo htmlspecialchars($user['first_name'] ?? 'User'); ?>!</h1>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h2 class="card-title">Personal Information</h2>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></p>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone_number'] ?? 'N/A'); ?></p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</button>
                            <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Change Password</button>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-body">
                            <h2 class="card-title">Membership Details</h2>
                            <p><strong>Current Plan:</strong> <?php echo htmlspecialchars($user['membership_plan'] ?? 'N/A'); ?></p>
                            <p><strong>Classes Enrolled:</strong></p>
                            <ul>
                                <?php while ($class = $classes_result->fetch_assoc()): ?>
                                    <li><?php echo htmlspecialchars($class['name']); ?></li>
                                <?php endwhile; ?>
                            </ul>
                            <p><strong>Monthly Payment:</strong> $<?php echo number_format(get_membership_price($user['membership_plan'] ?? ''), 2); ?></p>
                            <p><strong>Membership Expiry:</strong> <?php echo $user['membership_expiry'] ? date('F d, Y', strtotime($user['membership_expiry'])) : 'N/A'; ?></p>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#upgradePlanModal">Upgrade Plan</button>
                        </div>
                    </div>

                    <!-- New box for Additional Services and Payments -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <h2 class="card-title">Additional Services and Payments</h2>
                            <h3>Private Tuition</h3>
                            <p><strong>Total Hours Booked:</strong> <?php echo $total_private_tuition_hours; ?> hours</p>
                            <p><strong>Total Cost:</strong> $<?php echo number_format($total_private_tuition_cost, 2); ?></p>
                            
                            <h3>Specialist Courses and Fitness Training</h3>
                            <?php
                            // Fetch specialist bookings
                            $specialist_sql = "SELECT booking_type, COUNT(*) as count FROM specialist_bookings WHERE user_id = ? GROUP BY booking_type";
                            $specialist_stmt = $conn->prepare($specialist_sql);
                            $specialist_stmt->bind_param("i", $user_id);
                            $specialist_stmt->execute();
                            $specialist_result = $specialist_stmt->get_result();

                            $total_specialist_cost = 0;

                            while ($booking = $specialist_result->fetch_assoc()) {
                                if ($booking['booking_type'] == 'Self-Defence Course') {
                                    $cost = SELF_DEFENCE_COURSE_PRICE * $booking['count'];
                                    $total_specialist_cost += $cost;
                                    echo "<p><strong>Self-Defence Course:</strong> " . $booking['count'] . " (Total: $" . number_format($cost, 2) . ")</p>";
                                } elseif ($booking['booking_type'] == 'Use of Fitness Training') {
                                    $cost = FITNESS_TRAINING_PRICE_PER_HOUR * $booking['count'];
                                    $total_specialist_cost += $cost;
                                    echo "<p><strong>Fitness Training:</strong> " . $booking['count'] . " hours (Total: $" . number_format($cost, 2) . ")</p>";
                                } elseif ($booking['booking_type'] == 'Personal Fitness Training') {
                                    $cost = PERSONAL_FITNESS_TRAINING_PRICE_PER_HOUR * $booking['count'];
                                    $total_specialist_cost += $cost;
                                    echo "<p><strong>Personal Fitness Training:</strong> " . $booking['count'] . " hours (Total: $" . number_format($cost, 2) . ")</p>";
                                }
                            }
                            ?>
                            <p><strong>Total Specialist Courses and Fitness Training Cost:</strong> $<?php echo number_format($total_specialist_cost, 2); ?></p>
                            
                            <h3>Total Additional Services Cost</h3>
                            <p><strong>Total:</strong> $<?php echo number_format($total_private_tuition_cost + $total_specialist_cost, 2); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h2 class="card-title">Your Schedule This Week</h2>
                                <form action="user-profile.php" method="post" class="d-inline">
                                    <button type="submit" name="update_schedule" class="btn btn-primary">
                                        <i class="fas fa-sync-alt"></i> Update Schedule
                                    </button>
                                </form>
                            </div>
                            <?php if ($user['membership_plan'] == 'Elite' || $user['membership_plan'] == 'Junior'): ?>
                                <p>Your <?php echo $user['membership_plan']; ?> plan allows you to attend unlimited classes.</p>
                            <?php else: ?>
                                <p>Your <?php echo $user['membership_plan']; ?> plan allows you to attend <?php echo $session_limit; ?> sessions per week.</p>
                                <p>Your class schedule has <?php echo $regular_sessions; ?> available regular sessions this week.</p>
                            <?php endif; ?>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Day</th>
                                        <th>Time</th>
                                        <th>Class</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($schedule_array as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['day_of_week']); ?></td>
                                        <td><?php echo htmlspecialchars($row['start_time'] . ' - ' . $row['end_time']); ?></td>
                                        <td>
                                            <?php
                                            if ($row['class_name'] === NULL) {
                                                if ($row['start_time'] === '13:00:00') {
                                                    echo 'Open Mat/Personal Practice';
                                                } else {
                                                    echo 'Private Tuition';
                                                }
                                            } elseif (strpos($row['start_time'], '15:00:00') !== false) {
                                                echo 'Kids ' . htmlspecialchars($row['class_name']);
                                            } else {
                                                echo htmlspecialchars($row['class_name']);
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <button class="btn btn-secondary" onclick="window.print()">Print Schedule</button>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-body">
                            <h2 class="card-title">Specialist Courses and Fitness Training</h2>
                            <ul class="list-group mb-3">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Six-week beginner's self-defence course (2 x 1-hour session per week)
                                    <span class="badge bg-primary rounded-pill">$<?php echo number_format(SELF_DEFENCE_COURSE_PRICE, 2); ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Use of fitness training (per hour)
                                    <span class="badge bg-primary rounded-pill">$<?php echo number_format(FITNESS_TRAINING_PRICE_PER_HOUR, 2); ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Personal Fitness Training (per hour)
                                    <span class="badge bg-primary rounded-pill">$<?php echo number_format(PERSONAL_FITNESS_TRAINING_PRICE_PER_HOUR, 2); ?></span>
                                </li>
                            </ul>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bookSpecialistCourseModal">Book Specialist Course</button>
                            <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#bookFitnessTrainingModal">Book Fitness Training</button>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-body">
                            <h2 class="card-title">Book Private Tuition</h2>
                            <p>Private tuition cost: $<?php echo $private_tuition_cost_per_hour; ?> per hour</p>
                            <p>Total private tuition hours booked: <?php echo $total_private_tuition_hours; ?> hours</p>
                            <p>Total private tuition cost: $<?php echo number_format($total_private_tuition_cost, 2); ?></p>
                            <form id="bookPrivateTuitionForm" action="user-profile.php" method="post">
                                <div class="mb-3">
                                    <label for="instructor_id" class="form-label">Select Instructor</label>
                                    <select class="form-select" id="instructor_id" name="instructor_id" required>
                                        <option value="">Choose an instructor</option>
                                        <?php
                                        $all_instructors_result->data_seek(0);
                                        while ($instructor = $all_instructors_result->fetch_assoc()): 
                                        ?>
                                            <option value="<?php echo $instructor['id']; ?>"><?php echo htmlspecialchars($instructor['name']); ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="tuition_day" class="form-label">Day</label>
                                    <select class="form-select" id="tuition_day" name="tuition_day" required>
                                        <option value="">Select a day</option>
                                        <option value="Tuesday">Tuesday</option>
                                        <option value="Wednesday">Wednesday</option>
                                        <option value="Thursday">Thursday</option>
                                        <option value="Friday">Friday</option>
                                        <option value="Saturday">Saturday</option>
                                        <option value="Sunday">Sunday</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="tuition_time" class="form-label">Time</label>
                                    <select class="form-select" id="tuition_time" name="tuition_time" required>
                                        <option value="">Select a time</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="duration" class="form-label">Duration (hours)</label>
                                    <select class="form-select" id="duration" name="duration" required>
                                        <option value="1" selected>1 hour</option>
                                        <option value="2">2 hours</option>
                                    </select>
                                </div>
                                <button type="submit" name="book_private_tuition" class="btn btn-primary">Book Private Tuition</button>
                            </form>
                            <?php
                            if (isset($success_message)) {
                                echo "<div class='alert alert-success mt-3'>$success_message</div>";
                            } elseif (isset($error_message)) {
                                echo "<div class='alert alert-danger mt-3'>$error_message</div>";
                            }
                            ?>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-body">
                            <h2 class="card-title">Account Management</h2>
                            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">Delete Account</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modals for various actions -->
    <!-- Edit Profile Modal -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editProfileForm" action="update_profile.php" method="post">
                        <div class="mb-3">
                            <label for="edit-first-name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="edit-first-name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-last-name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="edit-last-name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit-email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-phone" class="form-label">Phone</label>
                            <input type="tel" class="form-control" id="edit-phone" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="changePasswordForm" action="change_password.php" method="post">
                        <div class="mb-3">
                            <label for="current-password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current-password" name="current_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new-password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new-password" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm-password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm-password" name="confirm_password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Upgrade Plan Modal -->
    <div class="modal fade" id="upgradePlanModal" tabindex="-1" aria-labelledby="upgradePlanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="upgradePlanModalLabel">Upgrade Your Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="upgradePlanForm" action="user-profile.php" method="post">
                        <div class="mb-3">
                            <label for="new-plan" class="form-label">Select New Plan</label>
                            <select class="form-select" id="new-plan" name="new_plan" required>
                                <option value="">Choose a plan</option>
                                <option value="Basic">Basic - $25/month</option>
                                <option value="Intermediate">Intermediate - $35/month</option>
                                <option value="Advanced">Advanced - $45/month</option>
                                <option value="Elite">Elite - $60/month</option>
                                <option value="Junior">Junior - $25/month</option>
                            </select>
                        </div>
                        <div class="mb-3" id="classSelection">
                            <label class="form-label">Select Classes</label>
                            <?php
                            // Fetch all available classes
                            $all_classes_sql = "SELECT id, name FROM classes";
                            $all_classes_result = $conn->query($all_classes_sql);
                            
                            while ($class = $all_classes_result->fetch_assoc()): 
                            ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="classes[]" 
                                           value="<?php echo $class['id']; ?>" 
                                           id="class_<?php echo $class['id']; ?>"
                                           <?php echo in_array($class['id'], $user_classes) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="class_<?php echo $class['id']; ?>">
                                        <?php echo htmlspecialchars($class['name']); ?>
                                    </label>
                                </div>
                            <?php endwhile; ?>
                        </div>
                        <button type="submit" name="test" class="btn btn-primary">Test</button>
                        <button type="submit" name="upgrade_plan" class="btn btn-primary">Upgrade </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Support Modal -->
    <div class="modal fade" id="contactSupportModal" tabindex="-1" aria-labelledby="contactSupportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactSupportModalLabel">Contact Support</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="contactSupportForm" action="contact_support.php" method="post">
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Send</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Account Modal -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAccountModalLabel">Delete Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete your account? This action cannot be undone.</p>
                    <p>Please enter your password to confirm:</p>
                    <form id="deleteAccountForm">
                        <div class="mb-3">
                            <input type="password" class="form-control" id="deleteAccountPassword" required>
                        </div>
                        <button type="submit" class="btn btn-danger">Delete Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer id="contact" class="bg-dark text-white py-4">
    <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="../index.php#home">Home</a></li>
                        <li><a href="../index.php#about">About</a></li>
                        <li><a href="classes.php">Classes</a></li>
                        <li><a href="schedule.php">Schedule</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Contact</h5>
                    <p>123 Martial Arts St, City, State 12345</p>
                    <p>Phone: (123) 456-7890</p>
                    <p>Email: info@dobumartialarts.com</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Follow Us</h5>
                    <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <section id="contact"></section>
            <hr>
            <p class="text-center mb-0">&copy; 2023 DoBu Martial Arts. All rights reserved.</p>
        </div>
    </footer>

    <!-- Add before Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <div class="modal fade" id="bookSpecialistCourseModal" tabindex="-1" aria-labelledby="bookSpecialistCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookSpecialistCourseModalLabel">Book Specialist Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="bookSpecialistCourseForm" action="user-profile.php" method="post">
                        <p>Six-week beginner's self-defence course</p>
                        <p>Price: $<?php echo number_format(SELF_DEFENCE_COURSE_PRICE, 2); ?></p>
                        <div class="mb-3">
                            <label for="course_start_date" class="form-label">Start Date</label>
                            <input type="date" class="form-control" id="course_start_date" name="course_start_date" required>
                        </div>
                        <button type="submit" name="book_specialist_course" class="btn btn-primary">Book Course</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Book Fitness Training Modal -->
    <div class="modal fade" id="bookFitnessTrainingModal" tabindex="-1" aria-labelledby="bookFitnessTrainingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookFitnessTrainingModalLabel">Book Fitness Training</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="bookFitnessTrainingForm" action="user-profile.php" method="post">
                        <div class="mb-3">
                            <label for="training_type" class="form-label">Training Type</label>
                            <select class="form-select" id="training_type" name="training_type" required>
                                <option value="Use of Fitness Training">Use of Fitness Training ($<?php echo number_format(FITNESS_TRAINING_PRICE_PER_HOUR, 2); ?> per hour)</option>
                                <option value="Personal Fitness Training">Personal Fitness Training ($<?php echo number_format(PERSONAL_FITNESS_TRAINING_PRICE_PER_HOUR, 2); ?> per hour)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="training_date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="training_date" name="training_date" required>
                        </div>
                        <div class="mb-3">
                            <label for="training_duration" class="form-label">Duration (hours)</label>
                            <input type="number" class="form-control" id="training_duration" name="training_duration" min="1" max="5" value="1" required>
                        </div>
                        <button type="submit" name="book_fitness_training" class="btn btn-primary">Book Training</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/main.js"></script>
    <script>
    // Prevent form resubmission on page refresh
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    </script>
</body>
</html>
