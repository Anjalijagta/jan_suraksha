<?php
// contributors.php - Unified Maintainer-style Cards
include 'header.php';

// Configurable Project Lead
$PROJECT_LEAD = [
    'username' => 'Anjalijagta',
    'name' => 'Anjali Jagtap',
    'description' => 'Founder & core maintainer of Jan Suraksha, responsible for project direction, architecture, and community leadership.',
    'avatar_url' => 'https://avatars.githubusercontent.com/u/138389224?v=4&s=80',
    'html_url' => 'https://github.com/Anjalijagta'
];

// Cache keys
$cache_key_contributors = 'jan_suraksha_contributors';
$cache_key_stats = 'jan_suraksha_contributor_stats';
$cache_duration = 300;
$contributors = [];

// Fetch contributors
if (isset($_SESSION[$cache_key_contributors]) && (time() - $_SESSION[$cache_key_contributors . '_timestamp']) < $cache_duration) {
    $contributors = $_SESSION[$cache_key_contributors];
} else {
    $api_url = 'https://api.github.com/repos/Anjalijagta/jan_suraksha/contributors?page=1&per_page=100';
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $api_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_USERAGENT => 'JanSuraksha/1.0 (https://github.com/Anjalijagta/jan_suraksha)',
        CURLOPT_HTTPHEADER => ['Accept: application/vnd.github.v3+json'],
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code === 200) {
        $contributors = json_decode($response, true) ?: [];
        $_SESSION[$cache_key_contributors] = $contributors;
        $_SESSION[$cache_key_contributors . '_timestamp'] = time();
    }
}

// Remove Project Lead and fetch individual stats
$filtered_contributors = [];
if (!empty($contributors)) {
    foreach ($contributors as $contributor) {
        if (strtolower($contributor['login']) !== strtolower($PROJECT_LEAD['username'])) {
            $contributor['prs'] = 0;
            $contributor['commits'] = $contributor['contributions'] ?? 0;
            
            $stat_key = $contributor['login'] . '_stats';
            if (isset($_SESSION[$stat_key]) && (time() - $_SESSION[$stat_key . '_timestamp']) < $cache_duration) {
                $contributor['prs'] = $_SESSION[$stat_key]['prs'] ?? 0;
            } else {
                $pr_url = "https://api.github.com/repos/Anjalijagta/jan_suraksha/pulls?state=closed&per_page=100";
                $pr_ch = curl_init();
                curl_setopt_array($pr_ch, [
                    CURLOPT_URL => $pr_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => 8,
                    CURLOPT_USERAGENT => 'JanSuraksha/1.0',
                    CURLOPT_HTTPHEADER => ['Accept: application/vnd.github.v3+json'],
                ]);
                
                $pr_response = curl_exec($pr_ch);
                $pr_http = curl_getinfo($pr_ch, CURLINFO_HTTP_CODE);
                curl_close($pr_ch);
                
                if ($pr_http === 200) {
                    $prs = json_decode($pr_response, true) ?: [];
                    $merged_prs = array_filter($prs, function($pr) use ($contributor) {
                        return strtolower($pr['user']['login'] ?? '') === strtolower($contributor['login']) 
                            && ($pr['merged_at'] ?? null);
                    });
                    $contributor['prs'] = count($merged_prs);
                    
                    $_SESSION[$stat_key] = ['prs' => $contributor['prs']];
                    $_SESSION[$stat_key . '_timestamp'] = time();
                }
            }
            
            $filtered_contributors[] = $contributor;
        }
    }
}

// Fallback
if (empty($filtered_contributors)) {
    $filtered_contributors = [
        [
            'login' => 'sayeeg-11', 
            'avatar_url' => 'https://avatars.githubusercontent.com/u/175196758?v=4&s=48', 
            'html_url' => 'https://github.com/sayeeg-11',
            'contributions' => 15,
            'prs' => 3
        ],
    ];
}

// Sort by total contributions (commits + PRs * 10)
usort($filtered_contributors, function($a, $b) {
    $score_a = ($a['contributions'] ?? 0) + (($a['prs'] ?? 0) * 10);
    $score_b = ($b['contributions'] ?? 0) + (($b['prs'] ?? 0) * 10);
    return $score_b <=> $score_a;
});
?>

