<script setup>
import axiosClient from '@/axios'; // Use the custom Axios client
import { onMounted, ref, reactive } from 'vue';
import { useToast } from 'primevue/usetoast';
import { FilterMatchMode } from '@primevue/core/api';

const toast = useToast();
const dt = ref();

const users = ref([]);
const userDialog = ref(false);
const deleteUserDialog = ref(false);
const deleteUsersDialog = ref(false);

const user = reactive({
    id: null,
    name: '',
    email: '',
    cpf_no: '',
    password: '',
    is_admin: 0,
    user_status: 'active'
});

const userTypes = [
    { label: 'Admin', value: 1 },
    { label: 'User', value: 0 }
];

const showPassword = ref(false);
function togglePassword() {
    showPassword.value = !showPassword.value;
}

const userStatusOptions = [
    { label: 'Active', value: 'active' },
    { label: 'Inactive', value: 'inactive' }
];

const selectedUsers = ref([]);
const submitted = ref(false);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

const apiBase = '/users';

onMounted(() => {
    loadUsers();
});

function loadUsers() {
    axiosClient
        .get(apiBase)
        .then((response) => {
            users.value = response.data || [];
        })
        .catch((error) => {
            console.error('loadUsers error:', error.response?.data || error.message);
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load users', life: 3000 });
        });
}

function openNew() {
    Object.assign(user, {
        id: null,
        name: '',
        email: '',
        cpf_no: '',
        password: '',
        is_admin: 0, 
        user_status: 'active'
    });
    submitted.value = false;
    userDialog.value = true;
}

function hideDialog() {
    userDialog.value = false;
    submitted.value = false;
}

function saveUser() {
    submitted.value = true;

    // Name validation
    if (!user.name?.trim()) {
        toast.add({ severity: 'warn', summary: 'Validation', detail: 'Name is required', life: 3000 });
        return;
    }

    // Email validation
    if (!user.email?.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(user.email)) {
        toast.add({ severity: 'warn', summary: 'Validation', detail: 'A valid email is required', life: 3000 });
        return;
    }

    // Password validation
    if (!user.id && !user.password?.trim()) {
        toast.add({ severity: 'warn', summary: 'Validation', detail: 'Password is required', life: 3000 });
        return;
    }

    // is_admin validation
    if (![0, 1].includes(user.is_admin)) {
        toast.add({ severity: 'warn', summary: 'Validation', detail: 'User type must be selected', life: 3000 });
        return;
    }

    // user_status validation
    if (!['active', 'inactive'].includes(user.user_status)) {
        toast.add({ severity: 'warn', summary: 'Validation', detail: 'User status must be selected', life: 3000 });
        return;
    }

    // Prepare payload
    const payload = { ...user };
    if (user.id && !user.password?.trim()) {
        delete payload.password; 
    }

    const request = user.id
        ? axiosClient.put(`/users/${user.id}`, payload)
        : axiosClient.post('/users', payload);

    request
        .then(() => {
            toast.add({
                severity: 'success',
                summary: 'Success',
                detail: user.id ? 'User updated successfully' : 'User created successfully',
                life: 3000
            });
            loadUsers();
            userDialog.value = false;
        })
        .catch((error) => {
            console.error('saveUser error:', error.response?.data || error.message);
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to save user', life: 3000 });
        });
}


function editUser(u) {
    axiosClient
        .get(`${apiBase}/${u.id}`)
        .then((response) => {
            Object.assign(user, response.data);
            userDialog.value = true;
        })
        .catch((error) => {
            console.error('editUser error:', error.response?.data || error.message);
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load user details', life: 3000 });
        });
}

function confirmDeleteUser(u) {
    Object.assign(user, u);
    deleteUserDialog.value = true;
}

function deleteUser() {
    axiosClient
        .delete(`${apiBase}/${user.id}`)
        .then(() => {
            toast.add({ severity: 'success', summary: 'Deleted', detail: 'User deleted', life: 3000 });
            deleteUserDialog.value = false;
            loadUsers();
        })
        .catch((error) => {
            console.error('deleteUser error:', error.response?.data || error.message);
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete user', life: 3000 });
        });
}

function confirmDeleteSelected() {
    deleteUsersDialog.value = true;
}

function deleteSelectedUsers() {
    const deletePromises = selectedUsers.value.map((u) => axiosClient.delete(`${apiBase}/${u.id}`));
    Promise.all(deletePromises)
        .then(() => {
            toast.add({ severity: 'success', summary: 'Deleted', detail: 'Selected users deleted', life: 3000 });
            deleteUsersDialog.value = false;
            selectedUsers.value = [];
            loadUsers();
        })
        .catch((error) => {
            console.error('deleteSelectedUsers error:', error.response?.data || error.message);
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete selected users', life: 3000 });
        });
}


</script>

