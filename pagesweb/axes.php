<?php
require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect;
require_once __DIR__ . '/track_visitor.php';
require_once __DIR__ . '/csrf_helper.php';

$pageCss = CSS_DIR . 'axes.css';

$axesBaseUrl = BASE_URL . 'pagesweb/axes/';
$printUrl = BASE_URL . 'pagesweb/axes_print.php';

$axes = [
    'participation' => [
        'title' => 'Axe Participation',
        'label' => 'Participation',
        'intro' => 'Renforcer la presence effective des femmes dans les mecanismes de paix, les espaces de decision et les institutions de securite.',
        'summary' => 'La participation se veut non seulement la presence des femmes, mais plus encore une presence accrue, en ordre utile et significative dans les instances de prise des decisions et dans les mecanismes de maintien de la paix. Elle trouve son fondement dans les articles 1, 2, 3 et 4 de la Resolution 1325, qui recommande au Secretaire General et aux Etats membres de faire participer les femmes sur un pied d egalite avec les hommes aux instances decisionnelles dans le domaine de la paix et de la securite.',
        'context' => 'Cet axe qui comporte 3 objectifs vise a augmenter la participation des femmes a tous les niveaux de prise de decision dans les processus de paix et de securite.',
        'objectives' => [
            [
                'title' => 'Objectif 1',
                'lead' => 'Accroitre a 40% la participation des femmes et filles dans les instances de prise de decisions dans les mecanismes et initiatives de paix.',
                'detail' => 'Au cours de la periode de mise en oeuvre du Plan d Action National de 2eme generation, les femmes ont represente une moyenne de 21% dans les mecanismes et initiatives de paix, notamment dans les provinces de l Est. Le relevement de cette representativite a 40% permettrait de renforcer la voix des femmes dans l adoption des resolutions issues du reglement des conflits.'
            ],
            [
                'title' => 'Objectif 2',
                'lead' => 'Accroitre de 20% le taux de participation des femmes et filles dans les services de securite.',
                'detail' => 'Le rapport d evaluation de la mise en oeuvre du plan d action national renseigne une moyenne de 3% pour la representativite des femmes dans les postes de service de securite. Avec une telle faible representativite, il est peu probable que les decisions prises integrent correctement les besoins specifiques des femmes.'
            ],
            [
                'title' => 'Objectif 3',
                'lead' => 'Accroitre a 35% le taux de participation des femmes et des filles dans les instances de prise de decision des institutions publiques et privees au niveau national, a 20% aux niveaux provincial et local et a 40% au sein de la magistrature.',
                'detail' => 'Le rapport d evaluation du PAN 2 revele que les femmes occupent actuellement 33% des postes au sein du gouvernement national, 4% comme gouverneures des provinces, entre 10 et 20% au sein des gouvernements provinciaux, 13,6% a l Assemblee Nationale, 17,6% au Senat et 25% dans la magistrature. La presence des femmes dans la magistrature constitue un atout important dans la lutte contre l impunite des violations graves des droits des femmes.'
            ]
        ]
    ],
    'prevention' => [
        'title' => 'Axe Prevention',
        'label' => 'Prevention',
        'intro' => 'Prevenir les conflits, reduire les risques de violences et promouvoir l inclusion des femmes dans les mecanismes de prevention et de consolidation de la paix.',
        'summary' => 'Cet axe vise a prevenir la survenance des conflits et de toutes formes de violations des droits des femmes, des jeunes femmes et des petites filles avant, pendant et apres les conflits armes. Il vise aussi a promouvoir l inclusion des femmes dans les efforts de prevention des conflits, de resolution des conflits et de consolidation de la paix.',
        'context' => 'La prevention est pensee ici comme un travail institutionnel, communautaire et social combinant normes, sensibilisation, dispositifs d alerte et reduction des facteurs de risque.',
        'objectives' => [
            ['title' => 'Objectif 1', 'lead' => 'Assurer la vulgarisation de la Resolution 1325, d autres instruments juridiques pertinents de prevention des violences contre les femmes et les filles, ainsi que le PAN 1325 de la 3eme Generation du CSNU.', 'detail' => 'La diffusion de ces textes est essentielle pour leur appropriation par les institutions, les communautes et les organisations chargees de leur mise en oeuvre.'],
            ['title' => 'Objectif 2', 'lead' => 'Promouvoir la masculinite positive aupres des acteurs cles.', 'detail' => 'Cet objectif vise a transformer les comportements et representations sociales qui nourrissent les violences et les discriminations.'],
            ['title' => 'Objectif 3', 'lead' => 'Mettre en place des mecanismes communautaires d alerte precoce, de dialogue permanent et de resolution pacifique des conflits.', 'detail' => 'La prevention locale repose sur une veille active, la mediation et des espaces de dialogue inclusifs au plus pres des communautes.'],
            ['title' => 'Objectif 4', 'lead' => 'Contribuer a reduire le taux d enrolement des enfants-soldats au sein des groupes armes.', 'detail' => 'La protection de l enfance et l action communautaire sont ici integrees a la prevention des conflits.'],
            ['title' => 'Objectif 5', 'lead' => 'Renforcer le controle et la reduction de la circulation des armes legeres et de petit calibre.', 'detail' => 'La reduction des armes en circulation contribue directement a diminuer l intensite et la frequence des violences dans les zones fragiles.'],
            ['title' => 'Objectif 6', 'lead' => 'Lutter contre l exclusion des communautes dans l exploitation des ressources naturelles.', 'detail' => 'La prevention passe aussi par une gouvernance inclusive des ressources afin de reduire les tensions economiques et sociales.']
        ]
    ],
    'protection' => [
        'title' => 'Axe Protection',
        'label' => 'Protection',
        'intro' => 'Garantir la protection des droits des femmes, des filles et des personnes vulnerables pendant et apres les conflits.',
        'summary' => 'Cet axe vise a assurer aux femmes, filles et autres personnes vulnerables la protection de leurs droits pendant et apres les conflits, ainsi que le respect et la promotion de leurs droits.',
        'context' => 'L axe protection renforce a la fois la reponse institutionnelle, l acces a la justice et la lutte contre les violences graves liees aux conflits.',
        'objectives' => [
            ['title' => 'Objectif 1', 'lead' => 'Assurer le respect des droits des femmes et des filles pendant et apres les conflits.', 'detail' => 'Le cadre d action doit renforcer la prevention, la reponse et la garantie des droits fondamentaux dans les contextes de crise.'],
            ['title' => 'Objectif 2', 'lead' => 'Lutter contre l impunite des violences sexuelles et violences basees sur le genre faites aux femmes et filles pendant et apres les conflits armes.', 'detail' => 'La justice, la prise en charge des survivantes et la redevabilite des auteurs constituent des leviers centraux de cet objectif.'],
            ['title' => 'Objectif 3', 'lead' => 'Appuyer la lutte contre la traite des personnes.', 'detail' => 'La protection contre les reseaux de traite et d exploitation doit etre renforcee en lien avec les mecanismes judiciaires et communautaires.']
        ]
    ],
    'relevement' => [
        'title' => 'Axe Relevement',
        'label' => 'Relevement',
        'intro' => 'Integrer le genre dans la reconstruction et soutenir l autonomisation socio-economique des femmes affectees par les conflits.',
        'summary' => 'L axe relevement vise a integrer la dimension genre dans les projets de reconstruction pendant et apres les conflits, en vue d assurer l autonomisation socio-economique des femmes victimes de conflits pour le retablissement de la stabilite et d une paix durable.',
        'context' => 'Le relevement ne se limite pas a la reconstruction materielle. Il concerne aussi l acces aux opportunites, aux ressources et aux politiques publiques favorables a la resilience des femmes et des filles.',
        'objectives' => [
            ['title' => 'Objectif 1', 'lead' => 'Poursuivre l integration de la dimension genre dans la gestion des politiques et programmes de relevement.', 'detail' => 'Les programmes de relevement doivent integrer des leur conception les besoins differencies des femmes, des filles et des autres groupes vulnerables.'],
            ['title' => 'Objectif 2', 'lead' => 'Assurer l autonomisation socio-economique des femmes et des filles victimes des conflits.', 'detail' => 'Le soutien aux activites generatrices de revenus, a la formation et a l acces aux services de base est un levier majeur pour restaurer l autonomie durable.'],
            ['title' => 'Objectif 3', 'lead' => 'Mobiliser les ressources financieres necessaires en faveur des politiques et programmes de relevement post conflit au niveau national et provincial.', 'detail' => 'La perennite des actions de relevement depend d un financement structure, visible et aligne avec les priorites definies dans le PAN 1325.']
        ]
    ],
    'gestion' => [
        'title' => 'Axe Gestion des Conflits Emergents et Aide Humanitaire',
        'label' => 'Gestion',
        'intro' => 'Structurer une reponse adaptee aux nouveaux risques, aux urgences humanitaires et aux formes emergentes de violence.',
        'summary' => 'Cet axe developpe les objectifs relatifs a la gestion des conflits emergents, a la reponse humanitaire, a la lutte contre le blanchiment des capitaux et la criminalite urbaine, ainsi qu a la protection contre le cyber-harcelement et la traite des personnes.',
        'context' => 'Il complete les autres axes en prenant en compte les dynamiques nouvelles qui fragilisent la securite des femmes et des filles dans les contextes contemporains.',
        'objectives' => [
            ['title' => 'Objectif 1', 'lead' => 'Renforcer la reponse aux conflits emergents et aux situations humanitaires qui affectent les femmes et les filles.', 'detail' => 'La reponse humanitaire doit rester sensible au genre, rapide et coordonnee avec les mecanismes de protection.'],
            ['title' => 'Objectif 2', 'lead' => 'Prendre en compte les nouvelles formes de criminalite et de violence dans les politiques de securite humaine.', 'detail' => 'Cet objectif elargit le champ d action aux risques urbains, financiers et numeriques qui touchent de maniere differenciee les femmes et les filles.']
        ]
    ]
];

