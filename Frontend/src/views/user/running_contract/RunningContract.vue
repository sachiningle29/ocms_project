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

const contract = ref({
    rid: '',
    contractor_name: '',
    work_order_number: '',
    contract_type: '',
    department: '',
    section: '',
    status: '',
    title: '',
    case_type: '',
    pr_no: '',
    dealing_officer: '',
    value_usd: null,
    value_inr: null,
    contract_start_date: '',
    contract_end_date: '',
    funds_utilised: null,
    remarks: '',
    trigger: '',
    original_date_of_delivery: '',
    no_of_extensions: null,
    extended_po_lc_last_date_of_shipment: '',
    ec_and_sims_status: '',
    post_contract_issues_in_brief: '',
    current_status: ''
});

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

            <Column field="rid" header="Contract ID" sortable />
            <Column field="title" header="Title" sortable />
            <Column field="contractor_name" header="Contractor Name" sortable />
            <Column field="pr_no" header="PR No" sortable />
            <Column field="work_order_number" header="Work Order Number" sortable />
            <Column field="contract_type" header="Contract Type" sortable />
            <Column field="case_type" header="Case Type" sortable />
            <Column field="department" header="Department" sortable />
            <Column field="section" header="Section" sortable />
            <Column field="dealing_officer" header="Dealing Officer" sortable />
            <Column field="value_usd" header="Value (USD)" sortable />
            <Column field="value_inr" header="Value (INR)" sortable />
            <Column field="contract_start_date" header="Start Date" sortable />
            <Column field="contract_end_date" header="End Date" sortable />
            <Column field="original_date_of_delivery" header="Original Delivery Date" sortable />
            <Column field="no_of_extensions" header="Extensions" sortable />
            <Column field="extended_po_lc_last_date_of_shipment" header="Extended PO/LC Date" sortable />
            <Column field="ec_and_sims_status" header="EC & SIMS Status" sortable />
            <Column field="post_contract_issues_in_brief" header="Post-Contract Issues" sortable />
            <Column field="funds_utilised" header="Funds Utilised" sortable />
            <Column field="remarks" header="Remarks" sortable />
            <Column field="trigger" header="Trigger" sortable />
            <Column field="current_status" header="Current Status" sortable />
            <Column field="status" header="Status" sortable />
            <Column field="created_at" header="Created At" sortable />


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

        <Dialog v-model:visible="contractDialog" modal header="Contract Details" :closable="true" style="width: 90vw">
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="font-bold mb-1 block">RID</label>
                        <InputText v-model="contract.rid" class="w-full" />
                    </div>
                    <div>
                        <label class="font-bold mb-1 block">Vendor Name</label>
                        <InputText v-model="contract.contractor_name" class="w-full" />
                    </div>
                    <div>
                        <label class="font-bold mb-1 block">Work Order Number</label>
                        <InputText v-model="contract.work_order_number" class="w-full" />
                    </div>
                    <div>
                        <label class="font-bold mb-1 block">Contract Type</label>
                        <Dropdown v-model="contract.contract_type" :options="contractTypes" optionLabel="label"
                            placeholder="Select Type" class="w-full" />
                    </div>
                    <div>
                        <label class="font-bold mb-1 block">Department</label>
                        <InputText v-model="contract.department" class="w-full" />
                    </div>
                    <div>
                        <label class="font-bold mb-1 block">Section</label>
                        <Dropdown v-model="contract.section" :options="sections" optionLabel="label"
                            placeholder="Select Section" class="w-full" />
                    </div>
                    <div>
                        <label class="font-bold mb-1 block">Title</label>
                        <InputText v-model="contract.title" class="w-full" />
                    </div>
                    <div>
                        <label class="font-bold mb-1 block">Case Type</label>
                        <InputText v-model="contract.case_type" class="w-full" />
                    </div>
                    <div>
                        <label class="font-bold mb-1 block">PR No</label>
                        <InputText v-model="contract.pr_no" class="w-full" />
                    </div>
                    <div>
                        <label class="font-bold mb-1 block">Dealing Officer</label>
                        <InputText v-model="contract.dealing_officer" class="w-full" />
                    </div>
                    <div>
                        <label class="font-bold mb-1 block">Value (USD)</label>
                        <InputNumber v-model="contract.value_usd" class="w-full" />
                    </div>
                    <div>
                        <label class="font-bold mb-1 block">Value (INR)</label>
                        <InputNumber v-model="contract.value_inr" class="w-full" />
                    </div>
                    <div>
                        <label class="font-bold mb-1 block">Contract Start Date</label>
                        <Calendar v-model="contract.contract_start_date" class="w-full" dateFormat="yy-mm-dd" />
                    </div>
                    <div>
                        <label class="font-bold mb-1 block">Contract End Date</label>
                        <Calendar v-model="contract.contract_end_date" class="w-full" dateFormat="yy-mm-dd" />
                    </div>
                    <div>
                        <label class="font-bold mb-1 block">Funds Utilised</label>
                        <InputNumber v-model="contract.funds_utilised" class="w-full" />
                    </div>
                    <div>
                        <label class="font-bold mb-1 block">Remarks</label>
                        <Textarea v-model="contract.remarks" class="w-full" rows="3" />
                    </div>
                    <div>
                        <label class="font-bold mb-1 block">Trigger</label>
                        <InputText v-model="contract.trigger" class="w-full" />
                    </div>
                    <div>
                        <label class="font-bold mb-1 block">Original Date of Delivery</label>
                        <Calendar v-model="contract.original_date_of_delivery" class="w-full" dateFormat="yy-mm-dd" />
                    </div>
                    <div>
                        <label class="font-bold mb-1 block">No. of Extensions</label>
                        <InputNumber v-model="contract.no_of_extensions" class="w-full" />
                    </div>
                    <div>
                        <label class="font-bold mb-1 block">Extended PO/LC Last Shipment</label>
                        <Calendar v-model="contract.extended_po_lc_last_date_of_shipment" class="w-full"
                            dateFormat="yy-mm-dd" />
                    </div>
                    <div>
                        <label class="font-bold mb-1 block">EC and SIMS Status</label>
                        <InputText v-model="contract.ec_and_sims_status" class="w-full" />
                    </div>
                    <div>
                        <label class="font-bold mb-1 block">Post Contract Issues (Brief)</label>
                        <Textarea v-model="contract.post_contract_issues_in_brief" class="w-full" rows="3" />
                    </div>
                    <div>
                        <label class="font-bold mb-1 block">Current Status</label>
                        <InputText v-model="contract.current_status" class="w-full" />
                    </div>
                </div>
            </div>

            <template #footer>
                <Button label="Cancel" icon="pi pi-times" class="p-button-text" @click="hideDialog" />
                <Button label="Save" icon="pi pi-check" class="p-button-primary" @click="saveContract" />
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
