<?php

return [
    [
        'name' => 'View Dashboard',
        'slug' => 'dashboard.view',
        'description' => 'View the user dashboard.',
        'roles' => ['admin', 'editor', 'user'],
    ],
    [
        'name' => 'View Profile',
        'slug' => 'profile.view',
        'description' => 'View user profile.',
        'roles' => ['admin', 'editor', 'user'],
    ],
    [
        'name' => 'Update Profile',
        'slug' => 'profile.update',
        'description' => 'Update user profile.',
        'roles' => ['admin', 'editor', 'user'],
    ],
    [
        'name' => 'Update Password',
        'slug' => 'password.update',
        'description' => 'Update account password.',
        'roles' => ['admin', 'editor', 'user'],
    ],
    [
        'name' => 'View Datatable',
        'slug' => 'datatable.view',
        'description' => 'View datatable records.',
        'roles' => ['admin', 'editor'],
    ],
    [
        'name' => 'Create Datatable Records',
        'slug' => 'datatable.create',
        'description' => 'Create datatable records.',
        'roles' => ['admin', 'editor'],
    ],
    [
        'name' => 'Update Datatable Records',
        'slug' => 'datatable.update',
        'description' => 'Update datatable records.',
        'roles' => ['admin', 'editor'],
    ],
    [
        'name' => 'Delete Datatable Records',
        'slug' => 'datatable.delete',
        'description' => 'Delete datatable records.',
        'roles' => ['admin'],
    ],
    [
        'name' => 'View WebSocket',
        'slug' => 'websocket.view',
        'description' => 'View WebSocket information.',
        'roles' => ['admin', 'editor'],
    ],
    [
        'name' => 'View Users',
        'slug' => 'users.view',
        'description' => 'View user records.',
        'roles' => ['admin'],
    ],
    [
        'name' => 'Create Users',
        'slug' => 'users.create',
        'description' => 'Create user records.',
        'roles' => ['admin'],
    ],
    [
        'name' => 'Update Users',
        'slug' => 'users.update',
        'description' => 'Update user records.',
        'roles' => ['admin'],
    ],
    [
        'name' => 'Delete Users',
        'slug' => 'users.delete',
        'description' => 'Delete user records.',
        'roles' => ['admin'],
    ],
];
