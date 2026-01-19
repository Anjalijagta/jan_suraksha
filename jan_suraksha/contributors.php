<?php
// contributors.php - GitHub Issue #140 Step 1 Implementation
include 'header.php';

// Cache key and duration (5 minutes = 300 seconds)
$cache_key = 'jan_suraksha_contributors';
$cache_duration = 300;
$contributors = [];

// Check session cache first
if (isset($_SESSION[$cache_key]) && (time() - $_SESSION[$cache_key . '_timestamp']) < $cache_duration) {
    $contributors = $_SESSION[$cache_key];
} else {
    // GitHub API endpoint (corrected repo owner from issue)
    $api_url = 'https://api.github.com/repos/Anjalijagta/jan_suraksha/contributors?page=1&per_page=100';
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $api_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_USERAGENT => 'JanSuraksha/1.0 (https://github.com/Anjalijagta/jan_suraksha)', // Required header [web:3]
        CURLOPT_HTTPHEADER => ['Accept: application/vnd.github.v3+json'],
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code === 200) {
        $contributors = json_decode($response, true) ?: [];
        // Cache for 5 minutes
        $_SESSION[$cache_key] = $contributors;
        $_SESSION[$cache_key . '_timestamp'] = time();
    }
}

// Fallback static list if API fails [attached_file:1]
if (empty($contributors)) {
    $contributors = [
        ['login' => 'Anjalijagta', 'avatar_url' => 'https://avatars.githubusercontent.com/u/138389224?v=4&s=48', 'html_url' => 'https://github.com/Anjalijagta'],
        ['login' => 'sayeeg-11', 'avatar_url' => 'https://avatars.githubusercontent.com/u/175196758?v=4&s=48', 'html_url' => 'https://github.com/sayeeg-11'],
    ];
}
?>

<style>
.contributors-hero {
    background: linear-gradient(135deg, var(--color-primary) 0%, color-mix(in srgb, var(--color-primary) 70% white));
    color: white;
    padding: 4rem 0;
    text-align: center;
}
.contributor-grid {
    display: grid;
    grid-template-columns: 1fr; /* Mobile: 1 column */
    gap: 1.5rem;
    padding: 2rem 0;
}
.contributor-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    border: 1px solid var(--color-border);
    border-radius: 12px;
    background: var(--color-bg);
    transition: all 0.3s ease;
    text-decoration: none;
    color: inherit;
}
.contributor-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0,0,0,0.15);
    border-color: var(--color-primary);
}
.contributor-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    border: 2px solid white;
}
.loading-spinner {
    display: inline-block;
    width: 2rem;
    height: 2rem;
    border: 3px solid rgba(255,255,255,0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 1s ease-in-out infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Tablet: 2 columns */
@media (min-width: 768px) {
    .contributor-grid { grid-template-columns: repeat(2, 1fr); }
}
/* Desktop: 3 columns */
@media (min-width: 1024px) {
    .contributor-grid { grid-template-columns: repeat(3, 1fr); }
}
</style>

<main>
    <section class="contributors-hero">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3">Our Contributors</h1>
            <p class="lead mb-4">Heartfelt thanks to everyone building <strong>Jan Suraksha</strong> with us! ❤️</p>
            <?php if (empty($contributors)): ?>
                <div class="loading-spinner"></div>
            <?php endif; ?>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <?php if (!empty($contributors)): ?>
                <div class="contributor-grid">
                    <?php foreach ($contributors as $contributor): ?>
                        <a href="<?= htmlspecialchars($contributor['html_url']) ?>" target="_blank" class="contributor-card" rel="noopener noreferrer">
                            <img src="<?= htmlspecialchars($contributor['avatar_url']) ?>" alt="<?= htmlspecialchars($contributor['login']) ?>" class="contributor-avatar">
                            <div>
                                <strong><?= htmlspecialchars($contributor['login']) ?></strong>
                                <small class="d-block text-muted">View Profile</small>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
                <?php if (count($contributors) >= 100): ?>
                    <div class="text-center mt-4">
                        <p class="text-muted">Showing top 100 contributors. <a href="https://github.com/Anjalijagta/jan_suraksha/graphs/contributors" target="_blank">View full graph →</a></p>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <h3>Unable to load contributors at the moment</h3>
                    <p class="text-muted">Using fallback list. Please refresh to try again.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>
