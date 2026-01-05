<script setup lang="ts">
import { computed } from 'vue';
import { usePage, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { AlertTriangle } from 'lucide-vue-next';
import type { ImpersonationData } from '@/types/user-management';

interface PageProps {
  impersonating?: boolean;
  impersonationData?: ImpersonationData;
}

const page = usePage<PageProps>();

const impersonating = computed(() => page.props.impersonating || false);
const impersonationData = computed(() => page.props.impersonationData);

const stopImpersonating = () => {
  router.post('/admin/stop-impersonating', {}, {
    onSuccess: () => {
      // Redirect to admin panel after stopping
      window.location.href = '/admin/tenants';
    },
  });
};
</script>

<template>
  <div
    v-if="impersonating && impersonationData"
    class="sticky top-0 z-50 bg-yellow-500 text-yellow-950 px-4 py-3 shadow-md"
  >
    <div class="container mx-auto flex items-center justify-between">
      <div class="flex items-center gap-3">
        <AlertTriangle class="h-5 w-5" />
        <div class="text-sm">
          <span class="font-semibold">Modo Impersonation Ativo:</span>
          Você está operando como
          <strong>{{ impersonationData.user_name }}</strong>
          ({{ impersonationData.user_email }})
          no tenant
          <strong>{{ impersonationData.tenant_name }}</strong>
        </div>
      </div>
      <Button
        @click="stopImpersonating"
        variant="outline"
        size="sm"
        class="bg-yellow-950 text-yellow-50 hover:bg-yellow-900 border-yellow-700"
      >
        Sair do Impersonate
      </Button>
    </div>
  </div>
</template>
