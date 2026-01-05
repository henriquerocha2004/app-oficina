<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
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
import { UserPlus } from 'lucide-vue-next';
import type { Role } from '@/types/user-management';

interface Props {
  roles: Role[];
}

const props = defineProps<Props>();

const form = useForm({
  email: '',
  role_id: '',
});

const submit = () => {
  form.post('/invitations', {
    onSuccess: () => {
      form.reset();
    },
  });
};
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Convidar Usuário" />

    <div class="max-w-2xl mx-auto space-y-6">
      <!-- Header -->
      <div>
        <h1 class="text-3xl font-bold">Convidar Usuário</h1>
        <p class="text-muted-foreground mt-1">
          Envie um convite por email para um novo usuário
        </p>
      </div>

      <!-- Form -->
      <Card>
        <CardHeader>
          <CardTitle class="flex items-center gap-2">
            <UserPlus class="h-5 w-5" />
            Dados do Convite
          </CardTitle>
          <CardDescription>
            O usuário receberá um email com um link para definir sua senha e acessar o sistema
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Email -->
            <div class="space-y-2">
              <Label for="email">Email *</Label>
              <Input
                id="email"
                v-model="form.email"
                type="email"
                placeholder="usuario@exemplo.com"
                required
                :disabled="form.processing"
              />
              <p v-if="form.errors.email" class="text-sm text-red-600">
                {{ form.errors.email }}
              </p>
            </div>

            <!-- Role -->
            <div class="space-y-2">
              <Label for="role_id">Role *</Label>
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
              <p class="text-sm text-muted-foreground">
                Defina qual será a role (função) do usuário no sistema
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
                :disabled="form.processing"
              >
                {{ form.processing ? 'Enviando...' : 'Enviar Convite' }}
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>

      <!-- Info -->
      <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-md text-sm">
        <strong>Importante:</strong> O convite expira em 7 dias. O usuário precisará clicar no link do email para criar sua senha e ativar sua conta.
      </div>
    </div>
  </AuthenticatedLayout>
</template>
