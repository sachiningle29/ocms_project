<script setup>
import axiosClient from '@/axios'; // Your configured axios instance
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
    user_status: 'Active',
    section_id: null,
    section: ''
});

const userTypes = [
    { label: 'Admin', value: 1 },
    { label: 'User', value: 0 }
];

const userStatusOptions = [
    { label: 'Active', value: 'Active' },
    { label: 'Inactive', value: 'Inactive' }
];

const showPassword = ref(false);
function togglePassword() {
    showPassword.value = !showPassword.value;
}

const selectedUsers = ref([]);
const submitted = ref(false);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

const apiBase = '/users';

const sections = ref([]); // Loaded sections from API
const selectedSection = ref(null);

const validationErrors = ref({});

onMounted(() => {
    loadUsers();
    loadSections();
});

function loadUsers() {
    axiosClient
        .get(apiBase)
        .then((response) => {
            users.value = response.data || [];

            console.warn(response);
        })
        .catch((error) => {
            console.error('loadUsers error:', error.response?.data || error.message);
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load users', life: 3000 });
        });
}

function loadSections() {
    axiosClient
        .get('/sections')
        .then((res) => {
            sections.value = res.data || [];
        })
        .catch((err) => {
            console.error('Failed to fetch sections:', err.response?.data || err.message);
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
        user_status: 'Active',
        section_id: null,
        section: ''
    });
    selectedSection.value = null;
    validationErrors.value = {};
    submitted.value = false;
    userDialog.value = true;
}

function hideDialog() {
    userDialog.value = false;
    submitted.value = false;
    validationErrors.value = {};
}

function saveUser() {
    submitted.value = true;
    validationErrors.value = {};

    // Basic client-side validation
    if (!user.name?.trim()) {
        toast.add({ severity: 'warn', summary: 'Validation', detail: 'Name is required', life: 3000 });
        return;
    }
    if (!user.email?.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(user.email)) {
        toast.add({ severity: 'warn', summary: 'Validation', detail: 'A valid email is required', life: 3000 });
        return;
    }
    if (!user.id && !user.password?.trim()) {
        toast.add({ severity: 'warn', summary: 'Validation', detail: 'Password is required', life: 3000 });
        return;
    }
    if (![0, 1].includes(user.is_admin)) {
        toast.add({ severity: 'warn', summary: 'Validation', detail: 'User type must be selected', life: 3000 });
        return;
    }
    if (!['Active', 'Inactive'].includes(user.user_status)) {
        toast.add({ severity: 'warn', summary: 'Validation', detail: 'User status must be selected', life: 3000 });
        return;
    }
    if (!selectedSection.value) {
        toast.add({ severity: 'warn', summary: 'Validation', detail: 'Section must be selected', life: 3000 });
        return;
    }

    // Assign section data from selectedSection
    const selectedSectionObj = sections.value.find((s) => s.id === selectedSection.value);
    user.section_id = selectedSection.value;
    user.section = selectedSectionObj ? selectedSectionObj.section_name : '';

    // Prepare payload
    const payload = { ...user };
    if (user.id && !user.password?.trim()) {
        delete payload.password; // Don't send password if empty on update
    }

    const request = user.id ? axiosClient.put(`${apiBase}/${user.id}`, payload) : axiosClient.post(apiBase, payload);

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
            if (error.response && error.response.status === 422) {
                // Validation errors from Laravel
                const errors = error.response.data.errors;
                validationErrors.value = errors;
                Object.values(errors).forEach((msgs) => {
                    msgs.forEach((msg) => {
                        toast.add({ severity: 'warn', summary: 'Validation error', detail: msg, life: 4000 });
                    });
                });
            } else {
                toast.add({
                    severity: 'error',
                    summary: 'Error',
                    detail: error.response?.data?.message || 'Failed to save user',
                    life: 3000
                });
            }
        });
}

