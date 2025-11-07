<?php
session_start();
include '../includes/db.php'; // adjust path to your DB connection

$trails = []; // define the variable so it's never null

try {
    $stmt = $pdo->query("SELECT * FROM trails"); // fetch from your trails table
    if ($stmt) {
        $trails = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    // optional: log or display error
    $trails = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Zamboanga Hiking System</title>
    <style>
/* Reset & Base Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', Roboto, sans-serif;
    background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 50%, #fff9c4 100%);
    color: #1f2937;
    line-height: 1.6;
    min-height: 100vh;
    position: relative;
}

body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 30%, rgba(129, 199, 132, 0.08) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(174, 213, 129, 0.08) 0%, transparent 50%);
    pointer-events: none;
    z-index: 0;
}

/* Navbar */
nav {
    background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 50%, #388e3c 100%);
    padding: 1.2rem 2.5rem;
    box-shadow: 0 4px 30px rgba(27, 94, 32, 0.25), 0 2px 8px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    gap: 2rem;
    position: sticky;
    top: 0;
    z-index: 1000;
    backdrop-filter: blur(10px);
}

nav a {
    color: #fff;
    text-decoration: none;
    padding: 0.7rem 1.4rem;
    border-radius: 12px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    font-weight: 600;
    font-size: 0.95rem;
    position: relative;
    overflow: hidden;
    letter-spacing: 0.3px;
}

nav a::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
}

nav a:hover::before {
    left: 100%;
}

nav a:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

nav a:active {
    transform: translateY(0);
}

nav span {
    color: #c8e6c9;
    font-weight: 700;
    margin-left: auto;
    font-size: 0.95rem;
    letter-spacing: 0.5px;
}

/* Floating Leaves Animation */
.floating-leaves {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
    overflow: hidden;
}

.leaf {
    position: absolute;
    top: -50px;
    font-size: 2rem;
    animation: float-down linear infinite;
    opacity: 0.6;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
}

@keyframes float-down {
    0% {
        transform: translateY(-100px) rotate(0deg) translateX(0);
        opacity: 0;
    }
    10% {
        opacity: 0.6;
    }
    50% {
        transform: translateY(50vh) rotate(180deg) translateX(50px);
    }
    90% {
        opacity: 0.6;
    }
    100% {
        transform: translateY(100vh) rotate(360deg) translateX(0);
        opacity: 0;
    }
}

/* Hero Section */
.hero-section {
    background: 
        linear-gradient(135deg, rgba(27, 94, 32, 0.96) 0%, rgba(46, 125, 50, 0.92) 100%),
        url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,144C960,149,1056,139,1152,122.7C1248,107,1344,85,1392,74.7L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
    background-size: cover;
    background-position: center;
    padding: 6.5rem 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    margin-bottom: 3.5rem;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 30% 40%, rgba(139, 195, 74, 0.25), transparent 60%),
        radial-gradient(circle at 70% 60%, rgba(174, 213, 129, 0.2), transparent 60%);
    animation: pulse 10s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 0.6; }
    50% { opacity: 1; }
}

.hero-content {
    position: relative;
    z-index: 2;
    max-width: 950px;
    margin: 0 auto;
}

.hero-badge {
    display: inline-block;
    background: rgba(255, 255, 255, 0.25);
    color: #fff;
    padding: 0.6rem 1.8rem;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 700;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    margin-bottom: 1.8rem;
    backdrop-filter: blur(12px);
    border: 2px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    transition: all 0.3s ease;
}

.hero-badge:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

.hero-section h1 {
    font-size: 4.5rem;
    color: #fff;
    margin-bottom: 1.2rem;
    font-weight: 900;
    text-shadow: 3px 3px 8px rgba(0, 0, 0, 0.3);
    letter-spacing: -1px;
    line-height: 1.1;
}

.highlight {
    background: linear-gradient(120deg, #81c784, #aed581, #dce775);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
}

.hero-section p {
    font-size: 1.35rem;
    color: #e8f5e9;
    margin-bottom: 3rem;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
    font-weight: 500;
    line-height: 1.7;
}

/* Search Container */
.search-container {
    position: relative;
    max-width: 650px;
    margin: 0 auto 3.5rem;
}

.search-container input {
    width: 100%;
    padding: 1.3rem 4rem 1.3rem 1.8rem;
    border: none;
    border-radius: 50px;
    font-size: 1.05rem;
    background: rgba(255, 255, 255, 0.98);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2), 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    font-weight: 500;
}

.search-container input:focus {
    outline: none;
    box-shadow: 0 15px 60px rgba(0, 0, 0, 0.25), 0 8px 20px rgba(46, 125, 50, 0.2);
    transform: translateY(-3px);
}

.search-container input::placeholder {
    color: #9ca3af;
}

