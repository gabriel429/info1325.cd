<?php
require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
// Minimal printable page: reuse header for styles then content and auto-print
?><!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Axes - Impression</title>
  <link rel="stylesheet" href="<?= CSS_DIR ?>bootstrap.min.css">
  <style>
    body { font-family: Poppins, Arial, sans-serif; color:#222; }
    .container { max-width:900px; margin:20px auto; }
    h1 { color:#0d6efd; }
    h3 { color:#0d6efd; }
    @media print { .no-print { display:none; } }
  </style>
</head>
<body>
  <div class="container">
    <div class="no-print mb-3">
      <button onclick="window.print()" class="btn btn-primary">Imprimer / Enregistrer en PDF</button>
      <a href="<?= BASE_URL ?>pagesweb/axes.php" class="btn btn-outline-secondary">Retour</a>
    </div>

    <h1>AXES STRATÉGIQUES DU PLAN D’ACTION NATIONAL 1325 DE LA 3ème GÉNÉRATION</h1>

      <!-- Small printable map with clickable nodes (clickable when viewed in browser before printing) -->
      <div style="text-align:center;margin:18px 0;">
        <svg id="axesMapPrint" width="100%" height="180" viewBox="0 0 1000 180" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Carte des axes - impression">
          <defs>
            <style>
              .node { cursor: pointer; fill: #ffffff; stroke: #0d6efd; stroke-width: 2px; }
              .node:hover { fill:#0d6efd; }
              g.clicked div { color: #0d6efd !important; }
              /* styling for HTML labels inside foreignObject */
              .labelbox { width:100%;height:100%;display:flex;align-items:center;justify-content:center;padding:4px;color:#000;text-align:justify;font-family: Poppins, Arial, sans-serif;font-size:12px;line-height:1.05; }
              a { cursor: pointer; }
            </style>
          </defs>
          <a href="<?= BASE_URL ?>pagesweb/axes.php?axe=participation"><g id="p" data-axe="participation" transform="translate(100,90)"><circle class="node" r="40" /><foreignObject x="-40" y="-40" width="80" height="80"><div xmlns="http://www.w3.org/1999/xhtml" class="labelbox">Participation</div></foreignObject></g></a>
          <a href="<?= BASE_URL ?>pagesweb/axes.php?axe=prevention"><g id="pv" data-axe="prevention" transform="translate(300,45)"><rect class="node" x="-60" y="-30" width="120" height="60" rx="8" /><foreignObject x="-60" y="-30" width="120" height="60"><div xmlns="http://www.w3.org/1999/xhtml" class="labelbox">Prévention</div></foreignObject></g></a>
          <a href="<?= BASE_URL ?>pagesweb/axes.php?axe=protection"><g id="pr" data-axe="protection" transform="translate(500,90)"><circle class="node" r="40" /><foreignObject x="-40" y="-40" width="80" height="80"><div xmlns="http://www.w3.org/1999/xhtml" class="labelbox">Protection</div></foreignObject></g></a>
          <a href="<?= BASE_URL ?>pagesweb/axes.php?axe=relevement"><g id="rl" data-axe="relevement" transform="translate(700,45)"><rect class="node" x="-60" y="-30" width="120" height="60" rx="8" /><foreignObject x="-60" y="-30" width="120" height="60"><div xmlns="http://www.w3.org/1999/xhtml" class="labelbox">Relèvement</div></foreignObject></g></a>
          <a href="<?= BASE_URL ?>pagesweb/axes.php?axe=gestion"><g id="gs" data-axe="gestion" transform="translate(900,90)"><circle class="node" r="40" /><foreignObject x="-40" y="-40" width="80" height="80"><div xmlns="http://www.w3.org/1999/xhtml" class="labelbox">Gestion</div></foreignObject></g></a>
        </svg>
      </div>

    <h3>III.1. AXE PARTICIPATION</h3>
    <p>La participation se veut non seulement la présence des femmes, mais plus encore une présence accrue, en ordre utile et significative dans les instances de prise des décisions et dans les mécanismes de maintien de la paix. Elle trouve son fondement dans les articles 1, 2, 3 et 4 de la Résolution 1325, qui recommande au Secrétaire Général et aux États membres de « faire participer les femmes sur un pied d’égalité que les hommes aux instances décisionnelles dans le domaine de la paix et la sécurité, ainsi que la participation égale des femmes et des hommes dans toutes les négociations conduisant à la consolidation de la paix.</p>

    <p>Cet axe qui comporte 3 objectifs vise à augmenter la participation des femmes à tous les niveaux de prise de décision dans les processus de paix et de sécurité.</p>

    <h5>Objectif 1</h5>
    <p>Accroître à 40% la participation des femmes et filles dans les instances de prise de décisions dans les mécanismes et initiatives de paix.</p>

    <h5>Objectif 2</h5>
    <p>Accroître de 20% le taux de participation des femmes et filles dans les services de sécurité.</p>

    <h5>Objectif 3</h5>
    <p>Accroître à 35% le taux de participation des femmes et des filles dans les instances de prise de décision des institutions publiques et privées au niveau national, à 20% aux niveaux provincial et local et à 40% au sein de la magistrature.</p>

    <hr>
    <h3>III.2. AXE PRÉVENTION</h3>
    <p>Cet axe vise à prévenir la survenance des conflits et de toutes formes de violations des droits des femmes, des jeunes femmes et des petites filles avant, pendant et après les conflits armés, et à promouvoir l’inclusion des femmes dans les efforts de prévention des conflits, de résolution des conflits et de consolidation de la paix, en reconnaissant leur rôle crucial dans ces domaines.</p>

    <h5>Objectif 1</h5>
    <p>Assurer la vulgarisation de la Résolution 1325, d’autres instruments juridiques pertinents de prévention des violences contre les femmes et les filles, ainsi que le PAN 1325 de la 3ème Génération du CSNU.</p>

    <h5>Objectif 2</h5>
    <p>Promouvoir Masculinité Positive auprès des acteurs clés.</p>

    <h5>Objectif 3</h5>
    <p>Mettre en place des mécanismes communautaires d’alerte précoce, de dialogue permanent et de résolution pacifique des conflits.</p>

    <h5>Objectif 4</h5>
    <p>Contribuer à réduire le taux d’enrôlement des enfants-soldats au sein des groupes armés.</p>

    <h5>Objectif 5</h5>
    <p>Renforcer le contrôle et la réduction de la circulation des armes légères et de petit calibre.</p>

    <h5>Objectif 6</h5>
    <p>Lutter contre l’exclusion des communautés dans l’exploitation des ressources naturelles.</p>

    <hr>
    <h3>III.3. AXE PROTECTION</h3>
    <p>Cet axe vise à assurer aux femmes, filles et autres personnes vulnérables la protection de leurs droits pendant et après les conflits, ainsi que le respect et la promotion de leurs droits.</p>

    <h5>Objectif 1</h5>
    <p>Assurer le respect des droits des femmes et des filles pendant et après les conflits.</p>

    <h5>Objectif 2</h5>
    <p>Lutter contre l’impunité des violences sexuelles et violences basées sur le genre faites aux femmes et filles pendant et après les conflits armés.</p>

    <h5>Objectif 3</h5>
    <p>Appuyer la lutte contre la traite des personnes.</p>

    <hr>
    <h3>III.4. AXE RELÈVEMENT</h3>
    <p>L’axe relèvement vise à intégrer la dimension genre dans les projets de reconstruction pendant et après les conflits, en vue d’assurer l’autonomisation socio-économique des femmes victimes de conflits pour le rétablissement de la stabilité et d’une paix durable.</p>

    <h5>Objectif 1</h5>
    <p>Poursuivre l’intégration de la dimension genre dans la gestion des politiques et programmes de relèvement.</p>

    <h5>Objectif 2</h5>
    <p>Assurer l’autonomisation socio-économique des femmes et des filles victimes des conflits.</p>

    <h5>Objectif 3</h5>
    <p>Mobiliser des ressources financières nécessaires en faveur des politiques et programmes de relèvement post conflit au niveau national et provincial.</p>

    <hr>
    <h3>III.5. AXE GESTION DES CONFLITS ÉMERGENTS ET L’AIDE HUMANITAIRE</h3>
    <p>Le texte développe les objectifs relatifs à la gestion des conflits émergents, la réponse humanitaire, la lutte contre le blanchiment des capitaux et la criminalité urbaine, ainsi que la protection contre le cyber-harcèlement et la traite des personnes.</p>

  </div>
  <script>
    // mark node as clicked based on ?axe= query so printed view can reflect selection
    (function(){
      try{
        const params = new URLSearchParams(window.location.search);
        const axe = (params.get('axe')||'').toLowerCase();
        if (!axe) return;
        const map = document.getElementById('axesMapPrint');
        if (!map) return;
        const node = map.querySelector('g[data-axe="'+axe+'"]');
        if (node) node.classList.add('clicked');
      }catch(e){console.warn(e)}
    })();
  </script>
</body>
</html>
