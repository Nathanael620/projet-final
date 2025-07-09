<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Profile Settings
    |--------------------------------------------------------------------------
    |
    | Configuration pour les paramètres de profil utilisateur
    |
    */

    'avatar' => [
        'max_size' => 2048, // 2MB en KB
        'allowed_types' => ['jpeg', 'png', 'jpg', 'gif'],
        'storage_path' => 'avatars',
        'default_avatar' => '/images/default-avatar.png',
    ],

    'skills' => [
        'mathematics' => 'Mathématiques',
        'physics' => 'Physique',
        'chemistry' => 'Chimie',
        'biology' => 'Biologie',
        'computer_science' => 'Informatique',
        'languages' => 'Langues',
        'literature' => 'Littérature',
        'history' => 'Histoire',
        'geography' => 'Géographie',
        'economics' => 'Économie',
        'philosophy' => 'Philosophie',
        'art' => 'Art',
        'music' => 'Musique',
        'sports' => 'Sport',
        'other' => 'Autre',
    ],

    'levels' => [
        'beginner' => 'Débutant',
        'intermediate' => 'Intermédiaire',
        'advanced' => 'Avancé',
    ],

    'tutor' => [
        'min_hourly_rate' => 5,
        'max_hourly_rate' => 200,
        'default_hourly_rate' => 20,
        'recommended_hourly_rate_min' => 15,
        'recommended_hourly_rate_max' => 50,
    ],

    'privacy' => [
        'default_public_profile' => true,
        'show_email_to_public' => false,
        'show_phone_to_public' => false,
    ],

    'notifications' => [
        'profile_updated' => true,
        'new_message' => true,
        'session_request' => true,
        'session_reminder' => true,
    ],
]; 