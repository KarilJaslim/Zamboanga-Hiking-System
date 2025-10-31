<?php 
require_once "../includes/db.php"; 
require_once "../includes/header.php"; 

// Check if ID parameter exists
if(!isset($_GET['id']) || empty($_GET['id'])){
    echo "<p>Trail not found</p>"; 
    include "../includes/footer.php"; 
    exit();
}

// Get and validate trail ID
$id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
if($id === false) {
    echo "<p>Invalid trail ID</p>"; 
    include "../includes/footer.php"; 
    exit();
}

// Fetch trail from database
$stmt = $pdo->prepare("SELECT * FROM trails WHERE id = ?"); 
$stmt->execute([$id]); 
$trail = $stmt->fetch(PDO::FETCH_ASSOC); 

// Check if trail exists
if(!$trail){ 
    echo "<p>Trail not found</p>"; 
    include "../includes/footer.php"; 
    exit();
}

// Set default values for optional fields
$trail['distance'] = $trail['distance'] ?? '5.2';
$trail['duration'] = $trail['duration'] ?? '2-3';
$trail['elevation'] = $trail['elevation'] ?? '450';
$trail['difficulty'] = $trail['difficulty'] ?? 'moderate';
$trail['location'] = $trail['location'] ?? 'Nature Reserve';
$trail['rating'] = $trail['rating'] ?? '4.8';
$trail['reviews'] = $trail['reviews'] ?? '287';

