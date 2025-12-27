<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import admin from '@/routes/admin';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(admin.login.url(), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>

    <Head title="Admin Login" />

    <div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900">
        <Card class="w-full max-w-md">
            <CardHeader>
                <CardTitle class="text-2xl text-center">Admin Panel</CardTitle>
                <CardDescription class="text-center">
                    Faça login para acessar o painel administrativo
                </CardDescription>
            </CardHeader>
            <CardContent>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <Label for="email">Email</Label>
                        <Input id="email" v-model="form.email" type="email" required autofocus
                            autocomplete="username" />
                        <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <div>
                        <Label for="password">Senha</Label>
                        <Input id="password" v-model="form.password" type="password" required
                            autocomplete="current-password" />
                        <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <div class="flex items-center">
                        <input id="remember" v-model="form.remember" type="checkbox"
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" />
                        <label for="remember" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">
                            Lembrar de mim
                        </label>
                    </div>

                    <Button type="submit" class="w-full" :disabled="form.processing">
                        Entrar
                    </Button>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
