<?php
/**
 * Visitor Stats Widget for Admin Dashboard
 * Include this file in your admin dashboard to display visitor statistics
 */

// Ensure we have a database connection
if (!isset($pdo)) {
    require_once __DIR__ . '/connectDb.php';
}

define('SKIP_AUTO_TRACK', true); // Don't track admin page visits
require_once __DIR__ . '/track_visitor.php';

// Get statistics
$visitor_stats = get_visitor_stats($pdo);
$daily_chart_data = get_daily_stats_chart($pdo, 14); // Last 14 days
$allowedCountryRanges = [1, 7, 30];
$sessionCountryRange = isset($_SESSION['visitor_country_days']) ? (int)$_SESSION['visitor_country_days'] : 1;

if (isset($_GET['country_days'])) {
    $selectedCountryRange = (int)$_GET['country_days'];
} else {
    $selectedCountryRange = $sessionCountryRange;
}

if (!in_array($selectedCountryRange, $allowedCountryRanges, true)) {
    $selectedCountryRange = 1;
}

$_SESSION['visitor_country_days'] = $selectedCountryRange;

$country_stats = get_country_stats($pdo, $selectedCountryRange, 10);
$documentation_stats = get_documentation_stats($pdo, 30, 5);

$countryRangeLabels = [
    1 => "Aujourd'hui",
    7 => '7 jours',
    30 => '30 jours'
];

function format_country_label($countryCode) {
    $code = strtoupper((string)$countryCode);
    if ($code === '') {
        return 'Inconnu';
    }

    $countryMap = [
        'CD' => 'RDC',
        'CG' => 'Congo-Brazzaville',
        'FR' => 'France',
        'BE' => 'Belgique',
        'CA' => 'Canada',
        'US' => 'États-Unis',
        'GB' => 'Royaume-Uni',
        'DE' => 'Allemagne',
        'IT' => 'Italie',
        'ES' => 'Espagne',
        'ZA' => 'Afrique du Sud',
        'CM' => 'Cameroun',
        'SN' => 'Sénégal',
        'CI' => 'Côte d’Ivoire',
        'NG' => 'Nigeria',
        'RW' => 'Rwanda',
        'BI' => 'Burundi',
        'UG' => 'Ouganda',
        'KE' => 'Kenya',
        'TZ' => 'Tanzanie'
    ];

    return $countryMap[$code] ?? $code;
}
?>

<div class="row mb-4">
    <div class="col-12">
        <h3 class="mb-3"><i class="bi bi-graph-up"></i> Statistiques des Visiteurs</h3>
    </div>

    <!-- Stats Cards -->
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-white bg-primary shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase mb-1">Aujourd'hui</h6>
                        <h2 class="mb-0"><?= number_format($visitor_stats['today']) ?></h2>
                        <small>dont <?= $visitor_stats['today_unique'] ?> uniques</small>
                    </div>
                    <div class="fs-1"><i class="bi bi-calendar-day"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-white bg-info shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase mb-1">Cette Semaine</h6>
                        <h2 class="mb-0"><?= number_format($visitor_stats['this_week']) ?></h2>
                        <small>visites</small>
                    </div>
                    <div class="fs-1"><i class="bi bi-calendar-week"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-white bg-success shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase mb-1">Ce Mois</h6>
                        <h2 class="mb-0"><?= number_format($visitor_stats['this_month']) ?></h2>
                        <small>visites</small>
                    </div>
                    <div class="fs-1"><i class="bi bi-calendar-month"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card text-white bg-dark shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-uppercase mb-1">Total</h6>
                        <h2 class="mb-0"><?= number_format($visitor_stats['total_visits']) ?></h2>
                        <small><?= number_format($visitor_stats['unique_visitors']) ?> visiteurs uniques</small>
                    </div>
                    <div class="fs-1"><i class="bi bi-people-fill"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Row -->
