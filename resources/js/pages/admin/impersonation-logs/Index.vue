<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import { Search, Calendar, User, Building2 } from 'lucide-vue-next';
import type { ImpersonationLog } from '@/types/user-management';

interface Props {
  logs: ImpersonationLog[];
}

const props = defineProps<Props>();

const searchQuery = ref('');

const filteredLogs = computed(() => {
  if (!searchQuery.value) return props.logs;
  
  const query = searchQuery.value.toLowerCase();
  return props.logs.filter(log => 
    log.admin_name.toLowerCase().includes(query) ||
    log.admin_email.toLowerCase().includes(query) ||
    log.tenant_name.toLowerCase().includes(query) ||
    log.user_name.toLowerCase().includes(query) ||
    log.user_email.toLowerCase().includes(query)
  );
});

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

const calculateDuration = (startedAt: string, endedAt: string | null) => {
  if (!endedAt) return 'Em andamento';
  
  const start = new Date(startedAt);
  const end = new Date(endedAt);
  const diffMs = end.getTime() - start.getTime();
  
  const hours = Math.floor(diffMs / (1000 * 60 * 60));
  const minutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
  const seconds = Math.floor((diffMs % (1000 * 60)) / 1000);
  
  const parts = [];
  if (hours > 0) parts.push(`${hours}h`);
  if (minutes > 0) parts.push(`${minutes}m`);
  if (seconds > 0 || parts.length === 0) parts.push(`${seconds}s`);
  
  return parts.join(' ');
};
</script>

<template>
  <div>
    <Head title="Logs de Impersonation" />

    <div class="space-y-6">
      <!-- Header -->
      <div>
        <h1 class="text-3xl font-bold">Logs de Impersonation</h1>
        <p class="text-muted-foreground mt-1">
          Histórico completo de todas as sessões de impersonation
        </p>
      </div>

      <!-- Search -->
      <div class="flex items-center gap-4">
        <div class="relative flex-1 max-w-md">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
          <Input
            v-model="searchQuery"
            placeholder="Buscar por admin, tenant ou usuário..."
            class="pl-9"
          />
        </div>
      </div>

      <!-- Logs table -->
      <div class="border rounded-lg">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Admin</TableHead>
              <TableHead>Tenant</TableHead>
              <TableHead>Usuário Impersonado</TableHead>
              <TableHead>Início</TableHead>
              <TableHead>Fim</TableHead>
              <TableHead>Duração</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow
              v-for="log in filteredLogs"
              :key="log.id"
            >
              <TableCell>
                <div class="flex items-start gap-2">
                  <User class="h-4 w-4 text-muted-foreground mt-0.5 flex-shrink-0" />
                  <div>
                    <p class="font-medium">{{ log.admin_name }}</p>
                    <p class="text-sm text-muted-foreground">{{ log.admin_email }}</p>
                  </div>
                </div>
              </TableCell>
              <TableCell>
                <div class="flex items-center gap-2">
                  <Building2 class="h-4 w-4 text-muted-foreground" />
                  <span>{{ log.tenant_name }}</span>
                </div>
              </TableCell>
              <TableCell>
                <div>
                  <p class="font-medium">{{ log.user_name }}</p>
                  <p class="text-sm text-muted-foreground">{{ log.user_email }}</p>
                </div>
              </TableCell>
              <TableCell>
                <div class="flex items-center gap-2 text-sm">
                  <Calendar class="h-4 w-4 text-muted-foreground" />
                  {{ formatDate(log.started_at) }}
                </div>
              </TableCell>
              <TableCell>
                <div class="text-sm">
                  {{ log.ended_at ? formatDate(log.ended_at) : '-' }}
                </div>
              </TableCell>
              <TableCell>
                <Badge
                  :variant="log.ended_at ? 'secondary' : 'outline'"
                  :class="log.ended_at ? '' : 'text-green-600 border-green-200'"
                >
                  {{ calculateDuration(log.started_at, log.ended_at) }}
                </Badge>
              </TableCell>
            </TableRow>
            <TableRow v-if="filteredLogs.length === 0">
              <TableCell colspan="6" class="text-center text-muted-foreground py-8">
                Nenhum log encontrado
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>

      <!-- Info -->
      <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-md text-sm">
        <strong>Informação:</strong> Todos os acessos via impersonation são registrados com detalhes do admin, tenant, usuário, horário e IP para fins de auditoria e segurança.
      </div>
    </div>
  </div>
</template>
