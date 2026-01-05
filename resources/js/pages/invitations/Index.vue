<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table';
import { Badge } from '@/components/ui/badge';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { 
  Mail, 
  MoreHorizontal, 
  MailPlus, 
  Trash2,
  Clock,
  CheckCircle,
} from 'lucide-vue-next';
import type { UserInvitation } from '@/types/user-management';

interface Props {
  invitations: UserInvitation[];
}

const props = defineProps<Props>();

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('pt-BR', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

const isExpired = (invitation: UserInvitation) => {
  return new Date(invitation.expires_at) < new Date();
};

const resendInvitation = (invitationId: string) => {
  router.post(`/invitations/${invitationId}/resend`, {}, {
    preserveScroll: true,
  });
};

const cancelInvitation = (invitationId: string, email: string) => {
  if (confirm(`Tem certeza que deseja cancelar o convite para ${email}?`)) {
    router.delete(`/invitations/${invitationId}`);
  }
};
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Convites Pendentes" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold">Convites Pendentes</h1>
          <p class="text-muted-foreground mt-1">
            Gerencie os convites enviados para novos usuários
          </p>
        </div>
        <Button @click="router.visit('/users/invite')" class="gap-2">
          <Mail class="h-4 w-4" />
          Novo Convite
        </Button>
      </div>

      <!-- Invitations table -->
      <div class="border rounded-lg">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Email</TableHead>
              <TableHead>Role</TableHead>
              <TableHead>Convidado por</TableHead>
              <TableHead>Expira em</TableHead>
              <TableHead>Status</TableHead>
              <TableHead class="w-[70px]"></TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow
              v-for="invitation in invitations"
              :key="invitation.id"
            >
              <TableCell class="font-medium">
                {{ invitation.email }}
              </TableCell>
              <TableCell>
                <Badge variant="secondary">
                  {{ invitation.role?.name }}
                </Badge>
              </TableCell>
              <TableCell class="text-muted-foreground">
                {{ invitation.invited_by?.name }}
              </TableCell>
              <TableCell>
                <div class="flex items-center gap-2">
                  <Clock class="h-4 w-4 text-muted-foreground" />
                  <span :class="{ 'text-red-600': isExpired(invitation) }">
                    {{ formatDate(invitation.expires_at) }}
                  </span>
                </div>
              </TableCell>
              <TableCell>
                <Badge
                  v-if="invitation.accepted_at"
                  variant="outline"
                  class="text-green-600 border-green-200"
                >
                  <CheckCircle class="mr-1 h-3 w-3" />
                  Aceito
                </Badge>
                <Badge
                  v-else-if="isExpired(invitation)"
                  variant="outline"
                  class="text-red-600 border-red-200"
                >
                  Expirado
                </Badge>
                <Badge
                  v-else
                  variant="outline"
                  class="text-yellow-600 border-yellow-200"
                >
                  Pendente
                </Badge>
              </TableCell>
              <TableCell>
                <DropdownMenu v-if="!invitation.accepted_at">
                  <DropdownMenuTrigger as-child>
                    <Button variant="ghost" size="icon">
                      <MoreHorizontal class="h-4 w-4" />
                    </Button>
                  </DropdownMenuTrigger>
                  <DropdownMenuContent align="end">
                    <DropdownMenuItem @click="resendInvitation(invitation.id)">
                      <MailPlus class="mr-2 h-4 w-4" />
                      Reenviar
                    </DropdownMenuItem>
                    <DropdownMenuItem
                      @click="cancelInvitation(invitation.id, invitation.email)"
                      class="text-red-600"
                    >
                      <Trash2 class="mr-2 h-4 w-4" />
                      Cancelar
                    </DropdownMenuItem>
                  </DropdownMenuContent>
                </DropdownMenu>
              </TableCell>
            </TableRow>
            <TableRow v-if="invitations.length === 0">
              <TableCell colspan="6" class="text-center text-muted-foreground py-8">
                Nenhum convite pendente
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
