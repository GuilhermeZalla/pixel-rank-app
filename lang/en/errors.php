<?php
return [
    'home' => 'Go to home',
    'pages' => [
        403 => ['title' => 'Access denied', 'message' => 'You do not have permission to view this page.'],
        404 => ['title' => 'Page not found', 'message' => 'The page you are looking for could not be found.'],
        419 => ['title' => 'Page expired', 'message' => 'Your session expired. Please go back and try again.'],
        500 => ['title' => 'Server error', 'message' => 'Something went wrong on our end. Please try again later.'],
        503 => ['title' => 'Temporarily unavailable', 'message' => 'The site is temporarily unavailable. Please try again shortly.'],
    ],
];
