<script setup>
import axiosClient from '@/axios'; // Use the custom Axios client
import { onMounted, ref, reactive } from 'vue';
import { useToast } from 'primevue/usetoast';
import { FilterMatchMode } from '@primevue/core/api';

const toast = useToast();
const dt = ref();

const subSections = ref([]);
const subSectionDialog = ref(false);
const deletesubSectionDialog = ref(false);
const deletesubSectionsDialog = ref(false);

const subSection = reactive({
    id: null,
    name: ''
});

const selectedSubSections = ref([]);
const submitted = ref(false);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

const apiBase = '/subSections';

onMounted(() => {
    loadSubSections();
});


function loadSubSections() {
    axiosClient
        .get(apiBase)
        .then((response) => {
          subSections.value = response.data || [];
        })
        .catch((error) => {
            console.error('loadSubSections error:', error.response?.data || error.message);
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load subSections', life: 3000 });
        });
}

function openNew() {
    Object.assign(subSection, {
        id: null,
        name: ''
    });
   submitted.value = false;
    subSectionDialog.value = true;
}

function hideDialog() {
    subSectionDialog.value = false;
    submitted.value = false;
}


function saveSubSection() {
    submitted.value = true;

    // Name validation
    if (!subSection.name?.trim()) {
        toast.add({ severity: 'warn', summary: 'Validation', detail: 'Name is required', life: 3000 });
        return;
    }

    // Prepare payload
    const payload = { ...subSection };
    if (subSection.id && !subSection.password?.trim()) {
        delete payload.password; 
    }

    const request = subSection.id ? axiosClient.put(`/subSections/${subSection.id}`, payload) : axiosClient.post('/subSections', payload);

    request
        .then(() => {
            toast.add({
                severity: 'success',
                summary: 'Success',
                detail: subSection.id ? 'Sub Section updated successfully' : 'Sub Section created successfully',
                life: 3000
            });
            loadSubSections();
            subSectionDialog.value = false;
        })
        .catch((error) => {
            console.error('saveSubSection error:', error.response?.data || error.message);
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to save subSection', life: 3000 });
        });
}


function editSubSection(u) {
    axiosClient
        .get(`${apiBase}/${u.id}`)
        .then((response) => {
            Object.assign(subSection, response.data);
            loadSubSectionsDialog.value = true;
        })
        .catch((error) => {
            console.error('editSubSection error:', error.response?.data || error.message);
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load sub section details', life: 3000 });
        });
}

function confirmDeleteSubSection(u) {
    Object.assign(subSection, u);
    deleteSubSectionDialog.value = true;
}

function deleteSubSection() {
    axiosClient
        .delete(`${apiBase}/${subSection.id}`)
        .then(() => {
            toast.add({ severity: 'success', summary: 'Deleted', detail: 'Sub Section deleted', life: 3000 });
            deletesubSectionDialog.value = false;
            loadSubSections();
        })
        .catch((error) => {
            console.error('deleteSubSection error:', error.response?.data || error.message);
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete sub section', life: 3000 });
        });
}

function confirmDeleteSelected() {
    deleteSubSectionsDialog.value = true;
}

function deleteSelectedSubSections() {
    const deletePromises = selectedSubSections.value.map((u) => axiosClient.delete(`${apiBase}/${u.id}`));
    Promise.all(deletePromises)
        .then(() => {
            toast.add({ severity: 'success', summary: 'Deleted', detail: 'Selected subSections deleted', life: 3000 });
            deleteSubSectionsDialog.value = false;
            selectedSubSections.value = [];
            loadSubSections();
        })
        .catch((error) => {
            console.error('deleteSelectedSubSections error:', error.response?.data || error.message);
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete selected subSections', life: 3000 });
        });
}


</script>

<template>
    <div class="card">
        <Toolbar class="mb-4">
            <template #start>
                <Button label="New" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openNew" />
                <Button label="Delete" icon="pi pi-trash" severity="secondary" @click="confirmDeleteSelected" :disabled="!selectedsubSections || !selectedsubSections.length" />
           
        </template>
        </Toolbar>

        <DataTable ref="dt" v-model:selection="selectedSubSections" :value="subSections" dataKey="id" :paginator="true" :rows="10" :filters="filters" :rowsPerPageOptions="[5, 10, 25]" currentPageReportTemplate="Showing {first} to {last} of {totalRecords} subSections">
            <template #header>
                <div class="flex justify-between items-center">
                    <h4 class="m-0">Sub Section Management</h4>
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
            <Column field="sub_section_name" header="Section Name" sortable />
            <Column :exportable="false" header="Actions" style="width: 10rem">
                <template #body="slotProps">
                    <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editSubSection(slotProps.data)" />
                    <Button icon="pi pi-trash" outlined rounded severity="danger" @click="confirmDeleteSubSection(slotProps.data)" />
                </template>
            </Column>

            <template #empty>
                <div class="text-center text-gray-500 py-4">No sub section found.</div>
            </template>
        </DataTable>
    

        <!-- Create/Edit Dialog -->
        <Dialog v-model:visible="subSectionDialog" :draggable="false" modal header="Sub Section Details" :closable="false" style="width: 40vw">
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="font-bold mb-1 block">Sub Section Name</label>
                        <InputText v-model="subSection.name" class="w-full" />
                    </div>
                </div>
            </div>

            <template #footer>
                <Button label="Close" icon="pi pi-times" class="p-button-text" @click="hideDialog" />
                <Button label="Save" icon="pi pi-check" class="p-button-primary" @click="saveSubSection" />
            </template>
        </Dialog>

        <!-- Delete One -->
     <Dialog v-model:visible="deleteSubSectionDialog" modal header="Confirm" :style="{ width: '450px' }">
            <div class="confirmation-content">
                <i class="pi pi-exclamation-triangle mr-3 text-red-500" style="font-size: 2rem" />
                <span
                    >Are you sure you want to delete <b>{{ subSection.name }}</b
                    >?</span
                >
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" text @click="deleteSubSectionDialog = false" />
                <Button label="Yes" icon="pi pi-check" severity="danger" @click="deleteSubSection" />
            </template>
        </Dialog>

        <!-- Delete Multiple -->
       <Dialog v-model:visible="deleteSubSectionsDialog" modal header="Confirm" :style="{ width: '450px' }">
            <div class="confirmation-content">
                <i class="pi pi-exclamation-triangle mr-3 text-red-500" style="font-size: 2rem" />
                <span>Are you sure you want to delete the selected subSections?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" text @click="deleteSubSectionsDialog = false" />
                <Button label="Yes" icon="pi pi-check" severity="danger" @click="deleteSelectedSubSections" />
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
