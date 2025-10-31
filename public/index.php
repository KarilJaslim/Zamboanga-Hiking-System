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
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #e8f5e9 0%, #f1f8e9 100%);
    color: #2d3748;
    line-height: 1.6;
    min-height: 100vh;
}

/* Navbar */
nav {
    background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 100%);
    padding: 1.2rem 2rem;
    box-shadow: 0 4px 20px rgba(27, 94, 32, 0.3);
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
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    transition: all 0.3s ease;
    font-weight: 500;
    position: relative;
    overflow: hidden;
}

nav a::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.2);
    transition: left 0.3s ease;
}

nav a:hover::before {
    left: 0;
}

nav a:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-2px);
}

nav span {
    color: #c8e6c9;
    font-weight: 600;
    margin-left: auto;
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
    opacity: 0.7;
}

@keyframes float-down {
    0% {
        transform: translateY(-100px) rotate(0deg);
        opacity: 0;
    }
    10% {
        opacity: 0.7;
    }
    90% {
        opacity: 0.7;
    }
    100% {
        transform: translateY(100vh) rotate(360deg);
        opacity: 0;
    }
}

/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, rgba(27, 94, 32, 0.95) 0%, rgba(46, 125, 50, 0.9) 100%),
                url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,144C960,149,1056,139,1152,122.7C1248,107,1344,85,1392,74.7L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
    background-size: cover;
    background-position: center;
    padding: 6rem 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    margin-bottom: 3rem;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 30% 50%, rgba(139, 195, 74, 0.3), transparent 50%);
    animation: pulse 8s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 0.5; }
    50% { opacity: 1; }
}

.hero-content {
    position: relative;
    z-index: 2;
    max-width: 900px;
    margin: 0 auto;
}

