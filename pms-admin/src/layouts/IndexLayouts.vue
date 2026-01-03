<script setup lang="ts">
import { RouterView } from 'vue-router'
import IndexSidebar from '@/components/IndexSidebar.vue';
</script>

<template>
  <div class="min-h-screen text-black font-default bg-secondary">
    <IndexSidebar />
    <!-- CONTENT -->
    <div class="pt-20 lg:pt-6 transition-all duration-300 lg:ml-64">
      <RouterView />
    </div>
  </div>
</template>
