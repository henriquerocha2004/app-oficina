<script setup lang="ts">
import { computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { UserCog, AlertTriangle } from 'lucide-vue-next';
import type { User, Role } from '@/types/user-management';

interface Props {
  user: User;
  roles: Role[];
}

const props = defineProps<Props>();

const form = useForm({
  role_id: props.user.role_id,
});

const submit = () => {
  form.put(`/users/${props.user.id}/change-role`, {
    onSuccess: () => {
      // Redirect to users list
    },
  });
};

const selectedRole = computed(() => {
  return props.roles.find(r => r.id === form.role_id);
});
</script>

<template>
  <AuthenticatedLayout>
    <Head :title="`Alterar Role - ${user.name}`" />

    <div class="max-w-2xl mx-auto space-y-6">
      <!-- Header -->
      <div>
        <h1 class="text-3xl font-bold">Alterar Role</h1>
        <p class="text-muted-foreground mt-1">
          Modificar as permissões de {{ user.name }}
        </p>
      </div>

      <!-- Warning -->
      <Alert>
        <AlertTriangle class="h-4 w-4" />
        <AlertDescription>
          Alterar a role de um usuário modificará suas permissões imediatamente. Certifique-se de que esta é a role correta antes de confirmar.
        </AlertDescription>
      </Alert>

      <!-- Form -->
      <Card>
        <CardHeader>
          <CardTitle class="flex items-center gap-2">
            <UserCog class="h-5 w-5" />
            Selecionar Nova Role
          </CardTitle>
          <CardDescription>
            Usuário: <strong>{{ user.name }}</strong> ({{ user.email }})<br>
            Role atual: <strong>{{ user.role?.name }}</strong>
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Role -->
            <div class="space-y-2">
              <Label for="role_id">Nova Role *</Label>
              <Select
                v-model="form.role_id"
                :disabled="form.processing"
                required
              >
                <SelectTrigger id="role_id">
                  <SelectValue placeholder="Selecione uma role" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem
                    v-for="role in roles"
                    :key="role.id"
                    :value="role.id"
                  >
                    {{ role.name }}
                  </SelectItem>
                </SelectContent>
              </Select>
              <p v-if="form.errors.role_id" class="text-sm text-red-600">
                {{ form.errors.role_id }}
              </p>
            </div>

            <!-- Role description -->
            <div
              v-if="selectedRole?.description"
              class="bg-muted p-4 rounded-md"
            >
              <p class="text-sm">
                <strong>Descrição:</strong> {{ selectedRole.description }}
              </p>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3">
              <Button
                type="button"
                variant="outline"
                @click="$inertia.visit('/users')"
                :disabled="form.processing"
              >
                Cancelar
              </Button>
              <Button
                type="submit"
                :disabled="form.processing || form.role_id === user.role_id"
              >
                {{ form.processing ? 'Salvando...' : 'Confirmar Alteração' }}
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </AuthenticatedLayout>
</template>
