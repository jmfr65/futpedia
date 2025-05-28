<?php
// languages/fr.php - Fichier de langue française

if (defined('FUTPEDIA_ACCESS') && !FUTPEDIA_ACCESS && php_sapi_name() !== 'cli') {
    die('Accès direct non autorisé au fichier de langue.');
}

return [
    // Général
    'site_name' => 'Futpedia',
    'toggle_navigation' => 'Basculer la navigation',
    'home' => 'Accueil',
    'login' => 'Connexion',
    'register' => 'Inscription',
    'logout' => 'Déconnexion',
    'my_profile' => 'Mon Profil',
    'admin_panel' => 'Panneau Admin',
    'search' => 'Rechercher',
    'go' => 'Aller',
    'yes' => 'Oui',
    'no' => 'Non',
    'save' => 'Enregistrer',
    'edit' => 'Modifier',
    'delete' => 'Supprimer',
    'cancel' => 'Annuler',
    'error' => 'Erreur',
    'success' => 'Succès',
    'page_not_found' => 'Page non trouvée',
    'oops_error_occurred' => 'Oups ! Une erreur est survenue.',
    'welcome_message' => 'Bienvenue au Cœur de Futpedia !',
    'current_datetime_label' => 'Date et heure actuelles (formatées) :',
    'db_connection_ok' => 'La connexion à la base de données semble être configurée et fonctionner correctement.',
    'db_connection_error' => 'Erreur : DB_HOST est défini, mais l\'instance de la base de données n\'a pas été créée ou la connexion a échoué.',
    'under_construction_title' => 'Bienvenue chez Futpedia',
    'under_construction_message' => 'Ceci est le point d\'entrée principal de l\'application, maintenant avec un design de base.',
    'under_construction_info' => 'Bientôt, vous verrez ici du contenu dynamique sur le monde du football.',

    // Spécifique à l'en-tête/pied de page (exemples)
    'main_navigation' => 'Navigation principale',
    'copyright_notice' => '&copy; %year% %site_name%. Tous droits réservés.',

    // Formulaires (exemples)
    'username' => 'Nom d\'utilisateur',
    'password' => 'Mot de passe',
    'email' => 'Adresse e-mail',
    'remember_me' => 'Se souvenir de moi',
    
    // Messages flash (exemples)
    'flash_config_loaded_successfully' => 'Configuration et session chargées avec succès !',
    'flash_test_error_message' => 'Ceci est un message d\'erreur de test.',
];