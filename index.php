<?php
session_start();
require_once 'connect.php';

$loggedIn = isset($_SESSION['user_id']);
$userName = $loggedIn ? $_SESSION['first_name'] . ' ' . $_SESSION['last_name'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DoBu Martial Arts - Empower Your Body and Mind</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="Style/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#"><img src="Media/picture/DojoLogo.png" alt="DoBu Martial Arts" height="40"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#classes-preview">Classes</a></li>
                    <li class="nav-item"><a class="nav-link" href="#instructors">Instructors</a></li>   
                    <li class="nav-item"><a class="nav-link" href="#facilities">Facilities</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="Pages/schedule.php">Schedule</a></li>
                    <?php if ($loggedIn): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo htmlspecialchars($userName); ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="Pages/user-profile.php">Dashboard</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="Pages/logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link btn btn-primary ms-2" href="Pages/login.php">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <header id="home" class="hero">
        <div class="container text-center text-white">
            <h1 class="display-3 mb-4">Discover Your Inner Strength</h1>
            <p class="lead mb-4">Join DoBu Martial Arts and embark on a journey of self-discovery, discipline, and empowerment.</p>
            <a href="Pages/classes.php" class="btn btn-primary btn-lg me-2">Explore Classes</a>
            <a href="#contact" class="btn btn-outline-light btn-lg">Contact Us</a>
        </div>
    </header>

    <main>
        <section id="about" class="pt-6 " style="padding-top: 5rem;">
            <div class="container">
                <h2 class="text-center mb-4">About DoBu Martial Arts</h2>
                <div class="row align-items-center mb-4">
                    <div class="col-lg-6">
                        <p>Welcome to DoBu Martial Arts a place where discipline, community, and empowerment come together to create a holistic martial arts experience for everyone. Founded with a passion for martial arts and a commitment to excellence, DoBu Martial Arts is more than just a gym; it's a thriving community where individuals of all ages and abilities come to learn, grow, and transform.</p>
                        <p>We're dedicated to teaching the art of self-defense, the value of fitness, and the importance of mental resilience in an environment that is supportive, inclusive, and inspiring.</p>
                    </div>
                    <div class="col-lg-6">
                        <img src="Media/picture/dojo-interior.jpg" alt="DoBu Martial Arts Dojo" class="img-fluid rounded shadow">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <h3>Our Mission</h3>
                        <p>At DoBu Martial Arts, our mission is to empower individuals through martial arts and fitness training. We believe that martial arts are not only about learning techniques but also about cultivating inner strength, respect, and discipline.</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h3>What We Offer</h3>
                        <p>We offer a wide range of classes and training options to suit various interests, goals, and skill levels. From traditional martial arts to specialized fitness programs, each class is designed to help you build strength, improve agility, and develop confidence.</p>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h3>Our Facilities</h3>
                        <ul>
                            <li>Matted Martial Arts Area</li>
                            <li>Fully-Equipped Gym</li>
                            <li>Sauna and Steam Room</li>
                            <li>Changing and Shower Facilities</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section id="classes-preview" class="pt-6 " style="padding-top: 5rem;">
            <div class="container">
                <h2 class="text-center mb-4">Our Classes</h2>
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 shadow">
                            <img src="Media/picture/karate-class.jpg" class="card-img-top" alt="Karate Class">
                            <div class="card-body">
                                <h3 class="card-title">Karate</h3>
                                <p class="card-text">Master the art of striking and self-defense</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 shadow">
                            <img src="Media/picture/judo-class.jpg" class="card-img-top" alt="Judo Class">
                            <div class="card-body">
                                <h3 class="card-title">Judo</h3>
                                <p class="card-text">Learn throwing and grappling techniques</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 shadow">
                            <img src="Media/picture/muay-thai-class.jpg" class="card-img-top" alt="Muay Thai Class">
                            <div class="card-body">
                                <h3 class="card-title">Muay Thai</h3>
                                <p class="card-text">Develop powerful striking skills</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 shadow">
                            <img src="Media/picture/jiu-jitsu.jpg" class="card-img-top" alt="Jiu-Jitsu Class">
                            <div class="card-body">
                                <h3 class="card-title">Jiu-Jitsu</h3>
                                <p class="card-text">Grappling-based martial art focusing on ground fighting and submission techniques.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-4">
                    <a href="Pages/classes.php" class="btn btn-primary btn-lg">View Classes Info</a>
                </div>
            </div>
        </section>

        <section id="instructors" class="pt-6 " style="padding-top: 5rem;">
            <div class="container">
                <h2 class="text-center mb-5">Meet Our Expert Instructors</h2>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="Media/picture/mauricio-gomez.jpg" class="card-img-top" alt="Mauricio Gomez">
                            <div class="card-body">
                                <h3 class="card-title">Mauricio Gomez</h3>
                                <p class="card-text">Head Martial Arts Coach and Gym Owner</p>
                                <ul class="list-unstyled">
                                    <li>4th Dan Blackbelt Judo</li>
                                    <li>3rd Dan Blackbelt Jiu-jitsu</li>
                                    <li>1st Dan Blackbelt Karate</li>
                                    <li>Accredited Muay Thai Coach</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="Media/picture/sarah-nova.jpg" class="card-img-top" alt="Sarah Nova">
                            <div class="card-body">
                                <h3 class="card-title">Sarah Nova</h3>
                                <p class="card-text">Assistant Martial Arts Coach</p>
                                <ul class="list-unstyled">
                                    <li>5th Dan Karate</li>
                                    <li>Specializes in form and technique</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="Media/picture/guy-victory.jpg" class="card-img-top" alt="Guy Victory">
                            <div class="card-body">
                                <h3 class="card-title">Guy Victory</h3>
                                <p class="card-text">Assistant Martial Arts Coach</p>
                                <ul class="list-unstyled">
                                    <li>2nd Dan Blackbelt Jiu-jitsu</li>
                                    <li>1st Dan Blackbelt Judo</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="Media/picture/morris-davis.jpg" class="card-img-top" alt="Morris Davis">
                            <div class="card-body">
                                <h3 class="card-title">Morris Davis</h3>
                                <p class="card-text">Assistant Martial Arts Coach</p>
                                <ul class="list-unstyled"> 
                                    <li>Accredited Muay Thai Coach</li>
                                    <li>3rd Dan Blackbelt Karate</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="Media/picture/traci-santiago.jpg" class="card-img-top" alt="Traci Santiago">
                            <div class="card-body">
                                <h3 class="card-title">Traci Santiago</h3>
                                <p class="card-text">Fitness Coach</p>
                                <ul class="list-unstyled">
                                    <li>BSc in Sports Science</li>
                                    <li>Specializes in strength and conditioning for combat athletes</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="Media/picture/harpreet-kaur.jpg" class="card-img-top" alt="Harpreet Kaur">
                            <div class="card-body">
                                <h3 class="card-title">Harpreet Kaur</h3>
                                <p class="card-text">Fitness Coach</p>
                                <ul class="list-unstyled">
                                    <li>BSc in Physiotherapy</li>
                                    <li>MSc in Sport Science</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="facilities" class="pt-6 " style="padding-top: 5rem;">
            <div class="container">
                <h2 class="text-center mb-5">DoBu Facilities</h2>
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="card h-100">
                            <img src="Media/picture/gym.jpg" class="card-img-top" alt="Fully-equipped Gym">
                            <div class="card-body">
                                <h3 class="card-title">Fully-equipped Gym</h3>
                                <p class="card-text">State-of-the-art equipment for strength and conditioning training.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100">
                            <img src="Media/picture/sauna.jpg" class="card-img-top" alt="Sauna">
                            <div class="card-body">
                                <h3 class="card-title">Sauna</h3>
                                <p class="card-text">Relax and recover after intense training sessions.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100">
                            <img src="Media/picture/steam-room.jpg" class="card-img-top" alt="Steam Room">
                            <div class="card-body">
                                <h3 class="card-title">Steam Room</h3>
                                <p class="card-text">Improve circulation and promote muscle relaxation.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-4">
                        <div class="card h-100">
                            <img src="Media/picture/changing-rooms.jpg" class="card-img-top" alt="Changing and Shower Facilities">
                            <div class="card-body">
                                <h3 class="card-title">Changing and Shower Facilities</h3>
                                <p class="card-text">Clean and spacious facilities for your convenience.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <?php if (!$loggedIn): ?>
        <section id="cta" class="py-5 bg-primary text-white text-center">
            <div class="container">
                <h2 class="mb-4">Ready to Start Your Martial Arts Journey?</h2>
                <a href="signup.php" class="btn btn-light btn-lg">Join Now</a>
            </div>
        </section>
        <?php endif; ?>

    
    </main>

    <footer class="bg-dark text-white py-4">
        
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#home">Home</a></li>
                        <li><a href="#about">About</a></li>
                        <li><a href="Pages/classes.php">Classes</a></li>
                        <li><a href="Pages/schedule.php">Schedule</a></li>
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
    <script src="js/main.js"></script>
</body>
</html>
