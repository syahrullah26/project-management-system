<template>
  <Teleport to="body">
    <!-- OVERLAY TRANSITION -->
    <transition
      enter-active-class="transition-opacity duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="modelValue"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
        @click.self="close"
      >
        <!-- MODAL TRANSITION -->
        <transition
          enter-active-class="transition-all duration-300 ease-out"
          enter-from-class="opacity-0 scale-95 translate-y-4"
          enter-to-class="opacity-100 scale-100 translate-y-0"
          leave-active-class="transition-all duration-200 ease-in"
          leave-from-class="opacity-100 scale-100 translate-y-0"
          leave-to-class="opacity-0 scale-95 translate-y-4"
        >
          <div
            v-if="modelValue"
            class="w-full max-w-lg rounded-2xl bg-white shadow-xl"
          >
            <!-- HEADER -->
            <div class="border-b px-5 py-4">
              <h3 class="text-lg font-semibold text-gray-800">
                Detail Reports
              </h3>
            </div>

            <!-- BODY -->
            <div class="px-5 py-4 space-y-3 text-sm text-gray-700">
              <slot />
            </div>

            <!-- FOOTER -->
            <div class="border-t px-5 py-3 flex justify-end">
              <button
                @click="close"
                class="rounded-lg bg-gray-600 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition"
              >
                Tutup
              </button>
            </div>
          </div>
        </transition>
      </div>
    </transition>
  </Teleport>
</template>

<script setup lang="ts">
defineProps<{
  modelValue: boolean
}>()

const emit = defineEmits(['update:modelValue'])

const close = () => {
  emit('update:modelValue', false)
}
</script>
