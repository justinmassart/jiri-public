<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Popup Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for popup translation.
    |
    */

    // General errors
    'unexpected_error' => 'Une erreur inattendue est survenue. Merci de réessayer plus tard.',
    'action_unauthorized' => 'Vous n’êtes pas autorisé à effectuer cette action.',

    // Auth popup
    'welcome' => 'Salut :firstname !',
    'welcome_contact' => 'Salut :firstname !<br/>Vous êtes connecté en tant qu’évaluateur.',

    // Recover password popup
    'recover_password_mail_sent' => 'Un mail pour récupérer votre mot de passe vous a été envoyé à l’adresse : :email.',
    'new_recover_token' => 'Un nouveau code de récupération vous a été envoyé.',
    'password_reset' => 'Votre mot de passe a bien été réinitialisé.',

    // Contacts popup
    'add_contact_success' => ':firstname :lastname a bien été ajouté.',
    'add_contact_error' => 'Une erreur est survenue lors de la création du contact.',
    'update_contact_success' => ':firstname :lastname a bien été mis à jour.',
    'update_contact_error' => 'Une erreur est survenue lors de la mise à jour du contact.',
    'delete_contact_success' => ':firstname :lastname a bien été supprimé.',
    'delete_contact_error' => 'Une erreur est survenue lors de la suppression du contact.',
    'contact_not_found_error' => ':firstname :lastname n’a pas été trouvé dans vos contacts.',
    'add_contact_to_jiri_success' => ':firstname :lastname a bien été ajouté et assigné au jury : :jiri.',
    'add_contact_to_jiri_error' => 'Une erreur est survenue lors de l’ajout du contact au jury.',

    // Jiris popup
    'add_jiri_success' => 'Le jury :name a bien été créé.',
    'add_jiri_error' => 'Une erreur est survenue lors de la création du jury.',
    'update_jiri_success' => 'Le jury :name a bien été mis à jour.',
    'update_jiri_error' => 'Une erreur est survenue lors de la mise à jour du jury.',
    'delete_jiri_success' => 'Le jury :name a bien été supprimé.',
    'delete_jiri_error' => 'Une erreur est survenue lors de la suppression du jury.',
    'jiri_not_found_error' => 'Le jury :name n’a pas été trouvé.',

    // Attendances popup
    'add_attendance_success' => 'La présence de :firstname :lastname a bien été ajoutée.',
    'add_attendance_error' => 'Une erreur est survenue lors de l’ajout de la présence.',
    'update_attendance_success' => 'La présence de :firstname :lastname a bien été mise à jour.',
    'update_attendance_error' => 'Une erreur est survenue lors de la mise à jour de la présence.',
    'delete_attendance_success' => 'La présence de :firstname :lastname a bien été supprimée.',
    'delete_attendance_error' => 'Une erreur est survenue lors de la suppression de la présence.',
    'attendance_not_found_error' => 'La présence de :firstname :lastname n’a pas été trouvée.',

    // Projects popup
    'add_project_success' => 'Le projet :name a bien été créé.',
    'add_project_error' => 'Une erreur est survenue lors de la création du projet.',
    'update_project_success' => 'Le projet :name a bien été mis à jour.',
    'update_project_error' => 'Une erreur est survenue lors de la mise à jour du projet.',
    'delete_project_success' => 'Le projet :name a bien été supprimé.',
    'delete_project_error' => 'Une erreur est survenue lors de la suppression du projet.',
    'project_not_found_error' => 'Le projet :name n’a pas été trouvé.',
    'add_link_to_project_success' => 'Le/les lien(s) du/des projet(s) a/ont bien été mis à jour.',

    // Implementations popup
    'add_implementation_success' => 'L’implémentation :name a bien été créée.',
    'add_implementation_error' => 'Une erreur est survenue lors de la création de l’implémentation.',
    'update_implementation_success' => 'L’implémentation :name a bien été mise à jour.',
    'update_implementation_error' => 'Une erreur est survenue lors de la mise à jour de l’implémentation.',
    'delete_implementation_success' => 'L’implémentation :name a bien été supprimée.',
    'delete_implementation_error' => 'Une erreur est survenue lors de la suppression de l’implémentation.',
    'implementation_not_found_error' => 'L’implémentation :name n’a pas été trouvée.',

    // Authentication popup
    'user_not_found' => 'Ce compte n’a pas été trouvé.',
    'password_not_match' => 'Le mot de passe est incorrect.',

    // Register popup
    'register_success' => 'Votre compte a bien été créé.',
    'user_not_created' => 'Une erreur est survenue lors de la création de votre compte.',
];
