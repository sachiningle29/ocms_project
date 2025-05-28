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
            sub_sections: subSectionInputs.value.filter((sub) => sub.name?.trim()).map((sub) => ({ name: sub.name.trim() }))
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
const groupedSubSections = computed(() => {
  let result = [];
  let srNo = 0;

  sections.value.forEach((section) => {
    const filteredSubs = subSections.value.filter(sub => sub.section_id === section.id);

    if (filteredSubs.length) {
      srNo += 1;
    }

    filteredSubs.forEach((sub, subIndex) => {
      result.push({
        ...sub,
        section_name: section.section_name,
        _srNo: srNo,
        _subNo: subIndex + 1,
        _showSection: subIndex === 0,
        _showSrNo: subIndex === 0,
      });
    });
  });

  return result;
});

const viewDialog = ref(false);
const viewedSection = ref({});
const viewedSubSections = ref([]);

function viewSubSections(sectionId) {
    const section = sections.value.find(s => s.id === sectionId);
    const subs = subSections.value.filter(sub => sub.section_id === sectionId);

    viewedSection.value = section || {};
    viewedSubSections.value = subs;
    viewDialog.value = true;
}
</script>

<template>
    <div class="card">
        <Toolbar class="mb-4">
            <template #start>
                <Button label="Add New Subsection" icon="pi pi-plus" severity="primary" class="mr-4" @click="openNew" />
                <Button label="Delete" icon="pi pi-trash" severity="danger"  @click="confirmDeleteSelected" :disabled="!selectedSubSections || !selectedSubSections.length" />
            </template>
        </Toolbar>

      <DataTable
    ref="dt"
    :value="groupedSubSections"
    v-model:selection="selectedSubSections"
    selectionMode="multiple"
    dataKey="id"
    :paginator="true"
    :rows="10"
    :rowsPerPageOptions="[5, 10, 25]"
    currentPageReportTemplate="Showing {first} to {last} of {totalRecords} subSections"
    :filters="filters"                         
    :globalFilter="filters.global.value"        
>
    <template #header>
        <div class="flex justify-between items-center">
            <h4 class="m-0">Sub Section Management</h4>
            <div class="flex items-center gap-2">
               
                <span class="p-input-icon-left">
                    <InputText v-model="filters['global'].value" placeholder="Search Sub Section..." />
                </span>
            </div>
        </div>
    </template>

    <!-- ✅ Selection Checkbox Column -->
    <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>

   <Column header="Sr No." style="width: 6rem">
  <template #body="slotProps">
    <span v-if="slotProps.data._showSrNo">{{ slotProps.data._srNo }}</span>
  </template>
</Column>

<Column header="Section Name" style="width: 20rem">
  <template #body="slotProps">
    <span v-if="slotProps.data._showSection" style="font-weight: bold;">
      {{ slotProps.data.section_name }}
    </span>
  </template>
</Column>


<Column header="Sub Section Name">
  <template #body="slotProps">
    {{ slotProps.data._srNo }}.{{ slotProps.data._subNo }})&nbsp;{{ slotProps.data.sub_section_name }}
  </template>
</Column>

    <!-- Actions -->
    <Column :exportable="false" header="Actions" style="width: 12rem">
        <template #body="slotProps">
            <Button
                icon="pi pi-eye"
                outlined
                severity="info"
                rounded
                class="mr-2"
                @click="viewSubSections(slotProps.data.section_id)"
            />
            <Button
                icon="pi pi-pencil"
                outlined
                rounded
                class="mr-2"
                @click="editSubSection(slotProps.data)"
            />
            <Button
                icon="pi pi-trash"
                outlined
                rounded
                severity="danger"
                @click="confirmDeleteSubSection(slotProps.data)"
            />
        </template>
    </Column>
</DataTable>

<Dialog v-model:visible="viewDialog" header="Section & Sub-Sections" modal class="rounded-xl" style="width: 50vw">
    <template #default>
        <div class="p-4">
            <div class="mb-4">
                <h2 class="text-xl font-semibold text-primary flex items-center gap-2">
                    <i class="pi pi-folder-open text-lg text-blue-500" />
                    {{ viewedSection.section_name || 'No Section Selected' }}
                </h2>
                <p class="text-sm text-gray-500">Below are the sub-sections linked to this section.</p>
            </div>

            <div v-if="viewedSubSections.length" class="space-y-3">
                <div
                    v-for="(sub, i) in viewedSubSections"
                    :key="i"
                    class="p-3 border border-gray-200 rounded-lg bg-gray-50 shadow-sm flex items-center gap-3"
                >
                    <i class="pi pi-angle-right text-blue-500" />
                    <span class="text-base">{{ sub.sub_section_name }}</span>
                </div>
            </div>
            <div v-else class="text-center text-gray-400 py-5">
                <i class="pi pi-info-circle text-xl" />
                <p class="mt-2">No sub-sections found for this section.</p>
            </div>
        </div>
    </template>

    <template #footer>
        <Button label="Close" icon="pi pi-times" class="p-button-text" @click="viewDialog = false" />
    </template>
</Dialog>


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
