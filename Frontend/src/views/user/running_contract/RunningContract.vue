<script setup>
import axios from 'axios';
import { onMounted, ref } from 'vue';
import { useToast } from 'primevue/usetoast';
import { FilterMatchMode } from '@primevue/core/api';

const toast = useToast();
const dt = ref();

const contracts = ref([]);
const contractDialog = ref(false);
const deleteContractDialog = ref(false);
const deleteContractsDialog = ref(false);

const contract = ref({});
const selectedContracts = ref();
const submitted = ref(false);

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

const statusOptions = ref([
    { label: 'Active', value: 'active' },
    { label: 'Inactive', value: 'inactive' },
    { label: 'Terminated', value: 'terminated' }
]);

const apiBase = '/api/running-contracts';

onMounted(() => {
    loadContracts();
});

function loadContracts() {
    axios.get(`${apiBase}/list`, { headers: { 'Cache-Control': 'no-cache' } }).then(response => {
        console.log('Contracts response:', response.data);
        // Filter out empty rows or ones without an ID
        contracts.value = (response.data || []).filter(c => c && c.id);
    }).catch(() => {
        toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load contracts', life: 3000 });
    });
}


function openNew() {
    contract.value = {};
    submitted.value = false;
    contractDialog.value = true;
}

function hideDialog() {
    contractDialog.value = false;
    submitted.value = false;
}

function saveContract() {
    submitted.value = true;
    if (contract.value.contractor_name?.trim()) {
        if (contract.value.id) {
            axios.put(`${apiBase}/edit/${contract.value.id}`, contract.value).then(() => {
                toast.add({ severity: 'success', summary: 'Updated', detail: 'Contract updated', life: 3000 });
                loadContracts();
                contractDialog.value = false;
            });
        } else {
            axios.post(`${apiBase}/add`, contract.value).then(() => {
                toast.add({ severity: 'success', summary: 'Created', detail: 'Contract created', life: 3000 });
                loadContracts();
                contractDialog.value = false;
            });
        }
    }
}

function editContract(c) {
    axios.get(`${apiBase}/view/${c.id}`).then(response => {
        contract.value = response.data;
        contractDialog.value = true;
    });
}

function confirmDeleteContract(c) {
    contract.value = c;
    deleteContractDialog.value = true;
}

function deleteContract() {
    axios.delete(`${apiBase}/delete/${contract.value.id}`).then(() => {
        toast.add({ severity: 'success', summary: 'Deleted', detail: 'Contract deleted', life: 3000 });
        deleteContractDialog.value = false;
        contract.value = {};
        loadContracts();
    });
}

function confirmDeleteSelected() {
    deleteContractsDialog.value = true;
}

function deleteSelectedContracts() {
    const deletePromises = selectedContracts.value.map(c =>
        axios.delete(`${apiBase}/delete/${c.id}`)
    );
    Promise.all(deletePromises).then(() => {
        toast.add({ severity: 'success', summary: 'Deleted', detail: 'Selected contracts deleted', life: 3000 });
        deleteContractsDialog.value = false;
        selectedContracts.value = null;
        loadContracts();
    });
}
</script>

