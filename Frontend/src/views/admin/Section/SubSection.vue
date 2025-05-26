<script setup>
import axiosClient from '@/axios';
import { onMounted, ref, reactive, computed } from 'vue';
import { useToast } from 'primevue/usetoast';
import { FilterMatchMode } from '@primevue/core/api';

const toast = useToast();
const dt = ref();

const subSections = ref([]);
const sections = ref([]); // Holds all section options
const selectedSection = ref(null); // Selected section ID
const subSectionInputs = ref([{ name: '' }]); // Holds multiple sub-section names

const subSectionDialog = ref(false);
const deleteSubSectionDialog = ref(false);
const deleteSubSectionsDialog = ref(false);

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

// Fetch sections and sub-sections
onMounted(() => {
    loadSubSections();
    axiosClient
        .get('/sections')
        .then((res) => {
            sections.value = res.data || [];
        })
        .catch((err) => {
            console.error('Failed to fetch sections:', err.response?.data || err.message);
        });
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
    Object.assign(subSection, { id: null, name: '' });
    submitted.value = false;
    subSectionDialog.value = true;
}

function hideDialog() {
    subSectionDialog.value = false;
    submitted.value = false;
}

function saveSubSection() {
    submitted.value = true;

    if (!selectedSection.value) {
        toast.add({ severity: 'warn', summary: 'Validation', detail: 'Section is required', life: 3000 });
        return;
    }

    if (subSection.id) {
        // Update single subSection
        axiosClient
            .put(`${apiBase}/${subSection.id}`, {
                section_id: selectedSection.value,
                name: subSectionInputs.value[0].name.trim()
            })
            .then(() => {
                toast.add({ severity: 'success', summary: 'Success', detail: 'Sub Section updated successfully', life: 3000 });
                loadSubSections();
                subSectionDialog.value = false;
                selectedSection.value = null;
                subSectionInputs.value = [{ name: '' }];
            })
            .catch((error) => {
                console.error('updateSubSection error:', error.response?.data || error.message);
                toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to update subSection', life: 3000 });
            });
    } else {
        // Create multiple subSections
        const payload = {
            section_id: selectedSection.value,
            sub_sections: subSectionInputs.value
                .filter((sub) => sub.name?.trim())
                .map((sub) => ({ name: sub.name.trim() }))
        };

        axiosClient
            .post(apiBase, payload)
            .then(() => {
                toast.add({ severity: 'success', summary: 'Success', detail: 'Sub Sections created successfully', life: 3000 });
                loadSubSections();
                subSectionDialog.value = false;
                selectedSection.value = null;
                subSectionInputs.value = [{ name: '' }];
            })
            .catch((error) => {
                console.error('saveSubSection error:', error.response?.data || error.message);
                toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to save subSections', life: 3000 });
            });
    }
}

function editSubSection(u) {
    axiosClient
        .get(`${apiBase}/${u.id}`)
        .then((response) => {
            Object.assign(subSection, response.data);
            selectedSection.value = response.data.section_id;
            subSectionInputs.value = [{ name: response.data.sub_section_name }];
            subSectionDialog.value = true;
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
            deleteSubSectionDialog.value = false;
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

function addSubSection() {
    subSectionInputs.value.push({ name: '' });
}

function removeSubSection(index) {
    subSectionInputs.value.splice(index, 1);
}

// ✅ Grouped data for display in table
const groupedSubSections = computed(() => {
    const grouped = [];
    let srNo = 1;

    sections.value.forEach((section) => {
        const subs = subSections.value.filter((sub) => sub.section_id === section.id);

        subs.forEach((sub, index) => {
            grouped.push({
                ...sub,
                section_name: section.section_name,
                _showSection: index === 0,
                _showSrNo: index === 0,
                _srNo: index === 0 ? srNo : ''
            });
        });

        if (subs.length > 0) srNo++;
    });

    return grouped;
});
</script>


<template>
    <div class="card">
        <Toolbar class="mb-4">
            <template #start>
                <Button label="New" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openNew" />
                <Button label="Delete" icon="pi pi-trash" severity="secondary" @click="confirmDeleteSelected" :disabled="!selectedSubSections || !selectedSubSections.length" />
            </template>
        </Toolbar>

        <DataTable ref="dt" :value="groupedSubSections" dataKey="id" :paginator="true" :rows="10" :rowsPerPageOptions="[5, 10, 25]" currentPageReportTemplate="Showing {first} to {last} of {totalRecords} subSections">
            <template #header>
                <div class="flex justify-between items-center">
                    <h4 class="m-0">Sub Section Management</h4>
                    <span class="p-input-icon-left">
                        <i class="pi pi-search" />
                        <InputText v-model="filters['global'].value" placeholder="Search..." />
                    </span>
                </div>
            </template>

            <!-- Sr No. -->
            <Column header="Sr No." style="width: 6rem">
                <template #body="slotProps">
                    <span v-if="slotProps.data._showSrNo">{{ slotProps.data._srNo }}</span>
                </template>
            </Column>

            <!-- Sub Section Name -->
            <Column field="sub_section_name" header="Sub Section Name" />

            <!-- Section Name -->
            <Column header="Section Name">
                <template #body="slotProps">
                    <span v-if="slotProps.data._showSection">{{ slotProps.data.section_name }}</span>
                </template>
            </Column>

            

            <!-- Actions -->
            <Column :exportable="false" header="Actions" style="width: 10rem">
                <template #body="slotProps">
                    <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editSubSection(slotProps.data)" />
                    <Button icon="pi pi-trash" outlined rounded severity="danger" @click="confirmDeleteSubSection(slotProps.data)" />
                </template>
            </Column>
        </DataTable>

        <!-- Create/Edit Dialog -->
        <Dialog v-model:visible="subSectionDialog" :draggable="false" modal header="Sub Section Details" :closable="false" style="width: 40vw">
            <div class="p-4">
                <!-- Section Dropdown -->
                <div class="mb-4">
                    <label class="font-bold mb-1 block">Select Section</label>
                    <Dropdown v-model="selectedSection" :options="sections" optionLabel="section_name" optionValue="id" placeholder="Select Section" class="w-full" />
                </div>

                <!-- Dynamic Sub Section Inputs -->
                <div class="space-y-3">
                    <div v-for="(sub, index) in subSectionInputs" :key="index" class="flex items-center gap-2">
                        <InputText v-model="sub.name" placeholder="Sub Section Name" class="flex-1" />
                        <Button icon="pi pi-trash" severity="danger" outlined @click="removeSubSection(index)" v-if="subSectionInputs.length > 1" />
                    </div>
                    <Button icon="pi pi-plus" label="Add Sub Section" outlined class="mt-2" @click="addSubSection" />
                </div>
            </div>

            <template #footer>
                <Button label="Close" icon="pi pi-times" class="p-button-text" @click="hideDialog" />
                <Button label="Save" icon="pi pi-check" class="p-button-primary" @click="saveSubSection" />
            </template>
        </Dialog>

        <!-- Delete One Dialog -->
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

        <!-- Delete Multiple Dialog -->
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
