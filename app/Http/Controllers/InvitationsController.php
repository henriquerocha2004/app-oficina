<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcceptInvitationRequest;
use App\Http\Requests\InviteUserRequest;
use App\Services\InvitationService;
use App\Services\RoleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InvitationsController extends Controller
{
    public function __construct(
        private InvitationService $invitationService,
        private RoleService $roleService,
    ) {
    }

    /**
     * Display a listing of invitations.
     */
    public function index(): Response
    {
        $invitations = $this->invitationService->getPaginated();
        $roles = $this->roleService->getAll();

        return Inertia::render('invitations/Index', [
            'invitations' => $invitations,
            'roles' => $roles,
        ]);
    }

    /**
     * Send a new invitation.
     */
    public function store(InviteUserRequest $request): RedirectResponse
    {
        try {
            $this->invitationService->invite($request->toDTO());

            return redirect()->route('users.index')->with('success', 'Convite enviado com sucesso.');
        } catch (\DomainException $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Resend an invitation.
     */
    public function resend(string $id): RedirectResponse
    {
        try {
            $this->invitationService->resend($id);

            return redirect()->back()->with('success', 'Convite reenviado com sucesso.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Cancel an invitation.
     */
    public function cancel(string $id): RedirectResponse
    {
        try {
            $this->invitationService->cancel($id);

            return redirect()->back()->with('success', 'Convite cancelado com sucesso.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show the accept invitation form.
     */
    public function showAcceptForm(string $token): Response
    {
        $invitation = $this->invitationService->findByToken($token);

        if (!$invitation) {
            abort(404, 'Convite não encontrado.');
        }

        if ($invitation->isExpired()) {
            return Inertia::render('invitations/Expired', [
                'invitation' => $invitation,
            ]);
        }

        if ($invitation->isAccepted()) {
            return Inertia::render('invitations/AlreadyAccepted');
        }

        return Inertia::render('invitations/Accept', [
            'invitation' => [
                'email' => $invitation->email,
                'role' => $invitation->role->name,
                'tenant' => tenancy()->tenant?->name,
                'invitedBy' => $invitation->invitedBy->name,
                'expiresAt' => $invitation->expires_at->toISOString(),
            ],
            'token' => $token,
        ]);
    }

    /**
     * Accept an invitation.
     */
    public function accept(AcceptInvitationRequest $request): RedirectResponse
    {
        try {
            $user = $this->invitationService->accept($request->toDTO());

            auth()->login($user);

            return redirect()->route('dashboard')
                ->with('success', 'Bem-vindo! Sua conta foi criada com sucesso.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }
}