<div class="row mb-4">
    <div class="col-md-8 mb-3">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-bar-chart-line"></i> Évolution des Visites (14 derniers jours)</h5>
            </div>
            <div class="card-body">
                <canvas id="visitorChart" height="80"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-speedometer2"></i> Statistiques</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Moyenne quotidienne (30j)</span>
                        <strong><?= $visitor_stats['avg_daily'] ?></strong>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-primary" role="progressbar"
                             style="width: <?= min(100, ($visitor_stats['today'] / max(1, $visitor_stats['avg_daily'])) * 100) ?>%">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Visiteurs uniques</span>
                        <strong><?= number_format($visitor_stats['unique_visitors']) ?></strong>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-success" role="progressbar"
                             style="width: <?= ($visitor_stats['unique_visitors'] / max(1, $visitor_stats['total_visits'])) * 100 ?>%">
                        </div>
                    </div>
                </div>

                <hr>
                <div class="text-center">
                    <small class="text-muted">
                        <i class="bi bi-info-circle"></i>
                        Suivi depuis le <?= date('d/m/Y', strtotime('-30 days')) ?>
                    </small>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mt-3">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h5 class="mb-0"><i class="bi bi-geo-alt"></i> Visiteurs par pays (<?= htmlspecialchars($countryRangeLabels[$selectedCountryRange]) ?>)</h5>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Période pays">
                        <?php foreach ($allowedCountryRanges as $range): ?>
                            <?php
                                $isActive = $selectedCountryRange === $range;
                                $queryParams = $_GET;
                                $queryParams['country_days'] = $range;
                                $rangeUrl = '?' . http_build_query($queryParams);
                            ?>
                            <a href="<?= htmlspecialchars($rangeUrl) ?>"
                               class="btn <?= $isActive ? 'btn-primary' : 'btn-outline-primary' ?>">
                                <?= htmlspecialchars($countryRangeLabels[$range]) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($country_stats)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col">Pays</th>
                                    <th scope="col" class="text-end">Visites</th>
                                    <th scope="col" class="text-end">Uniques</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($country_stats as $row): ?>
                                    <tr>
                                        <td><?= htmlspecialchars(format_country_label($row['country'])) ?></td>
                                        <td class="text-end"><?= number_format((int)$row['visits']) ?></td>
                                        <td class="text-end"><?= number_format((int)$row['unique_visits']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="p-3 text-center text-muted">Aucune donnee pour aujourd'hui.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card shadow-sm mt-3">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-journal-text"></i> Documentation (30 derniers jours)</h5>
            </div>
            <div class="card-body">
                <div class="row text-center mb-3">
                    <div class="col-6 border-end">
                        <div class="text-muted small">Lectures (Voir)</div>
                        <div class="h4 mb-0"><?= number_format((int)$documentation_stats['total_views']) ?></div>
                    </div>
                    <div class="col-6">
                        <div class="text-muted small">Téléchargements</div>
                        <div class="h4 mb-0"><?= number_format((int)$documentation_stats['total_downloads']) ?></div>
                    </div>
                </div>

                <h6 class="mb-2">Top documents</h6>
                <?php if (!empty($documentation_stats['top_documents'])): ?>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Document</th>
                                    <th class="text-end">Vues</th>
                                    <th class="text-end">Téléch.</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($documentation_stats['top_documents'] as $docRow): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($docRow['titreDoc']) ?></td>
                                        <td class="text-end"><?= number_format((int)$docRow['views']) ?></td>
                                        <td class="text-end"><?= number_format((int)$docRow['downloads']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-muted">Aucune interaction enregistrée sur la période.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Chart Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartData = <?= json_encode($daily_chart_data) ?>;

    const labels = chartData.map(d => {
        const date = new Date(d.visit_date);
        return date.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short' });
    });

    const visits = chartData.map(d => parseInt(d.visits));
    const uniqueVisits = chartData.map(d => parseInt(d.unique_visits));

    const ctx = document.getElementById('visitorChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Visites totales',
                data: visits,
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                tension: 0.3,
                fill: true
            }, {
                label: 'Visiteurs uniques',
                data: uniqueVisits,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>

<!-- Add Bootstrap Icons if not already included -->
<?php if (!defined('BOOTSTRAP_ICONS_LOADED')): ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <?php define('BOOTSTRAP_ICONS_LOADED', true); ?>
<?php endif; ?>
