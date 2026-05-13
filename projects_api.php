<?php
require 'config.php';

$exampleProjects = [
    [
        'id' => 1,
        'title' => 'Süleyman Seven Website',
        'subtitle' => 'Business Website',
        'description' => 'A polished business site featuring responsive layouts and strong visual storytelling. Clean design with modern interactions.',
        'tech' => 'HTML, CSS, JavaScript',
        'repo_url' => null,
        'live_url' => 'https://suleymanseven.com/',
        'image_url' => null
    ],
    [
        'id' => 2,
        'title' => 'MRT Kurubuz Website',
        'subtitle' => 'Landing Page',
        'description' => 'A modern landing page experience built for clarity, conversion, and brand trust. Optimized for mobile and desktop.',
        'tech' => 'HTML, CSS, JavaScript',
        'repo_url' => null,
        'live_url' => 'https://mrtkurubuz.com/',
        'image_url' => null
    ]
];

try {
    $pdo = connect_db();
    $stmt = $pdo->query('SELECT id, title, subtitle, description, tech, repo_url, live_url, image_url FROM projects ORDER BY sort_order DESC, id DESC');
    $projects = $stmt->fetchAll();
    
    // If no projects in database, return examples
    if (empty($projects)) {
        jsonResponse(['projects' => $exampleProjects]);
    }
    
    jsonResponse(['projects' => $projects]);
} catch (Exception $e) {
    // Database connection failed, return examples as fallback
    jsonResponse(['projects' => $exampleProjects]);
}
