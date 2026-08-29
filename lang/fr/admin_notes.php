<?php

/**
 * Notes d'aide affichées en haut de chaque section de l'administration.
 * Chaque clé doit exister à l'identique dans lang/ar/admin_notes.php.
 */

return [

    'panel' => [
        'title' => 'À propos de cette page',
        'language' => 'Langue de la note',
    ],

    'dashboard' => [
        'heading' => 'Tableau de bord',
        'body' => [
            'Vue d\'ensemble de l\'activité : les demandes de devis reçues, leur avancement et les derniers messages arrivés du site.',
            'Cliquez sur une demande pour l\'ouvrir. Le menu de gauche donne accès à toutes les sections.',
        ],
    ],

    'quotes' => [
        'heading' => 'Les devis',
        'body' => [
            'Toutes les demandes envoyées depuis le site arrivent ici, ainsi que les devis que vous créez à la main.',
            'Le parcours est le suivant : appelez le client, chiffrez le devis en ajoutant les lignes et les dimensions, puis marquez-le comme envoyé — un numéro lui est attribué automatiquement. Vous pouvez ensuite télécharger le PDF ou le fichier Excel à envoyer au client.',
            'Les onglets en haut filtrent par étape, pour voir d\'un coup d\'œil ce qu\'il reste à traiter.',
        ],
    ],

    'invoices' => [
        'heading' => 'Les factures',
        'body' => [
            'Les factures reprennent les lignes et les montants d\'un devis accepté. Créez-les depuis le devis concerné plutôt qu\'à partir de zéro : les informations du client et les totaux sont alors recopiés automatiquement.',
            'Une facture reste modifiable tant qu\'elle est en brouillon. Marquez-la comme envoyée puis comme payée au fil des règlements.',
        ],
    ],

    'services' => [
        'heading' => 'Les prestations',
        'body' => [
            'Les 9 prestations présentées sur le site : menuiserie, portes, fenêtres, vérandas, etc. Elles alimentent la page d\'accueil, la page Services, le pied de page et le formulaire de devis.',
            'Chaque prestation se saisit en trois langues (français, anglais, arabe). Si une traduction manque, le français est affiché à la place.',
        ],
    ],

    'projects' => [
        'heading' => 'Les réalisations',
        'body' => [
            'Vos chantiers terminés, avec photos, présentés sur la page Réalisations du site.',
            'Cette page n\'apparaît sur le site que si vous l\'activez dans Paramètres du site. Ajoutez d\'abord quelques projets, puis activez-la.',
        ],
    ],

    'project_types' => [
        'heading' => 'Les types de projet',
        'body' => [
            'Les catégories qui servent à classer vos réalisations et à filtrer la galerie du site.',
            'Créez-les avant d\'ajouter des projets : chaque réalisation se rattache à un type.',
        ],
    ],

    'testimonials' => [
        'heading' => 'Les témoignages',
        'body' => [
            'Les avis de clients affichés sur le site. Saisissez le nom, la ville et le commentaire.',
            'Pensez à demander l\'accord du client avant de publier son témoignage.',
        ],
    ],

    'faqs' => [
        'heading' => 'Les questions fréquentes',
        'body' => [
            'Les questions et réponses affichées sur la page Questions fréquentes du site.',
            'Une bonne FAQ réduit les appels : répondez aux questions de délais, de prix et de garantie que l\'on vous pose le plus souvent.',
        ],
    ],

    'hero_slides' => [
        'heading' => 'Les slides d\'accueil',
        'body' => [
            'Les grandes images qui défilent en haut de la page d\'accueil, avec leur titre et leur bouton.',
            'Utilisez des photos larges et de bonne qualité. Deux à quatre slides suffisent : au-delà, les visiteurs ne les voient plus.',
        ],
    ],

    'chatbot_flows' => [
        'heading' => 'Les réponses du chatbot',
        'body' => [
            'Les questions et les réponses de l\'assistant qui apparaît en bas à droite du site.',
            'Chaque réponse peut proposer des boutons de suivi, pour guider le visiteur jusqu\'à la demande de devis.',
        ],
    ],

    'settings' => [
        'heading' => 'Les paramètres du site',
        'body' => [
            'Les informations de l\'entreprise, les textes de la page À propos, les coordonnées et le référencement.',
            'L\'onglet « Pages & visibilité » permet d\'afficher ou de masquer des sections entières, comme la page Réalisations ou la facturation.',
            'Les modifications sont visibles sur le site dès l\'enregistrement.',
        ],
    ],

];
