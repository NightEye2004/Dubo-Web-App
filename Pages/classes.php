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
    <title>DoBu Martial Arts - Our Classes and Training Plans</title>
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
                    <li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="../index.php#about">About</a></li>
                    <li class="nav-item"><a class="nav-link active" href="classes.php">Classes</a></li>
                    <li class="nav-item"><a class="nav-link" href="../index.php#instructors">Instructors</a></li>
                    <li class="nav-item"><a class="nav-link" href="#facilities">Facilities</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="schedule.php">Schedule</a></li>
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
                        <li class="nav-item"><a class="nav-link btn btn-primary ms-2" href="Pages/login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <header class="page-header bg-primary text-white text-center py-5">
        <div class="container">
       <h1></h1>
        <h1 class="display-4">Our Classes and Training Plans</h1>
            <p class="lead">Discover the perfect martial art and training plan to empower your body and mind</p>
        </div>
    </header>

    <main class="py-5">
        <div class="container">
            <!-- Classes section -->
            <h2 class="text-center mb-4">Our Classes</h2>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <img src="../Media/picture/karate-class.jpg" class="card-img-top" alt="Karate Class">
                        <div class="card-body">
                            <h2 class="card-title">Karate</h2>
                            <p class="card-text">Karate is a Japanese martial art that focuses on striking techniques such as punching, kicking, and knee strikes. Our Karate classes emphasize discipline, respect, and self-control while building strength, flexibility, and coordination.</p>
                            <h5>Benefits:</h5>
                            <ul>
                                <li>Improved physical fitness and flexibility</li>
                                <li>Enhanced self-defense skills</li>
                                <li>Increased focus and concentration</li>
                                <li>Stress relief and mental clarity</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <img src="../Media/picture/judo-class.jpg" class="card-img-top" alt="Judo Class">
                        <div class="card-body">
                            <h2 class="card-title">Judo</h2>
                            <p class="card-text">Judo is a modern Japanese martial art and Olympic sport that focuses on throws, grappling, and submission techniques. Our Judo classes teach balance, leverage, and timing, promoting both physical and mental development.</p>
                            <h5>Benefits:</h5>
                            <ul>
                                <li>Improved balance and body awareness</li>
                                <li>Enhanced strength and cardiovascular fitness</li>
                                <li>Effective self-defense techniques</li>
                                <li>Development of strategic thinking</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <img src="../Media/picture/muay-thai-class.jpg" class="card-img-top" alt="Muay Thai Class">
                        <div class="card-body">
                            <h2 class="card-title">Muay Thai</h2>
                            <p class="card-text">Muay Thai, also known as Thai Boxing, is a striking-based martial art from Thailand. Our Muay Thai classes focus on powerful kicks, punches, elbow and knee strikes, as well as clinching techniques.</p>
                            <h5>Benefits:</h5>
                            <ul>
                                <li>High-intensity cardiovascular workout</li>
                                <li>Improved striking power and technique</li>
                                <li>Enhanced core strength and stability</li>
                                <li>Increased mental toughness and resilience</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <img src="../Media/picture/jiu-jitsu.jpg" class="card-img-top" alt="Jiu-Jitsu Class">
                        <div class="card-body">
                            <h2 class="card-title">Jiu-Jitsu</h2>
                            <p class="card-text">Jiu-Jitsu is a grappling-based martial art that focuses on ground fighting and submission techniques. Our Jiu-Jitsu classes teach leverage, technique, and strategy to overcome larger opponents.</p>
                            <h5>Benefits:</h5>
                            <ul>
                                <li>Effective self-defense for all body types</li>
                                <li>Improved problem-solving skills</li>
                                <li>Full-body workout and increased flexibility</li>
                                <li>Stress relief and boosted confidence</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Training Plans and Pricing section -->
            <h2 class="text-center mb-4 mt-5">Training Plans and Pricing</h2>
            <div class="row">
                <div class="col-md-6">
                    <h3>Membership Options</h3>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Basic (1 Martial Art - 2 sessions per week)
                            <span class="badge bg-primary rounded-pill">$25.00/month</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Intermediate (1 Martial Art - 3 sessions per week)
                            <span class="badge bg-primary rounded-pill">$35.00/month</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Advanced (any 2 Martial Arts - 5 sessions per week)
                            <span class="badge bg-primary rounded-pill">$45.00/month</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Elite (Unlimited Classes)
                            <span class="badge bg-primary rounded-pill">$60.00/month</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Junior Membership (all-kinds Martial Art sessions)
                            <span class="badge bg-primary rounded-pill">$25.00/month</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Private Martial Art tuition
                            <span class="badge bg-primary rounded-pill">Per hour rate</span>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h3>Specialist Courses and Fitness Training</h3>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Six-week beginner's self-defence course (2 x 1-hour session per week)
                            <span class="badge bg-primary rounded-pill">$180.00</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Use of fitness training
                            <span class="badge bg-primary rounded-pill">$6.00/hour</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Personal Fitness Training
                            <span class="badge bg-primary rounded-pill">$35.00/hour</span>
                        </li>
                    </ul>
                </div>
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
