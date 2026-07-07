<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import axiosClient from '@/axios';
import { useRouter, useRoute } from 'vue-router';
import { useLayout } from '@/layout/composables/layout';
import { useToast } from 'primevue/usetoast';
import ViewContractDialogueForm from '@/components/HiringContracts/ViewContractDialog.vue';

const router = useRouter();
const route = useRoute();
const toast = useToast();
const { toggleMenu, toggleDarkMode, isDarkTheme } = useLayout();

const showDropdown = ref(false);
const showAllModal = ref(false);
const showContractModal = ref(false);
const notifications = ref([]);
const selectedContract = ref(null); // Initialize as null instead of empty object
const unreadCount = computed(() => notifications.value.filter(n => !n.read).length);

const user = ref({
    name: '',
    email: '',
    role: null,
    rigName: '',
    id: null,
});

const isAdmin = computed(() => user.value.role === 'admin');
const userType = computed(() =>
    isAdmin.value ? 'Admin' :
        (user.value.role === 'user' ? 'User' : 'Guest')
);
const dashboardPath = computed(() =>
    isAdmin.value ? '/admin/dashboard' :
        (user.value.role === 'user' ? '/user/dashboard' : '/')
);

const nestedMenuitems = ref([
    {
        label: 'Account',
        icon: 'pi pi-fw pi-user',
        items: [
            { label: 'User', icon: 'pi pi-fw pi-user', disabled: true },
            { label: 'user@example.com', icon: 'pi pi-fw pi-envelope', disabled: true },
            { label: 'Default Rig', icon: 'pi pi-fw pi-sitemap', disabled: true },
            { separator: true },
            {
                label: 'Settings',
                icon: 'pi pi-fw pi-cog',
                command: () => {
                    router.push(isAdmin.value ? '/admin/settings' : '/user/settings');
                },
            },
            {
                label: 'Logout',
                icon: 'pi pi-fw pi-sign-out',
                command: () => logout(),
            },
        ],
    },
]);

// Fetch latest notifications (both read and unread)
const fetchNotifications = async () => {
    try {
        const res = await axiosClient.get('/notifications/all');
        notifications.value = res.data.notifications.map(n => ({
            ...n,
            read: n.read || n.pivot?.read || false
        })).slice(0, 10);
    } catch (err) {
        console.error('Error fetching notifications:', err);
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Failed to load notifications',
            life: 3000
        });
    }
};

// Fetch contract details by ID
const fetchContractById = async (contractId) => {
    try {
        console.log('Making API call to /contracts/' + contractId);
        const res = await axiosClient.get(`/contracts/${contractId}`);
        console.log('API Response:', res);
        console.log('Response data:', res.data);

        // Try different possible response structures
        const contract = res.data.contract || res.data.data || res.data;
        console.log('Extracted contract:', contract);

        return contract;
    } catch (err) {
        console.error('Error fetching contract:', err);
        console.error('Error response:', err.response);
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Failed to load contract details',
            life: 3000
        });
        return null;
    }
};

const markAsReadAndShowContract = async (notification) => {
    console.log('Clicked notification:', notification);

    try {
        // Mark as read if unread
        if (!notification.read) {
            await axiosClient.post(`/notifications/${notification.id}/mark-read`);
            // Update the notification in the local array
            const notificationIndex = notifications.value.findIndex(n => n.id === notification.id);
            if (notificationIndex !== -1) {
                notifications.value[notificationIndex].read = true;
            }
        }

        // Close dropdowns
        showDropdown.value = false;
        showAllModal.value = false;

        // Fetch and show contract
        if (notification.model_id) {
            console.log('Fetching contract ID:', notification.model_id);
            const contractData = await fetchContractById(notification.model_id);
            console.log('Contract data received:', contractData);

            if (contractData && Object.keys(contractData).length > 0) {
                selectedContract.value = contractData;
                console.log('Opening contract modal with data:', selectedContract.value);
                showContractModal.value = true;
            } else {
                console.log('Contract data is empty or null');
                // Show error message
                toast.add({
                    severity: 'error',
                    summary: 'Contract Not Found',
                    detail: `Contract with ID ${notification.model_id} could not be found`,
                    life: 3000
                });
            }
        } else {
            console.log('No model_id in notification');
            toast.add({
                severity: 'warn',
                summary: 'Invalid Notification',
                detail: 'This notification is not linked to a contract',
                life: 3000
            });
        }

    } catch (err) {
        console.error('Error:', err);
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Failed to process notification',
            life: 3000
        });
    }
};

