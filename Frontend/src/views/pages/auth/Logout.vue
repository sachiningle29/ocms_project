<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useToast } from 'primevue/usetoast';

const router = useRouter();
const toast = useToast();

onMounted(() => {
  // Clear all authentication data
  localStorage.removeItem('isAuthenticated');
  localStorage.removeItem('userRole');
  localStorage.removeItem('token'); // If you're using tokens
  localStorage.removeItem('userEmail'); // If you store user email
  localStorage.removeItem('adminEmail'); // If you store admin email

  // Show logout success message
  toast.add({
    severity: 'success',
    summary: 'Logged Out',
    detail: 'You have been successfully logged out.',
    life: 3000
  });

  // Redirect to welcome page after a short delay
  setTimeout(() => {
    router.push({ name: 'welcome' });
  }, 500);
});
</script>

<template>
  <div class="flex justify-content-center align-items-center min-h-screen">
    <div class="text-center">
      <i class="pi pi-spin pi-spinner" style="font-size: 2rem"></i>
      <p class="mt-3">Logging out...</p>
    </div>
  </div>
</template>

<style scoped>
.min-h-screen {
  min-height: 100vh;
}
</style>