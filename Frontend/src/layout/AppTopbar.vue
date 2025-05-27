<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useLayout } from '@/layout/composables/layout';
import { useToast } from 'primevue/usetoast';

const router = useRouter();
const route = useRoute();
const toast = useToast();
const { toggleMenu, toggleDarkMode, isDarkTheme } = useLayout();

// Get user data from localStorage or your auth store
const user = ref({
    name: localStorage.getItem('userName') || 'User',
    email: localStorage.getItem('userEmail') || 'user@example.com',
    role: localStorage.getItem('userRole') || null,
    rigName: localStorage.getItem('rigName') || 'Default Rig'
});

const userType = computed(() => {
    if (user.value.role === 'admin') {
        return 'Admin';
    } else if (user.value.role === 'user') {
        return 'User';
    } else {
        return 'Guest';
    }
});

const dashboardPath = computed(() => {
    if (user.value.role === 'admin') {
        return '/admin/dashboard';
    } else if (user.value.role === 'user') {
        return '/user/dashboard';
    }
    return '/';
});

const logout = () => {
    // Clear all authentication data
    localStorage.removeItem('isAuthenticated');
    localStorage.removeItem('userRole');
    localStorage.removeItem('token');
    localStorage.removeItem('userEmail');
    localStorage.removeItem('userName');
    localStorage.removeItem('rigName');
    
    // Show logout message
    toast.add({
        severity: 'success',
        summary: 'Logged Out',
        detail: 'You have been successfully logged out.',
        life: 3000
    });

    // Redirect to welcome page
    router.push({ name: 'welcome' });
};

const nestedMenuitems = ref([
    {
        label: 'Account (' + userType.value + ')',
        icon: 'pi pi-fw pi-user',
        items: [
            {
                label: user.value.name,
                icon: 'pi pi-fw pi-user',
                disabled: true
            },
            {
                label: user.value.email,
                icon: 'pi pi-fw pi-envelope',
                disabled: true
            },
            {
                label: user.value.rigName,
                icon: 'pi pi-fw pi-sitemap',
                disabled: true
            },
            {
                separator: true
            },
            {
                label: 'Settings',
                icon: 'pi pi-fw pi-cog',
                command: () => {
                    router.push(user.value.role === 'admin' 
                        ? '/admin/settings' 
                        : '/user/settings');
                }
            },
            {
                label: 'Logout',
                icon: 'pi pi-fw pi-sign-out',
                command: logout
            }
        ]
    }
]);

// Update menu items when user changes
onMounted(() => {
    nestedMenuitems.value[0].label = 'Account (' + userType.value + ')';
    nestedMenuitems.value[0].items[0].label = user.value.name;
    nestedMenuitems.value[0].items[1].label = user.value.email;
    nestedMenuitems.value[0].items[2].label = user.value.rigName;
});
</script>

<template>
    <div class="layout-topbar">
        <div class="layout-topbar-logo-container">
            <button class="layout-menu-button layout-topbar-action" @click="toggleMenu">
                <i class="pi pi-bars"></i>
            </button>
            <router-link :to="dashboardPath" class="layout-topbar-logo">
                <svg viewBox="0 0 54 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Your SVG logo remains unchanged -->
                    <path fill-rule="evenodd" clip-rule="evenodd" d="..." fill="var(--primary-color)" />
                </svg>
                <span>OCMS</span>
            </router-link>
            <span class="ml-2 text-sm text-gray-400">({{ userType }})</span>
        </div>

        <div class="layout-topbar-actions">
            <Menubar :model="nestedMenuitems" />
            
            <div class="layout-config-menu">
                <button type="button" class="layout-topbar-action" @click="toggleDarkMode">
                    <i :class="['pi', { 'pi-moon': isDarkTheme, 'pi-sun': !isDarkTheme }]"></i>
                </button>

                <div class="relative">
                    <button
                        v-styleclass="{ 
                            selector: '@next', 
                            enterFromClass: 'hidden', 
                            enterActiveClass: 'animate-scalein', 
                            leaveToClass: 'hidden', 
                            leaveActiveClass: 'animate-fadeout', 
                            hideOnOutsideClick: true 
                        }"
                        type="button" class="layout-topbar-action layout-topbar-action-highlight">
                        <i class="pi pi-palette"></i>
                    </button>
                    <AppConfigurator />
                </div>
            </div>

            <button class="layout-topbar-menu-button layout-topbar-action"
                v-styleclass="{ 
                    selector: '@next', 
                    enterFromClass: 'hidden', 
                    enterActiveClass: 'animate-scalein', 
                    leaveToClass: 'hidden', 
                    leaveActiveClass: 'animate-fadeout', 
                    hideOnOutsideClick: true 
                }">
                <i class="pi pi-ellipsis-v"></i>
            </button>
        </div>
    </div>
</template>

<style scoped>
.layout-topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 2rem;
    height: 70px;
    background-color: var(--surface-card);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 1000;
}

.layout-topbar-logo-container {
    display: flex;
    align-items: center;
}

.layout-topbar-logo {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: var(--text-color);
    font-weight: 600;
    font-size: 1.25rem;
}

.layout-topbar-logo svg {
    height: 2rem;
    margin-right: 0.5rem;
}

.layout-topbar-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.layout-config-menu {
    display: flex;
    gap: 0.5rem;
}

.layout-topbar-action {
    background: transparent;
    border: none;
    cursor: pointer;
    color: var(--text-color);
    border-radius: 50%;
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.2s;
}

.layout-topbar-action:hover {
    background-color: var(--surface-hover);
}

.layout-topbar-action-highlight {
    background-color: var(--primary-color);
    color: white;
}

.layout-topbar-action-highlight:hover {
    background-color: var(--primary-600);
}

.p-menubar {
    background: transparent;
    border: none;
}

.text-gray-400 {
    color: var(--text-color-secondary);
}
</style>