const markAllAsRead = async () => {
    try {
        await axiosClient.post('/notifications/mark-all-read');
        notifications.value.forEach(n => n.read = true);
        showAllModal.value = false;
        toast.add({
            severity: 'success',
            summary: 'Notifications Cleared',
            detail: 'All notifications have been marked as read.',
            life: 3000
        });
    } catch (err) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Failed to mark notifications as read',
            life: 3000
        });
    }
};

const logout = () => {
    localStorage.clear();
    toast.add({
        severity: 'success',
        summary: 'Logged Out',
        detail: 'You have been successfully logged out.',
        life: 3000,
    });
    router.push({ name: 'welcome' });
};

// Close dropdown when clicking outside
const closeDropdown = () => {
    showDropdown.value = false;
};

// Handle contract modal close
const handleContractModalClose = () => {
    showContractModal.value = false;
    selectedContract.value = null;
};

onMounted(async () => {
    try {
        const res = await axiosClient.get('/get-user-details');
        const data = res.data.user.userdata;

        user.value.name = data.name;
        user.value.email = data.email;
        user.value.role = data.is_admin === 1 ? 'admin' : 'user';
        user.value.rigName = data.cpf_no ?? 'Default Rig';
        user.value.id = data.id;

        nestedMenuitems.value[0].label = `Account (${userType.value})`;
        nestedMenuitems.value[0].items[0].label = data.name;
        nestedMenuitems.value[0].items[1].label = data.email;
        nestedMenuitems.value[0].items[2].label = user.value.rigName;

        if (isAdmin.value) {
            await fetchNotifications();

            if (window.Echo && user.value.id) {
                window.Echo.private(`notifications.${user.value.id}`)
                    .listen('.NewNotificationCreated', (e) => {
                        notifications.value.unshift({
                            ...e.notification,
                            read: false
                        });
                    });
            }
        }

        // Add click outside listener to close dropdown
        document.addEventListener('click', (event) => {
            const notificationWrapper = document.querySelector('.notification-wrapper');
            if (notificationWrapper && !notificationWrapper.contains(event.target)) {
                closeDropdown();
            }
        });
    } catch (err) {
        toast.add({
            severity: 'error',
            summary: 'User Load Failed',
            detail: 'Could not fetch user details.',
            life: 3000
        });
    }
});

// Reconnect Echo when role becomes admin dynamically
watch(() => user.value.role, async (newRole) => {
    if (newRole === 'admin' && window.Echo && user.value.id) {
        await fetchNotifications();
        window.Echo.private(`notifications.${user.value.id}`)
            .listen('.NewNotificationCreated', (e) => {
                notifications.value.unshift({
                    ...e.notification,
                    read: false
                });
            });
    }
});
</script>

