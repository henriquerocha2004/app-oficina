<?php

namespace App\Http\Controllers;

use App\DTOs\SearchDTO;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\SyncPermissionsRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Services\RoleService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RolesController extends Controller
{
    public function __construct(
        private RoleService $roleService,
    ) {
    }

    /**
     * Display a listing of roles.
     */
    public function index(): Response
    {
        $searchDTO = SearchDTO::fromRequest(request());
        $roles = $this->roleService->list($searchDTO);

        return Inertia::render('roles/Index', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created role.
     */
    public function store(CreateRoleRequest $request): RedirectResponse
    {
        try {
            $this->roleService->create($request->toDTO());

            return redirect()->back()->with('success', 'Perfil criado com sucesso.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Update the specified role.
     */
    public function update(UpdateRoleRequest $request, string $id): RedirectResponse
    {
        try {
            $this->roleService->update($id, $request->toDTO());

            return redirect()->back()->with('success', 'Perfil atualizado com sucesso.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified role.
     */
    public function destroy(string $id): RedirectResponse
    {
        try {
            $this->roleService->delete($id);

            return redirect()->back()->with('success', 'Perfil removido com sucesso.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Sync permissions for a role.
     */
    public function syncPermissions(SyncPermissionsRequest $request, string $id): RedirectResponse
    {
        try {
            $this->roleService->syncPermissions($id, $request->input('permission_ids'));

            return redirect()->back()->with('success', 'Permissões atualizadas com sucesso.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Get permissions grouped by module.
     */
    public function permissions(): Response
    {
        $permissions = $this->roleService->getPermissionsGroupedByModule();

        return Inertia::render('roles/Permissions', [
            'permissions' => $permissions,
        ]);
    }
}