.hero-badge {
    display: inline-block;
    background: rgba(255, 255, 255, 0.2);
    color: #fff;
    padding: 0.5rem 1.5rem;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
    letter-spacing: 1px;
    margin-bottom: 1.5rem;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.hero-section h1 {
    font-size: 4rem;
    color: #fff;
    margin-bottom: 1rem;
    font-weight: 700;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.highlight {
    background: linear-gradient(120deg, #81c784, #aed581);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-section p {
    font-size: 1.3rem;
    color: #e8f5e9;
    margin-bottom: 2.5rem;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
}

/* Search Container */
.search-container {
    position: relative;
    max-width: 600px;
    margin: 0 auto 3rem;
}

.search-container input {
    width: 100%;
    padding: 1.2rem 3.5rem 1.2rem 1.5rem;
    border: none;
    border-radius: 50px;
    font-size: 1rem;
    background: rgba(255, 255, 255, 0.95);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}

.search-container input:focus {
    outline: none;
    box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
    transform: translateY(-2px);
}

.search-icon {
    position: absolute;
    right: 1.5rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.3rem;
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
    padding: 1.5rem;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 15px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    min-width: 150px;
    transition: transform 0.3s ease;
}

.stat-item:hover {
    transform: translateY(-5px);
}

.stat-number {
    display: block;
    font-size: 2.5rem;
    font-weight: 700;
    color: #fff;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.stat-label {
    display: block;
    font-size: 0.9rem;
    color: #c8e6c9;
    margin-top: 0.5rem;
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
    margin-bottom: 3rem;
}

.section-header h2 {
    font-size: 3rem;
    color: #1b5e20;
    margin-bottom: 0.5rem;
    font-weight: 700;
}

.subtitle {
    color: #558b2f;
    font-size: 1.2rem;
}

/* Filter Tabs */
.filter-tabs {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-bottom: 3rem;
    flex-wrap: wrap;
}

.filter-btn {
    background: #fff;
    border: 2px solid #81c784;
    padding: 0.8rem 2rem;
    border-radius: 50px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 1rem;
    font-weight: 600;
    color: #2e7d32;
}

.filter-btn:hover {
    background: #e8f5e9;
    transform: translateY(-3px);
    box-shadow: 0 5px 20px rgba(46, 125, 50, 0.2);
}

.filter-btn.active {
    background: linear-gradient(135deg, #2e7d32, #388e3c);
    color: #fff;
    border-color: #2e7d32;
    box-shadow: 0 5px 20px rgba(46, 125, 50, 0.3);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 5rem 2rem;
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
}

.empty-state-icon {
    font-size: 5rem;
    margin-bottom: 1.5rem;
}

.empty-state h3 {
    font-size: 2rem;
    color: #2e7d32;
    margin-bottom: 1rem;
}

.empty-state p {
    color: #666;
    font-size: 1.1rem;
}

/* Trails Grid */
.trails-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 2rem;
    list-style: none;
}

/* Trail Card */
.trail-card {
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: all 0.4s ease;
    position: relative;
}

.trail-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 50px rgba(46, 125, 50, 0.3);
}

.trail-image {
    height: 220px;
    background: linear-gradient(135deg, #66bb6a 0%, #81c784 50%, #a5d6a7 100%);
    position: relative;
    overflow: hidden;
}

.trail-image::before {
    content: '🏔️';
    position: absolute;
    font-size: 8rem;
    bottom: -20px;
    right: -20px;
    opacity: 0.3;
}

.popular-badge {
    position: absolute;
    top: 1rem;
    left: 1rem;
    background: linear-gradient(135deg, #ff6b6b, #ff8e53);
    color: #fff;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
    box-shadow: 0 4px 15px rgba(255, 107, 107, 0.4);
}

.difficulty-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
    backdrop-filter: blur(10px);
    color: #fff;
}

.difficulty-easy {
    background: rgba(139, 195, 74, 0.9);
}

.difficulty-moderate {
    background: rgba(255, 152, 0, 0.9);
}

.difficulty-hard {
    background: rgba(244, 67, 54, 0.9);
}

/* Trail Content */
.trail-content {
    padding: 1.5rem;
}

.trail-content h3 {
    font-size: 1.5rem;
    margin-bottom: 1rem;
    color: #1b5e20;
}

.trail-content h3 a {
    color: #1b5e20;
    text-decoration: none;
    transition: color 0.3s ease;
}

.trail-content h3 a:hover {
    color: #2e7d32;
}

.trail-meta {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.meta-item {
    color: #558b2f;
    font-weight: 600;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.meta-item::before {
    content: '📍';
}

.meta-item:nth-child(2)::before {
    content: '⏱️';
}

.meta-item:nth-child(3)::before {
    content: '⛰️';
}

.trail-content p {
    color: #666;
    margin-bottom: 1rem;
    line-height: 1.6;
}

/* Trail Features */
.trail-features {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-bottom: 1.5rem;
}

.feature-tag {
    background: linear-gradient(135deg, #e8f5e9, #f1f8e9);
    color: #2e7d32;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    border: 1px solid #c8e6c9;
}

/* Trail Footer */
.trail-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid #e0e0e0;
}

.rating {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: #2e7d32;
}

.rating-stars {
    color: #ffc107;
}

.view-btn {
    background: linear-gradient(135deg, #2e7d32, #388e3c);
    color: #fff;
    padding: 0.7rem 1.5rem;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(46, 125, 50, 0.3);
}

.view-btn:hover {
    background: linear-gradient(135deg, #1b5e20, #2e7d32);
    transform: translateX(5px);
    box-shadow: 0 6px 20px rgba(46, 125, 50, 0.4);
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-section h1 {
        font-size: 2.5rem;
    }
    
    .hero-section p {
        font-size: 1.1rem;
    }
    
    .section-header h2 {
        font-size: 2rem;
    }
    
    .trails-grid {
        grid-template-columns: 1fr;
    }
    
    nav {
        flex-direction: column;
        gap: 1rem;
    }
    
    .stats-container {
        gap: 1rem;
    }
    
    .stat-item {
        min-width: 120px;
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
