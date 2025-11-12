<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Include config and db
require_once '../../includes/config.php';
include '../../includes/db.php';

// Define BASE_URL if not already defined in config
if (!defined('BASE_URL')) {
    define('BASE_URL', SITE_URL . '/public/');
}

// Check if ID parameter exists
if(!isset($_GET['id']) || empty($_GET['id'])){
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

// Get and validate trail ID
$id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
if($id === false) {
    header('Location: ' . BASE_URL . 'index.php');
    exit();
}

// Fetch trail from database
$stmt = $pdo->prepare("SELECT * FROM trails WHERE id = ?"); 
$stmt->execute([$id]); 
$trail = $stmt->fetch(PDO::FETCH_ASSOC); 

// Check if trail exists
if(!$trail){ 
    header('Location: ' . BASE_URL . 'index.php');
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

// Get trail image - check database first, then fallback
$trailImage = '';
if (!empty($trail['image'])) {
    $imagePath = "../../public/assets/uploads/" . $trail['image'];
    if (file_exists($imagePath)) {
        $trailImage = BASE_URL . "assets/uploads/" . htmlspecialchars($trail['image']);
    }
}
// Fallback to default Unsplash image if no custom image
if (empty($trailImage)) {
    $trailImage = 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1920&q=80';
}

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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<style>
/* Reset & Base Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 50%, #bbf7d0 100%);
    color: #0f172a;
    line-height: 1.6;
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
        radial-gradient(circle at 20% 30%, rgba(34, 197, 94, 0.08) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(74, 222, 128, 0.08) 0%, transparent 50%),
        url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><circle cx="30" cy="30" r="1" fill="%2316a34a" opacity="0.1"/></svg>');
    pointer-events: none;
    z-index: 0;
}

/* Navbar */
nav {
    background: linear-gradient(135deg, rgba(5, 46, 22, 0.95) 0%, rgba(20, 83, 45, 0.95) 50%, rgba(22, 101, 52, 0.95) 100%);
    padding: 1.2rem 2.5rem;
    box-shadow: 0 4px 30px rgba(5, 46, 22, 0.3), 0 2px 8px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    gap: 2rem;
    position: sticky;
    top: 0;
    z-index: 1000;
    backdrop-filter: blur(20px) saturate(180%);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

nav a {
    color: #fff;
    text-decoration: none;
    padding: 0.7rem 1.4rem;
    border-radius: 14px;
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
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
    transition: left 0.6s ease;
}

nav a:hover::before {
    left: 100%;
}

nav a:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
}

nav a:active {
    transform: translateY(0);
}

nav span {
    color: #bbf7d0;
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
    opacity: 0.5;
    filter: drop-shadow(0 2px 6px rgba(22, 163, 74, 0.3));
}

@keyframes float-down {
    0% {
        transform: translateY(-100px) rotate(0deg) translateX(0);
        opacity: 0;
    }
    10% {
        opacity: 0.5;
    }
    50% {
        transform: translateY(50vh) rotate(180deg) translateX(100px);
    }
    90% {
        opacity: 0.5;
    }
    100% {
        transform: translateY(100vh) rotate(360deg) translateX(-50px);
        opacity: 0;
    }
}

/* Breadcrumb */
.breadcrumb {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem 2.5rem 1rem;
    position: relative;
    z-index: 2;
}

.breadcrumb-nav {
    display: flex;
    align-items: center;
    gap: 12px;
    color: #15803d;
    font-size: 0.95rem;
    flex-wrap: wrap;
    background: rgba(255, 255, 255, 0.8);
    padding: 12px 24px;
    border-radius: 16px;
    backdrop-filter: blur(10px);
    box-shadow: 0 4px 16px rgba(5, 150, 105, 0.08);
    border: 1px solid rgba(22, 163, 74, 0.15);
}

.breadcrumb-nav a {
    color: #16a34a;
    text-decoration: none;
    transition: all 0.3s ease;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 8px;
}

.breadcrumb-nav a:hover {
    color: #15803d;
    background: #f0fdf4;
}

.breadcrumb-nav span {
    opacity: 0.4;
    font-weight: 600;
}

/* Trail Hero Section */
.trail-hero {
    position: relative;
    background: 
        linear-gradient(135deg, rgba(5, 46, 22, 0.92) 0%, rgba(20, 83, 45, 0.85) 40%, rgba(22, 101, 52, 0.80) 100%),
        url('<?= $trailImage ?>');
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
    padding: 6rem 2rem;
    margin: 0 auto 4rem;
    max-width: 1400px;
    border-radius: 32px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
}

.trail-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 15% 25%, rgba(34, 197, 94, 0.35), transparent 45%),
        radial-gradient(circle at 85% 75%, rgba(74, 222, 128, 0.3), transparent 45%);
    animation: heroGlow 12s ease-in-out infinite;
    mix-blend-mode: overlay;
}

