<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card';
import { Shield } from 'lucide-vue-next';

const form = useForm({
  name: '',
  description: '',
});

const submit = () => {
  form.post('/roles', {
    onSuccess: () => {
      form.reset();
    },
  });
};
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Criar Nova Role" />

    <div class="max-w-2xl mx-auto space-y-6">
      <!-- Header -->
      <div>
        <h1 class="text-3xl font-bold">Criar Nova Role</h1>
        <p class="text-muted-foreground mt-1">
          Adicione uma nova função personalizada ao sistema
        </p>
      </div>

      <!-- Form -->
      <Card>
        <CardHeader>
          <CardTitle class="flex items-center gap-2">
            <Shield class="h-5 w-5" />
            Dados da Role
          </CardTitle>
          <CardDescription>
            Defina o nome e a descrição da nova role. Você poderá configurar as permissões após a criação.
          </CardDescription>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Name -->
            <div class="space-y-2">
              <Label for="name">Nome *</Label>
              <Input
                id="name"
                v-model="form.name"
                type="text"
                placeholder="Ex: Coordenador, Assistente, etc."
                required
                :disabled="form.processing"
              />
              <p v-if="form.errors.name" class="text-sm text-red-600">
                {{ form.errors.name }}
              </p>
              <p class="text-sm text-muted-foreground">
                Um nome descritivo para identificar esta role
              </p>
            </div>

            <!-- Description -->
            <div class="space-y-2">
              <Label for="description">Descrição</Label>
              <Textarea
                id="description"
                v-model="form.description"
                placeholder="Descreva as responsabilidades desta role..."
                rows="4"
                :disabled="form.processing"
              />
              <p v-if="form.errors.description" class="text-sm text-red-600">
                {{ form.errors.description }}
              </p>
              <p class="text-sm text-muted-foreground">
                Uma breve descrição das responsabilidades e acessos desta role
              </p>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3">
              <Button
                type="button"
                variant="outline"
                @click="$inertia.visit('/roles')"
                :disabled="form.processing"
              >
                Cancelar
              </Button>
              <Button
                type="submit"
                :disabled="form.processing"
              >
                {{ form.processing ? 'Criando...' : 'Criar Role' }}
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>

      <!-- Info -->
      <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-md text-sm">
        <strong>Próximo passo:</strong> Após criar a role, você será redirecionado para configurar suas permissões específicas.
      </div>
    </div>
  </AuthenticatedLayout>
</template>
