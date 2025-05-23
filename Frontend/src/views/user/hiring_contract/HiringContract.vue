<script setup>
import axiosClient from '@/axios';
import { onMounted, ref, reactive } from 'vue';
import { useToast } from 'primevue/usetoast';
import { FilterMatchMode } from '@primevue/core/api';

const toast = useToast();
const dt = ref();

const contracts = ref([]);
const contractDialog = ref(false);
const deleteContractDialog = ref(false);
const deleteContractsDialog = ref(false);

const selectedContracts = ref([]);
const submitted = ref(false);

const contract = reactive({
    id: null,
    rid: '',
    title: '',
    contractor_name: '',   // maps to "Vendor" column
    deliverables: '',
    indenting_section: '',
    indentor_do: '',
    value_inr: '',
    reqmt_recd_date: '',
    case_initiation_date: '',
    aa_date: '',
    sanction_date: '',
    indent_date: '',
    vendor_type: '',
    tender_do: '',
    tender_type: '',
    tendering_section: '',
    nit_date: '',
    tbo_date: '',
    pbo_date: '',
    noa_po_date: '',
    delivery_date: '',
    post_contract: '',
    pr_no: '',
    method: '',
    contract_no: '',
    sanction_value_cr: '',
    percentage_above_below: '',
    contract_start_date: '',
    contract_end_date: '',
    physical_progress: '',
    status: 'active',
    addl_dealing_officer: ''
});

const statusOptions = [
    { label: 'Active', value: 'active' },
    { label: 'Closed', value: 'closed' },
    { label: 'On Hold', value: 'on_hold' }
];

const vendorTypeOptions = [
    { label: 'OEM', value: 'OEM' },
    { label: 'Non OEM', value: 'Non-OEM' }
];

// New dropdown options you asked for:
const deliverablesOptions = [
    { label: 'Material', value: 'Material' },
    { label: 'Services', value: 'Services' },
    { label: 'Capital', value: 'Capital' }
];

const tenderTypeOptions = [
    { label: 'Non RC', value: 'Non RC' },
    { label: 'Open', value: 'Open' }
];

const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

const apiBase = '/hiring-contracts';

onMounted(() => {
    loadContracts();
});

function loadContracts() {
    axiosClient.get(apiBase)
        .then((res) => {
            contracts.value = res.data || [];
        })
        .catch(() => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load contracts', life: 3000 });
        });
}

function openNew() {
    Object.assign(contract, {
        id: null,
        rid: '',
        title: '',
        deliverables: '',
        indenting_section: '',
        indentor_do: '',
        value_inr: '',
        reqmt_recd_date: '',
        case_initiation_date: '',
        aa_date: '',
        sanction_date: '',
        indent_date: '',
        vendor_type: '',
        tender_do: '',
        tender_type: '',
        tendering_section: '',
        nit_date: '',
        tbo_date: '',
        pbo_date: '',
        noa_po_date: '',
        delivery_date: '',
        post_contract: '',
        pr_no: '',
        method: '',
        contract_no: '',
        sanction_value_cr: '',
        percentage_above_below: '',
        contract_start_date: '',
        contract_end_date: '',
        contractor_name: '',
        physical_progress: '',
        status: 'active',
        addl_dealing_officer: ''
    });
    submitted.value = false;
    contractDialog.value = true;
}

function hideDialog() {
    contractDialog.value = false;
    submitted.value = false;
}

function saveContract() {
    submitted.value = true;

    if (!contract.title.trim()) {
        toast.add({ severity: 'warn', summary: 'Validation', detail: 'Title is required', life: 3000 });
        return;
    }

    const payload = { ...contract };

    const request = contract.id
        ? axiosClient.put(`${apiBase}/${contract.id}`, payload)
        : axiosClient.post(apiBase, payload);

    request
        .then(() => {
            toast.add({ severity: 'success', summary: 'Success', detail: contract.id ? 'Updated successfully' : 'Created successfully', life: 3000 });
            loadContracts();
            contractDialog.value = false;
        })
        .catch(() => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to save contract', life: 3000 });
        });
}

function editContract(c) {
    axiosClient.get(`${apiBase}/${c.id}`)
        .then((res) => {
            Object.assign(contract, res.data);
            contractDialog.value = true;
        })
        .catch(() => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load contract', life: 3000 });
        });
}

function confirmDeleteContract(c) {
    Object.assign(contract, c);
    deleteContractDialog.value = true;
}

function deleteContract() {
    axiosClient.delete(`${apiBase}/${contract.id}`)
        .then(() => {
            toast.add({ severity: 'success', summary: 'Deleted', detail: 'Contract deleted', life: 3000 });
            deleteContractDialog.value = false;
            loadContracts();
        })
        .catch(() => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete contract', life: 3000 });
        });
}

function confirmDeleteSelected() {
    deleteContractsDialog.value = true;
}

function deleteSelectedContracts() {
    const promises = selectedContracts.value.map(c => axiosClient.delete(`${apiBase}/${c.id}`));
    Promise.all(promises)
        .then(() => {
            toast.add({ severity: 'success', summary: 'Deleted', detail: 'Selected contracts deleted', life: 3000 });
            selectedContracts.value = [];
            deleteContractsDialog.value = false;
            loadContracts();
        })
        .catch(() => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to delete contracts', life: 3000 });
        });
}
</script>

