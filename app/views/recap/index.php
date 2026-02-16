<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: #1e293b;">
            <i class="bi bi-clipboard-data me-2"></i>Récapitulation
        </h4>
        <p class="text-muted mb-0 small">
            Vue d'ensemble des besoins, dons et dispatches —
            <span id="last-update">Dernière mise à jour : -</span>
        </p>
    </div>
    <button type="button" class="btn btn-primary" onclick="actualiserDonnees()" id="btn-actualiser">
        <i class="bi bi-arrow-clockwise me-1"></i> Actualiser
    </button>
</div>

<!-- Cartes de résumé -->
<div class="row g-4 mb-4" id="cards-resume">
    <div class="col-md-4">
        <div class="card h-100 border-primary">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2"><i class="bi bi-card-checklist me-1"></i> Total Besoins</h6>
                <h3 class="fw-bold text-primary" id="total-besoins">-</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border-success">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2"><i class="bi bi-check-circle me-1"></i> Besoins Satisfaits</h6>
                <h3 class="fw-bold text-success" id="total-satisfait">-</h3>
                <div class="small text-muted">
                    <span id="detail-dispatch">Dispatch: -</span> | 
                    <span id="detail-achats">Achats: -</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 border-danger">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2"><i class="bi bi-exclamation-circle me-1"></i> Besoins Restants</h6>
                <h3 class="fw-bold text-danger" id="total-restant">-</h3>
            </div>
        </div>
    </div>
</div>

<!-- Barre de progression -->
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <span class="fw-semibold">Taux de couverture global</span>
            <span class="fw-bold" id="taux-couverture">-</span>
        </div>
        <div class="progress" style="height: 25px;">
            <div class="progress-bar bg-success" role="progressbar" id="progress-bar" style="width: 0%;">
                <span id="progress-text">0%</span>
            </div>
        </div>
    </div>
</div>

<!-- Détail par type de besoin -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h6 class="fw-bold mb-0"><i class="bi bi-tags me-2 text-primary"></i>Détail par type de besoin</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Catégorie</th>
                    <th class="text-end">Besoin total</th>
                    <th class="text-end">Dispatché</th>
                    <th class="text-end">Acheté</th>
                    <th class="text-end">Restant</th>
                    <th style="width: 150px;">Couverture</th>
                </tr>
            </thead>
            <tbody id="table-types">
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-arrow-clockwise"></i> Cliquez sur "Actualiser" pour charger les données
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Détail par ville -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h6 class="fw-bold mb-0"><i class="bi bi-building me-2 text-primary"></i>Détail par ville</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Ville</th>
                    <th>Région</th>
                    <th class="text-end">Besoin total</th>
                    <th class="text-end">Dispatché</th>
                    <th class="text-end">Acheté</th>
                    <th class="text-end">Restant</th>
                    <th style="width: 150px;">Couverture</th>
                </tr>
            </thead>
            <tbody id="table-villes">
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                        <i class="bi bi-arrow-clockwise"></i> Cliquez sur "Actualiser" pour charger les données
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
function formatAr(montant) {
    return new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(montant) + ' Ar';
}

function getCategorieLabel(cat) {
    const labels = { 'nature': 'En nature', 'materiau': 'Matériaux', 'argent': 'Argent' };
    return labels[cat] || cat;
}

function getCategorieBadge(cat) {
    const badges = { 'nature': 'bg-success', 'materiau': 'bg-warning text-dark', 'argent': 'bg-info text-dark' };
    return badges[cat] || 'bg-secondary';
}

function getProgressClass(taux) {
    if (taux >= 75) return 'bg-success';
    if (taux >= 40) return 'bg-warning';
    return 'bg-danger';
}

