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
            <div class="p-4 space-y-6">

                <!-- Indenter Section -->
                <fieldset class="border rounded p-4">
                    <legend class="font-semibold text-lg mb-2">Indenter Section</legend>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        <!-- Sl.No. (Assuming index from DataTable, else add as needed) -->

                        <div class="flex flex-col">
                            <label for="title" class="font-bold mb-1 block">Case Short Title</label>
                            <InputText v-model="contract.title" class="w-full" />
                        </div>

                        <div class="flex flex-col">
                            <label for="deliverables" class="font-bold mb-1 block">Deliverables</label>
                            <Dropdown v-model="contract.deliverables" :options="deliverablesOptions" optionLabel="label"
                                placeholder="Select Deliverable" class="w-full" />
                        </div>

                        <div class="flex flex-col">
                            <label for="indenting_section" class="font-bold mb-1 block">Indenting Section</label>
                            <InputText v-model="contract.indenting_section" class="w-full" />
                        </div>

                        <div class="flex flex-col">
                            <label for="indentor_do" class="font-bold mb-1 block">Indentor DO</label>
                            <InputText v-model="contract.indentor_do" class="w-full" />
                        </div>

                        <div class="flex flex-col">
                            <label for="value_inr" class="font-bold mb-1 block">Value in ₹</label>
                            <InputText v-model="contract.value_inr" class="w-full" />
                        </div>

                        <div class="flex flex-col">
                            <label for="reqmt_recd_date" class="font-bold mb-1 block">Reqmt Recd Date</label>
                            <InputText v-model="contract.reqmt_recd_date" type="date" class="w-full" />
                        </div>

                        <div class="flex flex-col">
                            <label for="case_initiation_date" class="font-bold mb-1 block">Case Initiation Date</label>
                            <InputText v-model="contract.case_initiation_date" type="date" class="w-full" />
                        </div>

                        <div class="flex flex-col">
                            <label for="aa_date" class="font-bold mb-1 block">AA Date</label>
                            <InputText v-model="contract.aa_date" type="date" class="w-full" />
                        </div>

                        <div class="flex flex-col">
                            <label for="sanction_date" class="font-bold mb-1 block">Sanction Date</label>
                            <InputText v-model="contract.sanction_date" type="date" class="w-full" />
                        </div>

                        <div class="flex flex-col">
                            <label for="indent_date" class="font-bold mb-1 block">Indent Date</label>
                            <InputText v-model="contract.indent_date" type="date" class="w-full" />
                        </div>

                    </div>
                </fieldset>

                <!-- Tender Section -->
                <fieldset class="border rounded p-4">
                    <legend class="font-semibold text-lg mb-2">Tender Section</legend>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        <div class="flex flex-col">
                            <label for="contractor_name" class="font-bold mb-1 block">Vendor (OEM / Non-OEM)</label>
                            <InputText v-model="contract.contractor_name" class="w-full" />
                        </div>

                        <div class="flex flex-col">
                            <label for="tender_do" class="font-bold mb-1 block">Tender DO</label>
                            <InputText v-model="contract.tender_do" class="w-full" />
                        </div>

                        <div class="flex flex-col">
                            <label for="tender_type" class="font-bold mb-1 block">Tender Type</label>
                            <Dropdown v-model="contract.tender_type" :options="tenderTypeOptions" optionLabel="label"
                                placeholder="Select Tender Type" class="w-full" />
                        </div>

                        <div class="flex flex-col">
                            <label for="tendering_section" class="font-bold mb-1 block">Tendering Section</label>
                            <InputText v-model="contract.tendering_section" class="w-full" />
                        </div>

                        <div class="flex flex-col">
                            <label for="nit_date" class="font-bold mb-1 block">NIT Date</label>
                            <InputText v-model="contract.nit_date" type="date" class="w-full" />
                        </div>

                        <div class="flex flex-col">
                            <label for="tbo_date" class="font-bold mb-1 block">TBO Date</label>
                            <InputText v-model="contract.tbo_date" type="date" class="w-full" />
                        </div>

                        <div class="flex flex-col">
                            <label for="pbo_date" class="font-bold mb-1 block">PBO Date</label>
                            <InputText v-model="contract.pbo_date" type="date" class="w-full" />
                        </div>

                        <div class="flex flex-col">
                            <label for="noa_po_date" class="font-bold mb-1 block">NOA/PO Date</label>
                            <InputText v-model="contract.noa_po_date" type="date" class="w-full" />
                        </div>

                        <div class="flex flex-col">
                            <label for="delivery_date" class="font-bold mb-1 block">Delivery Date</label>
                            <InputText v-model="contract.delivery_date" type="date" class="w-full" />
                        </div>

                        <div class="flex flex-col">
                            <label for="post_contract" class="font-bold mb-1 block">Post Contract</label>
                            <InputText v-model="contract.post_contract" class="w-full" />
                        </div>

                    </div>
                </fieldset>

                <!-- Misc Section -->
                <fieldset class="border rounded p-4">
                    <legend class="font-semibold text-lg mb-2">Miscellaneous</legend>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        <div class="flex flex-col">
                            <label for="pr_no" class="font-bold mb-1 block">PR No.</label>
                            <InputText v-model="contract.pr_no" class="w-full" />
                        </div>

                        <div class="flex flex-col">
                            <label for="method" class="font-bold mb-1 block">Method (GeM / GePNIC / E-tender / Impetus /
                                Email /
                                Physical)</label>
                            <InputText v-model="contract.method" class="w-full" />
                        </div>

                        <div class="flex flex-col">
                            <label for="contract_no" class="font-bold mb-1 block">NOA / Contract / PO No.</label>
                            <InputText v-model="contract.contract_no" class="w-full" />
                        </div>

                        <div class="flex flex-col">
                            <label for="sanction_value_cr" class="font-bold mb-1 block">Sanction (PR) Value (Cr.)
                                (INR)</label>
                            <InputText v-model="contract.sanction_value_cr" class="w-full" />
                        </div>

                        <div class="flex flex-col">
                            <label for="percentage_above_below" class="font-bold mb-1 block">Percentage Above /
                                Below</label>
                            <InputText v-model="contract.percentage_above_below" class="w-full" />
                        </div>

                        <div class="flex flex-col">
                            <label for="contract_start_date" class="font-bold mb-1 block">Contract Start Date</label>
                            <InputText v-model="contract.contract_start_date" type="date" class="w-full" />
                        </div>

                        <div class="flex flex-col">
                            <label for="contract_end_date" class="font-bold mb-1 block">Contract End Date</label>
                            <InputText v-model="contract.contract_end_date" type="date" class="w-full" />
                        </div>

                        <div class="flex flex-col">
                            <label for="contractor_name" class="font-bold mb-1 block">Contractor Name</label>
                            <InputText v-model="contract.contractor_name" class="w-full" />
                        </div>

                        <div class="flex flex-col">
                            <label for="physical_progress" class="font-bold mb-1 block">Physical Progress of Work (%)
                                till
                                Date</label>
                            <InputText v-model="contract.physical_progress" class="w-full" />
                        </div>

                        <div class="flex flex-col">
                            <label for="status" class="font-bold mb-1 block">Status</label>
                            <InputText v-model="contract.status" class="w-full" />
                        </div>

                    </div>
                </fieldset>

                <div class="flex justify-end space-x-3">
                    <Button label="Save" icon="pi pi-check" @click="saveContract" />
                    <Button label="Cancel" icon="pi pi-times" class="p-button-secondary"
                        @click="contractDialog = false" />
                </div>
            </div>
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