.search-icon {
    position: absolute;
    right: 1.8rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.4rem;
    opacity: 0.6;
    pointer-events: none;
}

/* Stats Container */
.stats-container {
    display: flex;
    justify-content: center;
    gap: 3rem;
    flex-wrap: wrap;
}

.stat-item {
    text-align: center;
    padding: 1.8rem 2rem;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 20px;
    backdrop-filter: blur(12px);
    border: 2px solid rgba(255, 255, 255, 0.3);
    min-width: 160px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.stat-item:hover {
    transform: translateY(-8px) scale(1.05);
    background: rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
}

.stat-number {
    display: block;
    font-size: 3rem;
    font-weight: 900;
    color: #fff;
    text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.3);
    letter-spacing: -1px;
}

.stat-label {
    display: block;
    font-size: 0.95rem;
    color: #c8e6c9;
    margin-top: 0.5rem;
    font-weight: 600;
    letter-spacing: 0.5px;
}

/* Container */
.container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
    position: relative;
    z-index: 2;
}

/* Section Header */
.section-header {
    text-align: center;
    margin-bottom: 3.5rem;
}

.section-header h2 {
    font-size: 3.5rem;
    color: #1b5e20;
    margin-bottom: 0.8rem;
    font-weight: 900;
    letter-spacing: -1px;
    position: relative;
    display: inline-block;
}

.section-header h2::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 5px;
    background: linear-gradient(90deg, #2e7d32, #81c784);
    border-radius: 3px;
}

.subtitle {
    color: #558b2f;
    font-size: 1.25rem;
    font-weight: 500;
    margin-top: 1rem;
}

/* Filter Tabs */
.filter-tabs {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-bottom: 3.5rem;
    flex-wrap: wrap;
}

.filter-btn {
    background: #fff;
    border: 2px solid #81c784;
    padding: 0.9rem 2.2rem;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    font-size: 1rem;
    font-weight: 700;
    color: #2e7d32;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    letter-spacing: 0.3px;
}

.filter-btn:hover {
    background: #e8f5e9;
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(46, 125, 50, 0.2);
    border-color: #2e7d32;
}

.filter-btn:active {
    transform: translateY(-2px);
}

.filter-btn.active {
    background: linear-gradient(135deg, #2e7d32, #388e3c);
    color: #fff;
    border-color: #1b5e20;
    box-shadow: 0 6px 25px rgba(46, 125, 50, 0.35);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 6rem 2rem;
    background: linear-gradient(135deg, #fff, #f8f9fa);
    border-radius: 28px;
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.08);
    border: 2px solid rgba(46, 125, 50, 0.1);
}

.empty-state-icon {
    font-size: 6rem;
    margin-bottom: 1.8rem;
    filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-15px); }
}

.empty-state h3 {
    font-size: 2.2rem;
    color: #2e7d32;
    margin-bottom: 1rem;
    font-weight: 800;
}

.empty-state p {
    color: #6b7280;
    font-size: 1.15rem;
    font-weight: 500;
}

/* Trails Grid */
.trails-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 2.5rem;
    list-style: none;
}

/* Trail Card */
.trail-card {
    background: #fff;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.trail-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(46, 125, 50, 0.05), transparent);
    opacity: 0;
    transition: opacity 0.4s;
    pointer-events: none;
    z-index: 1;
}

.trail-card:hover::before {
    opacity: 1;
}

.trail-card:hover {
    transform: translateY(-12px);
    box-shadow: 0 25px 60px rgba(46, 125, 50, 0.25);
}