$aliases = [
    'relvement' => 'relevement',
    'relevement' => 'relevement'
];

$requestedAxe = strtolower(trim((string) ($_GET['axe'] ?? '')));
$selectedKey = null;

if ($requestedAxe !== '') {
    $normalizedKey = $aliases[$requestedAxe] ?? $requestedAxe;
    if (isset($axes[$normalizedKey])) {
        $selectedKey = $normalizedKey;
    }
}

$selectedAxis = $selectedKey !== null ? $axes[$selectedKey] : null;
$selectedObjectives = $selectedAxis['objectives'] ?? [];

$SKIP_PAGE_TITLE = true;
require_once $headerPath;
?>

<section class="site-page-hero">
  <div class="container">
    <div class="site-page-heading">
      <span><?= $selectedAxis ? 'Axe stratégique' : 'Axes stratégiques' ?></span>
      <h1><?= $selectedAxis ? htmlspecialchars($selectedAxis['title']) : 'Axes strategiques du Plan d Action National 1325' ?></h1>
      <p class="lead"><?= $selectedAxis ? htmlspecialchars($selectedAxis['intro']) : 'Les cinq priorites du PAN 1325 structurent l action nationale autour de la participation, la prevention, la protection, le relevement et la reponse humanitaire.' ?></p>
    </div>
  </div>
