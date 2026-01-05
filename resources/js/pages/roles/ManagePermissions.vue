<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { Settings, Lock, Info } from 'lucide-vue-next';
import type { Role, Permission, PermissionsByModule } from '@/types/user-management';

interface Props {
  role: Role;
  permissions: Permission[];
}

const props = defineProps<Props>();

// Group permissions by module
const permissionsByModule = computed<PermissionsByModule>(() => {
  return props.permissions.reduce((acc, permission) => {
    if (!acc[permission.module]) {
      acc[permission.module] = [];
    }
    acc[permission.module].push(permission);
    return acc;
  }, {} as PermissionsByModule);
});

// Initialize form with current role permissions
const selectedPermissions = ref<string[]>(
  props.role.permissions?.map(p => p.id) || []
);

const form = useForm({
  permission_ids: selectedPermissions.value,
});

// Module names in Portuguese
const moduleNames: Record<string, string> = {
  clients: 'Clientes',
  vehicles: 'Veículos',
  services: 'Serviços',
  products: 'Produtos',
  suppliers: 'Fornecedores',
  'stock-movements': 'Movimentação de Estoque',
  settings: 'Configurações',
  users: 'Usuários',
};

const togglePermission = (permissionId: string) => {
  const index = selectedPermissions.value.indexOf(permissionId);
  if (index > -1) {
    selectedPermissions.value.splice(index, 1);
  } else {
    selectedPermissions.value.push(permissionId);
  }
  form.permission_ids = selectedPermissions.value;
};

const toggleModule = (module: string) => {
  const modulePermissions = permissionsByModule.value[module];
  const allSelected = modulePermissions.every(p => 
    selectedPermissions.value.includes(p.id)
  );

  if (allSelected) {
    // Deselect all permissions from this module
    selectedPermissions.value = selectedPermissions.value.filter(id => 
      !modulePermissions.some(p => p.id === id)
    );
  } else {
    // Select all permissions from this module
    const newPermissions = modulePermissions
      .map(p => p.id)
      .filter(id => !selectedPermissions.value.includes(id));
    selectedPermissions.value.push(...newPermissions);
  }
  form.permission_ids = selectedPermissions.value;
};

const isModuleFullySelected = (module: string) => {
  const modulePermissions = permissionsByModule.value[module];
  return modulePermissions.every(p => selectedPermissions.value.includes(p.id));
};

const isModulePartiallySelected = (module: string) => {
  const modulePermissions = permissionsByModule.value[module];
  const selectedCount = modulePermissions.filter(p => 
    selectedPermissions.value.includes(p.id)
  ).length;
  return selectedCount > 0 && selectedCount < modulePermissions.length;
};

const submit = () => {
  form.put(`/roles/${props.role.id}/permissions`, {
    preserveScroll: true,
  });
};
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="`Gerenciar Permissões - ${role.name}`" />

    <div class="max-w-4xl mx-auto space-y-6">
      <!-- Header -->
      <div>
        <h1 class="text-3xl font-bold">Gerenciar Permissões</h1>
        <p class="text-muted-foreground mt-1">
          Configure as permissões para a role <strong>{{ role.name }}</strong>
        </p>
      </div>

      <!-- System role warning -->
      <Alert v-if="role.is_system" class="border-blue-200 bg-blue-50">
        <Lock class="h-4 w-4 text-blue-600" />
        <AlertDescription class="text-blue-800">
          <strong>Role do Sistema:</strong> Esta é uma role predefinida. Você pode customizar suas permissões, mas não pode excluí-la.
        </AlertDescription>
      </Alert>

      <!-- Form -->
      <form @submit.prevent="submit" class="space-y-6">
        <!-- Permissions by module -->
        <div
          v-for="(permissions, module) in permissionsByModule"
          :key="module"
          class="space-y-4"
        >
          <Card>
            <CardHeader>
              <div class="flex items-center justify-between">
                <CardTitle class="text-lg">
                  {{ moduleNames[module] || module }}
                </CardTitle>
                <Button
                  type="button"
                  variant="ghost"
                  size="sm"
                  @click="toggleModule(module)"
                >
                  {{ isModuleFullySelected(module) ? 'Desmarcar todos' : 'Marcar todos' }}
                </Button>
              </div>
              <CardDescription>
                {{ permissions.length }} permissões disponíveis
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div
                  v-for="permission in permissions"
                  :key="permission.id"
                  class="flex items-start space-x-3"
                >
                  <Checkbox
                    :id="permission.id"
                    :checked="selectedPermissions.includes(permission.id)"
                    @update:checked="togglePermission(permission.id)"
                    :disabled="form.processing"
                  />
                  <div class="grid gap-1.5 leading-none">
                    <Label
                      :for="permission.id"
                      class="cursor-pointer font-medium"
                    >
                      {{ permission.name }}
                    </Label>
                    <p
                      v-if="permission.description"
                      class="text-sm text-muted-foreground"
                    >
                      {{ permission.description }}
                    </p>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Actions -->
        <div class="flex justify-between items-center">
          <Button
            type="button"
            variant="outline"
            @click="$inertia.visit('/roles')"
            :disabled="form.processing"
          >
            Voltar
          </Button>
          <div class="flex gap-3">
            <p class="text-sm text-muted-foreground self-center">
              {{ selectedPermissions.length }} permissões selecionadas
            </p>
            <Button
              type="submit"
              :disabled="form.processing"
            >
              {{ form.processing ? 'Salvando...' : 'Salvar Permissões' }}
            </Button>
          </div>
        </div>
      </form>

      <!-- Info -->
      <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-md text-sm flex gap-2">
        <Info class="h-4 w-4 mt-0.5 flex-shrink-0" />
        <div>
          <strong>Importante:</strong> As alterações nas permissões afetarão imediatamente todos os usuários que possuem esta role.
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
