<script setup>
import axiosClient from '@/axios';
import { onMounted, ref, reactive } from 'vue';
import { useToast } from 'primevue/usetoast';
import { FilterMatchMode } from '@primevue/core/api';

const toast = useToast();
const dt = ref();

const sections = ref([]);
const sectionDialog = ref(false);
const deleteSectionDialog = ref(false);
const deleteSectionsDialog = ref(false);

const section = reactive({
    id: null,
    section_name: ''
});

const selectedSections = ref([]);
const submitted = ref(false);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

const apiBase = '/sections';

onMounted(() => {
    loadSections();
});

function loadSections() {
    axiosClient
        .get(apiBase)
        .then((response) => {
            sections.value = response.data || [];
        })
        .catch((error) => {
            console.error('loadSections error:', error.response?.data || error.message);
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load sections', life: 3000 });
        });
}

function openNew() {
    Object.assign(section, { id: null, section_name: '' });
    submitted.value = false;
    sectionDialog.value = true;
}

function hideDialog() {
    sectionDialog.value = false;
    submitted.value = false;
}

function saveSection() {
    submitted.value = true;

if (!section.section_name?.trim()) {
        toast.add({ severity: 'warn', summary: 'Validation', detail: 'Section name is required', life: 3000 });
        return;
    }

    const payload = {
        section_name: section.section_name
    };

    const request = section.id
        ? axiosClient.put(`${apiBase}/${section.id}`, payload)
        : axiosClient.post(apiBase, payload);

    request.then(() => {
        toast.add({
            severity: 'success',
            summary: 'Success',
            detail: section.id ? 'Section updated successfully' : 'Section created successfully',
            life: 3000
        });
        loadSections();
        sectionDialog.value = false;
    }).catch((error) => {
        console.error('saveSection error:', error.response?.data || error.message);
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to save section', life: 3000 });
    });
}

function editSection(s) {
    axiosClient
        .get(`${apiBase}/${s.id}`)
        .then((response) => {
            Object.assign(section, response.data);
            sectionDialog.value = true;
        })
        .catch((error) => {
            console.error('editSection error:', error.response?.data || error.message);
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load section details', life: 3000 });
        });
}

function confirmDeleteSection(s) {
    Object.assign(section, s);
    deleteSectionDialog.value = true;
}

function deleteSection() {
    axiosClient
        .delete(`${apiBase}/${section.id}`)
        .then(() => {
            toast.add({ severity: 'success', summary: 'Deleted', detail: 'Section deleted', life: 3000 });
            deleteSectionDialog.value = false;
            loadSections();
        })
        .catch((error) => {
            console.error('deleteSection error:', error.response?.data || error.message);
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete section', life: 3000 });
        });
}

function confirmDeleteSelected() {
    deleteSectionsDialog.value = true;
}

function deleteSelectedSections() {
    const deletePromises = selectedSections.value.map((s) => axiosClient.delete(`${apiBase}/${s.id}`));
    Promise.all(deletePromises)
        .then(() => {
            toast.add({ severity: 'success', summary: 'Deleted', detail: 'Selected sections deleted', life: 3000 });
            deleteSectionsDialog.value = false;
            selectedSections.value = [];
            loadSections();
        })
        .catch((error) => {
            console.error('deleteSelectedSections error:', error.response?.data || error.message);
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete selected sections', life: 3000 });
        });
}
</script>

<template>
    <div class="card">
        <Toolbar class="mb-4">
            <template #start>
                <Button label="New" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openNew" />
                <Button label="Delete" icon="pi pi-trash" severity="secondary" @click="confirmDeleteSelected"
                    :disabled="!selectedSections || !selectedSections.length" />
            </template>
        </Toolbar>

        <DataTable ref="dt" v-model:selection="selectedSections" :value="sections" dataKey="id" :paginator="true" :rows="10"
            :filters="filters" :rowsPerPageOptions="[5, 10, 25]"
            currentPageReportTemplate="Showing {first} to {last} of {totalRecords} sections">
            <template #header>
                <div class="flex justify-between items-center">
                    <h4 class="m-0">Section Management</h4>
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
            
            <Column field="section_name" header="Section Name" sortable />
            <Column :exportable="false" header="Actions" style="width: 10rem">
                <template #body="slotProps">
                    <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editSection(slotProps.data)" />
                    <Button icon="pi pi-trash" outlined rounded severity="danger"
                        @click="confirmDeleteSection(slotProps.data)" />
                </template>
            </Column>

            <template #empty>
                <div class="text-center text-gray-500 py-4">No section found.</div>
            </template>
        </DataTable>

        <!-- Create/Edit Dialog -->
        <Dialog v-model:visible="sectionDialog" :draggable="false" modal header="Section Details" :closable="false"
            style="width: 20vw">
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="font-bold mb-1 block">Section Name</label>
                        <InputText v-model="section.section_name" class="w-full" />
                    </div>
                </div>
            </div>

            <template #footer>
                <Button label="Close" icon="pi pi-times" class="p-button-text" @click="hideDialog" />
                <Button label="Save" icon="pi pi-check" class="p-button-primary" @click="saveSection" />
            </template>
        </Dialog>

        <!-- Delete One -->
        <Dialog v-model:visible="deleteSectionDialog" modal header="Confirm" :style="{ width: '450px' }">
            <div class="confirmation-content">
                <i class="pi pi-exclamation-triangle mr-3 text-red-500" style="font-size: 2rem" />
                <span>Are you sure you want to delete <b>{{ section.section_name }}</b>?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" text @click="deleteSectionDialog = false" />
                <Button label="Yes" icon="pi pi-check" severity="danger" @click="deleteSection" />
            </template>
        </Dialog>

        <!-- Delete Multiple -->
        <Dialog v-model:visible="deleteSectionsDialog" modal header="Confirm" :style="{ width: '450px' }">
            <div class="confirmation-content">
                <i class="pi pi-exclamation-triangle mr-3 text-red-500" style="font-size: 2rem" />
                <span>Are you sure you want to delete the selected sections?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" text @click="deleteSectionsDialog = false" />
                <Button label="Yes" icon="pi pi-check" severity="danger" @click="deleteSelectedSections" />
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
