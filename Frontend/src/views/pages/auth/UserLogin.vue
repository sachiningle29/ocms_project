<script setup>
import FloatingConfigurator from '@/components/FloatingConfigurator.vue';
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useStore } from 'vuex';

import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Checkbox from 'primevue/checkbox';
import Button from 'primevue/button';

const store = useStore();
const router = useRouter();

const loading = ref(false);
const errorMsg = ref("");

const user = ref({
  email: '',
  password: '',
  remember: false
});

function validate() {
  let valid = true;
  errors.value = { email: '', password: '' };

  if (!user.value.email) {
    errors.value.email = 'Email is required';
    valid = false;
  } else if (!/^\S+@\S+\.\S+$/.test(user.value.email)) {
    errors.value.email = 'Please enter a valid email';
    valid = false;
  }

  if (!user.value.password) {
    errors.value.password = 'Password is required';
    valid = false;
  }

  return valid;
}

const errors = ref({
  email: '',
  password: ''
});

function login() {
  if (!validate()) return;
  
  loading.value = true;
  errorMsg.value = "";

  store.dispatch('Userlogin', user.value)
    .then(() => {
      loading.value = false;
      router.push({ name: 'Userdashboard' });
    })
    .catch(({ response }) => {
      loading.value = false;
      errorMsg.value = response?.data?.message || "Login failed.";
      console.log(errorMsg.value);
    });
}
</script>


<template>
  <FloatingConfigurator />

  <form @submit.prevent="login">
    <div class="bg-surface-50 dark:bg-surface-950 flex items-center justify-center min-h-screen min-w-[100vw] overflow-hidden">
      <div class="flex flex-col items-center justify-center">
        <div style="border-radius: 56px; padding: 0.3rem; background: linear-gradient(180deg, var(--primary-color) 10%, rgba(33, 150, 243, 0) 30%)">
          <div class="w-full bg-surface-0 dark:bg-surface-900 py-20 px-8 sm:px-20" style="border-radius: 53px">
            <div class="text-center mb-8">
              <!-- SVG logo omitted for brevity -->
              <div class="text-surface-900 dark:text-surface-0 text-3xl font-medium mb-4">Welcome to User Portal</div>
              <span class="text-muted-color font-medium">Sign in to continue</span>
            </div>

            <div>
              <label for="email1" class="block text-surface-900 dark:text-surface-0 text-xl font-medium mb-2">Email</label>
              <InputText id="email1" type="text" placeholder="Email address" class="w-full md:w-[30rem] mb-8" v-model="user.email" />

              <label for="password1" class="block text-surface-900 dark:text-surface-0 font-medium text-xl mb-2">Password</label>
              <Password id="password1" v-model="user.password" placeholder="Password" :toggleMask="true" class="mb-4" fluid :feedback="false" />

              <div class="flex items-center justify-between mt-2 mb-8 gap-8">
                <div class="flex items-center">
                  <Checkbox v-model="user.remember" id="rememberme1" binary class="mr-2" />
                  <label for="rememberme1">Remember me</label>
                </div>
                <!-- Uncomment if you want to add forgot password -->
                <!-- <span class="font-medium no-underline ml-2 text-right cursor-pointer text-primary">Forgot password?</span> -->
              </div>

              <Button label="Login as user" class="w-full" type="submit" :loading="loading" />

              <div v-if="errorMsg" class="mt-4 text-red-600 font-medium text-center">
                {{ errorMsg }}
              </div>
            </div>

            <div class="mt-8 text-center">
              <router-link to="/" class="text-primary font-medium hover:underline">Back to Welcome Page</router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>

<style scoped>
.pi-eye {
  transform: scale(1.6);
  margin-right: 1rem;
}

.pi-eye-slash {
  transform: scale(1.6);
  margin-right: 1rem;
}
</style>
