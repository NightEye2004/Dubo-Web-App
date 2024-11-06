<?php
session_start();
require_once '../connect.php';

$loggedIn = isset($_SESSION['user_id']);
$userName = $loggedIn ? $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DoBu Martial Arts - Class Schedule</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../Style/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php"><img src="../Media/picture/DojoLogo.png" alt="DoBu Martial Arts" height="40"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="../index.php#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="../index.php#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="classes.php">Classes</a></li>
                    <li class="nav-item"><a class="nav-link" href="../index.php#instructors">Instructors</a></li>   
                    <li class="nav-item"><a class="nav-link" href="../index.php#facilities">Facilities</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                    <li class="nav-item"><a class="nav-link active" href="schedule.php">Schedule</a></li>
                    <?php if ($loggedIn): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo htmlspecialchars($userName); ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="user-profile.php">Dashboard</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link btn btn-primary ms-2" href="login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <header class="page-header bg-primary text-white text-center py-5">
        <div class="container">
            <h1 class="display-4">Class Schedule</h1>
            <p class="lead">Find the perfect time for your training</p>
        </div>
    </header>

    <main class="py-5">
        <div class="container">
            <div class="schedule-container">
                <table class="table table-bordered table-hover table-schedule">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Monday</th>
                            <th>Tuesday</th>
                            <th>Wednesday</th>
                            <th>Thursday</th>
                            <th>Friday</th>
                            <th>Saturday</th>
                            <th>Sunday</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="time-column">06:00 - 07:30</td>
                            <td>Jiu-jitsu</td>
                            <td>Karate</td>
                            <td>Judo</td>
                            <td>Jiu-jitsu</td>
                            <td>Muay Thai</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td class="time-column">08:00 - 10:00</td>
                            <td>Muay Thai</td>
                            <td>Private Tuition</td>
                            <td>Private Tuition</td>
                            <td>Private Tuition</td>
                            <td>Jiu-jitsu</td>
                            <td>Private Tuition</td>
                            <td>Private Tuition</td>
                        </tr>
                        <tr>
                            <td class="time-column">10:30 - 12:00</td>
                            <td>Private Tuition</td>
                            <td>Private Tuition</td>
                            <td>Private Tuition</td>
                            <td>Private Tuition</td>
                            <td>Private Tuition</td>
                            <td>Judo</td>
                            <td>Karate</td>
                        </tr>
                        <tr>
                            <td class="time-column">13:00 - 14:30</td>
                            <td>Open mat/personal practice</td>
                            <td>Open mat/personal practice</td>
                            <td>Open mat/personal practice</td>
                            <td>Open mat/personal practice</td>
                            <td>Open mat/personal practice</td>
                            <td>Karate</td>
                            <td>Judo</td>
                        </tr>
                        <tr>
                            <td class="time-column">15:00 - 17:00</td>
                            <td>Kids Jiu-jitsu</td>
                            <td>Kids Judo</td>
                            <td>Kids Karate</td>
                            <td>Kids Jiu-jitsu</td>
                            <td>Kids Judo</td>
                            <td>Muay Thai</td>
                            <td>Jiu-jitsu</td>
                        </tr>
                        <tr>
                            <td class="time-column">17:30 - 19:00</td>
                            <td>Karate</td>
                            <td>Muay Thai</td>
                            <td>Judo</td>
                            <td>Jiu-jitsu</td>
                            <td>Muay Thai</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td class="time-column">19:00 - 21:00</td>
                            <td>Jiu-jitsu</td>
                            <td>Judo</td>
                            <td>Jiu-jitsu</td>
                            <td>Karate</td>
                            <td>Private Tuition</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <?php if (!$loggedIn): ?>
    <section id="cta" class="py-5 bg-primary text-white text-center">
        <div class="container">
            <h2 class="mb-4">Ready to Start Your Martial Arts Journey?</h2>
            <a href="signup.php" class="btn btn-light btn-lg">Join Now</a>
        </div>
    </section>
    <?php endif; ?>

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
