<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🎓 A.A.N.M & V.V.R.S.R Polytechnic | Premier Diploma Engineering</title>
    <!-- Font Awesome 6 (free) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Google Fonts (Inter + fallback) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(145deg, #f0f5fb 0%, #e9f0f7 100%);
            color: #1e2b3f;
            line-height: 1.5;
            padding: 2rem 1.5rem;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* main glossy card */
        .poly-card {
            max-width: 1300px;
            width: 100%;
            background: rgba(255,255,255,0.75);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-radius: 3.5rem 3.5rem 2.5rem 2.5rem;
            box-shadow: 0 30px 60px rgba(0, 20, 40, 0.2),
                        0 8px 20px rgba(0, 32, 64, 0.1),
                        inset 0 1px 2px rgba(255,255,255,0.6);
            border: 1px solid rgba(255,255,255,0.5);
            padding: 2.5rem 2.2rem;
            transition: all 0.3s ease;
            position: relative; /* for nav button placement if needed */
        }

        /* ----- NAVIGATION BACK BUTTON (added) ----- */
        .nav-back {
            margin-bottom: 1.5rem;
            display: flex;
        }
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(8px);
            padding: 0.7rem 1.8rem 0.7rem 1.4rem;
            border-radius: 60px;
            font-weight: 600;
            color: #0b2f54;
            border: 1px solid rgba(31,99,146,0.4);
            box-shadow: 0 10px 18px -8px rgba(0,40,70,0.3);
            text-decoration: none;
            transition: 0.2s;
            font-size: 1rem;
            border: 1px solid rgba(255,255,255,0.8);
        }
        .back-btn i {
            font-size: 1.4rem;
            color: #1f6392;
        }
        .back-btn:hover {
            background: white;
            transform: scale(1.02);
            box-shadow: 0 14px 22px -8px #1f4b7a;
            border-color: #5282b9;
        }

        /* header with emblem style */
        .header-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.2rem;
            border-bottom: 2px dashed rgba(0, 54, 102, 0.2);
            padding-bottom: 1.8rem;
        }

        .title-section h1 {
            font-size: 2.3rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, #0b2f54, #1f4b7a);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            line-height: 1.2;
            text-shadow: 0 4px 12px rgba(21, 76, 121, 0.2);
        }

        .estd-badge {
            background: #17456b;
            color: white;
            display: inline-block;
            padding: 0.4rem 1.2rem;
            border-radius: 40px;
            font-weight: 600;
            font-size: 0.95rem;
            box-shadow: 0 6px 14px rgba(22, 68, 110, 0.4);
            margin-top: 0.5rem;
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255,255,255,0.3);
        }

        .rank-achievement {
            background: #f9cf5c;
            color: #0a2b44;
            padding: 0.8rem 1.6rem;
            border-radius: 60px;
            font-weight: 700;
            font-size: 1.2rem;
            box-shadow: 0 10px 20px rgba(230, 162, 20, 0.3);
            display: flex;
            align-items: center;
            gap: 0.7rem;
            border: 1px solid #ffe5a3;
        }

        .rank-achievement i {
            font-size: 2rem;
            color: #a75c07;
        }

        /* grid for key details chips */
        .key-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 0.8rem 1.2rem;
            margin: 2rem 0 1.8rem;
        }

        .chip {
            background: rgba(255,255,255,0.7);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(72, 120, 184, 0.3);
            padding: 0.6rem 1.4rem;
            border-radius: 60px;
            font-weight: 500;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 10px rgba(0,30,60,0.08);
            color: #153e5a;
        }

        .chip i {
            color: #1f6392;
            font-size: 1.2rem;
            width: 1.4rem;
        }

        /* courses — fancy grid */
        .section-title {
            font-size: 2rem;
            font-weight: 700;
            margin: 2rem 0 1.5rem 0;
            display: flex;
            align-items: center;
            gap: 12px;
            border-left: 8px solid #1f6392;
            padding-left: 1.3rem;
            background: linear-gradient(90deg, rgba(31,99,146,0.1), transparent);
        }

        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.2rem;
        }

        .course-item {
            background: white;
            border-radius: 2rem 1rem 2rem 1rem;
            padding: 1.2rem 0.8rem;
            text-align: center;
            font-weight: 600;
            font-size: 1.1rem;
            color: #113855;
            box-shadow: 0 15px 25px -10px rgba(0,55,100,0.2);
            transition: 0.2s ease-in-out;
            border: 1px solid rgba(255,255,255,0.8);
            backdrop-filter: blur(4px);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }

        .course-item i {
            font-size: 2rem;
            color: #2266a8;
            background: #e3f0ff;
            padding: 0.6rem;
            border-radius: 50%;
        }

        .course-item:hover {
            transform: scale(1.02) translateY(-5px);
            background: #fcfdff;
            border-color: #5282b9;
            box-shadow: 0 20px 30px -8px #1b4d7a;
        }

        /* infrastructure & placement stats — fun cards */
        .stats-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .stat-tile {
            flex: 1 1 200px;
            background: rgba(255,255,255,0.5);
            backdrop-filter: blur(8px);
            border-radius: 2rem;
            padding: 1.8rem 1rem;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.7);
            box-shadow: 0 10px 30px -10px #0d3d60;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: #0f3c60;
            line-height: 1.2;
        }

        .stat-label {
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            color: #284e72;
        }

        .stat-tile i {
            font-size: 2.2rem;
            background: #ffffffb8;
            padding: 0.7rem;
            border-radius: 60%;
            color: #194d77;
            margin-bottom: 0.5rem;
            border: 1px solid white;
        }

        /* placement highlight */
        .placement-banner {
            background: linear-gradient(115deg, #0f2b40, #193e5e);
            color: white;
            border-radius: 2.5rem;
            padding: 2rem 2.5rem;
            margin: 2rem 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 30px 30px -10px #0a2d44;
        }

        .placement-banner .company-strip {
            display: flex;
            flex-wrap: wrap;
            gap: 1.8rem;
            font-weight: 600;
            font-size: 1.2rem;
            align-items: center;
        }

        .company-strip span {
            background: rgba(255,255,255,0.15);
            padding: 0.4rem 1.2rem;
            border-radius: 40px;
            border: 1px solid #5a8ec0;
        }

        .highest-package {
            background: #fedb5f;
            color: #0e2d45;
            padding: 0.8rem 2rem;
            border-radius: 40px;
            font-weight: 800;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* contact / address block */
        .contact-block {
            background: #d6e7f9;
            border-radius: 2rem;
            padding: 2rem;
            margin-top: 2.2rem;
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
            justify-content: space-between;
            align-items: center;
            border: 2px solid white;
            box-shadow: inset 0 2px 5px rgba(255,255,255,0.8), 0 15px 20px rgba(39, 73, 109, 0.2);
        }

        .address {
            display: flex;
            gap: 1.2rem;
            align-items: center;
            font-weight: 500;
        }

        .address i {
            font-size: 2.5rem;
            color: #103956;
        }

        .contact-icons a {
            background: white;
            padding: 0.8rem 1.5rem;
            border-radius: 3rem;
            font-weight: 600;
            text-decoration: none;
            color: #0d3250;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            box-shadow: 0 10px 12px rgba(0,0,0,0.1);
            transition: 0.2s;
            border: 1px solid #97bce4;
        }

        .contact-icons a:hover {
            background: #17456b;
            color: white;
            transform: scale(1.05);
        }

        .activities-sports {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin: 1.8rem 0;
        }

        .sports-chip {
            background: white;
            border-radius: 30px;
            padding: 0.6rem 1.6rem;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            box-shadow: 0 6px 14px rgba(70, 120, 160, 0.25);
        }

        /* footer / small print */
        .footer-note {
            text-align: center;
            margin-top: 3rem;
            font-size: 0.9rem;
            opacity: 0.8;
            font-weight: 400;
        }

        hr {
            border: none;
            height: 2px;
            background: linear-gradient(90deg, transparent, #729fcb, transparent);
            margin: 2rem 0 0.5rem;
        }

        /* responsiveness */
        @media (max-width:700px) {
            .poly-card { padding: 1.5rem; }
            .title-section h1 { font-size: 1.9rem; }
            .rank-achievement { font-size: 1rem; }
        }
    </style>
</head>
<body>
    <div class="poly-card">
        <!-- ++++++++++ NAVIGATION BACK BUTTON (added) ++++++++++ -->
        <div class="nav-back">
            <a href="#" class="back-btn" onclick="history.back(); return false;" title="Go back to previous page">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <!-- also provide a fallback link if history empty? We'll just use href="#", but the onclick handles back. -->
        </div>

        <!-- header with estd and state rank -->
        <div class="header-row">
            <div class="title-section">
                <h1>A.A.N.M & V.V.R.S.R Polytechnic</h1>
                <div class="estd-badge"><i class="fas fa-calendar-alt" style="margin-right: 8px;"></i>ESTD. 1981 | Gudlavalleru</div>
                <div style="margin-top: 0.8rem; display: flex; gap: 10px; flex-wrap: wrap;">
                    <span class="chip"><i class="fas fa-check-circle"></i> AICTE approved</span>
                    <span class="chip"><i class="fas fa-trophy"></i> AP ECET 1st Rank (2022,2024)</span>
                </div>
            </div>
            <div class="rank-achievement">
                <i class="fas fa-crown"></i> 
                <span>#1 state rank <br> <span style="font-size:0.8rem;">ECET 2022 & 2024</span></span>
            </div>
        </div>

        <!-- admissions + quick facts chips -->
        <div class="key-chips">
            <span class="chip"><i class="fas fa-door-open"></i> Admissions: POLYCET rank</span>
            <span class="chip"><i class="fas fa-flask"></i> 42 labs</span>
            <span class="chip"><i class="fas fa-book-open"></i> Central library</span>
            <span class="chip"><i class="fas fa-dumbbell"></i> Gym & sports</span>
            <span class="chip"><i class="fas fa-person-booth"></i> Hostels (400+ seats)</span>
            <span class="chip"><i class="fas fa-tree"></i> 10 acre campus</span>
        </div>

        <!-- courses offered (vibrant grid) -->
        <div class="section-title">
            <i class="fas fa-graduation-cap" style="font-size: 2rem; color:#2266a8;"></i> 
            <span>📘 diploma specializations</span>
        </div>
        <div class="courses-grid">
            <div class="course-item"><i class="fas fa-draw-polygon"></i> Civil Eng.</div>
            <div class="course-item"><i class="fas fa-microchip"></i> Artificial Intelligence And Machine Learning</div>
            <div class="course-item"><i class="fas fa-laptop-code"></i> Computer Eng.</div>
            <div class="course-item"><i class="fas fa-satellite-dish"></i> ECE</div>
            <div class="course-item"><i class="fas fa-bolt"></i> Electrical & Electronic</div>
            <div class="course-item"><i class="fas fa-cogs"></i> Mechanical Eng.</div>
            <div class="course-item"><i class="fas fa-database"></i> Internet Of Things</div>
        </div>

        <!-- infrastructure stats (cards) -->
        <div class="stats-wrapper">
            <div class="stat-tile"><i class="fas fa-building"></i><div class="stat-number">10</div><div class="stat-label">acre campus</div></div>
            <div class="stat-tile"><i class="fas fa-microscope"></i><div class="stat-number">42</div><div class="stat-label">labs</div></div>
            <div class="stat-tile"><i class="fas fa-bed"></i><div class="stat-number">400+</div><div class="stat-label">hostel capacity</div></div>
            <div class="stat-tile"><i class="fas fa-chalkboard-user"></i><div class="stat-number">50+</div><div class="stat-label">experienced faculty</div></div>
        </div>

        <!-- placements & recruiters ( attractive banner ) -->
        <div class="placement-banner">
            <div>
                <h3 style="font-size: 2rem; font-weight: 600; margin-bottom: 0.5rem; display: flex; gap:10px;"><i class="fas fa-briefcase"></i> placements</h3>
                <div class="company-strip">
                    <span>Moschip Technologies</span> <span>Intel</span> <span>Medha</span> <span>Thought Works</span> <span>+ many more</span>
                </div>
                <p style="margin-top: 1rem; opacity: 0.9;"><i class="fas fa-calendar"></i> 2017-18 batch • annual drives</p>
            </div>
            <div class="highest-package">
                <i class="fas fa-sack-dollar"></i> INR 9 LPA (highest)
            </div>
        </div>

        <!-- activities & sports wins (dynamic) -->
        <div style="display: flex; justify-content: space-between; flex-wrap: wrap; align-items: center; margin: 1rem 0 0;">
            <div class="section-title" style="margin:1rem 0; border-left-color: #e67700;">
                <i class="fas fa-futbol"></i> sports & activities
            </div>
            <div class="activities-sports">
                <span class="sports-chip"><i class="fas fa-volleyball-ball" style="color:#b14512;"></i> Volleyball (state winners)</span>
                <span class="sports-chip"><i class="fas fa-person-running" style="color:#0e6b4e;"></i> Kho-Kho champions</span>
                <span class="sports-chip"><i class="fas fa-microphone-alt"></i> guest lectures</span>
                <span class="sports-chip"><i class="fas fa-wrench"></i> workshops</span>
            </div>
        </div>

        <!-- contact & address area (with email, website, map pin) -->
        <div class="contact-block">
            <div class="address">
                <i class="fas fa-map-pin" style="color:#1d4d73;"></i>
                <div>
                    <strong>Seshadri Rao Knowledge Village</strong><br>
                    Gudlavalleru, Krishna Dist., Andhra Pradesh – 521356
                </div>
            </div>
            <div class="contact-icons">
                <a href="mailto:poly.gvl@gmail.com"><i class="fas fa-envelope"></i> poly.gvl@gmail.com</a>
                <a href="#" target="_blank"><i class="fas fa-globe"></i> aanm&vvrsrpolytechnic.edu</a>
            </div>
        </div>

        <!-- extra detail: experienced faculty & industry collab (as footer) -->
        <hr>
        <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 2rem; margin: 1.5rem 0 0.5rem;">
            <span><i class="fas fa-star" style="color:#174e7c;"></i> Experienced faculty</span>
            <span><i class="fas fa-handshake" style="color:#174e7c;"></i> Active industry collaborations</span>
            <span><i class="fas fa-medal" style="color:#174e7c;"></i> Top ranks in AP ECET</span>  
        </div>
        <div class="footer-note">
            ⚙️ A.A.N.M & V.V.R.S.R Polytechnic — shaping engineers since 1981 | approved by AICTE
        </div>
    </div>

    <!-- optional tiny script for robust back navigation (fallback) -->
    <script>
        (function() {
            // make sure the back button works even if history is empty (just links to #)
            const backBtn = document.querySelector('.back-btn');
            if (backBtn) {
                backBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (document.referrer !== "" || window.history.length > 1) {
                        history.back();
                    } else {
                        // fallback: link to homepage or institute main (could replace "#" with actual site)
                        window.location.href = "#"; // you could replace with your home URL
                    }
                });
            }
        })();
    </script>
</body>
</html>