<template>
    <div class="card">
        <Toolbar class="mb-4">
            <template #start>
                <Button label="New" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openNew" />
                <Button label="Delete" icon="pi pi-trash" severity="secondary" @click="confirmDeleteSelected"
                    :disabled="!selectedContracts || !selectedContracts.length" />
            </template>
        </Toolbar>

        <DataTable ref="dt" v-model:selection="selectedContracts" :value="contracts.filter(c => c && c.id)" dataKey="id"
            :paginator="true" :rows="10" :filters="filters" :rowsPerPageOptions="[5, 10, 25]"
            currentPageReportTemplate="Showing {first} to {last} of {totalRecords} contracts">


            <template #header>
                <div class="flex justify-between items-center">
                    <h4 class="m-0">Running Contracts</h4>
                    <span class="p-input-icon-left">
                        <i class="pi pi-search" />
                        <InputText v-model="filters['global'].value" placeholder="Search..." />
                    </span>
                </div>
            </template>

            <Column selectionMode="multiple" style="width: 3rem" :exportable="false" />
            <Column header="Sr No" :body="(_, { index }) => index + 1" style="width: 4rem" />
            <Column field="contractor_name" header="Contractor Name" sortable />
            <Column field="rid" header="Contract ID" sortable />
            <Column field="work_order_number" header="Work Order Number" sortable />
            <Column field="contract_type" header="Contract Type" sortable />
            <Column field="department" header="Department" sortable />
            <Column field="section" header="Section" sortable />
            <Column field="status" header="Status" sortable />
            <Column field="created_at" header="Date" sortable />

            <Column :exportable="false" header="Actions" style="width: 10rem">
                <template #body="slotProps">
                    <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editContract(slotProps.data)" />
                    <Button icon="pi pi-trash" outlined rounded severity="danger"
                        @click="confirmDeleteContract(slotProps.data)" />
                </template>
            </Column>

            <template #empty>
                <div class="text-center text-gray-500 py-4">No contracts found.</div>
            </template>
        </DataTable>

        <Dialog v-model:visible="contractDialog" modal header="Contract Details" :style="{ width: '600px' }">
            <div class="flex flex-col gap-4">
                <div>
                    <label class="block font-bold mb-2">Contract ID (RID)</label>
                    <InputText v-model="contract.rid" class="w-full" />
                </div>

                <div>
                    <label class="block font-bold mb-2">Vendor / Contractor Name</label>
                    <InputText v-model="contract.contractor_name" class="w-full" required
                        :invalid="submitted && !contract.contractor_name" />
                    <small v-if="submitted && !contract.contractor_name" class="text-red-500">Contractor Name is
                        required.</small>
                </div>

                <div>
                    <label class="block font-bold mb-2">Work Order Number</label>
                    <InputText v-model="contract.work_order_number" class="w-full" />
                </div>

                <div>
                    <label class="block font-bold mb-2">Contract Type</label>
                    <InputText v-model="contract.contract_type" class="w-full" />
                </div>

                <div>
                    <label class="block font-bold mb-2">Department</label>
                    <InputText v-model="contract.department" class="w-full" />
                </div>

                <div>
                    <label class="block font-bold mb-2">Section</label>
                    <InputText v-model="contract.section" class="w-full" />
                </div>

                <div>
                    <label class="block font-bold mb-2">Status</label>
                    <Dropdown v-model="contract.status" :options="statusOptions" optionLabel="label" optionValue="value"
                        placeholder="Select Status" class="w-full" />
                </div>
            </div>

            <template #footer>
                <Button label="Cancel" icon="pi pi-times" text @click="hideDialog" />
                <Button label="Save" icon="pi pi-check" @click="saveContract" />
            </template>
        </Dialog>

        <Dialog v-model:visible="deleteContractDialog" modal header="Confirm" :style="{ width: '450px' }">
            <div class="confirmation-content">
                <i class="pi pi-exclamation-triangle mr-3 text-red-500" style="font-size: 2rem" />
                <span>Are you sure you want to delete <b>{{ contract.contractor_name }}</b>?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" text @click="deleteContractDialog = false" />
                <Button label="Yes" icon="pi pi-check" severity="danger" @click="deleteContract" />
            </template>
        </Dialog>

        <Dialog v-model:visible="deleteContractsDialog" modal header="Confirm" :style="{ width: '450px' }">
            <div class="confirmation-content">
                <i class="pi pi-exclamation-triangle mr-3 text-red-500" style="font-size: 2rem" />
                <span>Are you sure you want to delete the selected contracts?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" text @click="deleteContractsDialog = false" />
                <Button label="Yes" icon="pi pi-check" severity="danger" @click="deleteSelectedContracts" />
            </template>
        </Dialog>
    </div>
</template>