<template>
    <div class="card">
        <Toolbar class="mb-4">
            <template #start>
                <Button label="New" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openNew" />
                <Button label="Delete" icon="pi pi-trash" severity="secondary" @click="confirmDeleteSelected"
                    :disabled="!selectedContracts.length" />
            </template>
        </Toolbar>

        <DataTable ref="dt" v-model:selection="selectedContracts" :value="contracts" dataKey="id" :paginator="true"
            :rows="10" :filters="filters" :rowsPerPageOptions="[5, 10, 25]">
            <template #header>
                <div class="flex justify-between items-center">
                    <h4 class="m-0">Hiring Contracts</h4>
                    <span class="p-input-icon-left">
                        <i class="pi pi-search" />
                        <InputText v-model="filters['global'].value" placeholder="Search..." />
                    </span>
                </div>
            </template>

            <Column header="Sr. No">
                <template #body="slotProps">
                    {{ dt.first + slotProps.index + 1 }}
                </template>
            </Column>
            <Column header="RID" field="rid" sortable />
            <Column header="Title" field="title" sortable />
            <Column header="Vendor" field="contractor_name" sortable />
            <!-- Removed Work Order No and Contract Type Columns -->
            <!-- <Column header="Work Order No" field="work_order_no" sortable /> -->
            <!-- <Column header="Type" field="contract_type" sortable /> -->
            <Column header="Status" field="status" sortable />

            <Column header="Actions" :exportable="false">
                <template #body="slotProps">
                    <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editContract(slotProps.data)" />
                    <Button icon="pi pi-trash" outlined rounded severity="danger"
                        @click="confirmDeleteContract(slotProps.data)" />
                </template>
            </Column>
        </DataTable>

        <Dialog v-model:visible="contractDialog" modal header="Contract Details" style="width: 70vw" :draggable="false">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4">

                <!-- Render all fields except removed ones -->
                <div v-for="field in [
                    'rid', 'title',
                    // Removed 'work_order_no', 'contract_type',
                    'indenting_section', 'indentor_do', 'value_inr',
                    'reqmt_recd_date', 'case_initiation_date', 'aa_date', 'sanction_date', 'indent_date',
                    'tender_do', 'tendering_section', 'nit_date',
                    'tbo_date', 'pbo_date', 'noa_po_date', 'delivery_date', 'post_contract',
                    'pr_no', 'method', 'contract_no', 'sanction_value_cr', 'percentage_above_below',
                    'contract_start_date', 'contract_end_date', 'contractor_name', 'physical_progress',
                    'addl_dealing_officer'
                ]" :key="field" class="flex flex-col">
                    <label :for="field" class="font-bold mb-1 block">
                        {{field.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}}
                    </label>
                    <InputText v-model="contract[field]" :type="field.includes('date') ? 'date' : 'text'"
                        class="w-full" />
                </div>

                <!-- Deliverables Dropdown -->
                <div class="flex flex-col">
                    <label class="font-bold mb-1 block">Deliverables</label>
                    <Dropdown v-model="contract.deliverables" :options="deliverablesOptions" optionLabel="label"
                        optionValue="value" class="w-full" />
                </div>

                <!-- Vendor Type Dropdown -->
                <div class="flex flex-col">
                    <label class="font-bold mb-1 block">Vendor Type</label>
                    <Dropdown v-model="contract.vendor_type" :options="vendorTypeOptions" optionLabel="label"
                        optionValue="value" class="w-full" />
                </div>

                <!-- Tender Type Dropdown -->
                <div class="flex flex-col">
                    <label class="font-bold mb-1 block">Tender Type</label>
                    <Dropdown v-model="contract.tender_type" :options="tenderTypeOptions" optionLabel="label"
                        optionValue="value" class="w-full" />
                </div>

                <!-- Status Dropdown -->
                <div class="flex flex-col">
                    <label class="font-bold mb-1 block">Status</label>
                    <Dropdown v-model="contract.status" :options="statusOptions" optionLabel="label" optionValue="value"
                        class="w-full" />
                </div>
            </div>

            <template #footer>
                <Button label="Close" icon="pi pi-times" text @click="hideDialog" />
                <Button label="Save" icon="pi pi-check" @click="saveContract" />
            </template>
        </Dialog>

        <Dialog v-model:visible="deleteContractDialog" modal header="Confirm" style="width: 450px">
            <div class="confirmation-content">
                <i class="pi pi-exclamation-triangle mr-3 text-red-500" style="font-size: 2rem" />
                <span>Are you sure you want to delete <b>{{ contract.title }}</b>?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" text @click="deleteContractDialog = false" />
                <Button label="Yes" icon="pi pi-check" severity="danger" @click="deleteContract" />
            </template>
        </Dialog>

        <Dialog v-model:visible="deleteContractsDialog" modal header="Confirm" style="width: 450px">
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
<style scoped>
.p-input-icon-left {
    position: relative;
    display: inline-block;
}

.p-input-icon-left>i {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    color: #888;
    font-size: 1rem;
}

.p-input-icon-left>input {
    padding-left: 2.5rem !important;
}
</style>