<template>
    <div class="card">
        <Toolbar class="mb-4">
            <template #start>
                <Button label="New" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openNew" />
                <Button label="Delete" icon="pi pi-trash" severity="secondary" @click="confirmDeleteSelected" :disabled="!selectedUsers || !selectedUsers.length" />
            </template>
        </Toolbar>

        <DataTable ref="dt" v-model:selection="selectedUsers" :value="users" dataKey="id" :paginator="true" :rows="10" :filters="filters" :rowsPerPageOptions="[5, 10, 25]" currentPageReportTemplate="Showing {first} to {last} of {totalRecords} users">
            <template #header>
                <div class="flex justify-between items-center">
                    <h4 class="m-0">User Management</h4>
                    <span class="p-input-icon-left">
                        <i class="pi pi-search" />
                        <InputText v-model="filters['global'].value" placeholder="Search..." />
                    </span>
                </div>
            </template>

            <Column selectionMode="multiple" style="width: 3rem" :exportable="false" />
            <Column header="Sr No." style="width: 8rem">
                <template #body="slotProps">
                    {{ slotProps.index + 1 }}
                </template>
            </Column>
            <Column field="name" header="User Name" sortable />
            <Column field="email" header="Email" sortable />
            <Column field="cpf_no" header="CPF" sortable />
            <Column field="is_admin" header="Type" sortable>
                <template #body="slotProps">
                    {{ slotProps.data.is_admin === 1 ? 'Admin' : 'User' }}
                </template>
            </Column>

            <Column field="user_status" header="Status" sortable />

            <Column :exportable="false" header="Actions" style="width: 10rem">
                <template #body="slotProps">
                    <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editUser(slotProps.data)" />
                    <Button icon="pi pi-trash" outlined rounded severity="danger" @click="confirmDeleteUser(slotProps.data)" />
                </template>
            </Column>

            <template #empty>
                <div class="text-center text-gray-500 py-4">No users found.</div>
            </template>
        </DataTable>

        <!-- Create/Edit Dialog -->
        <Dialog v-model:visible="userDialog" :draggable="false" modal header="User Details" :closable="false" style="width: 40vw">
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="font-bold mb-1 block">Username</label>
                        <InputText v-model="user.name" class="w-full" />
                    </div>

                    <div>
                        <label class="font-bold mb-1 block">Email</label>
                        <InputText v-model="user.email" type="email" class="w-full" />
                    </div>

                    <div>
                        <label class="font-bold mb-1 block">CPF No</label>
                        <InputText v-model="user.cpf_no" class="w-full" />
                    </div>

                    <div>
                        <label class="font-bold mb-1 block">Password</label>
                        <div class="relative">
                            <InputText v-model="user.password" :type="showPassword ? 'text' : 'password'" class="w-full pr-10" />
                            <button type="button" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500" @click="togglePassword">
                                <i :class="showPassword ? 'pi pi-eye-slash' : 'pi pi-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="font-bold mb-1 block">User Type</label>
                        <Dropdown v-model="user.is_admin" :options="userTypes" optionLabel="label" optionValue="value" placeholder="Select Type" class="w-full" />
                    </div>

                    <div>
                        <label class="font-bold mb-1 block">User Status</label>
                        <Dropdown v-model="user.user_status" :options="userStatusOptions" optionLabel="label" optionValue="value" placeholder="Select Status" class="w-full" />
                    </div>
                </div>
            </div>

            <template #footer>
                <Button label="Close" icon="pi pi-times" class="p-button-text" @click="hideDialog" />
                <Button label="Save" icon="pi pi-check" class="p-button-primary" @click="saveUser" />
            </template>
        </Dialog>

        <!-- Delete One -->
        <Dialog v-model:visible="deleteUserDialog" modal header="Confirm" :style="{ width: '450px' }">
            <div class="confirmation-content">
                <i class="pi pi-exclamation-triangle mr-3 text-red-500" style="font-size: 2rem" />
                <span
                    >Are you sure you want to delete <b>{{ user.name }}</b
                    >?</span
                >
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" text @click="deleteUserDialog = false" />
                <Button label="Yes" icon="pi pi-check" severity="danger" @click="deleteUser" />
            </template>
        </Dialog>

        <!-- Delete Multiple -->
        <Dialog v-model:visible="deleteUsersDialog" modal header="Confirm" :style="{ width: '450px' }">
            <div class="confirmation-content">
                <i class="pi pi-exclamation-triangle mr-3 text-red-500" style="font-size: 2rem" />
                <span>Are you sure you want to delete the selected users?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" text @click="deleteUsersDialog = false" />
                <Button label="Yes" icon="pi pi-check" severity="danger" @click="deleteSelectedUsers" />
            </template>
        </Dialog>
    </div>
</template>

<style scoped>
.confirmation-content {
    display: flex;
    align-items: center;
    gap: 1rem;
    font-size: 1.2rem;
}
</style>
