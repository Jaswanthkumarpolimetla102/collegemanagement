<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AANM & VVRSR Polytechnic | Excellence in Technical Education</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            overflow-x: hidden;
            cursor: auto; /* Default cursor */
        }

        /* Modern Header */
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            animation: slideDown 0.8s ease;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .header h1 {
            font-size: 2.2rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
        }

        .header h4 {
            color: #666;
            font-weight: 400;
            letter-spacing: 1px;
        }

        .hover-float {
            transition: transform 0.3s ease;
        }

        .hover-float:hover {
            transform: translateY(-5px);
        }

        /* Modern Navigation */
        .modern-nav {
            background: linear-gradient(135deg, #1e1e2f 0%, #2a2a4a 100%);
            padding: 1rem 0;
            position: sticky;
            top: 120px;
            z-index: 999;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #ffd700 0%, #ffa500 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
            overflow: hidden;
        }

        .navbar-brand::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            to {
                left: 200%;
            }
        }

        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            font-weight: 500;
            margin: 0 0.5rem;
            position: relative;
            transition: all 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(90deg, #ffd700, #ffa500);
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .nav-link:hover {
            color: #ffd700 !important;
        }

        .nav-link:hover::after {
            width: 80%;
        }

        /* Hero Video Section */
        .hero-section {
            position: relative;
            height: 90vh;
            overflow: hidden;
        }

        #myVideo {
            position: absolute;
            top: 50%;
            left: 50%;
            min-width: 100%;
            min-height: 100%;
            width: auto;
            height: auto;
            transform: translateX(-50%) translateY(-50%);
            z-index: 0;
        }

        .video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.8) 0%, rgba(118, 75, 162, 0.8) 100%);
            z-index: 1;
        }

        .hero-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            z-index: 2;
            width: 80%;
            max-width: 800px;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 800;
            color: white;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            animation: fadeInUp 1s ease;
        }

        .hero-subtitle {
            font-size: 1.5rem;
            color: rgba(255,255,255,0.9);
            margin-bottom: 2rem;
            animation: fadeInUp 1s ease 0.2s both;
        }

        .hero-buttons {
            animation: fadeInUp 1s ease 0.4s both;
        }

        .btn-modern {
            padding: 1rem 2.5rem;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin: 0 0.5rem;
            text-decoration: none;
            display: inline-block;
        }

        .btn-modern::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255,255,255,0.3);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-modern:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, #ffd700 0%, #ffa500 100%);
            color: #1e1e2f;
            border: none;
            box-shadow: 0 10px 20px rgba(255,215,0,0.3);
        }

        .btn-outline-modern {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-outline-modern:hover {
            background: white;
            color: #667eea;
        }

        /* Founders Section */
        .founders-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            position: relative;
            overflow: hidden;
        }

        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title h2 {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
            display: inline-block;
        }

        .section-title h2::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, #ffd700, #ffa500);
            border-radius: 2px;
        }

        .founder-card {
            background: white;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
            position: relative;
        }

        .founder-card:hover {
            transform: translateY(-20px) scale(1.02);
            box-shadow: 0 40px 80px rgba(102, 126, 234, 0.3);
        }

        .founder-image-wrapper {
            position: relative;
            overflow: hidden;
            padding: 40px 40px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .founder-image {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            border: 5px solid white;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: all 0.5s ease;
            margin: 0 auto;
            display: block;
            object-fit: cover;
        }

        .founder-card:hover .founder-image {
            transform: scale(1.1) rotate(5deg);
        }

        .founder-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.9), rgba(118, 75, 162, 0.9));
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: all 0.5s ease;
        }

        .founder-card:hover .founder-overlay {
            opacity: 1;
        }

        .founder-social {
            display: flex;
            gap: 20px;
        }

        .founder-social a {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #667eea;
            font-size: 1.3rem;
            transition: all 0.3s ease;
            transform: translateY(50px);
            opacity: 0;
            text-decoration: none;
        }

        .founder-card:hover .founder-social a {
            transform: translateY(0);
            opacity: 1;
        }

        .founder-social a:nth-child(1) { transition-delay: 0.1s; }
        .founder-social a:nth-child(2) { transition-delay: 0.2s; }
        .founder-social a:nth-child(3) { transition-delay: 0.3s; }

        .founder-social a:hover {
            background: #ffd700;
            color: #1e1e2f;
            transform: translateY(-5px) scale(1.1);
        }

        .card-body {
            padding: 30px;
            text-align: center;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
        }

        .card-text {
            color: #666;
            line-height: 1.8;
        }

        /* Stats Section */
        .stats-section {
            background: linear-gradient(135deg, #1e1e2f 0%, #2a2a4a 100%);
            padding: 80px 0;
            position: relative;
            overflow: hidden;
        }

        .stats-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .stat-item {
            text-align: center;
            color: white;
            position: relative;
            z-index: 1;
        }

        .stat-number {
            font-size: 3.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ffd700 0%, #ffa500 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 1.2rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* CTA Section */
        .cta-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
        }

        .cta-title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .cta-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 40px;
        }

        /* Modern Footer */
        .modern-footer {
            background: linear-gradient(135deg, #1e1e2f 0%, #2a2a4a 100%);
            color: white;
            padding: 60px 0 30px;
            position: relative;
            overflow: hidden;
        }

        .footer-widget {
            margin-bottom: 30px;
        }

        .footer-widget h4 {
            color: #ffd700;
            margin-bottom: 20px;
            font-weight: 600;
            position: relative;
            padding-bottom: 10px;
        }

        .footer-widget h4::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 2px;
            background: linear-gradient(90deg, #ffd700, #ffa500);
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .footer-links a:hover {
            color: #ffd700;
            transform: translateX(5px);
        }

        .social-links {
            display: flex;
            gap: 15px;
        }

        .social-links a {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .social-links a:hover {
            background: #ffd700;
            color: #1e1e2f;
            transform: translateY(-5px) scale(1.1);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 30px;
            margin-top: 30px;
            text-align: center;
        }

        /* Floating Elements */
        .floating-element {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
        }

        .back-to-top {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #ffd700 0%, #ffa500 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1e1e2f;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(255,215,0,0.3);
            transition: all 0.3s ease;
        }

        .back-to-top:hover {
            transform: translateY(-10px) scale(1.1);
            box-shadow: 0 20px 40px rgba(255,215,0,0.4);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header h1 {
                font-size: 1.5rem;
            }

            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.2rem;
            }

            .btn-modern {
                display: block;
                margin: 10px auto;
                width: 80%;
            }

            .stat-number {
                font-size: 2.5rem;
            }
        }
    </style>
</head>

<body>
    <!-- Header Section -->
    <header class="header d-flex flex-column align-items-center py-3">
        <div class="d-flex justify-content-between align-items-center w-100 px-5">
            <img src="collage_logo-removebg-preview.png" alt="College Logo" height="100" class="hover-float">
            <div class="text-center">
                <h1 class="mb-2">A.A.N.M & V.V.R.S.R POLYTECHNIC</h1>
                <h4 class="mb-0">(Approved By AICTE, New Delhi) | ISO 9001:2015 Certified</h4>
                <div class="d-flex justify-content-center gap-3 mt-2">
                    
                </div>
            </div>
            <div class="d-flex align-items-center">
                <img src="aicte-removebg-preview.png" alt="AICTE Logo" height="100" class="me-3 hover-float">
                <div class="text-center">
                    <img src="43-removebg-preview.png" alt="Logo 43" height="70" class="hover-float">
                    <h6 class="mt-2"><strong class="text-primary">Counselling Code: AVGR</strong></h6>
                </div>
            </div>
        </div>
    </header>

    <!-- Modern Navigation -->
    <nav class="modern-nav navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-university me-2"></i>A.A.N.M & V.V.R.S.R POLYTECHNIC
            </a>
            <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-home me-1"></i>Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="root.php"><i class="fas fa-sign-in-alt me-1"></i>Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="button.php"><i class="fas fa-user-plus me-1"></i>Signup</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php"><i class="fas fa-info-circle me-1"></i>About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="Footer.php"><i class="fas fa-phone-alt me-1"></i>Contact</a>
                    </li>
                </ul>
                <div class="ms-3">
                    
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Video Section -->
    <div class="hero-section">
        <video autoplay muted loop id="myVideo">
            <source src="collage video - Made with Clipchamp.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="video-overlay"></div>
        <div class="hero-content">
            <h1 class="hero-title" data-aos="fade-up">Shape Your Future</h1>
            <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="200">Join the premier polytechnic institution with state-of-the-art facilities and industry-focused education</p>
            <div class="hero-buttons" data-aos="fade-up" data-aos-delay="400">
                <a href="#" class="btn-modern btn-primary-modern">Explore Programs</a>
                <a href="#" class="btn-modern btn-outline-modern">Virtual Tour</a>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-6 mb-4" data-aos="zoom-in" data-aos-delay="100">
                    <div class="stat-item">
                        <div class="stat-number">3000+</div>
                        <div class="stat-label">Students</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-4" data-aos="zoom-in" data-aos-delay="200">
                    <div class="stat-item">
                        <div class="stat-number">250+</div>
                        <div class="stat-label">Faculty</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-4" data-aos="zoom-in" data-aos-delay="300">
                    <div class="stat-item">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Placements</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-4" data-aos="zoom-in" data-aos-delay="400">
                    <div class="stat-item">
                        <div class="stat-number">45+</div>
                        <div class="stat-label">Years</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Founders Section -->
    <section id="founders" class="founders-section">
        <div class="container">
            <div class="section-title" data-aos="fade-up">
                <h2>Our Visionary Founders</h2>
            </div>
            <div class="row g-4 justify-content-center">
                <!-- Founder 1 -->
                <div class="col-lg-5 col-md-6" data-aos="fade-right" data-aos-delay="200">
                    <div class="founder-card">
                        <div class="founder-image-wrapper">
                            <img src="https://aanm-vvrsrpolytechnic.ac.in/wp-content/uploads/2025/03/aanm.jpg" class="founder-image" alt="Sri Adsumilli Aswardha Narayana Murthy">
                            <div class="founder-overlay">
                                <div class="founder-social">
                                    
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">Sri Adsumilli Aswardha Narayana Murthy (AANM)</h3>
                            <p class="card-text">Founder President (1906-1988). A visionary leader who laid the foundation for this institution, fostering technical education with a strong commitment to excellence. His legacy continues to inspire generations of students.</p>
                        </div>
                    </div>
                </div>
                <!-- Founder 2 -->
                <div class="col-lg-5 col-md-6" data-aos="fade-left" data-aos-delay="300">
                    <div class="founder-card">
                        <div class="founder-image-wrapper">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQJnCDZvGuDpCrnuVMKB6AvwZ0_e8J7eht6NQ&s" class="founder-image" alt="Sri Vallurupalli Venkata Rama Seshadri Rao">
                            <div class="founder-overlay">
                                <div class="founder-social">
                                    
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">Sri Vallurupalli Venkata Rama Seshadri Rao (VVRSR)</h3>
                            <p class="card-text">Founder Secretary & Correspondent (1932-2008). A distinguished figure whose dedication and leadership significantly shaped the institution's growth and legacy. His vision for quality education remains our guiding light.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <div class="cta-section">
        <div class="container">
            <h2 class="cta-title" data-aos="fade-up">Ready to Begin Your Journey?</h2>
            <p class="cta-subtitle" data-aos="fade-up" data-aos-delay="200">Join thousands of successful alumni who have shaped their careers with us</p>
            <div data-aos="fade-up" data-aos-delay="400">
                
                <a href="#" class="btn-modern btn-outline-modern">Download Brochure</a>
            </div>
        </div>
    </div>

    <!-- Modern Footer -->
    <footer class="modern-footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="footer-widget" data-aos="fade-right">
                        <h4>About Us</h4>
                        <p class="text-white-50">A.A.N.M & V.V.R.S.R Polytechnic College is committed to providing quality technical education and shaping future leaders in engineering and technology.</p>
                        <div class="social-links mt-3">
                            <a href="https://www.facebook.com/poly.gvl/"><i class="fab fa-facebook-f"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="https://www.instagram.com/aanm.vvrsr.polytechnic"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <div class="footer-widget" data-aos="fade-up">
                        <h4>Quick Links</h4>
                        <ul class="footer-links">
                            <li><a href="#"><i class="fas fa-chevron-right me-2"></i>About Us</a></li>
                            <li><a href="#"><i class="fas fa-chevron-right me-2"></i>Admissions</a></li>
                            <li><a href="#"><i class="fas fa-chevron-right me-2"></i>Academics</a></li>
                            <li><a href="#"><i class="fas fa-chevron-right me-2"></i>Placements</a></li>
                            <li><a href="#"><i class="fas fa-chevron-right me-2"></i>Contact</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget" data-aos="fade-up" data-aos-delay="200">
                        <h4>Programs</h4>
                        <ul class="footer-links">
                            <li><a href="#"><i class="fas fa-chevron-right me-2"></i>Civil Engineering</a></li>
                            <li><a href="#"><i class="fas fa-chevron-right me-2"></i>Mechanical Engineering</a></li>
                            <li><a href="#"><i class="fas fa-chevron-right me-2"></i>Electrical Engineering</a></li>
                            <li><a href="#"><i class="fas fa-chevron-right me-2"></i>Electronics & Communication</a></li>
                            <li><a href="#"><i class="fas fa-chevron-right me-2"></i>Computer Engineering</a></li>
                            <li><a href="#"><i class="fas fa-chevron-right me-2"></i>Artificial Intelligence and Machine Learning </a></li>
                            <li><a href="#"><i class="fas fa-chevron-right me-2"></i>Internet Of Things</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-widget" data-aos="fade-left">
                        <h4>Contact Info</h4>
                        <ul class="footer-links">
                            <li><i class="fas fa-map-marker-alt me-2 text-warning"></i> Gudlavalleru, Andhra Pradesh</li>
                            <li><i class="fas fa-phone me-2 text-warning"></i> +91 1234567890</li>
                            <li><i class="fas fa-envelope me-2 text-warning"></i> info@aanmvvrsr.edu.in</li>
                            <li><i class="fas fa-clock me-2 text-warning"></i> Mon - Sat: 9:00 AM - 5:00 PM</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <div class="row">
                    <div class="col-md-6 text-center text-md-start">
                        <p class="mb-0">&copy; 2023Batch  A.A.N.M & V.V.R.S.R Polytechnic College. All rights reserved.</p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <a href="#" class="text-white-50 me-3">Privacy Policy</a>
                        <a href="#" class="text-white-50 me-3">Terms of Use</a>
                        <a href="#" class="text-white-50">Sitemap</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating Elements -->
    <div class="floating-element">
        <div class="back-to-top" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
            <i class="fas fa-arrow-up"></i>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true
        });

        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('.modern-nav');
            if (window.scrollY > 100) {
                nav.style.background = 'linear-gradient(135deg, #1e1e2f 0%, #2a2a4a 100%)';
                nav.style.padding = '0.5rem 0';
            } else {
                nav.style.background = 'linear-gradient(135deg, #1e1e2f 0%, #2a2a4a 100%)';
                nav.style.padding = '1rem 0';
            }
        });

        // Counter animation for stats
        const stats = document.querySelectorAll('.stat-number');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const stat = entry.target;
                    const text = stat.innerText;
                    const number = parseInt(text.replace(/[^0-9]/g, ''));
                    let current = 0;
                    const increment = number / 50;
                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= number) {
                            stat.innerText = text;
                            clearInterval(timer);
                        } else {
                            stat.innerText = Math.floor(current) + (text.includes('+') ? '+' : '');
                        }
                    }, 20);
                }
            });
        });

        stats.forEach(stat => observer.observe(stat));
    </script>
</body>
        <!-- Footer with Developer Info -->
        <div style="margin-top: 2rem; text-align: center;">
            <div style="display: inline-block; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); border-radius: 50px; padding: 0.5rem 1.5rem;">
                <span style="color: white; opacity: 0.7; font-size: 0.9rem;">
                    <i class="fas fa-code me-2"></i>
                    Developed with 
                    <i class="fas fa-heart" style="color: #ff6b6b; margin: 0 4px;"></i> 
                    by 
                    <button onclick="openDeveloperModal()" style="background: none; border: none; color: #ffd93d; font-weight: 600; cursor: pointer; text-decoration: underline; text-underline-offset: 3px;">
                        23Batch Aiml 3BStudents
                    </button>
                    <i class="fas fa-mug-hot ms-2" style="color: #ffd93d;"></i>
                </span>
            </div>
        </div>
    </div> <!-- Close dashboard div -->
</html>