<template>
    <div class="layout-topbar">
        <div class="layout-topbar-logo-container">
            <button class="layout-menu-button layout-topbar-action" @click="toggleMenu">
                <i class="pi pi-bars"></i>
            </button>
            <router-link :to="dashboardPath" class="layout-topbar-logo">
                <svg viewBox="0 0 54 40" fill="none" xmlns="http://www.w3.org/2000/svg" width="32" height="24">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M27 0C41.912 0 54 12.088 54 27s-12.088 27-27 27S0 41.912 0 27 12.088 0 27 0zm0 6C16.507 6 8 14.507 8 25s8.507 19 19 19 19-8.507 19-19S37.493 6 27 6zm0 8c6.075 0 11 4.925 11 11s-4.925 11-11 11-11-4.925-11-11 4.925-11 11-11z"
                        fill="var(--primary-color)" />
                </svg>
                <span>OCMS</span>
            </router-link>
            <span class="ml-2 text-sm text-gray-400">({{ userType }})</span>
        </div>

        <div class="layout-topbar-actions">
            <!-- Notifications: Only for Admin -->
            <div v-if="isAdmin" class="notification-wrapper">
                <button class="notification-icon" @click="showDropdown = !showDropdown">
                    <i class="pi pi-bell"></i>
                    <span v-if="unreadCount > 0" class="notification-badge">
                        {{ unreadCount > 9 ? '9+' : unreadCount }}
                    </span>
                </button>

                <div v-if="showDropdown" class="notification-dropdown" @click.stop>
                    <div class="notification-dropdown-header">
                        <h4>Notifications</h4>
                        <button @click="closeDropdown" class="close-dropdown">
                            <i class="pi pi-times"></i>
                        </button>
                    </div>
                    <ul v-if="notifications.length > 0">
                        <li v-for="n in notifications" :key="n.id" @click="markAsReadAndShowContract(n)"
                            :class="{ 'unread': !n.read, 'read': n.read }" class="notification-dropdown-item">
                            <div class="notification-content">
                                <span class="title">{{ n.title }}</span>
                                <p class="msg">{{ n.message }}</p>
                                <span class="notification-time">{{ new Date(n.created_at).toLocaleString() }}</span>
                            </div>
                            <span v-if="!n.read" class="unread-dot"></span>
                        </li>
                    </ul>
                    <p v-else class="empty-text">No notifications.</p>
                    <div class="dropdown-actions">
                        <button @click="showAllModal = true; showDropdown = false;" class="see-all-btn">
                            See All
                        </button>
                    </div>
                </div>
            </div>

            <Menubar :model="nestedMenuitems" />

            <div class="layout-config-menu">
                <button type="button" class="layout-topbar-action" @click="toggleDarkMode">
                    <i :class="['pi', { 'pi-moon': isDarkTheme, 'pi-sun': !isDarkTheme }]"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal: All Notifications -->
    <!-- Modal: All Notifications -->
    <Dialog v-model:visible="showAllModal" modal header="All Notifications" :style="{ width: '50vw' }"
        :breakpoints="{ '960px': '75vw', '640px': '90vw' }">
        <div class="notification-modal-content">
            <div v-if="notifications.length > 0" class="notification-list">
                <div v-for="n in notifications" :key="n.id" @click="markAsReadAndShowContract(n)"
                    class="notification-item" :class="{ 'unread': !n.read, 'read': n.read }">
                    <div class="notification-header">
                        <span class="notification-title">{{ n.title }}</span>
                        <span class="notification-time">{{ new Date(n.created_at).toLocaleString() }}</span>
                        <span v-if="!n.read" class="unread-dot"></span>
                    </div>
                    <div class="notification-body">
                        {{ n.message }}
                    </div>
                </div>
            </div>
            <div v-else class="empty-notifications">
                <i class="pi pi-inbox" style="font-size: 2rem"></i>
                <p>No notifications</p>
            </div>
        </div>

        <!-- Footer with buttons -->
        <template #footer>
            <div class="modal-footer-buttons">
                <!-- Mark All as Read button - only show when there are notifications and at least one is unread -->
                <Button v-if="notifications.length > 0 && notifications.filter(n => !n.read).length > 0"
                    label="Mark All as Read" icon="pi pi-check" severity="secondary" class="p-button-outlined"
                    @click="markAllAsRead" />

                <!-- Close button -->
                <Button label="Close" icon="pi pi-times" severity="info" @click="showAllModal = false" />
            </div>
        </template>
    </Dialog>
    <!-- Contract Details Modal -->
    <ViewContractDialogueForm v-model:visible="showContractModal" :contract="selectedContract" />

    <!-- Debug info -->
    <div v-if="showContractModal"
        style="position: fixed; top: 10px; right: 10px; background: red; color: white; padding: 10px; z-index: 9999;">
        Modal should be open! Contract: {{ selectedContract?.id }}
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

.layout-topbar-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
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

.notification-wrapper {
    position: relative;
    margin-right: 1.5rem;
}

.notification-icon {
    position: relative;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1.75rem;
    color: var(--text-color);
    padding: 0.5rem;
    transition: transform 0.2s;
}

.notification-icon:hover {
    transform: scale(1.1);
}

.notification-badge {
    position: absolute;
    top: 5px;
    right: 5px;
    background: #ff3b30;
    color: white;
    font-size: 0.75rem;
    font-weight: bold;
    min-width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
    border: 2px solid var(--surface-card);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    transform: translate(30%, -30%);
    animation: pop-in 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    padding: 0 4px;
    min-width: 22px;
}

