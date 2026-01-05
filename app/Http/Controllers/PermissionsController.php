<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Inertia\Inertia;
use Inertia\Response;

class PermissionsController extends Controller
{
    /**
     * Get all permissions grouped by module.
     */
    public function index(): Response
    {
        $permissions = Permission::orderBy('module')
            ->orderBy('name')
            ->get()
            ->groupBy('module');

        return Inertia::render('permissions/Index', [
            'permissions' => $permissions,
        ]);
    }
}