<style>
/* CLASSIC UNIFIED CARDS - Maintainer Card Design Applied to All */
.unified-card {
    max-width: 500px;
    margin: 0 auto;
    text-align: center;
    padding: 2.5rem 2rem;
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.unified-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}

.lead-badge, .contributor-badge {
    display: inline-block;
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    color: white;
    padding: 0.5rem 1.5rem;
    border-radius: 25px;
    font-weight: 700;
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 12px rgba(251, 191, 36, 0.3);
}

.contributor-badge {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    font-size: 0.85rem;
    padding: 0.4rem 1.2rem;
}

.unified-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    border: 4px solid white;
    margin: 0 auto 1.5rem;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    display: block;
    object-fit: cover;
}

.unified-name {
    color: #1f2937;
    font-size: 1.8rem;
    font-weight: 800;
    margin: 0 0 0.75rem 0;
}

.unified-description {
    color: #6b7280;
    font-size: 1rem;
    line-height: 1.6;
    margin-bottom: 2rem;
}

.unified-stats {
    display: flex;
    justify-content: center;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.stat-item {
    text-align: center;
    padding: 1rem 1.25rem;
    background: white;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    min-width: 90px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.stat-icon { 
    font-size: 1.25rem; 
    display: block; 
    margin-bottom: 0.25rem; 
    color: #3b82f6; 
}
.stat-number { 
    font-size: 1.25rem; 
    font-weight: 800; 
    color: #1f2937; 
    display: block; 
}
.stat-label { 
    font-size: 0.75rem; 
    color: #6b7280; 
    text-transform: uppercase; 
    letter-spacing: 0.03em; 
    font-weight: 600;
}

.unified-socials {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.unified-socials a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    background: #f3f4f6;
    color: #6b7280;
    border-radius: 10px;
    text-decoration: none;
    transition: all 0.2s ease;
    border: 1px solid #e5e7eb;
    font-size: 1rem;
}

.unified-socials a:hover {
    background: #3b82f6;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.3);
}

.contributors-hero {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 50%, #1e40af 100%);
    color: white;
    padding: 4rem 0;
    text-align: center;
}

.section-title {
    text-align: center;
    font-size: 2rem;
    font-weight: 800;
    color: #1f2937;
    margin: 3rem 0 1rem 0;
}

.contributors-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(520px, 1fr));
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

@media (max-width: 768px) {
    .contributors-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .unified-card {
        margin: 0 1rem;
        padding: 2rem 1.5rem;
    }
    
    .unified-avatar {
        width: 72px;
        height: 72px;
    }
    
    .unified-name {
        font-size: 1.6rem;
    }
}

@media (max-width: 480px) {
    .unified-stats {
        gap: 1rem;
    }
    
    .stat-item {
        min-width: 80px;
        padding: 0.75rem 1rem;
    }
}
</style>

<main>
    <!-- Hero Section -->
    <section class="contributors-hero">
        <div class="container">
            <h1 class="display-4 fw-bold mb-3" style="font-size: 2.5rem; font-weight: 800; text-shadow: 0 2px 10px rgba(0,0,0,0.3);">Our Contributors</h1>
            <p class="lead mb-0" style="font-size: 1.2rem; opacity: 0.95;">Heartfelt thanks to everyone building <strong>Jan Suraksha</strong> with us! ❤️</p>
        </div>
    </section>

    <!-- Project Lead - Unified Card Design -->
    <section style="padding: 2rem 0;">
        <div class="container">
            <div class="unified-card">
                <span class="lead-badge">
                    👑 <?= htmlspecialchars($PROJECT_LEAD['name']) ?> - Project Lead
                </span>
                
                <img src="<?= htmlspecialchars($PROJECT_LEAD['avatar_url']) ?>" 
                     alt="<?= htmlspecialchars($PROJECT_LEAD['name']) ?> – Project Lead" 
                     class="unified-avatar" loading="lazy">
                
                <h3 class="unified-name"><?= htmlspecialchars($PROJECT_LEAD['name']) ?></h3>
                
                <p class="unified-description"><?= htmlspecialchars($PROJECT_LEAD['description']) ?></p>
                
                
                <div class="unified-socials">
                    <a href="<?= htmlspecialchars($PROJECT_LEAD['html_url']) ?>" target="_blank" rel="noopener noreferrer" title="GitHub Profile">
                        <i class="fab fa-github"></i>
                    </a>
                    <a href="<?= htmlspecialchars($PROJECT_LEAD['html_url']) ?>/pulls?q=is%3Apr+is%3Amerged" target="_blank" rel="noopener noreferrer" title="Merged PRs">
                        <i class="fas fa-code-branch"></i>
                    </a>
                    <a href="https://github.com/Anjalijagta/jan_suraksha/graphs/contributors" target="_blank" rel="noopener noreferrer" title="Contribution Graph">
                        <i class="fas fa-chart-line"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Contributors - EXACT SAME Unified Card Design -->
    <section style="background: #f8fafc; padding: 4rem 0;">
        <div class="container">
            <h2 class="section-title">Top Contributors</h2>
            <p style="text-align: center; color: #6b7280; margin-bottom: 3rem; font-size: 1.1rem;">
                Ranked by Commits + PRs • Excluding Project Lead
            </p>
            
            <?php if (!empty($filtered_contributors)): ?>
                <div class="contributors-grid">
                    <?php foreach (array_slice($filtered_contributors, 0, 99) as $index => $contributor): ?>
                        <?php 
                        $commits = $contributor['contributions'] ?? 0;
                        $prs = $contributor['prs'] ?? 0;
                        ?>
                        <div class="unified-card">
                            <span class="contributor-badge">
                                #<?= ($index + 1) ?> <?= htmlspecialchars($contributor['login']) ?>
                            </span>
                            
                            <img src="<?= htmlspecialchars($contributor['avatar_url']) ?>" 
                                 alt="<?= htmlspecialchars($contributor['login']) ?>" 
                                 class="unified-avatar" loading="lazy">
                            
                            <h3 class="unified-name"><?= htmlspecialchars($contributor['login']) ?></h3>
                            
                            <p class="unified-description">
                                Active contributor to Jan Suraksha with valuable commits and pull requests.
                            </p>
                            
                            <div class="unified-stats">
                                <div class="stat-item">
                                    <span class="stat-icon">💾</span>
                                    <span class="stat-number"><?= number_format($commits) ?></span>
                                    <span class="stat-label">Commits</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-icon">🔀</span>
                                    <span class="stat-number"><?= number_format($prs) ?></span>
                                    <span class="stat-label">PRs Merged</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-icon">📊</span>
                                    <span class="stat-number"><?= number_format($commits + ($prs * 10)) ?></span>
                                    <span class="stat-label">Score</span>
                                </div>
                            </div>
                            
                            <div class="unified-socials">
                                <a href="<?= htmlspecialchars($contributor['html_url']) ?>" target="_blank" rel="noopener noreferrer" title="GitHub Profile">
                                    <i class="fab fa-github"></i>
                                </a>
                                <a href="<?= htmlspecialchars($contributor['html_url']) ?>/pulls?q=is%3Apr+author:<?= htmlspecialchars($contributor['login']) ?>+is%3Amerged" target="_blank" rel="noopener noreferrer" title="Merged PRs">
                                    <i class="fas fa-code-branch"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (count($contributors) >= 100): ?>
                    <div class="text-center mt-5">
                        <p class="text-muted" style="font-size: 1rem;">Showing top 99 contributors. 
                           <a href="https://github.com/Anjalijagta/jan_suraksha/graphs/contributors" target="_blank" rel="noopener noreferrer" style="font-weight: 600; color: #3b82f6;">View complete list →</a>
                        </p>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-8" style="color: #6b7280;">
                    <div style="width: 64px; height: 64px; border: 4px solid #e5e7eb; border-radius: 50%; border-top-color: #3b82f6; animation: spin 1s ease-in-out infinite; margin: 0 auto 1rem;"></div>
                    <h3>Loading contributors...</h3>
                    <p>Fetching latest data from GitHub</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

<style>
@keyframes spin { to { transform: rotate(360deg); } }
</style>
