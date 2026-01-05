<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First, create all permissions organized by module
        $permissions = $this->createPermissions();

        // Then create roles and assign permissions
        $this->createOwnerRole($permissions);
        $this->createManagerRole($permissions);
        $this->createAttendantRole($permissions);
        $this->createMechanicRole($permissions);
        $this->createViewerRole($permissions);
    }

    /**
     * Create all permissions organized by module.
     */
    private function createPermissions(): array
    {
        $modules = [
            'clients' => [
                ['slug' => 'view-clients', 'name' => 'Visualizar Clientes', 'description' => 'Pode visualizar lista e detalhes de clientes'],
                ['slug' => 'create-clients', 'name' => 'Criar Clientes', 'description' => 'Pode criar novos clientes'],
                ['slug' => 'edit-clients', 'name' => 'Editar Clientes', 'description' => 'Pode editar dados de clientes'],
                ['slug' => 'delete-clients', 'name' => 'Deletar Clientes', 'description' => 'Pode deletar clientes'],
            ],
            'vehicles' => [
                ['slug' => 'view-vehicles', 'name' => 'Visualizar Veículos', 'description' => 'Pode visualizar lista e detalhes de veículos'],
                ['slug' => 'create-vehicles', 'name' => 'Criar Veículos', 'description' => 'Pode criar novos veículos'],
                ['slug' => 'edit-vehicles', 'name' => 'Editar Veículos', 'description' => 'Pode editar dados de veículos'],
                ['slug' => 'delete-vehicles', 'name' => 'Deletar Veículos', 'description' => 'Pode deletar veículos'],
            ],
            'services' => [
                ['slug' => 'view-services', 'name' => 'Visualizar Serviços', 'description' => 'Pode visualizar lista e detalhes de serviços'],
                ['slug' => 'create-services', 'name' => 'Criar Serviços', 'description' => 'Pode criar novos serviços'],
                ['slug' => 'edit-services', 'name' => 'Editar Serviços', 'description' => 'Pode editar dados de serviços'],
                ['slug' => 'delete-services', 'name' => 'Deletar Serviços', 'description' => 'Pode deletar serviços'],
            ],
            'products' => [
                ['slug' => 'view-products', 'name' => 'Visualizar Produtos', 'description' => 'Pode visualizar lista e detalhes de produtos'],
                ['slug' => 'create-products', 'name' => 'Criar Produtos', 'description' => 'Pode criar novos produtos'],
                ['slug' => 'edit-products', 'name' => 'Editar Produtos', 'description' => 'Pode editar dados de produtos'],
                ['slug' => 'delete-products', 'name' => 'Deletar Produtos', 'description' => 'Pode deletar produtos'],
            ],
            'suppliers' => [
                ['slug' => 'view-suppliers', 'name' => 'Visualizar Fornecedores', 'description' => 'Pode visualizar lista e detalhes de fornecedores'],
                ['slug' => 'create-suppliers', 'name' => 'Criar Fornecedores', 'description' => 'Pode criar novos fornecedores'],
                ['slug' => 'edit-suppliers', 'name' => 'Editar Fornecedores', 'description' => 'Pode editar dados de fornecedores'],
                ['slug' => 'delete-suppliers', 'name' => 'Deletar Fornecedores', 'description' => 'Pode deletar fornecedores'],
            ],
            'stock-movements' => [
                ['slug' => 'view-stock-movements', 'name' => 'Visualizar Movimentações', 'description' => 'Pode visualizar movimentações de estoque'],
                ['slug' => 'create-stock-movements', 'name' => 'Criar Movimentações', 'description' => 'Pode criar movimentações de estoque'],
                ['slug' => 'edit-stock-movements', 'name' => 'Editar Movimentações', 'description' => 'Pode editar movimentações de estoque'],
                ['slug' => 'delete-stock-movements', 'name' => 'Deletar Movimentações', 'description' => 'Pode deletar movimentações de estoque'],
            ],
            'settings' => [
                ['slug' => 'view-settings', 'name' => 'Visualizar Configurações', 'description' => 'Pode visualizar configurações do sistema'],
                ['slug' => 'edit-settings', 'name' => 'Editar Configurações', 'description' => 'Pode editar configurações do sistema'],
            ],
            'users' => [
                ['slug' => 'view-users', 'name' => 'Visualizar Usuários', 'description' => 'Pode visualizar lista de usuários'],
                ['slug' => 'invite-users', 'name' => 'Convidar Usuários', 'description' => 'Pode enviar convites para novos usuários'],
                ['slug' => 'edit-users', 'name' => 'Editar Usuários', 'description' => 'Pode editar dados de usuários'],
                ['slug' => 'delete-users', 'name' => 'Deletar Usuários', 'description' => 'Pode deletar usuários'],
                ['slug' => 'manage-roles', 'name' => 'Gerenciar Perfis', 'description' => 'Pode criar, editar e deletar perfis de acesso'],
            ],
        ];

        $createdPermissions = [];

        foreach ($modules as $module => $modulePermissions) {
            foreach ($modulePermissions as $permData) {
                $permission = Permission::firstOrCreate(
                    ['slug' => $permData['slug']],
                    [
                        'name' => $permData['name'],
                        'description' => $permData['description'],
                        'module' => $module,
                    ]
                );
                $createdPermissions[$permData['slug']] = $permission;
            }
        }

        return $createdPermissions;
    }

    /**
     * Create Owner role with all permissions.
     */
    private function createOwnerRole(array $permissions): void
    {
        $role = Role::firstOrCreate(
            ['slug' => 'owner'],
            [
                'name' => 'Proprietário',
                'description' => 'Acesso total ao sistema. Gerencia todos os aspectos da oficina incluindo usuários e configurações.',
                'is_system' => true,
            ]
        );

        // Owner has all permissions
        $role->permissions()->sync(array_values(array_map(fn($p) => $p->id, $permissions)));
    }

    /**
     * Create Manager role with operational permissions.
     */
    private function createManagerRole(array $permissions): void
    {
        $role = Role::firstOrCreate(
            ['slug' => 'manager'],
            [
                'name' => 'Gerente',
                'description' => 'Gerencia operações diárias. Acesso completo a clientes, veículos, serviços e produtos, mas não gerencia usuários.',
                'is_system' => true,
            ]
        );

        $managerPermissions = [
            // Clients - full access
            $permissions['view-clients']->id,
            $permissions['create-clients']->id,
            $permissions['edit-clients']->id,
            $permissions['delete-clients']->id,
            // Vehicles - full access
            $permissions['view-vehicles']->id,
            $permissions['create-vehicles']->id,
            $permissions['edit-vehicles']->id,
            $permissions['delete-vehicles']->id,
            // Services - full access
            $permissions['view-services']->id,
            $permissions['create-services']->id,
            $permissions['edit-services']->id,
            $permissions['delete-services']->id,
            // Products - full access
            $permissions['view-products']->id,
            $permissions['create-products']->id,
            $permissions['edit-products']->id,
            $permissions['delete-products']->id,
            // Suppliers - full access
            $permissions['view-suppliers']->id,
            $permissions['create-suppliers']->id,
            $permissions['edit-suppliers']->id,
            $permissions['delete-suppliers']->id,
            // Stock movements - full access
            $permissions['view-stock-movements']->id,
            $permissions['create-stock-movements']->id,
            $permissions['edit-stock-movements']->id,
            $permissions['delete-stock-movements']->id,
            // Settings - view only
            $permissions['view-settings']->id,
            // Users - view only
            $permissions['view-users']->id,
        ];

        $role->permissions()->sync($managerPermissions);
    }

    /**
     * Create Attendant role with customer-facing permissions.
     */
    private function createAttendantRole(array $permissions): void
    {
        $role = Role::firstOrCreate(
            ['slug' => 'attendant'],
            [
                'name' => 'Atendente',
                'description' => 'Atende clientes e gerencia ordens de serviço. Pode criar e editar clientes, veículos e serviços.',
                'is_system' => true,
            ]
        );

        $attendantPermissions = [
            // Clients - can create and edit
            $permissions['view-clients']->id,
            $permissions['create-clients']->id,
            $permissions['edit-clients']->id,
            // Vehicles - can create and edit
            $permissions['view-vehicles']->id,
            $permissions['create-vehicles']->id,
            $permissions['edit-vehicles']->id,
            // Services - can create and edit
            $permissions['view-services']->id,
            $permissions['create-services']->id,
            $permissions['edit-services']->id,
            // Products - view only
            $permissions['view-products']->id,
            // Suppliers - view only
            $permissions['view-suppliers']->id,
        ];

        $role->permissions()->sync($attendantPermissions);
    }

    /**
     * Create Mechanic role with workshop permissions.
     */
    private function createMechanicRole(array $permissions): void
    {
        $role = Role::firstOrCreate(
            ['slug' => 'mechanic'],
            [
                'name' => 'Mecânico',
                'description' => 'Executa serviços e registra movimentações. Pode atualizar status de serviços e registrar uso de peças.',
                'is_system' => true,
            ]
        );

        $mechanicPermissions = [
            // Clients - view only
            $permissions['view-clients']->id,
            // Vehicles - view only
            $permissions['view-vehicles']->id,
            // Services - can view and edit
            $permissions['view-services']->id,
            $permissions['edit-services']->id,
            // Products - view only
            $permissions['view-products']->id,
            // Stock movements - can create (register parts usage)
            $permissions['view-stock-movements']->id,
            $permissions['create-stock-movements']->id,
        ];

        $role->permissions()->sync($mechanicPermissions);
    }

    /**
     * Create Viewer role with read-only permissions.
     */
    private function createViewerRole(array $permissions): void
    {
        $role = Role::firstOrCreate(
            ['slug' => 'viewer'],
            [
                'name' => 'Visualizador',
                'description' => 'Acesso somente leitura. Pode visualizar todas as informações mas não pode fazer alterações.',
                'is_system' => true,
            ]
        );

        $viewerPermissions = [
            $permissions['view-clients']->id,
            $permissions['view-vehicles']->id,
            $permissions['view-services']->id,
            $permissions['view-products']->id,
            $permissions['view-suppliers']->id,
            $permissions['view-stock-movements']->id,
            $permissions['view-settings']->id,
            $permissions['view-users']->id,
        ];

        $role->permissions()->sync($viewerPermissions);
    }

    /**
     * Static method to seed roles for a specific tenant.
     * Useful when creating a new tenant.
     */
    public static function seedForTenant(): void
    {
        (new self())->run();
    }
}