function actualiserDonnees() {
    const btn = document.getElementById('btn-actualiser');
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-clockwise spin me-1"></i> Chargement...';

    fetch('<?= base_url('/recap/data') ?>')
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                const data = result.data;

                // Mise à jour des cartes
                document.getElementById('total-besoins').textContent = formatAr(data.total_besoins);
                document.getElementById('total-satisfait').textContent = formatAr(data.total_satisfait);
                document.getElementById('total-restant').textContent = formatAr(data.total_restant);
                document.getElementById('detail-dispatch').textContent = 'Dispatch: ' + formatAr(data.total_dispatch);
                document.getElementById('detail-achats').textContent = 'Achats: ' + formatAr(data.total_achats);

                // Barre de progression
                const taux = data.taux_couverture;
                document.getElementById('taux-couverture').textContent = taux + '%';
                const progressBar = document.getElementById('progress-bar');
                progressBar.style.width = taux + '%';
                progressBar.textContent = taux + '%';
                progressBar.className = 'progress-bar ' + getProgressClass(taux);

                // Table des types
                let htmlTypes = '';
                if (data.details_types.length === 0) {
                    htmlTypes = '<tr><td colspan="7" class="text-center text-muted py-4">Aucune donnée</td></tr>';
                } else {
                    data.details_types.forEach(t => {
                        const restant = parseFloat(t.total_besoin_valeur) - parseFloat(t.total_dispatch_valeur) - parseFloat(t.total_achat_valeur);
                        const tauxType = t.total_besoin_valeur > 0 ? Math.round(((parseFloat(t.total_dispatch_valeur) + parseFloat(t.total_achat_valeur)) / parseFloat(t.total_besoin_valeur)) * 100) : 0;
                        htmlTypes += `
                            <tr>
                                <td class="fw-semibold">${t.nom}</td>
                                <td><span class="badge ${getCategorieBadge(t.categorie)}">${getCategorieLabel(t.categorie)}</span></td>
                                <td class="text-end">${formatAr(parseFloat(t.total_besoin_valeur))}</td>
                                <td class="text-end text-success">${formatAr(parseFloat(t.total_dispatch_valeur))}</td>
                                <td class="text-end text-info">${formatAr(parseFloat(t.total_achat_valeur))}</td>
                                <td class="text-end text-danger">${formatAr(restant > 0 ? restant : 0)}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 7px;">
                                            <div class="progress-bar ${getProgressClass(tauxType)}" style="width: ${tauxType}%"></div>
                                        </div>
                                        <small class="fw-bold text-muted" style="min-width: 38px;">${tauxType}%</small>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                }
                document.getElementById('table-types').innerHTML = htmlTypes;

                // Table des villes
                let htmlVilles = '';
                if (data.details_villes.length === 0) {
                    htmlVilles = '<tr><td colspan="7" class="text-center text-muted py-4">Aucune donnée</td></tr>';
                } else {
                    data.details_villes.forEach(v => {
                        const restant = parseFloat(v.total_besoin) - parseFloat(v.total_dispatch) - parseFloat(v.total_achat);
                        const tauxVille = v.total_besoin > 0 ? Math.round(((parseFloat(v.total_dispatch) + parseFloat(v.total_achat)) / parseFloat(v.total_besoin)) * 100) : 0;
                        htmlVilles += `
                            <tr>
                                <td class="fw-semibold">${v.ville}</td>
                                <td><span class="badge bg-light text-dark border">${v.region}</span></td>
                                <td class="text-end">${formatAr(parseFloat(v.total_besoin))}</td>
                                <td class="text-end text-success">${formatAr(parseFloat(v.total_dispatch))}</td>
                                <td class="text-end text-info">${formatAr(parseFloat(v.total_achat))}</td>
                                <td class="text-end text-danger">${formatAr(restant > 0 ? restant : 0)}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress flex-grow-1" style="height: 7px;">
                                            <div class="progress-bar ${getProgressClass(tauxVille)}" style="width: ${tauxVille}%"></div>
                                        </div>
                                        <small class="fw-bold text-muted" style="min-width: 38px;">${tauxVille}%</small>
                                    </div>
                                </td>
                            </tr>
                        `;
                    });
                }
                document.getElementById('table-villes').innerHTML = htmlVilles;

                // Mise à jour timestamp
                document.getElementById('last-update').textContent = 'Dernière mise à jour : ' + result.timestamp;
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors du chargement des données');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i> Actualiser';
        });
}

// Charger les données au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    actualiserDonnees();
});
</script>

<style>
.spin {
    animation: spin 1s linear infinite;
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<?php include __DIR__ . '/../layout/footer.php'; ?>
