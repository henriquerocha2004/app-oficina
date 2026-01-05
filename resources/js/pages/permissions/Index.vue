<script setup lang="ts">
import { Head } from '@inertiajs/vue3'

defineProps<{
  permissions: Record<string, any[]>
}>()
</script>

<template>
  <Head title="Permissions" />
  <div>
    <h1>Permissions</h1>
    <div v-for="(perms, module) in permissions" :key="module">
      <h2>{{ module }}</h2>
      <ul>
        <li v-for="perm in perms" :key="perm.id">
          {{ perm.name }} ({{ perm.slug }})
        </li>
      </ul>
    </div>
  </div>
</template>
