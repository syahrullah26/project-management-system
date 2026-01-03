<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRoute, RouterLink } from 'vue-router'

const route = useRoute()

/* ================= STATE ================= */
const isOpen = ref(false) // mobile drawer
const isCollapsed = ref(false) // desktop collapse

/* ================= MENU ================= */
const menus = [
  { name: 'Dashboard', to: '/', icon: '🏠' },
  { name: 'Manage Project', to: '/project', icon: '📁' }, 
  { name: 'Manage Reports', to: '/reports', icon: '📊' },
]

const isActive = (path: string) =>
  computed(() => route.path === path || route.path.startsWith(path + '/'))
</script>

<template>
  <!-- OVERLAY (MOBILE) -->
  <div v-if="isOpen" @click="isOpen = false" class="fixed inset-0 bg-black/60 z-30 lg:hidden" />

  <!-- SIDEBAR -->
  <aside
    class="fixed top-0 left-0 h-screen z-40 bg-primary border-r border-black/10 flex flex-col transition-all duration-300"
    :class="[
      isCollapsed ? 'w-20' : 'w-64',
      isOpen ? 'translate-x-0' : '-translate-x-full',
      'lg:translate-x-0',
    ]"
  >
    <!-- HEADER -->
    <div class="h-16 flex items-center justify-between px-4 border-b border-black/10">
      <h1 v-if="!isCollapsed" class="text-lg font-bold text-text-primary">
        <span class="text-text-secondary">Admin</span> Dashboard
      </h1>

      <button
        @click="isCollapsed = !isCollapsed"
        class="hidden lg:flex text-text-secondary hover:text-text-primary transition"
      >
        ☰
      </button>
    </div>

    <!-- MENU -->
    <nav class="flex-1 px-3 py-4 space-y-2">
      <RouterLink
        v-for="menu in menus"
        :key="menu.name"
        :to="menu.to"
        @click="isOpen = false"
        class="flex items-center gap-3 px-4 py-3 rounded-xl text-text-primary transition-all duration-200"
        :class="
          isActive(menu.to).value
            ? 'bg-linear-to-r from-third to-secondary shadow-sm'
            : 'hover:bg-fourth/40'
        "
      >
        <span class="text-lg opacity-80">{{ menu.icon }}</span>
        <span v-if="!isCollapsed" class="font-medium">
          {{ menu.name }}
        </span>
      </RouterLink>
    </nav>

    <!-- FOOTER -->
    <div v-if="!isCollapsed" class="p-4 border-t border-black/10 text-sm text-text-secondary">
      Project Management System v 1.0
    </div>
  </aside>

  <!-- MOBILE TOP BAR -->
  <header
    class="lg:hidden fixed top-0 left-0 right-0 h-16 bg-secondary border-b border-black/10 flex items-center px-4 z-20"
  >
    <button @click="isOpen = true" class="text-text-primary text-xl">☰</button>
    <h1 class="ml-4 font-semibold text-text-primary">Admin Dashboard</h1>
  </header>
</template>