function editUser(u) {
    axiosClient.get(`${apiBase}/${u.id}`).then((response) => {
        let data = response.data;
        // Normalize first letter uppercase for status
        if (data.user_status) {
            data.user_status = data.user_status.charAt(0).toUpperCase() + data.user_status.slice(1).toLowerCase();
        }
        Object.assign(user, data);
        selectedSection.value = data.section_id;
        validationErrors.value = {};
        userDialog.value = true;
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
                <Button label="Add New User" icon="pi pi-plus" severity="primary" class="mr-4" @click="openNew" />
                <Button label="Delete" icon="pi pi-trash" severity="danger" @click="confirmDeleteSelected" :disabled="!selectedUsers.length" />
            </template>
        </Toolbar>

        <DataTable ref="dt" v-model:selection="selectedUsers" :value="users" dataKey="id" :paginator="true" :rows="10" :filters="filters" :rowsPerPageOptions="[5, 10, 25]" currentPageReportTemplate="Showing {first} to {last} of {totalRecords} users">
            <template #header>
                <div class="flex justify-between items-center">
                    <h4 class="m-0">User Management</h4>
                    <span class="p-input-icon-left">
                        <i class="pi pi-search" />
                        <InputText v-model="filters.global.value" placeholder="Search..." />
                    </span>
                </div>
            </template>

            <Column selectionMode="multiple" style="width: 3rem" :exportable="false" />
            <Column header="Sr No." style="width: 8rem">
                <template #body="slotProps">{{ slotProps.index + 1 }}</template>
            </Column>
            <Column field="name" header="User Name" sortable />
            <Column field="email" header="Email" sortable />
            <Column field="cpf_no" header="CPF" sortable />
            <Column field="is_admin" header="Type" sortable>
                <template #body="slotProps">{{ slotProps.data.is_admin === 1 ? 'Admin' : 'User' }}</template>
            </Column>
            <Column field="user_status" header="Status" sortable />
            <Column field="section" header="Sections" sortable />

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
                        <small v-if="validationErrors.name" class="p-error">{{ validationErrors.name[0] }}</small>
                    </div>

                    <div>
                        <label class="font-bold mb-1 block">Email</label>
                        <InputText v-model="user.email" type="email" class="w-full" />
                        <small v-if="validationErrors.email" class="p-error">{{ validationErrors.email[0] }}</small>
                    </div>

                    <div>
                        <label class="font-bold mb-1 block">CPF No</label>
                        <InputText v-model="user.cpf_no" class="w-full" />
                        <small v-if="validationErrors.cpf_no" class="p-error">{{ validationErrors.cpf_no[0] }}</small>
                    </div>

                    <div>
                        <label class="font-bold mb-1 block">Password</label>
                        <div class="relative">
                            <InputText v-model="user.password" :type="showPassword ? 'text' : 'password'" class="w-full pr-10" />
                            <button type="button" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500" @click="togglePassword">
                                <i :class="showPassword ? 'pi pi-eye-slash' : 'pi pi-eye'"></i>
                            </button>
                        </div>
                        <small v-if="validationErrors.password" class="p-error">{{ validationErrors.password[0] }}</small>
                    </div>

                    <div>
                        <label class="font-bold mb-1 block">User Type</label>
                        <Dropdown v-model="user.is_admin" :options="userTypes" optionLabel="label" optionValue="value" placeholder="Select Type" class="w-full" />
                        <small v-if="validationErrors.is_admin" class="p-error">{{ validationErrors.is_admin[0] }}</small>
                    </div>

                    <div>
                        <label class="font-bold mb-1 block">User Status</label>
                        <Dropdown v-model="user.user_status" :options="userStatusOptions" optionLabel="label" optionValue="value" placeholder="Select Status" class="w-full" />
                        <small v-if="validationErrors.user_status" class="p-error">{{ validationErrors.user_status[0] }}</small>
                    </div>

                    <div>
                        <label class="font-bold mb-1 block">Assign Section</label>
                        <Dropdown v-model="selectedSection" :options="sections" optionLabel="section_name" optionValue="id" placeholder="Select Section" class="w-full" />
                        <small v-if="validationErrors.section_id" class="p-error">{{ validationErrors.section_id[0] }}</small>
                    </div>
                </div>
            </div>

            <template #footer>
                <Button label="Cancel" icon="pi pi-times" outlined class="mr-2" @click="hideDialog" />
                <Button label="Save" icon="pi pi-check" @click="saveUser" />
            </template>
        </Dialog>

        <!-- Delete single user confirmation -->
        <Dialog v-model:visible="deleteUserDialog" modal header="Confirm" :closable="false" style="width: 350px">
            <div class="confirmation-content">
                <i class="pi pi-exclamation-triangle mr-3 text-xl text-yellow-700"></i>
                Are you sure you want to delete <b>{{ user.name }}</b
                >?
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" outlined @click="deleteUserDialog = false" />
                <Button label="Yes" icon="pi pi-check" severity="danger" @click="deleteUser" />
            </template>
        </Dialog>

        <!-- Delete multiple users confirmation -->
        <Dialog v-model:visible="deleteUsersDialog" modal header="Confirm" :closable="false" style="width: 350px">
            <div class="confirmation-content">
                <i class="pi pi-exclamation-triangle mr-3 text-xl text-yellow-700"></i>
                Are you sure you want to delete the selected users?
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" outlined @click="deleteUsersDialog = false" />
                <Button label="Yes" icon="pi pi-check" severity="danger" @click="deleteSelectedUsers" />
            </template>
        </Dialog>
    </div>
</template>

<style scoped>
.confirmation-content {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
    font-size: 1.1rem;
}
.p-error {
    color: #f44336;
    font-size: 0.85rem;
    margin-top: 0.25rem;
    display: block;
}
</style>
