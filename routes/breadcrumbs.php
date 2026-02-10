<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\RouteBreadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// E-Registry
Breadcrumbs::for('e-registry', function (BreadcrumbTrail $trail) {
    $trail->push('E-Registry', route('registry.boards.index'));
});

// E-Registry > Dispatches
Breadcrumbs::for('dispatches.index', function (BreadcrumbTrail $trail) {
    $trail->parent('e-registry');
    $trail->push('Dispatches', route('registry.dispatches.index'));
});

// E-Registry > Dispatches > File name
Breadcrumbs::for('dispatches.show', function (BreadcrumbTrail $trail, $file) {
    $trail->parent('dispatches.index');
    $trail->push("{$file->name}", route('registry.dispatches.show', $file));
});

// E-Registry > Circulations
Breadcrumbs::for('circulations.index', function (BreadcrumbTrail $trail) {
    $trail->parent('e-registry');
    $trail->push('Circulations', route('registry.file-circulations.index'));
});

// E-Registry > Circulations > File name
Breadcrumbs::for('circulations.show', function (BreadcrumbTrail $trail, $file) {
    $trail->parent('circulations.index');
    $trail->push("{$file->name}", route('registry.file-circulations.show', $file));
});


// For creating files with specific types
Breadcrumbs::for('files.create.withType', function ($trail, $createType) {

    if ($createType === 'dispatch') {
        $trail->parent('dispatches.index');
    } elseif ($createType === 'internal') {
        $trail->parent('circulations.index');
    } else {
        // optional fallback
        $trail->parent('files.index');
    }

    $trail->push(
        'Create ' . ucfirst($createType),
        route('registry.files.create.withType', $createType)
    );
});


// Management > 
Breadcrumbs::for('management', function (BreadcrumbTrail $trail) {
    $trail->push('Management', route('registry.boards.management'));
});

// Management > Users >
Breadcrumbs::for('users.index', function (BreadcrumbTrail $trail) {
    $trail->parent('management');
    $trail->push('Users', route('registry.users.index'));
});


// Management > Users > User Name
Breadcrumbs::for('users.show', function (BreadcrumbTrail $trail, $user) {
    $trail->parent('users.index');
    $trail->push("{$user->first_name}", route('registry.users.show', $user));
});


