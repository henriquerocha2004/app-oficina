<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/layouts/GuestLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card';
import { Alert, AlertDescription } from '@/components/ui/alert';
import { CheckCircle, Mail, Lock, User } from 'lucide-vue-next';
import type { UserInvitation } from '@/types/user-management';

interface Props {
  invitation: UserInvitation;
  token: string;
}

const props = defineProps<Props>();

const form = useForm({
  token: props.token,
  name: '',
  password: '',
  password_confirmation: '',
});

const submit = () => {
  form.post('/invitations/accept', {
    onSuccess: () => {
      // User will be redirected to login or dashboard
    },
  });
};
</script>

<template>
  <GuestLayout>
    <Head title="Aceitar Convite" />

    <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-md w-full space-y-6">
        <!-- Header -->
        <div class="text-center">
          <h1 class="text-3xl font-bold">Bem-vindo!</h1>
          <p class="text-muted-foreground mt-2">
            Você foi convidado para se juntar ao sistema
          </p>
        </div>

        <!-- Success Alert -->
        <Alert class="border-green-200 bg-green-50">
          <CheckCircle class="h-4 w-4 text-green-600" />
          <AlertDescription class="text-green-800">
            <strong>Convite válido!</strong><br>
            Complete o cadastro abaixo para ativar sua conta
          </AlertDescription>
        </Alert>

        <!-- Invitation Info -->
        <Card>
          <CardHeader>
            <CardTitle>Informações do Convite</CardTitle>
            <CardDescription>
              Confira seus dados antes de continuar
            </CardDescription>
          </CardHeader>
          <CardContent class="space-y-3">
            <div class="flex items-center gap-3 text-sm">
              <Mail class="h-4 w-4 text-muted-foreground" />
              <div>
                <p class="font-medium">Email</p>
                <p class="text-muted-foreground">{{ invitation.email }}</p>
              </div>
            </div>
            <div class="flex items-center gap-3 text-sm">
              <User class="h-4 w-4 text-muted-foreground" />
              <div>
                <p class="font-medium">Função (Role)</p>
                <p class="text-muted-foreground">{{ invitation.role?.name }}</p>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Form -->
        <Card>
          <CardHeader>
            <CardTitle>Criar Conta</CardTitle>
            <CardDescription>
              Defina seu nome e senha para acessar o sistema
            </CardDescription>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submit" class="space-y-4">
              <!-- Name -->
              <div class="space-y-2">
                <Label for="name">Nome Completo *</Label>
                <Input
                  id="name"
                  v-model="form.name"
                  type="text"
                  placeholder="Seu nome completo"
                  required
                  :disabled="form.processing"
                  autocomplete="name"
                />
                <p v-if="form.errors.name" class="text-sm text-red-600">
                  {{ form.errors.name }}
                </p>
              </div>

              <!-- Password -->
              <div class="space-y-2">
                <Label for="password">Senha *</Label>
                <Input
                  id="password"
                  v-model="form.password"
                  type="password"
                  placeholder="Mínimo 8 caracteres"
                  required
                  :disabled="form.processing"
                  autocomplete="new-password"
                />
                <p v-if="form.errors.password" class="text-sm text-red-600">
                  {{ form.errors.password }}
                </p>
              </div>

              <!-- Password Confirmation -->
              <div class="space-y-2">
                <Label for="password_confirmation">Confirmar Senha *</Label>
                <Input
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  type="password"
                  placeholder="Digite a senha novamente"
                  required
                  :disabled="form.processing"
                  autocomplete="new-password"
                />
                <p v-if="form.errors.password_confirmation" class="text-sm text-red-600">
                  {{ form.errors.password_confirmation }}
                </p>
              </div>

              <!-- Submit -->
              <Button
                type="submit"
                class="w-full"
                :disabled="form.processing"
              >
                {{ form.processing ? 'Criando conta...' : 'Criar Conta e Acessar' }}
              </Button>
            </form>
          </CardContent>
        </Card>

        <!-- Footer -->
        <p class="text-center text-sm text-muted-foreground">
          Ao criar sua conta, você concorda com nossos termos de uso
        </p>
      </div>
    </div>
  </GuestLayout>
</template>