@keyframes heroGlow {
    0%, 100% { opacity: 0.6; }
    50% { opacity: 1; }
}

.trail-hero-content {
    position: relative;
    z-index: 2;
    max-width: 1200px;
    margin: 0 auto;
    color: white;
}

.trail-hero-badges {
    display: flex;
    gap: 14px;
    margin-bottom: 24px;
    flex-wrap: wrap;
}

.hero-badge {
    padding: 12px 28px;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 900;
    letter-spacing: 1px;
    backdrop-filter: blur(12px);
    border: 3px solid;
    text-transform: uppercase;
    box-shadow: 0 6px 20px rgba(0,0,0,0.25);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.hero-badge:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 10px 30px rgba(0,0,0,0.35);
}

.badge-difficulty {
    background: linear-gradient(135deg, rgba(251, 191, 36, 0.95), rgba(245, 158, 11, 0.95));
    border-color: rgba(245, 158, 11, 0.6);
}

.badge-difficulty.easy {
    background: linear-gradient(135deg, rgba(34, 197, 94, 0.95), rgba(22, 163, 74, 0.95));
    border-color: rgba(22, 163, 74, 0.6);
}

.badge-difficulty.hard {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.95), rgba(220, 38, 38, 0.95));
    border-color: rgba(220, 38, 38, 0.6);
}

.badge-featured {
    background: linear-gradient(135deg, rgba(249, 115, 22, 0.95), rgba(234, 88, 12, 0.95));
    border-color: rgba(234, 88, 12, 0.6);
}

.trail-hero-content h1 {
    font-size: 4.5rem;
    font-weight: 900;
    margin-bottom: 18px;
    text-shadow: 2px 4px 25px rgba(0,0,0,0.4);
    line-height: 1.1;
    letter-spacing: -2px;
}

.trail-location {
    font-size: 1.4rem;
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 35px;
    font-weight: 600;
    opacity: 0.95;
}

.trail-quick-stats {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.quick-stat {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 1.1rem;
    font-weight: 800;
    background: rgba(255, 255, 255, 0.15);
    padding: 14px 24px;
    border-radius: 16px;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.25);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.quick-stat:hover {
    transform: translateY(-4px) scale(1.05);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
    background: rgba(255, 255, 255, 0.25);
}

.quick-stat-icon {
    font-size: 1.8rem;
}

/* Container */
.container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 2.5rem 5rem;
    position: relative;
    z-index: 2;
}

.trail-main-grid {
    display: grid;
    grid-template-columns: 1fr 450px;
    gap: 40px;
}

/* Trail Content Card */
.trail-content {
    background: #fff;
    border-radius: 28px;
    padding: 50px;
    box-shadow: 0 12px 48px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.06);
}

.section-title {
    font-size: 2.4rem;
    color: #14532d;
    margin-bottom: 32px;
    font-weight: 900;
    display: flex;
    align-items: center;
    gap: 16px;
    letter-spacing: -1px;
}

