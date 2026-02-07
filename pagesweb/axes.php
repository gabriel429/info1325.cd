<?php
require_once __DIR__ . '/../configUrl.php';
require_once __DIR__ . '/../defConstLiens.php';
require_once $dateDbConnect; // connexion PDO si besoin

// Header
require_once $headerPath;
?>

<!-- AXES Page -->
<section class="section" style="padding:20px 0;background:#f8f9fa;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="card shadow-sm">
          <div class="card-body p-5">
            <div class="d-flex align-items-start mb-4">
              <h1 class="mb-0 text-primary me-3">AXES STRATÉGIQUES DU PLAN D’ACTION NATIONAL 1325</h1>
              <div class="ms-auto">
                <a href="<?= BASE_URL ?>pagesweb/axes_print.php" class="btn btn-outline-secondary me-2 text-white" target="_blank">Imprimer / Télécharger</a>
              </div>
            </div>

            <p class="lead">Cliquez sur une zone de la carte pour afficher l'axe correspondant.</p>

            <!-- Carte interactive simple (SVG) -->
            <div class="my-4" style="text-align:center;">
              <svg id="axesMap" width="100%" height="340" viewBox="0 0 1000 340" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Carte des axes">
                <defs>
                  <style>
                    .node { cursor: pointer; fill: #ffffff; stroke: #0d6efd; stroke-width: 3px; transition: transform 180ms ease, filter 180ms ease, fill 180ms ease; transform-box: fill-box; transform-origin: center; }
                    .node:hover { transform: scale(1.06); filter: drop-shadow(0 8px 12px rgba(13,110,253,0.12)); fill: #0d6efd; }
                    /* fallback for any remaining text elements */
                    .label { font-family: 'Poppins', Arial, sans-serif; font-size:16px; fill:#fff; pointer-events:none; }
                    a { cursor: pointer; }
                    /* When hovering the group, make the HTML label text white for contrast */
                    g:hover div { color: #ffffff !important; }
                    /* clicked state: make label text blue */
                    g.clicked div { color: #0d6efd !important; }
                  </style>
                </defs>
                <!-- nodes positioned horizontally (labels use foreignObject for justified white text) -->
                <a href="<?= BASE_URL ?>pagesweb/axes.php?axe=participation"><g id="node-participation" data-axe="participation" transform="translate(100,170)"><circle class="node" r="60" />
                    <foreignObject x="-60" y="-60" width="120" height="120">
                      <div xmlns="http://www.w3.org/1999/xhtml" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;padding:6px;color:#000;text-align:justify;font-family:'Poppins',Arial,sans-serif;font-size:14px;line-height:1.1;">
                        Participation
                      </div>
                    </foreignObject>
                </g></a>
                <a href="<?= BASE_URL ?>pagesweb/axes.php?axe=prevention"><g id="node-prevention" data-axe="prevention" transform="translate(300,80)"><rect class="node" x="-70" y="-40" width="140" height="80" rx="12" />
                    <foreignObject x="-70" y="-40" width="140" height="80">
                      <div xmlns="http://www.w3.org/1999/xhtml" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;padding:6px;color:#000;text-align:justify;font-family:'Poppins',Arial,sans-serif;font-size:14px;line-height:1.1;">
                        Prévention
                      </div>
                    </foreignObject>
                </g></a>
                <a href="<?= BASE_URL ?>pagesweb/axes.php?axe=protection"><g id="node-protection" data-axe="protection" transform="translate(500,170)"><circle class="node" r="60" />
                    <foreignObject x="-60" y="-60" width="120" height="120">
                      <div xmlns="http://www.w3.org/1999/xhtml" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;padding:6px;color:#000;text-align:justify;font-family:'Poppins',Arial,sans-serif;font-size:14px;line-height:1.1;">
                        Protection
                      </div>
                    </foreignObject>
                </g></a>
                <a href="<?= BASE_URL ?>pagesweb/axes.php?axe=relevement"><g id="node-relevement" data-axe="relevement" transform="translate(700,80)"><rect class="node" x="-70" y="-40" width="140" height="80" rx="12" />
                    <foreignObject x="-70" y="-40" width="140" height="80">
                      <div xmlns="http://www.w3.org/1999/xhtml" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;padding:6px;color:#000;text-align:justify;font-family:'Poppins',Arial,sans-serif;font-size:14px;line-height:1.1;">
                        Relèvement
                      </div>
                    </foreignObject>
                </g></a>
                <a href="<?= BASE_URL ?>pagesweb/axes.php?axe=gestion"><g id="node-gestion" data-axe="gestion" transform="translate(900,170)"><circle class="node" r="60" />
                    <foreignObject x="-60" y="-60" width="120" height="120">
                      <div xmlns="http://www.w3.org/1999/xhtml" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;padding:6px;color:#000;text-align:justify;font-family:'Poppins',Arial,sans-serif;font-size:14px;line-height:1.1;">
                        Gestion
                      </div>
                    </foreignObject>
                </g></a>
                <!-- connector lines -->
                <line x1="160" y1="170" x2="240" y2="100" stroke="#dee2e6" stroke-width="4" />
                <line x1="340" y1="100" x2="420" y2="170" stroke="#dee2e6" stroke-width="4" />
                <line x1="560" y1="170" x2="640" y2="100" stroke="#dee2e6" stroke-width="4" />
                <line x1="760" y1="100" x2="840" y2="170" stroke="#dee2e6" stroke-width="4" />
              </svg>
            </div>

            <hr>

            <h3 class="mt-4" id="participation"><strong>AXE PARTICIPATION</strong></h3>

            <p>La participation se veut non seulement la présence des femmes, mais plus encore une présence accrue, en ordre utile et significative dans les instances de prise des décisions et dans les mécanismes de maintien de la paix. Elle trouve son fondement dans les articles 1, 2, 3 et 4 de la Résolution 1325, qui recommande au Secrétaire Général et aux États membres de « faire participer les femmes sur un pied d’égalité que les hommes aux instances décisionnelles dans le domaine de la paix et la sécurité, ainsi que la participation égale des femmes et des hommes dans toutes les négociations conduisant à la consolidation de la paix.</p>

            <p>Cet axe qui comporte 3 objectifs vise à augmenter la participation des femmes à tous les niveaux de prise de décision dans les processus de paix et de sécurité.</p>

            <h5>Objectif 1</h5>
            <p>Accroître à 40% la participation des femmes et filles dans les instances de prise de décisions dans les mécanismes et initiatives de paix.</p>

            <p>Au cours de la période de mise en oeuvre du Plan d’Action National de 2ème génération, les informations renseignent que les femmes ont représenté une moyenne de 21% dans les mécanismes et initiative de paix, notamment dans les provinces de l’Est. Il est possible de travailler pour porter cette représentativité à 40%, afin d’accroître la propension à faire entendre la voix des femmes lors de l’adoption des résolutions issues des règlements des conflits.</p>

            <h5>Objectif 2</h5>
            <p>Accroître de 20% le taux de participation des femmes et filles dans les services de sécurité.</p>

            <p>Le rapport d’évaluation de la mise en oeuvre du plan d’action National renseigne une moyenne de 3 % pour la représentativité des femmes dans les postes de service de sécurité. Avec une telle faible représentativité, il est peu probable que des décisions prises puissent prendre en compte les besoins spécifiques des femmes.</p>

            <h5>Objectif 3</h5>
            <p>Accroître à 35% le taux de participation des femmes et des filles dans les instances de prise de décision des institutions publiques et privées au niveau national, à 20% aux niveaux provincial et local et à 40% au sein de la magistrature.</p>

            <p>Le rapport d’évaluation du PAN 2 révèle que les femmes occupent actuellement 33% des postes au sein du gouvernement national, 4% comme gouverneures des provinces, entre 10 et 20% au sein des gouvernements provinciaux, 13,6% à l’Assemblée Nationale, 17,6% au Sénat et 25% dans la magistrature, sachant que la présence des femmes dans la magistrature constitue un atout important dans la lutte contre l’impunité des violations graves des droits des femmes.</p>

            <hr>
            <h3 class="mt-4"><strong>AXE PRÉVENTION</strong></h3>
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
            <h3 class="mt-4"><strong>AXE PROTECTION</strong></h3>
            <p>Cet axe vise à assurer aux femmes, filles et autres personnes vulnérables la protection de leurs droits pendant et après les conflits, ainsi que le respect et la promotion de leurs droits.</p>

            <h5>Objectif 1</h5>
            <p>Assurer le respect des droits des femmes et des filles pendant et après les conflits.</p>

            <h5>Objectif 2</h5>
            <p>Lutter contre l’impunité des violences sexuelles et violences basées sur le genre faites aux femmes et filles pendant et après les conflits armés.</p>

            <h5>Objectif 3</h5>
            <p>Appuyer la lutte contre la traite des personnes.</p>

            <hr>
            <h3 class="mt-4"><strong>AXE RELÈVEMENT</strong></h3>
            <p>L’axe relèvement vise à intégrer la dimension genre dans les projets de reconstruction pendant et après les conflits, en vue d’assurer l’autonomisation socio-économique des femmes victimes de conflits pour le rétablissement de la stabilité et d’une paix durable.</p>

            <h5>Objectif 1</h5>
            <p>Poursuivre l’intégration de la dimension genre dans la gestion des politiques et programmes de relèvement.</p>

            <h5>Objectif 2</h5>
            <p>Assurer l’autonomisation socio-économique des femmes et des filles victimes des conflits.</p>

            <h5>Objectif 3</h5>
            <p>Mobiliser des ressources financières nécessaires en faveur des politiques et programmes de relèvement post conflit au niveau national et provincial.</p>

            <hr>
            <h3 class="mt-4"><strong>AXE GESTION DES CONFLITS ÉMERGENTS ET L’AIDE HUMANITAIRE</strong></h3>
            <p>Le texte développe les objectifs relatifs à la gestion des conflits émergents, la réponse humanitaire, la lutte contre le blanchiment des capitaux et la criminalité urbaine, ainsi que la protection contre le cyber-harcèlement et la traite des personnes.</p>

            <!-- Retour button removed: navigation to map handled via header/compoAxe -->

          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once $footerPath; ?>

<style>
/* Styles supplémentaires pour axes.php */
.card-body h1 { font-family: 'Poppins', Arial, sans-serif; font-weight:700; }
.card-body p { line-height:1.6; color:#333; text-align:justify; text-justify:inter-word; hyphens:auto; }
.card-body h3 { color:#0d6efd; }
.highlight { background: linear-gradient(90deg, rgba(13,110,253,0.08), transparent); padding:6px 10px; border-radius:4px; }
@media (max-width:768px){ svg#axesMap { height:260px; } }
</style>

<script>
// Smooth scroll to axe if ?axe=... present and simple print button behavior
document.addEventListener('DOMContentLoaded', function(){
  try{
    const params = new URLSearchParams(window.location.search);
    const axe = (params.get('axe') || '').toLowerCase();

    // helper to mark svg node as clicked (blue text)
    function markNode(axeName){
      try{
        document.querySelectorAll('#axesMap g').forEach(g => g.classList.remove('clicked'));
        if (!axeName) return;
        const node = document.querySelector('#axesMap g[data-axe="' + axeName + '"]');
        if (node) node.classList.add('clicked');
      }catch(e){console.warn(e)}
    }

    // if ?axe= present, scroll to the section and mark node
    if (axe) {
      const idMap = {
        'participation':'participation',
        'prevention':'prevention',
        'protection':'protection',
        'relevement':'relevement',
        'gestion':'gestion'
      };
      const target = document.getElementById(idMap[axe]);
      markNode(axe);
      if (target) {
        setTimeout(function(){
          target.scrollIntoView({behavior:'smooth', block:'start'});
          target.classList.add('highlight');
          setTimeout(()=> target.classList.remove('highlight'),4000);
        },180);
      }
    }

    // Add click handlers to svg anchors to show immediate clicked state
    document.querySelectorAll('#axesMap a').forEach(a => {
      a.addEventListener('click', function(){
        try{
          // find child g and mark
          const g = this.querySelector('g[data-axe]');
          if (g){
            document.querySelectorAll('#axesMap g').forEach(x=>x.classList.remove('clicked'));
            g.classList.add('clicked');
          }
        }catch(e){/* ignore */}
        // allow navigation to proceed
      });
    });

    // download button removed — no JS action required
  } catch(e) { console.warn(e); }
});
</script>
