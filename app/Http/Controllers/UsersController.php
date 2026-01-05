<?php

namespace App\Http\Controllers;

use App\DTOs\SearchDTO;
use App\Http\Requests\ChangeRoleRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class UsersController extends Controller
{
    public function __construct(
        private UserService $userService,
        private RoleService $roleService,
    ) {
    }

    /**
     * Display a listing of users.
     */
    public function index(): Response
    {
        $searchDTO = SearchDTO::fromRequest(request());
        $users = $this->userService->list($searchDTO);
        $roles = $this->roleService->getAll();

        return Inertia::render('users/Index', [
            'users' => $users,
            'roles' => $roles,
            'filters' => $searchDTO->filters,
        ]);
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, string $id): RedirectResponse
    {
        try {
            $this->userService->update($id, $request->toDTO());

            return redirect()->back()->with('success', 'Usuário atualizado com sucesso.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Change user role.
     */
    public function changeRole(ChangeRoleRequest $request, string $id): RedirectResponse
    {
        try {
            $this->userService->changeRole($id, $request->input('role_id'));

            return redirect()->back()->with('success', 'Perfil alterado com sucesso.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified user.
     */
    public function destroy(string $id): RedirectResponse
    {
        try {
            $this->userService->delete($id);

            return redirect()->back()->with('success', 'Usuário removido com sucesso.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