.section-title::before {
    content: '';
    width: 8px;
    height: 42px;
    background: linear-gradient(135deg, #16a34a, #22c55e);
    border-radius: 6px;
    box-shadow: 0 4px 12px rgba(22, 163, 74, 0.4);
}

.trail-description {
    font-size: 1.15rem;
    line-height: 2;
    color: #334155;
    margin-bottom: 45px;
    font-weight: 500;
}

/* Feature Cards */
.trail-features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 20px;
    margin-bottom: 50px;
}

.feature-card {
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    padding: 28px;
    border-radius: 24px;
    text-align: center;
    border: 2px solid #bbf7d0;
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 16px rgba(22, 163, 74, 0.1);
}

.feature-card:hover {
    transform: translateY(-10px) scale(1.03);
    box-shadow: 0 20px 45px rgba(22, 163, 74, 0.25);
    border-color: #22c55e;
    background: linear-gradient(135deg, #dcfce7, #bbf7d0);
}

.feature-icon {
    font-size: 3.2rem;
    margin-bottom: 14px;
    display: block;
    filter: drop-shadow(0 4px 8px rgba(22, 163, 74, 0.3));
    animation: iconFloat 3s ease-in-out infinite;
}

@keyframes iconFloat {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}

.feature-label {
    font-weight: 800;
    color: #15803d;
    font-size: 1rem;
    letter-spacing: 0.3px;
}

/* Highlights Section */
.trail-highlights {
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    padding: 40px;
    border-radius: 24px;
    border-left: 8px solid #16a34a;
    margin-bottom: 50px;
    box-shadow: 0 8px 30px rgba(22, 163, 74, 0.15);
}

.highlights-title {
    font-size: 1.7rem;
    color: #14532d;
    font-weight: 900;
    margin-bottom: 28px;
    display: flex;
    align-items: center;
    gap: 14px;
    letter-spacing: -0.5px;
}

.highlights-list {
    list-style: none;
    display: grid;
    gap: 20px;
}

.highlights-list li {
    display: flex;
    align-items: start;
    gap: 16px;
    color: #0f172a;
    font-size: 1.08rem;
    line-height: 1.8;
    font-weight: 600;
    padding: 12px;
    background: rgba(255, 255, 255, 0.6);
    border-radius: 12px;
    transition: all 0.3s ease;
}

.highlights-list li:hover {
    background: rgba(255, 255, 255, 0.9);
    transform: translateX(8px);
    box-shadow: 0 4px 12px rgba(22, 163, 74, 0.15);
}

.highlights-list li::before {
    content: '✓';
    background: linear-gradient(135deg, #16a34a, #22c55e);
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-weight: 900;
    font-size: 1rem;
    box-shadow: 0 4px 12px rgba(22, 163, 74, 0.4);
}

/* Gallery */
.trail-gallery {
    margin-bottom: 45px;
}

.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
}

.gallery-item {
    aspect-ratio: 4/3;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 12px 35px rgba(0,0,0,0.15);
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    border: 4px solid white;
}

.gallery-item:hover {
    transform: scale(1.08) translateY(-8px) rotate(2deg);
    box-shadow: 0 25px 60px rgba(22, 163, 74, 0.35);
}

.gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.7s;
}

.gallery-item:hover img {
    transform: scale(1.15);
}

/* Sidebar */
.trail-sidebar {
    display: flex;
    flex-direction: column;
    gap: 28px;
}

.sidebar-card {
    background: #fff;
    border-radius: 28px;
    padding: 38px;
    box-shadow: 0 12px 48px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.06);
    transition: all 0.4s ease;
}

.sidebar-card:hover {
    box-shadow: 0 20px 60px rgba(22, 163, 74, 0.15);
    transform: translateY(-5px);
}

.sidebar-title {
    font-size: 1.6rem;
    color: #14532d;
    margin-bottom: 28px;
    font-weight: 900;
    letter-spacing: -0.5px;
}

/* Stats List */
.stats-list {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    border-radius: 16px;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid #bbf7d0;
}