</section>

<section class="axes-shell section">
  <div class="container">
    <div class="axes-topbar-card">
      <div>
        <span class="section-kicker">Plan d Action National</span>
        <h2>Priorites strategiques</h2>
        <p>Cadre de mise en oeuvre de l agenda Femmes, Paix et Securite en Republique Democratique du Congo.</p>
      </div>
      <div class="axes-actions">
        <a href="<?= htmlspecialchars($axesBaseUrl) ?>" class="btn btn-outline-primary">Voir l ensemble</a>
        <a href="<?= htmlspecialchars($printUrl) ?>" class="btn btn-primary" target="_blank" rel="noopener">Imprimer / Telecharger</a>
      </div>
    </div>

    <div class="axes-map-container">
      <div class="axes-map">
        <svg id="axesMap" width="100%" height="340" viewBox="0 0 1000 340" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Carte des axes">
          <a href="<?= htmlspecialchars($axesBaseUrl) ?>?axe=participation"><g class="<?= $selectedKey === 'participation' ? 'is-active' : '' ?>" data-axe="participation" transform="translate(100,170)"><circle class="node" r="60" /><foreignObject x="-60" y="-60" width="120" height="120"><div xmlns="http://www.w3.org/1999/xhtml" class="labelbox">Participation</div></foreignObject></g></a>
          <a href="<?= htmlspecialchars($axesBaseUrl) ?>?axe=prevention"><g class="<?= $selectedKey === 'prevention' ? 'is-active' : '' ?>" data-axe="prevention" transform="translate(300,80)"><rect class="node" x="-70" y="-40" width="140" height="80" rx="12" /><foreignObject x="-70" y="-40" width="140" height="80"><div xmlns="http://www.w3.org/1999/xhtml" class="labelbox">Prevention</div></foreignObject></g></a>
          <a href="<?= htmlspecialchars($axesBaseUrl) ?>?axe=protection"><g class="<?= $selectedKey === 'protection' ? 'is-active' : '' ?>" data-axe="protection" transform="translate(500,170)"><circle class="node" r="60" /><foreignObject x="-60" y="-60" width="120" height="120"><div xmlns="http://www.w3.org/1999/xhtml" class="labelbox">Protection</div></foreignObject></g></a>
          <a href="<?= htmlspecialchars($axesBaseUrl) ?>?axe=relevement"><g class="<?= $selectedKey === 'relevement' ? 'is-active' : '' ?>" data-axe="relevement" transform="translate(700,80)"><rect class="node" x="-70" y="-40" width="140" height="80" rx="12" /><foreignObject x="-70" y="-40" width="140" height="80"><div xmlns="http://www.w3.org/1999/xhtml" class="labelbox">Relevement</div></foreignObject></g></a>
          <a href="<?= htmlspecialchars($axesBaseUrl) ?>?axe=gestion"><g class="<?= $selectedKey === 'gestion' ? 'is-active' : '' ?>" data-axe="gestion" transform="translate(900,170)"><circle class="node" r="60" /><foreignObject x="-60" y="-60" width="120" height="120"><div xmlns="http://www.w3.org/1999/xhtml" class="labelbox">Gestion</div></foreignObject></g></a>
          <line x1="160" y1="170" x2="240" y2="100" stroke="#d7e4f2" stroke-width="4" />
          <line x1="340" y1="100" x2="420" y2="170" stroke="#d7e4f2" stroke-width="4" />
          <line x1="560" y1="170" x2="640" y2="100" stroke="#d7e4f2" stroke-width="4" />
          <line x1="760" y1="100" x2="840" y2="170" stroke="#d7e4f2" stroke-width="4" />
        </svg>
      </div>
    </div>

    <?php if ($selectedAxis): ?>
      <div class="axes-detail-layout">
        <aside class="axes-sidebar-card">
          <span class="section-kicker">Axes disponibles</span>
          <div class="axes-sidebar-list">
            <?php foreach ($axes as $key => $axis): ?>
              <a class="axes-sidebar-link <?= $selectedKey === $key ? 'is-active' : '' ?>" href="<?= htmlspecialchars($axesBaseUrl) ?>?axe=<?= htmlspecialchars($key) ?>">
                <strong><?= htmlspecialchars($axis['label']) ?></strong>
                <span><?= count($axis['objectives']) ?> objectifs</span>
              </a>
            <?php endforeach; ?>
          </div>
        </aside>

        <article class="axes-detail">
          <span class="section-kicker">Detail de l axe</span>
          <h2><?= htmlspecialchars($selectedAxis['title']) ?></h2>
          <p><?= htmlspecialchars($selectedAxis['summary']) ?></p>
          <div class="axes-context-panel">
            <strong>Lecture strategique</strong>
            <p><?= htmlspecialchars($selectedAxis['context']) ?></p>
          </div>

          <div class="objectives">
            <h3>Objectifs operationnels</h3>
            <ul>
              <?php foreach ($selectedObjectives as $objective): ?>
                <li><?= htmlspecialchars($objective['lead']) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>

          <div class="axes-objective-grid">
            <?php foreach ($selectedObjectives as $objective): ?>
              <article class="objective-card">
                <span class="objective-eyebrow"><?= htmlspecialchars($objective['title']) ?></span>
                <h3><?= htmlspecialchars($objective['lead']) ?></h3>
                <p><?= htmlspecialchars($objective['detail']) ?></p>
              </article>
            <?php endforeach; ?>
          </div>
        </article>
      </div>
    <?php else: ?>
      <div class="axes-grid">
        <?php foreach ($axes as $key => $axis): ?>
          <article class="axes-card">
            <div class="card-icon"><?= strtoupper(substr($axis['label'], 0, 1)) ?></div>
            <div class="card-content">
              <h3 class="card-title"><?= htmlspecialchars($axis['title']) ?></h3>
              <p class="card-description"><?= htmlspecialchars($axis['intro']) ?></p>
              <div class="card-actions">
                <a class="btn btn-primary" href="<?= htmlspecialchars($axesBaseUrl) ?>?axe=<?= htmlspecialchars($key) ?>">Voir le detail</a>
              </div>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require_once $footerPath; ?>
