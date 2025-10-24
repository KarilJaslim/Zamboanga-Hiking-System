<?php 
require_once "../includes/db.php"; 
require_once "../includes/header.php"; 


if(!isset($_GET['id']) || empty($_GET['id'])){
    echo "<p>Trail not found</p>"; 
    include "../includes/footer.php"; 
    exit();
}


$id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
if($id === false) {
    echo "<p>Invalid trail ID</p>"; 
    include "../includes/footer.php"; 
    exit();
}


$stmt = $pdo->prepare("SELECT * FROM trails WHERE id = ?"); 
$stmt->execute([$id]); 
$trail = $stmt->fetch(PDO::FETCH_ASSOC); 


if(!$trail){ 
    echo "<p>Trail not found</p>"; 
    include "../includes/footer.php"; 
    exit();
}


$trail['distance'] = $trail['distance'] ?? '5.2';
$trail['duration'] = $trail['duration'] ?? '2-3';
$trail['elevation'] = $trail['elevation'] ?? '450';
$trail['difficulty'] = $trail['difficulty'] ?? 'moderate';
$trail['location'] = $trail['location'] ?? 'Nature Reserve';
$trail['rating'] = $trail['rating'] ?? '4.8';
$trail['reviews'] = $trail['reviews'] ?? '287';


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