// Function to get difficulty class
function getDifficultyClass($difficulty) {
    $diff = strtolower($difficulty);
    if($diff === 'easy') return 'easy';
    if($diff === 'hard' || $diff === 'difficult') return 'hard';
    return 'moderate';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($trail['name']) ?> - Trail Details</title>
</head>
<body>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #0f2027 0%, #203a43 50%, #2c5364 100%);
        min-height: 100vh;
        position: relative;
        overflow-x: hidden;
    }

    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            radial-gradient(circle at 10% 20%, rgba(34, 197, 94, 0.15) 0%, transparent 40%),
            radial-gradient(circle at 90% 80%, rgba(16, 185, 129, 0.15) 0%, transparent 40%);
        pointer-events: none;
        z-index: 0;
    }

    .breadcrumb {
        max-width: 1200px;
        margin: 0 auto;
        padding: 30px 20px 20px;
        position: relative;
        z-index: 2;
    }

    .breadcrumb-nav {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #d1fae5;
        font-size: 0.95rem;
        flex-wrap: wrap;
    }

    .breadcrumb-nav a {
        color: #6ee7b7;
        text-decoration: none;
        transition: color 0.3s ease;
        font-weight: 600;
    }

    .breadcrumb-nav a:hover {
        color: #10b981;
    }

    .breadcrumb-nav span {
        opacity: 0.6;
    }

    .trail-hero {
        position: relative;
        height: 500px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        overflow: hidden;
        margin-bottom: -100px;
    }

    .trail-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            radial-gradient(circle at 30% 40%, rgba(255,255,255,0.15) 0%, transparent 50%),
            radial-gradient(circle at 70% 60%, rgba(255,255,255,0.1) 0%, transparent 50%);
    }

    .trail-hero-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.5;
        position: relative;
        z-index: 2;
    }

    .trail-hero-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 60px 20px 140px;
        background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 100%);
        z-index: 3;
    }

    .trail-hero-content {
        max-width: 1200px;
        margin: 0 auto;
        color: white;
    }

    .trail-hero-badges {
        display: flex;
        gap: 15px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .hero-badge {
        padding: 8px 20px;
        border-radius: 25px;
        font-size: 0.9rem;
        font-weight: 700;
        backdrop-filter: blur(10px);
        border: 2px solid rgba(255,255,255,0.3);
    }

    .badge-difficulty {
        background: rgba(251, 191, 36, 0.95);
    }

    .badge-difficulty.easy {
        background: rgba(34, 197, 94, 0.95);
    }

    .badge-difficulty.hard {
        background: rgba(239, 68, 68, 0.95);
    }

    .badge-featured {
        background: rgba(249, 115, 22, 0.95);
    }

    .trail-hero-content h1 {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 15px;
        text-shadow: 3px 3px 20px rgba(0,0,0,0.5);
        line-height: 1.2;
    }

    .trail-location {
        font-size: 1.3rem;
        opacity: 0.95;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 25px;
    }

    .trail-quick-stats {
        display: flex;
        gap: 35px;
        flex-wrap: wrap;
    }

    .quick-stat {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .quick-stat-icon {
        font-size: 1.8rem;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px 80px;
        position: relative;
        z-index: 2;
    }

    .trail-main-grid {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 40px;
        margin-top: 120px;
    }

    .trail-content {
        background: linear-gradient(135deg, rgba(255,255,255,0.98), rgba(255,255,255,0.95));
        border-radius: 25px;
        padding: 40px;
        box-shadow: 0 15px 50px rgba(0,0,0,0.15);
        border: 1px solid rgba(255,255,255,0.3);
    }

    .section-title {
        font-size: 2rem;
        color: #1f2937;
        margin-bottom: 25px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .section-title::before {
        content: '';
        width: 5px;
        height: 35px;
        background: linear-gradient(135deg, #10b981, #059669);
        border-radius: 3px;
    }

    .trail-description {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #4b5563;
        margin-bottom: 35px;
    }

    .trail-features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }

    .feature-card {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        padding: 20px;
        border-radius: 18px;
        text-align: center;
        border: 2px solid #6ee7b7;
        transition: all 0.3s ease;
    }

    .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
    }

    .feature-icon {
        font-size: 2.5rem;
        margin-bottom: 10px;
        display: block;
    }

    .feature-label {
        font-weight: 700;
        color: #065f46;
        font-size: 0.95rem;
    }

    .trail-highlights {
        background: rgba(16, 185, 129, 0.08);
        padding: 30px;
        border-radius: 18px;
        border-left: 5px solid #10b981;
        margin-bottom: 40px;
    }

    .highlights-title {
        font-size: 1.4rem;
        color: #065f46;
        font-weight: 800;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .highlights-list {
        list-style: none;
        display: grid;
        gap: 15px;
    }

    .highlights-list li {
        display: flex;
        align-items: start;
        gap: 12px;
        color: #1f2937;
        font-size: 1.05rem;
        line-height: 1.6;
    }

    .highlights-list li::before {
        content: '✓';
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-weight: 700;
        font-size: 0.9rem;
    }

    .trail-gallery {
        margin-bottom: 40px;
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
    }

    .gallery-item {
        aspect-ratio: 4/3;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        transition: all 0.3s ease;
        position: relative;
    }

    .gallery-item:hover {
        transform: scale(1.05);
        box-shadow: 0 15px 40px rgba(16, 185, 129, 0.3);
    }

    .gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .trail-sidebar {
        display: flex;
        flex-direction: column;
        gap: 30px;
    }

    .sidebar-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.98), rgba(255,255,255,0.95));
        border-radius: 25px;
        padding: 35px;
        box-shadow: 0 15px 50px rgba(0,0,0,0.15);
        border: 1px solid rgba(255,255,255,0.3);
    }

    .sidebar-title {
        font-size: 1.5rem;
        color: #1f2937;
        margin-bottom: 25px;
        font-weight: 800;
    }

    .stats-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .stat-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        background: rgba(16, 185, 129, 0.08);
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .stat-item:hover {
        background: rgba(16, 185, 129, 0.15);
        transform: translateX(5px);
    }

    .stat-label {
        font-weight: 600;
        color: #4b5563;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .stat-label-icon {
        font-size: 1.3rem;
    }

    .stat-value {
        font-weight: 800;
        color: #059669;
        font-size: 1.15rem;
    }

    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 16px 28px;
        border-radius: 30px;
        font-weight: 700;
        font-size: 1.05rem;
        text-decoration: none;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .btn::before {
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

    .btn:hover::before {
        width: 300px;
        height: 300px;
    }

    .btn span {
        position: relative;
        z-index: 1;
    }

    .btn-primary {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        box-shadow: 0 5px 20px rgba(16, 185, 129, 0.4);
    }

    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 30px rgba(16, 185, 129, 0.5);
    }

    .btn-secondary {
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
        border: 2px solid #10b981;
    }

    .btn-secondary:hover {
        background: rgba(16, 185, 129, 0.2);
        transform: translateY(-3px);
    }

    .rating-card {
        text-align: center;
    }

    .rating-number {
        font-size: 3.5rem;
        font-weight: 800;
        color: #059669;
        margin-bottom: 10px;
        display: block;
    }

    .rating-stars {
        font-size: 1.8rem;
        margin-bottom: 10px;
        display: block;
        color: #fbbf24;
    }

    .rating-count {
        color: #6b7280;
        font-size: 1rem;
        font-weight: 600;
    }

    .weather-widget {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        padding: 25px;
        border-radius: 18px;
        text-align: center;
    }

    .weather-icon {
        font-size: 3rem;
        margin-bottom: 10px;
    }

    .weather-temp {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 5px;
    }

    .weather-desc {
        opacity: 0.9;
        font-size: 1.05rem;
    }

    .tips-list {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .tips-list li {
        display: flex;
        align-items: start;
        gap: 12px;
        color: #4b5563;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .tips-list li::before {
        content: '💡';
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .trail-hero-image-container {
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
    }

    .trail-hero-image-container::before {
        content: '🏔️';
        position: absolute;
        font-size: 120px;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0.2;
        z-index: 1;
    }

    @media (max-width: 968px) {
        .trail-main-grid {
            grid-template-columns: 1fr;
            margin-top: 80px;
        }

        .trail-hero-content h1 {
            font-size: 2.5rem;
        }

        .trail-hero {
            height: 450px;
            margin-bottom: -60px;
        }

        .trail-hero-overlay {
            padding: 40px 20px 100px;
        }

        .trail-quick-stats {
            gap: 20px;
        }

        .quick-stat {
            font-size: 1rem;
        }

        .sidebar-card {
            padding: 30px;
        }
    }

    @media (max-width: 640px) {
        .trail-content {
            padding: 25px;
        }

        .section-title {
            font-size: 1.6rem;
        }

        .trail-features-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .trail-hero {
            height: 380px;
        }

        .trail-hero-content h1 {
            font-size: 2rem;
        }

        .trail-location {
            font-size: 1.1rem;
        }

        .quick-stat {
            font-size: 0.9rem;
        }

        .quick-stat-icon {
            font-size: 1.5rem;
        }

        .trail-hero-badges {
            flex-direction: column;
            align-items: flex-start;
        }

        .hero-badge {
            padding: 6px 16px;
            font-size: 0.85rem;
        }

        .gallery-grid {
            grid-template-columns: 1fr;
        }

        .breadcrumb {
            padding: 20px 20px 15px;
        }

        .breadcrumb-nav {
            font-size: 0.85rem;
        }

        .sidebar-card {
            padding: 25px;
        }

        .rating-number {
            font-size: 3rem;
        }
    }
</style>

<div class="breadcrumb">
    <nav class="breadcrumb-nav">
        <a href="../index.php">🏠 Home</a>
        <span>›</span>
        <a href="../index.php">Trails</a>
        <span>›</span>
        <span><?= htmlspecialchars($trail['name']) ?></span>
    </nav>
</div>

<div class="trail-hero">
    <div class="trail-hero-image-container">
        <?php if(!empty($trail['image']) && file_exists("../assets/uploads/" . $trail['image'])): ?>
            <img src="../assets/uploads/<?= htmlspecialchars($trail['image']) ?>" alt="<?= htmlspecialchars($trail['name']) ?>" class="trail-hero-image" onerror="this.style.display='none'">
        <?php endif; ?>
    </div>
    <div class="trail-hero-overlay">
        <div class="trail-hero-content">
            <div class="trail-hero-badges">
                <span class="hero-badge badge-difficulty <?= getDifficultyClass($trail['difficulty']) ?>">
                    <?= strtoupper(htmlspecialchars($trail['difficulty'])) ?> DIFFICULTY
                </span>
                <span class="hero-badge badge-featured">⭐ FEATURED TRAIL</span>
            </div>
            <h1><?= htmlspecialchars($trail['name']) ?></h1>
            <p class="trail-location">
                <span>📍</span>
                <?= htmlspecialchars($trail['location']) ?>
            </p>
            <div class="trail-quick-stats">
                <div class="quick-stat">
                    <span class="quick-stat-icon">📏</span>
                    <span><?= htmlspecialchars($trail['distance']) ?> km</span>
                </div>
                <div class="quick-stat">
                    <span class="quick-stat-icon">⏱️</span>
                    <span><?= htmlspecialchars($trail['duration']) ?> hours</span>
                </div>
                <div class="quick-stat">
                    <span class="quick-stat-icon">⛰️</span>
                    <span><?= htmlspecialchars($trail['elevation']) ?>m gain</span>
                </div>
                <div class="quick-stat">
                    <span class="quick-stat-icon">🥾</span>
                    <span>Hiking Trail</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="trail-main-grid">
        <div>
            <div class="trail-content">
                <h2 class="section-title">Trail Overview</h2>
                <p class="trail-description">
                    <?php 
                    $description = !empty($trail['description']) 
                        ? $trail['description'] 
                        : 'Embark on an unforgettable journey through pristine wilderness. This trail offers breathtaking views, diverse ecosystems, and an immersive nature experience that will leave you refreshed and inspired. Perfect for adventurers seeking to connect with the great outdoors.';
                    echo nl2br(htmlspecialchars($description));
                    ?>
                </p>

                <div class="trail-features-grid">
                    <div class="feature-card">
                        <span class="feature-icon">🌲</span>
                        <div class="feature-label">Forest Path</div>
                    </div>
                    <div class="feature-card">
                        <span class="feature-icon">🌊</span>
                        <div class="feature-label">Water Features</div>
                    </div>
                    <div class="feature-card">
                        <span class="feature-icon">🦅</span>
                        <div class="feature-label">Wildlife Viewing</div>
                    </div>
                    <div class="feature-card">
                        <span class="feature-icon">📸</span>
                        <div class="feature-label">Photo Opportunities</div>
                    </div>
                </div>

                <div class="trail-highlights">
                    <h3 class="highlights-title">🌟 Trail Highlights</h3>
                    <ul class="highlights-list">
                        <li>Spectacular panoramic views from multiple scenic viewpoints</li>
                        <li>Well-maintained trails with clear signage throughout</li>
                        <li>Diverse flora and fauna with excellent wildlife spotting opportunities</li>
                        <li>Natural water sources and refreshing stream crossings</li>
                        <li>Perfect for photography enthusiasts and nature lovers</li>
                    </ul>
                </div>

                <?php if(!empty($trail['image']) && file_exists("../assets/uploads/" . $trail['image'])): ?>
                <div class="trail-gallery">
                    <h2 class="section-title">Photo Gallery</h2>
                    <div class="gallery-grid">
                        <div class="gallery-item">
                            <img src="../assets/uploads/<?= htmlspecialchars($trail['image']) ?>" alt="Trail view 1" onerror="this.parentElement.style.display='none'">
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="trail-sidebar">
            <div class="sidebar-card">
                <h3 class="sidebar-title">Trail Stats</h3>
                <div class="stats-list">
                    <div class="stat-item">
                        <span class="stat-label">
                            <span class="stat-label-icon">📏</span>
                            Distance
                        </span>
                        <span class="stat-value"><?= htmlspecialchars($trail['distance']) ?> km</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">
                            <span class="stat-label-icon">⏱️</span>
                            Duration
                        </span>
                        <span class="stat-value"><?= htmlspecialchars($trail['duration']) ?> hrs</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">
                            <span class="stat-label-icon">⛰️</span>
                            Elevation Gain
                        </span>
                        <span class="stat-value"><?= htmlspecialchars($trail['elevation']) ?>m</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">
                            <span class="stat-label-icon">🎯</span>
                            Difficulty
                        </span>
                        <span class="stat-value"><?= ucfirst(htmlspecialchars($trail['difficulty'])) ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">
                            <span class="stat-label-icon">🚶</span>
                            Route Type
                        </span>
                        <span class="stat-value">Loop</span>
                    </div>
                </div>
            </div>

            <div class="sidebar-card rating-card">
                <h3 class="sidebar-title">Trail Rating</h3>
                <span class="rating-number"><?= htmlspecialchars($trail['rating']) ?></span>
                <span class="rating-stars">⭐⭐⭐⭐⭐</span>
                <p class="rating-count"><?= htmlspecialchars($trail['reviews']) ?> reviews from hikers</p>
            </div>

            <div class="sidebar-card">
                <div class="weather-widget">
                    <div class="weather-icon">⛅</div>
                    <div class="weather-temp">24°C</div>
                    <div class="weather-desc">Perfect hiking weather</div>
                </div>
            </div>

            <div class="sidebar-card">
                <h3 class="sidebar-title">Essential Tips</h3>
                <ul class="tips-list">
                    <li>Bring plenty of water and snacks</li>
                    <li>Wear appropriate hiking boots</li>
                    <li>Start early to avoid crowds</li>
                    <li>Check weather conditions before hiking</li>
                    <li>Pack sunscreen and insect repellent</li>
                </ul>
            </div>

            <div class="action-buttons">
                <a href="#" class="btn btn-primary">
                    <span>🗺️ Get Directions</span>
                </a>
                <a href="../index.php" class="btn btn-secondary">
                    <span>← Back to Trails</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>

</body>
</html>