.stat-item:hover {
    background: linear-gradient(135deg, #dcfce7, #bbf7d0);
    transform: translateX(8px);
    box-shadow: 0 6px 20px rgba(22, 163, 74, 0.2);
    border-color: #22c55e;
}

.stat-label {
    font-weight: 700;
    color: #15803d;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 1.05rem;
}

.stat-label-icon {
    font-size: 1.5rem;
}

.stat-value {
    font-weight: 900;
    color: #16a34a;
    font-size: 1.3rem;
    letter-spacing: -0.5px;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 20px 36px;
    border-radius: 50px;
    font-weight: 900;
    font-size: 1.1rem;
    text-decoration: none;
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    cursor: pointer;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.btn-primary {
    background: linear-gradient(135deg, #15803d, #16a34a, #22c55e);
    color: white;
    box-shadow: 0 8px 30px rgba(22, 163, 74, 0.4);
    border: 3px solid #14532d;
}

.btn-primary:hover {
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 15px 45px rgba(22, 163, 74, 0.5);
    background: linear-gradient(135deg, #14532d, #15803d, #16a34a);
}

.btn-secondary {
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    color: #15803d;
    border: 3px solid #22c55e;
    box-shadow: 0 6px 20px rgba(22, 163, 74, 0.15);
}

.btn-secondary:hover {
    background: linear-gradient(135deg, #dcfce7, #bbf7d0);
    transform: translateY(-5px) scale(1.02);
    box-shadow: 0 12px 35px rgba(22, 163, 74, 0.25);
}

/* Rating Card */
.rating-card {
    text-align: center;
}

.rating-number {
    font-size: 4.5rem;
    font-weight: 900;
    color: #16a34a;
    margin-bottom: 14px;
    display: block;
    letter-spacing: -2px;
    text-shadow: 0 4px 15px rgba(22, 163, 74, 0.3);
}

.rating-stars {
    font-size: 2.2rem;
    margin-bottom: 14px;
    display: block;
    filter: drop-shadow(0 4px 8px rgba(251, 191, 36, 0.4));
}

.rating-count {
    color: #15803d;
    font-size: 1.1rem;
    font-weight: 700;
}

/* Weather Widget */
.weather-widget {
    background: linear-gradient(135deg, #15803d, #16a34a, #22c55e);
    color: white;
    padding: 35px;
    border-radius: 24px;
    text-align: center;
    box-shadow: 0 12px 40px rgba(22, 163, 74, 0.4);
    position: relative;
    overflow: hidden;
}

.weather-widget::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
    animation: weatherPulse 5s ease-in-out infinite;
}

@keyframes weatherPulse {
    0%, 100% { transform: scale(1); opacity: 0.6; }
    50% { transform: scale(1.3); opacity: 0.3; }
}

.weather-icon {
    font-size: 4rem;
    margin-bottom: 14px;
    filter: drop-shadow(0 6px 12px rgba(0,0,0,0.3));
    position: relative;
    z-index: 1;
    animation: weatherFloat 4s ease-in-out infinite;
}

@keyframes weatherFloat {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.weather-temp {
    font-size: 3.2rem;
    font-weight: 900;
    margin-bottom: 10px;
    position: relative;
    z-index: 1;
    letter-spacing: -2px;
}

.weather-desc {
    opacity: 0.95;
    font-size: 1.15rem;
    font-weight: 700;
    position: relative;
    z-index: 1;
}

/* Tips List */
.tips-list {
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.tips-list li {
    display: flex;
    align-items: start;
    gap: 14px;
    color: #0f172a;
    font-size: 1.02rem;
    line-height: 1.8;
    font-weight: 600;
    padding: 16px;
    background: linear-gradient(135deg, #f0fdf4, #dcfce7);
    border-radius: 14px;
    transition: all 0.4s ease;
    border: 2px solid #bbf7d0;
}

.tips-list li:hover {
    background: linear-gradient(135deg, #dcfce7, #bbf7d0);
    transform: translateX(6px);
    box-shadow: 0 6px 18px rgba(22, 163, 74, 0.2);
    border-color: #22c55e;
}

.tips-list li::before {
    content: '💡';
    font-size: 1.5rem;
    flex-shrink: 0;
}

/* Responsive Design */
@media (max-width: 968px) {
    .trail-main-grid {
        grid-template-columns: 1fr;
    }
    
    .trail-hero {
        padding: 4rem 2rem;
    }
    
    .trail-hero-content h1 {
        font-size: 3.2rem;
    }
}

@media (max-width: 768px) {
    nav {
        flex-direction: column;
        gap: 1rem;
        padding: 1rem 1.5rem;
    }
    
    nav span {
        margin-left: 0;
    }
    
    .trail-content {
        padding: 32px;
    }
    
    .section-title {
        font-size: 1.9rem;
    }
    
    .trail-features-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 640px) {
    .trail-hero-content h1 {
        font-size: 2.5rem;
    }
    
    .trail-features-grid {
        grid-template-columns: 1fr;
    }
    
    .gallery-grid {
        grid-template-columns: 1fr;
    }
}
</style>
<body>
    <!-- Navbar -->
    <nav>
        <a href="<?= BASE_URL ?>index.php">Home</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <span>Welcome, <?= htmlspecialchars($_SESSION['user_username']) ?></span>
            <a href="<?= BASE_URL ?>logout_user.php">Logout</a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>login_user.php">User Login</a>
            <a href="<?= BASE_URL ?>register_user.php">Register</a>
        <?php endif; ?>
    </nav>

    <!-- Floating Leaves -->
    <div class="floating-leaves">
        <div class="leaf" style="left: 10%; animation-duration: 15s;">🍃</div>
        <div class="leaf" style="left: 30%; animation-duration: 18s; animation-delay: 3s;">🌿</div>
        <div class="leaf" style="left: 50%; animation-duration: 20s; animation-delay: 6s;">🍃</div>
        <div class="leaf" style="left: 70%; animation-duration: 17s; animation-delay: 2s;">🌿</div>
        <div class="leaf" style="left: 85%; animation-duration: 19s; animation-delay: 5s;">🍃</div>
    </div>

    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <nav class="breadcrumb-nav">
            <a href="<?= BASE_URL ?>index.php">🏠 Home</a>
            <span>›</span>
            <a href="<?= BASE_URL ?>index.php">Trails</a>
            <span>›</span>
            <span><?= htmlspecialchars($trail['name']) ?></span>
        </nav>
    </div>

    <!-- Trail Hero -->
    <div class="container">
        <div class="trail-hero">
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

        <!-- Main Content Grid -->
        <div class="trail-main-grid">
            <!-- Left Column - Main Content -->
            <div>
                <div class="trail-content">
                    <h2 class="section-title">Trail Overview</h2>
                    <p class="trail-description">
                        <?php 
                        $description = !empty($trail['description']) 
                            ? $trail['description'] 
                            : 'Embark on an unforgettable journey through pristine wilderness. This trail offers breathtaking views, diverse ecosystems, and an immersive nature experience that will leave you refreshed and inspired. Perfect for adventurers seeking to connect with the great outdoors while enjoying stunning panoramic vistas and encountering local wildlife in their natural habitat.';
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

                    <?php if(!empty($trail['image'])): ?>
                    <div class="trail-gallery">
                        <h2 class="section-title">Photo Gallery</h2>
                        <div class="gallery-grid">
                            <div class="gallery-item">
                                <img src="<?= $trailImage ?>" 
                                     alt="<?= htmlspecialchars($trail['name']) ?> view" 
                                     onerror="this.parentElement.style.display='none'">
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column - Sidebar -->
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
                    <a href="<?= BASE_URL ?>index.php" class="btn btn-secondary">
                        <span>← Back to Trails</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php 
    // Include footer if you have one
    // require_once "../../includes/footer.php"; 
    ?>
</body>
</html>