.trail-image {
    height: 240px;
    background: linear-gradient(135deg, #66bb6a 0%, #81c784 50%, #a5d6a7 100%);
    position: relative;
    overflow: hidden;
}

.trail-image::before {
    content: '🏔️';
    position: absolute;
    font-size: 10rem;
    bottom: -30px;
    right: -30px;
    opacity: 0.25;
    transition: all 0.4s;
}

.trail-card:hover .trail-image::before {
    transform: scale(1.1) rotate(5deg);
    opacity: 0.35;
}

.popular-badge {
    position: absolute;
    top: 1rem;
    left: 1rem;
    background: linear-gradient(135deg, #ff6b6b, #ff8e53);
    color: #fff;
    padding: 0.6rem 1.2rem;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 700;
    box-shadow: 0 4px 20px rgba(255, 107, 107, 0.4);
    letter-spacing: 0.5px;
    backdrop-filter: blur(5px);
    z-index: 2;
}

.difficulty-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    padding: 0.6rem 1.2rem;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 700;
    backdrop-filter: blur(10px);
    color: #fff;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    letter-spacing: 0.5px;
    z-index: 2;
}

.difficulty-easy {
    background: rgba(139, 195, 74, 0.95);
}

.difficulty-moderate {
    background: rgba(255, 152, 0, 0.95);
}

.difficulty-hard {
    background: rgba(244, 67, 54, 0.95);
}

/* Trail Content */
.trail-content {
    padding: 1.8rem;
}

.trail-content h3 {
    font-size: 1.6rem;
    margin-bottom: 1.2rem;
    color: #1b5e20;
    font-weight: 800;
    letter-spacing: -0.3px;
}

.trail-content h3 a {
    color: #1b5e20;
    text-decoration: none;
    transition: all 0.3s ease;
    position: relative;
}

.trail-content h3 a::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 0;
    height: 3px;
    background: linear-gradient(90deg, #2e7d32, #81c784);
    transition: width 0.3s ease;
}

.trail-content h3 a:hover::after {
    width: 100%;
}

.trail-content h3 a:hover {
    color: #2e7d32;
}

.trail-meta {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 1.2rem;
    flex-wrap: wrap;
}

.meta-item {
    color: #558b2f;
    font-weight: 700;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}

.meta-item::before {
    content: '📍';
    font-size: 1.1rem;
}

.meta-item:nth-child(2)::before {
    content: '⏱️';
}

.meta-item:nth-child(3)::before {
    content: '⛰️';
}

.trail-content p {
    color: #4b5563;
    margin-bottom: 1.2rem;
    line-height: 1.7;
    font-weight: 500;
}

/* Trail Features */
.trail-features {
    display: flex;
    gap: 0.6rem;
    flex-wrap: wrap;
    margin-bottom: 1.5rem;
}

.feature-tag {
    background: linear-gradient(135deg, #e8f5e9, #f1f8e9);
    color: #2e7d32;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 700;
    border: 1px solid #c8e6c9;
    transition: all 0.3s ease;
}

.feature-tag:hover {
    background: linear-gradient(135deg, #c8e6c9, #dcedc8);
    transform: translateY(-2px);
}

/* Trail Footer */
.trail-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1.2rem;
    border-top: 2px solid #f3f4f6;
}

.rating {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    font-weight: 700;
    color: #2e7d32;
    font-size: 1.05rem;
}

.rating-stars {
    color: #fbbf24;
    font-size: 1.1rem;
    filter: drop-shadow(0 2px 4px rgba(251, 191, 36, 0.3));
}

.view-btn {
    background: linear-gradient(135deg, #2e7d32, #388e3c);
    color: #fff;
    padding: 0.8rem 1.8rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 700;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 20px rgba(46, 125, 50, 0.3);
    letter-spacing: 0.3px;
    position: relative;
    overflow: hidden;
}

.view-btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

.view-btn:hover::before {
    width: 300px;
    height: 300px;
}

.view-btn:hover {
    background: linear-gradient(135deg, #1b5e20, #2e7d32);
    transform: translateX(5px);
    box-shadow: 0 8px 30px rgba(46, 125, 50, 0.45);
}

.view-btn:active {
    transform: translateX(3px);
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-section h1 {
        font-size: 2.8rem;
    }
    
    .hero-section p {
        font-size: 1.15rem;
    }
    
    .section-header h2 {
        font-size: 2.5rem;
    }
    
    .trails-grid {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    nav {
        flex-direction: column;
        gap: 1rem;
        padding: 1rem 1.5rem;
    }
    
    .stats-container {
        gap: 1.5rem;
    }
    
    .stat-item {
        min-width: 130px;
        padding: 1.5rem;
    }
    
    .stat-number {
        font-size: 2.5rem;
    }
    
    .search-container {
        max-width: 100%;
    }
    
    .filter-tabs {
        gap: 0.8rem;
    }
    
    .filter-btn {
        padding: 0.8rem 1.6rem;
        font-size: 0.9rem;
    }
}
</style>
</head>
<body>

    <!-- NAVBAR -->
    <nav>
        <a href="index.php">Home</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <span>Welcome, <?= htmlspecialchars($_SESSION['user_username']) ?></span>
            <a href="logout_user.php">Logout</a>
        <?php else: ?>
            <a href="login_user.php">User Login</a>
            <a href="register_user.php">Register</a>
        <?php endif; ?>
    </nav>

    <!-- FLOATING LEAVES -->
    <div class="floating-leaves">
        <div class="leaf" style="left: 10%; animation-duration: 15s;">🍃</div>
        <div class="leaf" style="left: 30%; animation-duration: 18s; animation-delay: 3s;">🌿</div>
        <div class="leaf" style="left: 50%; animation-duration: 20s; animation-delay: 6s;">🍃</div>
        <div class="leaf" style="left: 70%; animation-duration: 17s; animation-delay: 2s;">🌿</div>
        <div class="leaf" style="left: 85%; animation-duration: 19s; animation-delay: 5s;">🍃</div>
    </div>

    <!-- HERO SECTION -->
    <div class="hero-section">
        <div class="hero-content">
            <div class="hero-badge">🌲 WILDERNESS AWAITS</div>
            <h1>Explore Nature's <span class="highlight">Wonders</span></h1>
            <p>Discover breathtaking trails, embrace adventure, and reconnect with the great outdoors</p>
            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Search trails by name, location, or difficulty...">
                <span class="search-icon">🔍</span>
            </div>
            <div class="stats-container">
                <div class="stat-item">
                    <span class="stat-number"><?= count($trails) ?></span>
                    <span class="stat-label">Amazing Trails</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">500+</span>
                    <span class="stat-label">Km Explored</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">1K+</span>
                    <span class="stat-label">Happy Hikers</span>
                </div>
            </div>
        </div>
    </div>

    <!-- TRAILS SECTION -->
    <div class="container">
        <div class="section-header">
            <h2>Featured Trails</h2>
            <p class="subtitle">Handpicked adventures for every explorer</p>
        </div>

        <div class="filter-tabs">
            <button class="filter-btn active" data-filter="all"><span>🌍 All Trails</span></button>
            <button class="filter-btn" data-filter="easy"><span>🥾 Easy</span></button>
            <button class="filter-btn" data-filter="moderate"><span>⛰️ Moderate</span></button>
            <button class="filter-btn" data-filter="hard"><span>🏔️ Hard</span></button>
        </div>

        <?php if (empty($trails)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">🏔️</div>
                <h3>No Trails Available Yet</h3>
                <p>Check back soon for exciting new trails to explore!</p>
            </div>
        <?php else: ?>
            <ul class="trails-grid" id="trailsGrid">
                <?php 
                $features = [
                    ['🌊 Waterfall', '🌺 Wildflowers', '🦅 Wildlife'],
                    ['🌲 Forest Path', '🏞️ Scenic Views', '📸 Photo Spots'],
                    ['⛺ Camping', '🎣 Fishing', '🚴 Biking'],
                    ['🌅 Sunrise Views', '🌙 Night Hikes', '☁️ Cloud Forest']
                ];
                $index = 0;
                foreach ($trails as $trail): 
                    $isPopular = $index % 3 === 0;
                ?>
                    <li class="trail-card" data-difficulty="<?= strtolower($trail['difficulty'] ?? 'moderate') ?>">
                        <div class="trail-image">
                            <?php if ($isPopular): ?>
                                <span class="popular-badge">🔥 Popular</span>
                            <?php endif; ?>
                            <span class="difficulty-badge difficulty-<?= strtolower($trail['difficulty'] ?? 'moderate') ?>">
                                <?= ucfirst($trail['difficulty'] ?? 'Moderate') ?>
                            </span>
                        </div>
                        <div class="trail-content">
                            <h3>
                                <a href="trail.php?id=<?= $trail['id'] ?>"><?= htmlspecialchars($trail['name']) ?></a>
                            </h3>
                            <div class="trail-meta">
                                <span class="meta-item"><?= $trail['distance'] ?? '5.2' ?> km</span>
                                <span class="meta-item"><?= $trail['duration'] ?? '2-3' ?> hrs</span>
                                <span class="meta-item"><?= $trail['elevation'] ?? '450' ?> m</span>
                            </div>
                            <p><?= htmlspecialchars($trail['description'] ?? 'A beautiful trail awaiting your exploration.') ?></p>
                            <div class="trail-features">
                                <?php foreach (array_slice($features[$index % 4], 0, 3) as $feature): ?>
                                    <span class="feature-tag"><?= $feature ?></span>
                                <?php endforeach; ?>
                            </div>
                            <div class="trail-footer">
                                <div class="rating">
                                    <span class="rating-stars">⭐⭐⭐⭐⭐</span>
                                    <?= $trail['rating'] ?? '4.8' ?> <span style="color:#9ca3af">(<?= $trail['reviews'] ?? '234' ?>)</span>
                                </div>
                                <a href="trail.php?id=<?= $trail['id'] ?>" class="view-btn">Explore Trail →</a>
                            </div>
                        </div>
                    </li>
                <?php 
                    $index++;
                endforeach; 
                ?>
            </ul>
        <?php endif; ?>
    </div>

    <script>
        const searchInput = document.getElementById('searchInput');
        const trailCards = document.querySelectorAll('.trail-card');
        const filterBtns = document.querySelectorAll('.filter-btn');

        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                const term = e.target.value.toLowerCase();
                trailCards.forEach(card => {
                    card.style.display = card.textContent.toLowerCase().includes(term) ? 'block' : 'none';
                });
            });
        }

        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const filter = btn.dataset.filter;

                trailCards.forEach(card => {
                    if (filter === 'all' || card.dataset.difficulty === filter) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>

<?php require_once "../includes/footer.php"; ?>
</body>
</html>
