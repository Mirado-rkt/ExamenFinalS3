<?php

namespace app\controllers;

use app\logic\DispatchLogic;
use flight\Engine;

class DispatchController
{
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    /**
     * Afficher l'état actuel du dispatch
     */
    public function index(): void
    {
        $db = $this->app->db();
        $simulation_mode = $this->app->request()->query->simulation ?? false;

        // Résumé par ville
        $villes = $db->fetchAll("
            SELECT 
                v.id, v.nom AS ville, r.nom AS region,
                COALESCE(bs.total_besoin, 0) AS total_besoin,
                COALESCE(ds.total_dispatch, 0) AS total_dispatch
            FROM ville v
            JOIN region r ON v.region_id = r.id
            LEFT JOIN (
                SELECT b.ville_id, SUM(b.quantite * tb.prix_unitaire) AS total_besoin
                FROM besoin b
                JOIN type_besoin tb ON b.type_besoin_id = tb.id
                GROUP BY b.ville_id
            ) bs ON bs.ville_id = v.id
            LEFT JOIN (
                SELECT b.ville_id, SUM(dp.quantite * tb.prix_unitaire) AS total_dispatch
                FROM dispatch dp
                JOIN besoin b ON dp.besoin_id = b.id
                JOIN type_besoin tb ON b.type_besoin_id = tb.id
                GROUP BY b.ville_id
            ) ds ON ds.ville_id = v.id
            WHERE bs.total_besoin > 0
            ORDER BY v.nom
        ");

        // Détails des dispatches récents
        $dispatches = $db->fetchAll("
            SELECT dp.*, 
                   b.quantite AS besoin_qte,
                   v.nom AS ville_nom,
                   tb.nom AS type_nom, tb.categorie, tb.prix_unitaire,
                   d.donateur,
                   (dp.quantite * tb.prix_unitaire) AS valeur
            FROM dispatch dp
            JOIN besoin b ON dp.besoin_id = b.id
            JOIN ville v ON b.ville_id = v.id
            JOIN type_besoin tb ON b.type_besoin_id = tb.id
            JOIN don_detail dd ON dp.don_detail_id = dd.id
            JOIN don d ON dd.don_id = d.id
            ORDER BY dp.date_dispatch DESC
            LIMIT 50
        ");

        $nb_dispatches = (int) $db->fetchField("SELECT COUNT(*) FROM dispatch");

        $this->app->render('dispatch/index', [
            'page_title'      => 'Dispatch des dons',
            'active_menu'     => 'dispatch',
            'villes'          => $villes,
            'dispatches'      => $dispatches,
            'nb_dispatches'   => $nb_dispatches,
            'simulation_mode' => $simulation_mode,
        ]);
    }

    /**
     * Simuler le dispatch : recalculer sans message de validation
     */
    public function simuler(): void
    {
        $db = $this->app->db();

        // Exécuter le dispatch (recalcul complet)
        DispatchLogic::executer($db);

        flash('success', 'Simulation du dispatch effectuée. Vérifiez les résultats ci-dessous, puis cliquez sur "Valider le dispatch" pour confirmer.');
        $this->app->redirect(base_url('/dispatch?simulation=1'));
    }

    /**
     * Initialiser/réinitialiser les données (dispatch et achats)
     */
    public function initialiser(): void
    {
        $db = $this->app->db();

        // Supprimer tous les dispatches et achats
        $db->runQuery("DELETE FROM dispatch");
        $db->runQuery("DELETE FROM achat");

        flash('success', 'Initialisation effectuée : tous les dispatches et achats ont été supprimés.');
        $this->app->redirect(base_url('/dispatch'));
    }

    /**
     * Valider le dispatch : confirmer l'application définitive
     */
    public function valider(): void
    {
        $db = $this->app->db();

        // Exécuter le dispatch (confirmer)
        DispatchLogic::executer($db);

        flash('success', 'Le dispatch a été validé et appliqué avec succès.');
        $this->app->redirect(base_url('/dispatch'));
    }
}