@keyframes pop-in {
    0% {
        transform: translate(30%, -30%) scale(0);
        opacity: 0;
    }

    80% {
        transform: translate(30%, -30%) scale(1.15);
    }

    100% {
        transform: translate(30%, -30%) scale(1);
        opacity: 1;
    }
}

.notification-badge::after {
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background: rgba(255, 59, 48, 0.4);
    animation: pulse 2s infinite;
    z-index: -1;
}

@keyframes pulse {
    0% {
        transform: scale(1);
        opacity: 1;
    }

    100% {
        transform: scale(1.8);
        opacity: 0;
    }
}

.notification-dropdown {
    position: absolute;
    right: 0;
    top: 100%;
    margin-top: 0.5rem;
    background: var(--surface-card);
    border: 1px solid var(--surface-border);
    border-radius: 0.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    width: 350px;
    z-index: 999;
    overflow: hidden;
}

.notification-dropdown-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: var(--surface-ground);
    border-bottom: 1px solid var(--surface-border);
}

.notification-dropdown-header h4 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text-color);
}

.close-dropdown {
    background: none;
    border: none;
    cursor: pointer;
    color: var(--text-color-secondary);
    padding: 0.25rem;
    border-radius: 50%;
    transition: all 0.2s;
}

.close-dropdown:hover {
    background: var(--surface-hover);
    color: var(--text-color);
}

.notification-dropdown ul {
    list-style: none;
    padding: 0;
    margin: 0;
    max-height: 400px;
    overflow-y: auto;
}

.notification-dropdown-item {
    border-bottom: 1px solid var(--surface-border);
    padding: 1rem;
    position: relative;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.notification-dropdown-item:hover {
    background-color: var(--surface-hover);
}

.notification-dropdown-item.unread {
    background-color: rgba(var(--primary-color-rgb), 0.1);
    border-left: 3px solid var(--primary-color);
}

.notification-dropdown-item.read {
    opacity: 0.8;
}

.notification-content {
    flex: 1;
    padding-right: 1rem;
}

.notification-content .title {
    font-weight: 600;
    color: var(--text-color);
    display: block;
    margin-bottom: 0.25rem;
}

.notification-content .msg {
    font-size: 0.9rem;
    color: var(--text-color-secondary);
    margin: 0.25rem 0;
    line-height: 1.4;
}

.notification-content .notification-time {
    font-size: 0.8rem;
    color: var(--text-color-secondary);
    display: block;
    margin-top: 0.25rem;
}

.unread-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    background-color: var(--primary-color);
    border-radius: 50%;
    flex-shrink: 0;
}

.dropdown-actions {
    padding: 1rem;
    background: var(--surface-ground);
    border-top: 1px solid var(--surface-border);
}

.see-all-btn {
    width: 100%;
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 0.75rem;
    border-radius: 0.375rem;
    cursor: pointer;
    font-weight: 500;
    transition: background-color 0.2s;
}

.see-all-btn:hover {
    background: var(--primary-color-dark);
}

.empty-text {
    text-align: center;
    color: var(--text-color-secondary);
    padding: 2rem 1rem;
}

.notification-modal-content {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.notification-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    max-height: 60vh;
    overflow-y: auto;
    padding-right: 0.5rem;
}

.notification-item {
    background: var(--surface-card);
    border-left: 4px solid var(--surface-border);
    padding: 1rem;
    border-radius: 0 6px 6px 0;
    transition: all 0.2s;
    cursor: pointer;
}

.notification-item.unread {
    background-color: rgba(var(--primary-color-rgb), 0.1);
    border-left-color: var(--primary-color);
}

.notification-item.read {
    opacity: 0.8;
    border-left-color: var(--surface-border);
}

.notification-item:hover {
    background: var(--surface-hover);
    transform: translateX(2px);
}

.notification-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
    position: relative;
}

.notification-title {
    font-weight: 600;
    color: var(--text-color);
}

.notification-time {
    font-size: 0.8rem;
    color: var(--text-color-secondary);
}

.notification-body {
    color: var(--text-color);
    line-height: 1.5;
}

.empty-notifications {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem;
    color: var(--text-color-secondary);
}

.modal-footer-buttons {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
}
</style>