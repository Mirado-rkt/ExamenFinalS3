<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1" style="color: #1e293b;">
            <i class="bi bi-exclamation-circle me-2"></i>Besoins restants
        </h4>
        <p class="text-muted mb-0 small">
            Besoins non encore satisfaits par les dons directs ou les achats
        </p>
    </div>
</div>

<!-- Filtre par ville -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= base_url('/besoins-restants') ?>" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="ville_id" class="form-label">Filtrer par ville</label>
                <select name="ville_id" id="ville_id" class="form-select">
                    <option value="0">-- Toutes les villes --</option>
                    <?php foreach ($villes as $v): ?>
                        <option value="<?= $v['id'] ?>" <?= $ville_id == $v['id'] ? 'selected' : '' ?>>
                            <?= e($v['nom']) ?> (<?= e($v['region_nom']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-filter me-1"></i> Filtrer
                </button>
            </div>
            <?php if ($ville_id > 0): ?>
            <div class="col-md-2">
                <a href="<?= base_url('/besoins-restants') ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-x-lg me-1"></i> Réinitialiser
                </a>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Résumé -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="alert alert-warning mb-0">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Total des besoins restants :</strong> <?= format_ar($total_restant) ?>
        </div>
    </div>
    <div class="col-md-6">
        <div class="alert alert-info mb-0">
            <i class="bi bi-cash me-2"></i>
            <strong>Dons en argent disponibles :</strong> <?= format_ar($dons_argent_disponibles) ?>
        </div>
    </div>
</div>

<!-- Tableau des besoins restants -->
<div class="card table-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0"><i class="bi bi-card-checklist me-2"></i>Liste des besoins non satisfaits</h6>
        <a href="<?= base_url('/achats') ?>" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-cart me-1"></i> Voir les achats effectués
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ville</th>
                    <th>Région</th>
                    <th>Type de besoin</th>
                    <th>Catégorie</th>
                    <th class="text-end">Qté totale</th>
                    <th class="text-end">Qté dispatchée</th>
                    <th class="text-end">Qté achetée</th>
                    <th class="text-end">Qté restante</th>
                    <th class="text-end">Valeur restante</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($besoins)): ?>
                <tr>
                    <td colspan="11" class="empty-state">
                        <i class="bi bi-check-circle text-success"></i>
                        <p class="text-success">Tous les besoins sont satisfaits !</p>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($besoins as $b): ?>
                    <tr>
                        <td class="text-muted"><?= $b['id'] ?></td>
                        <td class="fw-semibold"><?= e($b['ville_nom']) ?></td>
                        <td><span class="badge bg-light text-dark border"><?= e($b['region_nom']) ?></span></td>
                        <td><?= e($b['type_nom']) ?></td>
                        <td>
                            <span class="badge <?= categorie_badge($b['categorie']) ?>">
                                <?= categorie_label($b['categorie']) ?>
                            </span>
                        </td>
                        <td class="text-end"><?= format_nb((float) $b['quantite']) ?></td>
                        <td class="text-end text-success"><?= format_nb((float) $b['quantite_dispatchee']) ?></td>
                        <td class="text-end text-info"><?= format_nb((float) $b['quantite_achetee']) ?></td>
                        <td class="text-end fw-bold text-danger"><?= format_nb((float) $b['quantite_restante']) ?></td>
                        <td class="text-end fw-semibold"><?= format_ar((float) $b['valeur_restante']) ?></td>
                        <td class="text-end">
                            <?php if ($b['categorie'] !== 'argent'): ?>
                            <a href="<?= base_url('/achats/create?besoin_id=' . $b['id']) ?>" 
                               class="btn btn-sm btn-success" title="Acheter ce besoin">
                                <i class="bi bi-cart-plus"></i> Acheter
                            </a>
                            <?php else: ?>
                            <span class="text-muted small">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
