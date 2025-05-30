<script setup>
import axiosClient from '@/axios';
import { onMounted, ref, reactive, computed } from 'vue';
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
const currentStep = ref(1);

const contract = reactive({
    id: null,
    rid: '',
    title: '',
    contractor_name: '',
    deliverables: '',
    indenting_section: '',
    indentor_sub_section: '', 
    indentor_do: '',
    value_inr: '',
    vendor_type: '',
    tender_type: '', 
    reqmt_recd_date_expected: '',
    reqmt_recd_date_norm: '',
    reqmt_recd_date_actual: '',
    reqmt_recd_date_notes: '',
    case_initiation_date_expected: '',
    case_initiation_date_norm: '',
    case_initiation_date_actual: '',
    case_initiation_date_notes: '',
    aa_date_expected: '',
    aa_date_norm: '',
    aa_date_actual: '',
    aa_date_notes: '',
    sanction_date_expected: '',
    sanction_date_norm: '',
    sanction_date_actual: '',
    sanction_date_notes: '',
    indent_date_expected: '',
    indent_date_norm: '',
    indent_date_actual: '',
    indent_date_notes: '',
    tender_do: '',
    tendering_section: '',
    nit_date_expected: '',
    nit_date_actual: '',
    nit_date_notes: '',
    tbo_date_expected: '',
    tbo_date_actual: '',
    tbo_date_notes: '',
    pbo_date_expected: '',
    pbo_date_actual: '',
    pbo_date_notes: '',
    noa_po_date_expected: '',
    noa_po_date_actual: '',
    noa_po_date_notes: '',
    delivery_date_expected: '',
    delivery_date_actual: '',
    delivery_date_notes: '',
    post_contract: '',
    pr_no: '',
    method: '',
    contract_no: '',
    sanction_value_cr: '',
    percentage_above_below: '',
    contract_start_date_expected: '',
    contract_start_date_actual: '',
    contract_start_date_notes: '',
    contract_end_date_expected: '',
    contract_end_date_actual: '',
    contract_end_date_notes: '',
    physical_progress: '',
    status: 'active',
    addl_dealing_officer: ''
});

const fieldErrors = reactive({
    title: false,
    deliverables: false,
    indenting_section: false,
    indentor_do: false,
    value_inr: false,
    vendor_type: false,
    tender_type: false,
    contractor_name: false,
    tender_do: false,
    tendering_section: false,
    pr_no: false,
    method: false,
    contract_no: false,
    status: false
});

// filter
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    rid: { value: null, matchMode: FilterMatchMode.CONTAINS },
    title: { value: null, matchMode: FilterMatchMode.CONTAINS },
    deliverables: { value: null, matchMode: FilterMatchMode.CONTAINS },
    tendering_section: { value: null, matchMode: FilterMatchMode.CONTAINS }
});

function clearFilters() {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        rid: { value: null, matchMode: FilterMatchMode.CONTAINS },
        title: { value: null, matchMode: FilterMatchMode.CONTAINS },
        deliverables: { value: null, matchMode: FilterMatchMode.EQUALS },
        tendering_section: { value: null, matchMode: FilterMatchMode.EQUALS }
    };
}
// filter

const statusOptions = [
    { label: 'Active', value: 'active' },
    { label: 'Closed', value: 'closed' },
    { label: 'On Hold', value: 'on_hold' }
];

const vendorTypeOptions = [
    { label: 'OEM', value: 'OEM' },
    { label: 'Non OEM', value: 'Non-OEM' }
];

const deliverablesOptions = [
    { label: 'Material', value: 'Material' },
    { label: 'Services', value: 'Services' },
    { label: 'Capital', value: 'Capital' }
];

const tenderTypeOptions = [
    { label: 'Non RC', value: 'Non RC' },
    { label: 'Open', value: 'Open' }
];

const indentorSubSectionOptions = [
    { label: 'Sub Section A', value: 'A' },
    { label: 'Sub Section B', value: 'B' },
    { label: 'Sub Section C', value: 'C' }
];

const tenderingSectionOptions = [
    { label: 'CPD', value: 'CPD' },
    { label: 'P&C', value: 'P&C' },
    { label: 'MM', value: 'MM' }
];

const apiBase = '/hiring-contracts';

const validateCreateCase = () => {
    const requiredFields = ['title', 'deliverables', 'indenting_section', 'indentor_sub_section', 'indentor_do', 'value_inr', 'vendor_type', 'tender_type', 'tendering_section'];

    return requiredFields.every((field) => {
        const value = contract[field];
        return typeof value === 'string' ? !!value.trim() : !!value;
    });
};

// For Next button - all fields must be filled
const validateIndenting = () => {
    const requiredFields = ['reqmt_recd_date_expected', 'case_initiation_date_expected', 'aa_date_expected', 'sanction_date_expected', 'indent_date_expected'];

    return requiredFields.every((field) => !!contract[field]);
};

// For Next button - all fields must be filled
const validateTendering = () => {
    const requiredFields = ['tender_do', 'nit_date_expected', 'tbo_date_expected', 'pbo_date_expected', 'noa_po_date_expected', 'delivery_date_expected'];

    return requiredFields.every((field) => !!contract[field]);
};

// For Final Save button - all fields must be filled
const validateMisc = () => {
    const requiredFields = ['pr_no', 'method', 'contract_no', 'status', 'contract_start_date_expected', 'contract_end_date_expected'];

    return requiredFields.every((field) => {
        const value = contract[field];
        return typeof value === 'string' ? !!value.trim() : !!value;
    });
};

// New validation functions for Save Section button (optional fields)
const validateIndentingForSave = () => {
    // All fields are optional for saving
    return true;
};

const validateTenderingForSave = () => {
    // All fields are optional for saving
    return true;
};

const validateMiscForSave = () => {
    // All fields are optional for saving
    return true;
};

const isCurrentStepValid = computed(() => {
    switch (currentStep.value) {
        case 1:
            return validateCreateCase();
        case 2:
            return validateIndenting();
        case 3:
            return validateTendering();
        case 4:
            return validateMisc();
        default:
            return false;
    }
});

// New computed property for Save Section button validation
const isCurrentStepValidForSave = computed(() => {
    switch (currentStep.value) {
        case 1:
            return validateCreateCase();
        case 2:
            return validateIndentingForSave();
        case 3:
            return validateTenderingForSave();
        case 4:
            return validateMiscForSave();
        default:
            return false;
    }
});

const loadUserSection = async () => {
    try {
        // Now using the nested resource endpoint
        const response = await axiosClient.get('/hiring-contracts/section');

        if (response.data.success) {
            const section = response.data.section;
            if (section?.name && !contract.tendering_section) {
                contract.tendering_section = section.name;
            }
        }
    } catch (error) {
        console.error('Error loading section:', error);
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Failed to load section information',
            life: 3000
        });
    }
};

onMounted(() => {
    loadContracts();
    loadUserSection();
});

function loadContracts() {
    axiosClient
        .get(apiBase)
        .then((res) => {
            contracts.value = res.data || [];
        })
        .catch(() => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load contracts', life: 3000 });
        });
}

function openNew() {
    currentStep.value = 1;
    Object.assign(contract, {
        id: null,
        rid: '',
        title: '',
        contractor_name: '',
        deliverables: '',
        indenting_section: '',
        indentor_do: '',
        value_inr: '',
        reqmt_recd_date_expected: '',
        reqmt_recd_date_norm: '',
        reqmt_recd_date_actual: '',
        reqmt_recd_date_notes: '',
        case_initiation_date_expected: '',
        case_initiation_date_norm: '',
        case_initiation_date_actual: '',
        case_initiation_date_notes: '',
        aa_date_expected: '',
        aa_date_norm: '',
        aa_date_actual: '',
        aa_date_notes: '',
        sanction_date_expected: '',
        sanction_date_norm: '',
        sanction_date_actual: '',
        sanction_date_notes: '',
        indent_date_expected: '',
        indent_date_norm: '',
        indent_date_actual: '',
        indent_date_notes: '',
        vendor_type: '',
        tender_do: '',
        tender_type: '',
        tendering_section: '',
        nit_date_expected: '',
        nit_date_actual: '',
        nit_date_notes: '',
        tbo_date_expected: '',
        tbo_date_actual: '',
        tbo_date_notes: '',
        pbo_date_expected: '',
        pbo_date_actual: '',
        pbo_date_notes: '',
        noa_po_date_expected: '',
        noa_po_date_actual: '',
        noa_po_date_notes: '',
        delivery_date_expected: '',
        delivery_date_actual: '',
        delivery_date_notes: '',
        post_contract: '',
        pr_no: '',
        method: '',
        contract_no: '',
        sanction_value_cr: '',
        percentage_above_below: '',
        contract_start_date_expected: '',
        contract_start_date_actual: '',
        contract_start_date_notes: '',
        contract_end_date_expected: '',
        contract_end_date_actual: '',
        contract_end_date_notes: '',
        physical_progress: '',
        status: 'active',
        addl_dealing_officer: ''
    });

    Object.keys(fieldErrors).forEach((key) => {
        fieldErrors[key] = false;
    });

    submitted.value = false;
    contractDialog.value = true;
    loadUserSection();
}

const nextStep = () => {
    if (isCurrentStepValid.value && currentStep.value < 4) {
        currentStep.value++;
    } else if (!isCurrentStepValid.value) {
        toast.add({
            severity: 'warn',
            summary: 'Validation',
            detail: 'Please fill all required fields before proceeding',
            life: 3000
        });
    }
};

const previousStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
        // Reset validation errors for the step we're returning to
        Object.keys(fieldErrors).forEach((key) => {
            fieldErrors[key] = false;
        });
        // Force revalidation of the current step
        switch (currentStep.value) {
            case 1:
                validateIndenting();
                break;
            case 2:
                validateTendering();
                break;
            case 3:
                validateMisc();
                break;
        }
    }
};

function hideDialog() {
    contractDialog.value = false;
    submitted.value = false;
    Object.keys(fieldErrors).forEach((key) => {
        fieldErrors[key] = false;
    });
    loadContracts();
}

function saveContract() {
    submitted.value = true;

    if (!isCurrentStepValid.value) {
        toast.add({ severity: 'warn', summary: 'Validation', detail: 'Please fill all required fields', life: 3000 });
        return;
    }

    const payload = { ...contract, sanction_value_cr: contract.sanction_value_cr ? Number(contract.sanction_value_cr) : null };

    const request = contract.id ? axiosClient.put(`${apiBase}/${contract.id}`, payload) : axiosClient.post(apiBase, payload);

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

const saveSection = () => {
    const payload = { ...contract };

    // Only send fields relevant to the current section
    const sectionFields = {
        1: ['title', 'deliverables', 'indenting_section', 'indentor_sub_section', 'indentor_do', 'value_inr', 'vendor_type', 'tender_type'],
        2: [
            'reqmt_recd_date_expected_date',
            'reqmt_recd_date_actual_date',
            'reqmt_recd_norm_date',
            'reqmt_recd_date_notes',
            'case_initiation_date_expected_date',
            'case_initiation_date_actual_date',
            'case_initiation_norm_date',
            'case_initiation_date_notes',
            'aa_date_expected_date',
            'aa_date_actual_date',
            'aa_norm_date',
            'aa_date_notes',
            'sanction_date_expected_date',
            'sanction_date_actual_date',
            'sanction_norm_date',
            'sanction_date_notes',
            'indent_date_expected_date',
            'indent_date_actual_date',
            'indent_norm_date',
            'indent_date_notes'
        ],
        3: [
            'tendering_section',
            'tender_do',
            'post_contract',
            'nit_date_expected',
            'nit_date_actual',
            'nit_date_notes',
            'tbo_date_expected',
            'tbo_date_actual',
            'tbo_date_notes',
            'pbo_date_expected',
            'pbo_date_actual',
            'pbo_date_notes',
            'noa_po_date_expected',
            'noa_po_date_actual',
            'noa_po_date_notes',
            'delivery_date_expected',
            'delivery_date_actual',
            'delivery_date_notes'
        ],
        4: [
            'pr_no',
            'method',
            'contract_no',
            'sanction_value_cr',
            'percentage_above_below',
            'contractor_name',
            'physical_progress',
            'status',
            'contract_start_date_expected',
            'contract_start_date_actual',
            'contract_start_date_notes',
            'contract_end_date_expected',
            'contract_end_date_actual',
            'contract_end_date_notes'
        ]
    };

    const filteredPayload = {};
    sectionFields[currentStep.value].forEach((field) => {
        filteredPayload[field] = payload[field];
    });

    const request = contract.id ? axiosClient.patch(`${apiBase}/${contract.id}`, filteredPayload) : axiosClient.post(apiBase, filteredPayload);

    request
        .then(() => {
            toast.add({
                severity: 'success',
                summary: 'Success',
                detail: `Section ${currentStep.value} saved successfully`,
                life: 3000
            });
            if (!contract.id) {
                loadContracts(); // Reload to get the ID if it's a new contract
            }
        })
        .catch(() => {
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'Failed to save section',
                life: 3000
            });
        });
};
// View Button

const viewContractDialog = ref(false);
const viewMode = ref(false);
function viewContract(c) {
    axiosClient
        .get(`${apiBase}/${c.id}`)
        .then((res) => {
            Object.assign(contract, res.data);
            viewMode.value = true;
            viewContractDialog.value = true;
        })
        .catch(() => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load contract', life: 3000 });
        });
}
// View Button

const editContract = (c) => {
    currentStep.value = 1;
    viewMode.value = false;
    axiosClient
        .get(`${apiBase}/${c.id}`)
        .then((res) => {
            Object.assign(contract, res.data);
            contractDialog.value = true;
        })
        .catch(() => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load contract', life: 3000 });
        });
};

function confirmDeleteContract(c) {
    Object.assign(contract, c);
    deleteContractDialog.value = true;
}

function deleteContract() {
    axiosClient
        .delete(`${apiBase}/${contract.id}`)
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
    const promises = selectedContracts.value.map((c) => axiosClient.delete(`${apiBase}/${c.id}`));
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
        <div class="flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Hiring Contracts</h4>
        </div>
        <Toolbar class="mb-4">
            <template #start>
                <Button label="New" icon="pi pi-plus" severity="secondary" class="mr-2" @click="openNew" />
                <Button label="Delete" icon="pi pi-trash" severity="secondary" @click="confirmDeleteSelected"
                    :disabled="!selectedContracts.length" />
            </template>
        </Toolbar>

        <DataTable ref="dt" v-model:selection="selectedContracts" :value="contracts" dataKey="id" :paginator="true"
            :rows="10" :filters="filters" :rowsPerPageOptions="[5, 10, 25]" filterDisplay="menu"
            currentPageReportTemplate="Showing {first} to {last} of {totalRecords} entries"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown">
            <template #header>
                <div class="flex flex-column gap-2">

                    <div class="flex flex-wrap align-items-center gap-3 p-2 surface-100 border-round">
                        <!-- RID Filter -->
                        <div style="min-width: 200px">
                            <span class="p-float-label">
                                <InputText placeholder="RID" v-model="filters.rid.value" class="w-full"
                                    @input="dt.filter($event.value, 'rid', 'contains')" id="ridFilter" />

                            </span>
                        </div>

                        <!-- Title Filter -->
                        <div style="min-width: 200px">
                            <span class="p-float-label">
                                <InputText placeholder="Title" v-model="filters.title.value" class="w-full"
                                    @input="dt.filter($event.value, 'title', 'contains')" id="titleFilter" />

                            </span>
                        </div>

                        <!-- Deliverables Filter -->
                        <div style="min-width: 200px">
                            <Dropdown v-model="filters.deliverables.value" :options="deliverablesOptions"
                                optionLabel="label" optionValue="value" placeholder="Deliverables" class="w-full"
                                @change="dt.filter($event.value, 'deliverables', 'equals')" :showClear="true"
                                id="deliverablesFilter" />
                        </div>

                        <!-- Tendering Section Filter -->
                        <div style="min-width: 200px">
                            <Dropdown v-model="filters.tendering_section.value" :options="tenderingSectionOptions"
                                optionLabel="label" optionValue="value" placeholder="Tendering Section" class="w-full"
                                @change="dt.filter($event.value, 'tendering_section', 'equals')" :showClear="true"
                                id="tenderingFilter" />
                        </div>
                        <div class="flex align-items-center gap-3">
                            <span class="p-input-icon-left" style="min-width: 250px">
                                <i class="pi pi-search" />
                                <InputText v-model="filters.global.value" placeholder="Global Search..."
                                    @input="dt.filter($event.value, 'global', 'contains')" class="w-full" />
                            </span>
                            <Button label="Clear" icon="pi pi-filter-slash" severity="warning" @click="clearFilters()"
                                class="p-button-text" />
                        </div>
                    </div>



                </div>
            </template>

            <!-- Columns remain the same -->
            <Column header="Sr. No">
                <template #body="slotProps">
                    {{ dt.first + slotProps.index + 1 }}
                </template>
            </Column>
            <Column header="RID" field="rid" sortable />
            <Column header="Title" field="title" sortable />
            <Column header="Vendor" field="contractor_name" sortable />
            <Column header="Status" field="status" sortable />

            <Column header="Actions" :exportable="false">
                <template #body="slotProps">
                    <Button icon="pi pi-eye" outlined rounded class="mr-2" @click="viewContract(slotProps.data)" />
                    <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editContract(slotProps.data)" />
                    <Button icon="pi pi-trash" outlined rounded severity="danger"
                        @click="confirmDeleteContract(slotProps.data)" />
                </template>
            </Column>
        </DataTable>

        <Dialog v-model:visible="viewContractDialog" modal header="Contract Details" style="width: 70vw"
            :draggable="false">
            <div class="p-4 space-y-6">
                <!-- Progress Indicators -->
                <div class="flex justify-center mb-6">
                    <div class="flex space-x-4">
                        <div v-for="(step, index) in ['Create Case', 'Indenting', 'Tendering', 'Miscellaneous']"
                            :key="index" class="flex flex-col items-center">
                            <div
                                class="w-8 h-8 rounded-full bg-primary-500 text-white flex items-center justify-center">
                                {{ index + 1 }}
                            </div>
                            <span class="text-sm mt-1">{{ step }}</span>
                        </div>
                    </div>
                </div>

                <!-- Create Case Section -->
                <fieldset class="border rounded p-4">
                    <legend class="font-semibold text-lg mb-2">Create Case</legend>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex flex-col">
                            <label class="font-bold mb-1 block">Case Short Title</label>
                            <InputText v-model="contract.title" class="w-full" readonly />
                        </div>
                        <div class="flex flex-col">
                            <label class="font-bold mb-1 block">Deliverables</label>
                            <InputText v-model="contract.deliverables" class="w-full" readonly />
                        </div>
                        <div class="flex flex-col">
                            <label class="font-bold mb-1 block">Indenting Section</label>
                            <InputText v-model="contract.indenting_section" class="w-full" readonly />
                        </div>
                        <div class="flex flex-col">
                            <label class="font-bold mb-1 block">Indentor Sub Section</label>
                            <InputText v-model="contract.indentor_sub_section" class="w-full" readonly />
                        </div>
                        <div class="flex flex-col">
                            <label class="font-bold mb-1 block">Indentor DO</label>
                            <InputText v-model="contract.indentor_do" class="w-full" readonly />
                        </div>
                        <div class="flex flex-col">
                            <label class="font-bold mb-1 block">Value in ₹</label>
                            <InputText v-model="contract.value_inr" class="w-full" readonly />
                        </div>
                        <div class="flex flex-col">
                            <label class="font-bold mb-1 block">Vendor Type</label>
                            <InputText v-model="contract.vendor_type" class="w-full" readonly />
                        </div>
                        <div class="flex flex-col">
                            <label class="font-bold mb-1 block">Tender Type</label>
                            <InputText v-model="contract.tender_type" class="w-full" readonly />
                        </div>
                        <div class="flex flex-col">
                            <label class="font-bold mb-1 block">Tendering Section</label>
                            <InputText v-model="contract.tendering_section" class="w-full" readonly />
                        </div>
                    </div>
                </fieldset>

                <!-- Indenting Section -->
                <fieldset class="border rounded p-4">
                    <legend class="font-semibold text-lg mb-2">Indenting</legend>

                    <!-- Date Fields -->
                    <div class="mt-4">
                        <div class="grid grid-cols-12 gap-2 mb-2 font-bold">
                            <div class="col-span-4">Field Name</div>
                            <div class="col-span-2">Expected Date</div>
                            <div class="col-span-2">Actual Date</div>
                            <div class="col-span-4">Notes</div>
                        </div>

                        <div class="grid grid-cols-12 gap-2 items-center mb-3">
                            <div class="col-span-4">Reqmt Recd Date</div>
                            <div class="col-span-2">
                                <InputText v-model="contract.reqmt_recd_date_expected" class="w-full" readonly />
                            </div>
                            <div class="col-span-2">
                                <InputText v-model="contract.reqmt_recd_date_actual" class="w-full" readonly />
                            </div>
                            <div class="col-span-4">
                                <InputText v-model="contract.reqmt_recd_date_notes" class="w-full" readonly />
                            </div>
                        </div>

                        <div class="grid grid-cols-12 gap-2 items-center mb-3">
                            <div class="col-span-4">Case Initiation Date</div>
                            <div class="col-span-2">
                                <InputText v-model="contract.case_initiation_date_expected" class="w-full" readonly />
                            </div>
                            <div class="col-span-2">
                                <InputText v-model="contract.case_initiation_date_actual" class="w-full" readonly />
                            </div>
                            <div class="col-span-4">
                                <InputText v-model="contract.case_initiation_date_notes" class="w-full" readonly />
                            </div>
                        </div>

                        <div class="grid grid-cols-12 gap-2 items-center mb-3">
                            <div class="col-span-4">AA Date</div>
                            <div class="col-span-2">
                                <InputText v-model="contract.aa_date_expected" class="w-full" readonly />
                            </div>
                            <div class="col-span-2">
                                <InputText v-model="contract.aa_date_actual" class="w-full" readonly />
                            </div>
                            <div class="col-span-4">
                                <InputText v-model="contract.aa_date_notes" class="w-full" readonly />
                            </div>
                        </div>

                        <div class="grid grid-cols-12 gap-2 items-center mb-3">
                            <div class="col-span-4">Sanction Date</div>
                            <div class="col-span-2">
                                <InputText v-model="contract.sanction_date_expected" class="w-full" readonly />
                            </div>
                            <div class="col-span-2">
                                <InputText v-model="contract.sanction_date_actual" class="w-full" readonly />
                            </div>
                            <div class="col-span-4">
                                <InputText v-model="contract.sanction_date_notes" class="w-full" readonly />
                            </div>
                        </div>

                        <div class="grid grid-cols-12 gap-2 items-center">
                            <div class="col-span-4">Indent Date</div>
                            <div class="col-span-2">
                                <InputText v-model="contract.indent_date_expected" class="w-full" readonly />
                            </div>
                            <div class="col-span-2">
                                <InputText v-model="contract.indent_date_actual" class="w-full" readonly />
                            </div>
                            <div class="col-span-4">
                                <InputText v-model="contract.indent_date_notes" class="w-full" readonly />
                            </div>
                        </div>
                    </div>
                </fieldset>

                <!-- Tendering Section -->
                <fieldset class="border rounded p-4">
                    <legend class="font-semibold text-lg mb-2">Tendering</legend>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div class="flex flex-col">
                            <label class="font-bold mb-1 block">Vendor Type</label>
                            <InputText v-model="contract.vendor_type" class="w-full" readonly />
                        </div>
                        <div class="flex flex-col">
                            <label class="font-bold mb-1 block">Tender DO</label>
                            <InputText v-model="contract.tender_do" class="w-full" readonly />
                        </div>
                        <div class="flex flex-col">
                            <label class="font-bold mb-1 block">Tender Type</label>
                            <InputText v-model="contract.tender_type" class="w-full" readonly />
                        </div>
                        <div class="flex flex-col">
                            <label class="font-bold mb-1 block">Post Contract</label>
                            <InputText v-model="contract.post_contract" class="w-full" readonly />
                        </div>
                    </div>

                    <!-- Date Fields -->
                    <div class="mt-4">
                        <div class="grid grid-cols-12 gap-2 mb-2 font-bold">
                            <div class="col-span-4">Field Name</div>
                            <div class="col-span-2">Expected Date</div>
                            <div class="col-span-2">Actual Date</div>
                            <div class="col-span-4">Notes</div>
                        </div>

                        <div class="grid grid-cols-12 gap-2 items-center mb-3">
                            <div class="col-span-4">NIT Date</div>
                            <div class="col-span-2">
                                <InputText v-model="contract.nit_date_expected" class="w-full" readonly />
                            </div>
                            <div class="col-span-2">
                                <InputText v-model="contract.nit_date_actual" class="w-full" readonly />
                            </div>
                            <div class="col-span-4">
                                <InputText v-model="contract.nit_date_notes" class="w-full" readonly />
                            </div>
                        </div>

                        <div class="grid grid-cols-12 gap-2 items-center mb-3">
                            <div class="col-span-4">TBO Date</div>
                            <div class="col-span-2">
                                <InputText v-model="contract.tbo_date_expected" class="w-full" readonly />
                            </div>
                            <div class="col-span-2">
                                <InputText v-model="contract.tbo_date_actual" class="w-full" readonly />
                            </div>
                            <div class="col-span-4">
                                <InputText v-model="contract.tbo_date_notes" class="w-full" readonly />
                            </div>
                        </div>

                        <div class="grid grid-cols-12 gap-2 items-center mb-3">
                            <div class="col-span-4">PBO Date</div>
                            <div class="col-span-2">
                                <InputText v-model="contract.pbo_date_expected" class="w-full" readonly />
                            </div>
                            <div class="col-span-2">
                                <InputText v-model="contract.pbo_date_actual" class="w-full" readonly />
                            </div>
                            <div class="col-span-4">
                                <InputText v-model="contract.pbo_date_notes" class="w-full" readonly />
                            </div>
                        </div>

                        <div class="grid grid-cols-12 gap-2 items-center mb-3">
                            <div class="col-span-4">NOA/PO Date</div>
                            <div class="col-span-2">
                                <InputText v-model="contract.noa_po_date_expected" class="w-full" readonly />
                            </div>
                            <div class="col-span-2">
                                <InputText v-model="contract.noa_po_date_actual" class="w-full" readonly />
                            </div>
                            <div class="col-span-4">
                                <InputText v-model="contract.noa_po_date_notes" class="w-full" readonly />
                            </div>
                        </div>

                        <div class="grid grid-cols-12 gap-2 items-center">
                            <div class="col-span-4">Delivery Date</div>
                            <div class="col-span-2">
                                <InputText v-model="contract.delivery_date_expected" class="w-full" readonly />
                            </div>
                            <div class="col-span-2">
                                <InputText v-model="contract.delivery_date_actual" class="w-full" readonly />
                            </div>
                            <div class="col-span-4">
                                <InputText v-model="contract.delivery_date_notes" class="w-full" readonly />
                            </div>
                        </div>
                    </div>
                </fieldset>

                <!-- Miscellaneous Section -->
                <fieldset class="border rounded p-4">
                    <legend class="font-semibold text-lg mb-2">Miscellaneous</legend>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div class="flex flex-col">
                            <label class="font-bold mb-1 block">PR No.</label>
                            <InputText v-model="contract.pr_no" class="w-full" readonly />
                        </div>
                        <div class="flex flex-col">
                            <label class="font-bold mb-1 block">Method</label>
                            <InputText v-model="contract.method" class="w-full" readonly />
                        </div>
                        <div class="flex flex-col">
                            <label class="font-bold mb-1 block">Contract/PO No.</label>
                            <InputText v-model="contract.contract_no" class="w-full" readonly />
                        </div>
                        <div class="flex flex-col">
                            <label class="font-bold mb-1 block">Sanction (PR) Value (Cr.) (INR)</label>
                            <InputText v-model="contract.sanction_value_cr" class="w-full" readonly />
                        </div>
                        <div class="flex flex-col">
                            <label class="font-bold mb-1 block">Percentage Above/Below</label>
                            <InputText v-model="contract.percentage_above_below" class="w-full" readonly />
                        </div>
                        <div class="flex flex-col">
                            <label class="font-bold mb-1 block">Contractor Name</label>
                            <InputText v-model="contract.contractor_name" class="w-full" readonly />
                        </div>
                        <div class="flex flex-col">
                            <label class="font-bold mb-1 block">Physical Progress (%)</label>
                            <InputText v-model="contract.physical_progress" class="w-full" readonly />
                        </div>
                        <div class="flex flex-col">
                            <label class="font-bold mb-1 block">Status</label>
                            <InputText v-model="contract.status" class="w-full" readonly />
                        </div>
                    </div>

                    <!-- Contract Date Fields -->
                    <div class="mt-4">
                        <div class="grid grid-cols-12 gap-2 mb-2 font-bold">
                            <div class="col-span-4">Field Name</div>
                            <div class="col-span-2">Expected Date</div>
                            <div class="col-span-2">Actual Date</div>
                            <div class="col-span-4">Notes</div>
                        </div>

                        <div class="grid grid-cols-12 gap-2 items-center mb-3">
                            <div class="col-span-4">Contract Start Date</div>
                            <div class="col-span-2">
                                <InputText v-model="contract.contract_start_date_expected" class="w-full" readonly />
                            </div>
                            <div class="col-span-2">
                                <InputText v-model="contract.contract_start_date_actual" class="w-full" readonly />
                            </div>
                            <div class="col-span-4">
                                <InputText v-model="contract.contract_start_date_notes" class="w-full" readonly />
                            </div>
                        </div>

                        <div class="grid grid-cols-12 gap-2 items-center">
                            <div class="col-span-4">Contract End Date</div>
                            <div class="col-span-2">
                                <InputText v-model="contract.contract_end_date_expected" class="w-full" readonly />
                            </div>
                            <div class="col-span-2">
                                <InputText v-model="contract.contract_end_date_actual" class="w-full" readonly />
                            </div>
                            <div class="col-span-4">
                                <InputText v-model="contract.contract_end_date_notes" class="w-full" readonly />
                            </div>
                        </div>
                    </div>
                </fieldset>

                <div class="flex justify-end mt-6">
                    <Button label="Close" icon="pi pi-times" @click="viewContractDialog = false" />
                </div>
            </div>
        </Dialog>
        <Dialog v-model:visible="contractDialog" modal header="Contract Details" style="width: 70vw" :draggable="false"
            @hide="loadContracts">
            <div class="p-4 space-y-6">
                <!-- Progress Dots -->
                <div class="flex justify-center mb-6">
                    <div class="flex space-x-4">
                        <div class="flex flex-col items-center">
                            <div
                                :class="['w-8 h-8 rounded-full flex items-center justify-center', currentStep >= 1 ? 'bg-primary-500 text-white' : 'bg-gray-200']">
                                1</div>
                            <span class="text-sm mt-1">Create Case</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div
                                :class="['w-8 h-8 rounded-full flex items-center justify-center', currentStep >= 2 ? 'bg-primary-500 text-white' : 'bg-gray-200']">
                                2</div>
                            <span class="text-sm mt-1">Indenting</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div
                                :class="['w-8 h-8 rounded-full flex items-center justify-center', currentStep >= 3 ? 'bg-primary-500 text-white' : 'bg-gray-200']">
                                3</div>
                            <span class="text-sm mt-1">Tendering</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div
                                :class="['w-8 h-8 rounded-full flex items-center justify-center', currentStep >= 4 ? 'bg-primary-500 text-white' : 'bg-gray-200']">
                                4</div>
                            <span class="text-sm mt-1">Miscellaneous</span>
                        </div>
                    </div>
                </div>

                <div v-show="currentStep === 1">
                    <fieldset class="border rounded p-4">
                        <legend class="font-semibold text-lg mb-2">Indenting Section</legend>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="flex flex-col">
                                <label for="title" class="font-bold mb-1 block">Case Short Title*</label>
                                <InputText v-model="contract.title" class="w-full"
                                    :class="{ 'p-invalid': fieldErrors.title }" />
                                <small v-if="fieldErrors.title" class="p-error">Title is required</small>
                            </div>

                            <div class="flex flex-col w-full max-w-md">
                                <label for="deliverables" class="font-bold mb-1 block">Deliverables*</label>
                                <Dropdown v-model="contract.deliverables" :options="deliverablesOptions"
                                    optionLabel="label" optionValue="value" placeholder="Select Deliverable"
                                    class="w-full" :class="{ 'p-invalid': fieldErrors.deliverables }" />
                                <small v-if="fieldErrors.deliverables" class="p-error">Deliverables is required</small>
                            </div>

                            <div class="flex flex-col">
                                <label for="indenting_section" class="font-bold mb-1 block">Indenting Section*</label>
                                <InputText v-model="contract.indenting_section" class="w-full"
                                    :class="{ 'p-invalid': fieldErrors.indenting_section }" />
                                <small v-if="fieldErrors.indenting_section" class="p-error">Indenting section is
                                    required</small>
                            </div>

                            <div class="flex flex-col">
                                <label for="indentor_sub_section" class="font-bold mb-1 block">Indentor Sub
                                    Section*</label>
                                <Dropdown v-model="contract.indentor_sub_section" :options="indentorSubSectionOptions"
                                    optionLabel="label" optionValue="value" placeholder="Select Sub Section"
                                    class="w-full" :class="{ 'p-invalid': fieldErrors.indentor_sub_section }" />
                                <small v-if="fieldErrors.indentor_sub_section" class="p-error">Sub section is
                                    required</small>
                            </div>

                            <div class="flex flex-col">
                                <label for="indentor_do" class="font-bold mb-1 block">Indentor DO*</label>
                                <InputText v-model="contract.indentor_do" class="w-full"
                                    :class="{ 'p-invalid': fieldErrors.indentor_do }" />
                                <small v-if="fieldErrors.indentor_do" class="p-error">Indentor DO is required</small>
                            </div>

                            <div class="flex flex-col">
                                <label for="value_inr" class="font-bold mb-1 block">Value in ₹*</label>
                                <InputText v-model="contract.value_inr" class="w-full"
                                    :class="{ 'p-invalid': fieldErrors.value_inr }" />
                                <small v-if="fieldErrors.value_inr" class="p-error">Value is required</small>
                            </div>

                            <div class="flex flex-col">
                                <label for="vendor_type" class="font-bold mb-1 block">Vendor Type*</label>
                                <Dropdown v-model="contract.vendor_type" :options="vendorTypeOptions"
                                    optionLabel="label" optionValue="value" placeholder="Select Vendor Type"
                                    class="w-full" :class="{ 'p-invalid': fieldErrors.vendor_type }" />
                                <small v-if="fieldErrors.vendor_type" class="p-error">Vendor type is required</small>
                            </div>

                            <div class="flex flex-col">
                                <label for="tender_type" class="font-bold mb-1 block">Tender Type*</label>
                                <Dropdown v-model="contract.tender_type" :options="tenderTypeOptions"
                                    optionLabel="label" optionValue="value" placeholder="Select Tender Type"
                                    class="w-full" :class="{ 'p-invalid': fieldErrors.tender_type }" />
                                <small v-if="fieldErrors.tender_type" class="p-error">Tender type is required</small>
                            </div>

                            <div class="flex flex-col">
                                <label class="font-bold mb-1 block">Tendering Section*</label>
                                <Dropdown v-model="contract.tendering_section" :options="tenderingSectionOptions"
                                    optionLabel="label" optionValue="value" placeholder="Select Tendering Section"
                                    class="w-full" :class="{ 'p-invalid': fieldErrors.tendering_section }" />
                                <small v-if="fieldErrors.tendering_section" class="p-error"> Tendering section is
                                    required
                                </small>
                            </div>
                        </div>
                    </fieldset>
                </div>

                <!-- Indenter Section -->
                <div v-show="currentStep === 2">
                    <fieldset class="border rounded p-4">
                        <legend class="font-semibold text-lg mb-2">Indenting Section</legend>

                        <!-- Date Fields Table for Indenter Section -->
                        <div class="mt-6">
                            <h4 class="font-semibold mb-3">Date Fields</h4>
                            <div class="grid grid-cols-12 gap-2 mb-2 font-bold">
                                <div class="col-span-3">Field Name</div>
                                <div class="col-span-2">Expected Date</div>
                                <div class="col-span-2">Norm Date</div>
                                <div class="col-span-2">Actual Date</div>
                                <div class="col-span-3">Notes</div>
                            </div>

                            <!-- Reqmt Recd Date -->
                            <div class="grid grid-cols-12 gap-2 items-center mb-3">
                                <div class="col-span-3">Reqmt Recd Date</div>
                                <div class="col-span-2">
                                    <InputText v-model="contract.reqmt_recd_date_expected" type="date" class="w-full" />
                                </div>
                                <div class="col-span-2">
                                    <InputText v-model="contract.reqmt_recd_date_norm" type="date" class="w-full" />
                                </div>
                                <div class="col-span-2">
                                    <InputText v-model="contract.reqmt_recd_date_actual" type="date" class="w-full" />
                                </div>
                                <div class="col-span-3">
                                    <InputText v-model="contract.reqmt_recd_date_notes" class="w-full" />
                                </div>
                            </div>

                            <!-- Case Initiation Date -->
                            <div class="grid grid-cols-12 gap-2 items-center mb-3">
                                <div class="col-span-3">Case Initiation Date</div>
                                <div class="col-span-2">
                                    <InputText v-model="contract.case_initiation_date_expected" type="date"
                                        class="w-full" />
                                </div>
                                <div class="col-span-2">
                                    <InputText v-model="contract.case_initiation_date_norm" type="date"
                                        class="w-full" />
                                </div>
                                <div class="col-span-2">
                                    <InputText v-model="contract.case_initiation_date_actual" type="date"
                                        class="w-full" />
                                </div>
                                <div class="col-span-3">
                                    <InputText v-model="contract.case_initiation_date_notes" class="w-full" />
                                </div>
                            </div>

                            <!-- AA Date -->
                            <div class="grid grid-cols-12 gap-2 items-center mb-3">
                                <div class="col-span-3">AA Date</div>
                                <div class="col-span-2">
                                    <InputText v-model="contract.aa_date_expected" type="date" class="w-full" />
                                </div>
                                <div class="col-span-2">
                                    <InputText v-model="contract.aa_date_norm" type="date" class="w-full" />
                                </div>
                                <div class="col-span-2">
                                    <InputText v-model="contract.aa_date_actual" type="date" class="w-full" />
                                </div>
                                <div class="col-span-3">
                                    <InputText v-model="contract.aa_date_notes" class="w-full" />
                                </div>
                            </div>

                            <!-- Sanction Date -->
                            <div class="grid grid-cols-12 gap-2 items-center mb-3">
                                <div class="col-span-3">Sanction Date</div>
                                <div class="col-span-2">
                                    <InputText v-model="contract.sanction_date_expected" type="date" class="w-full" />
                                </div>
                                <div class="col-span-2">
                                    <InputText v-model="contract.sanction_date_norm" type="date" class="w-full" />
                                </div>
                                <div class="col-span-2">
                                    <InputText v-model="contract.sanction_date_actual" type="date" class="w-full" />
                                </div>
                                <div class="col-span-3">
                                    <InputText v-model="contract.sanction_date_notes" class="w-full" />
                                </div>
                            </div>

                            <!-- Indent Date -->
                            <div class="grid grid-cols-12 gap-2 items-center">
                                <div class="col-span-3">Indent Date</div>
                                <div class="col-span-2">
                                    <InputText v-model="contract.indent_date_expected" type="date" class="w-full" />
                                </div>
                                <div class="col-span-2">
                                    <InputText v-model="contract.indent_date_norm" type="date" class="w-full" />
                                </div>
                                <div class="col-span-2">
                                    <InputText v-model="contract.indent_date_actual" type="date" class="w-full" />
                                </div>
                                <div class="col-span-3">
                                    <InputText v-model="contract.indent_date_notes" class="w-full" />
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>


                <!-- Tender Section -->
                <div v-show="currentStep === 3">
                    <fieldset class="border rounded p-4">
                        <legend class="font-semibold text-lg mb-2">Tendering Section</legend>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- <div class="flex flex-col w-full md:w-[29rem]">
                                <label for="contractor_name" class="font-bold mb-1 block">Vendor (OEM/Non-OEM)*</label>
                                <Dropdown v-model="contract.contractor_name" :options="vendorTypeOptions"
                                    optionLabel="label" optionValue="value" placeholder="Select Vendor Type"
                                    class="w-full" :class="{ 'p-invalid': fieldErrors.contractor_name }"
                                    panelClass="min-w-[20rem]" />
                                <small v-if="fieldErrors.contractor_name" class="p-error">Vendor is required</small>
                            </div> -->
                            <div class="flex flex-col w-90">
                                <label for="vendor_type" class="font-bold mb-1 block">Vendor (OEM/Non-OEM)*</label>
                                <Dropdown v-model="contract.vendor_type" :options="vendorTypeOptions"
                                    optionLabel="label" optionValue="value" placeholder="Select Vendor Type"
                                    :class="['w-full', { 'border border-red-500': fieldErrors.vendor_type }]" />
                                <small v-if="fieldErrors.vendor_type" class="text-red-500 mt-1">Vendor type is
                                    required</small>
                            </div>

                            <div class="flex flex-col">
                                <label for="tender_do" class="font-bold mb-1 block">Tender DO*</label>
                                <InputText v-model="contract.tender_do" class="w-full"
                                    :class="{ 'p-invalid': fieldErrors.tender_do }" />
                                <small v-if="fieldErrors.tender_do" class="p-error">Tender DO is required</small>
                            </div>

                            <div class="flex flex-col w-90">
                                <label for="tender_type" class="font-bold mb-1 block">Tender Type*</label>
                                <Dropdown v-model="contract.tender_type" :options="tenderTypeOptions"
                                    optionLabel="label" optionValue="value" placeholder="Select Tender Type"
                                    class="w-full" :class="{ 'p-invalid': fieldErrors.tender_type }" />
                                <small v-if="fieldErrors.tender_type" class="p-error">Tender type is required</small>
                            </div>

                            <!-- <div class="flex flex-col">
                                <label for="tendering_section" class="font-bold mb-1 block">Tendering Section*</label>
                                <InputText v-model="contract.tendering_section" class="w-full"
                                    :class="{ 'p-invalid': fieldErrors.tendering_section }" />
                                <small v-if="fieldErrors.tendering_section" class="p-error">Tendering section is
                                    required</small>
                            </div> -->
                            <!-- <div class="flex flex-col">
                                <label class="font-bold mb-1 block">Tendering Section*</label>
                                <InputText v-model="contract.tendering_section" class="w-full"
                                    :class="{ 'p-invalid': fieldErrors.tendering_section }" />
                                <small v-if="fieldErrors.tendering_section" class="p-error">
                                    Tendering section is required
                                </small>
                            </div> -->

                            <div class="flex flex-col">
                                <label for="post_contract" class="font-bold mb-1 block">Post Contract</label>
                                <InputText v-model="contract.post_contract" class="w-full" />
                            </div>
                        </div>

                        <!-- Date Fields Table for Tender Section -->
                        <div class="mt-6">
                            <h4 class="font-semibold mb-3">Date Fields</h4>
                            <div class="grid grid-cols-12 gap-2 mb-2 font-bold">
                                <div class="col-span-4">Field Name</div>
                                <div class="col-span-2">Expected Date</div>
                                <div class="col-span-2">Actual Date</div>
                                <div class="col-span-4">Notes</div>
                            </div>

                            <div class="grid grid-cols-12 gap-2 items-center mb-3">
                                <div class="col-span-4">NIT Date</div>
                                <div class="col-span-2">
                                    <InputText v-model="contract.nit_date_expected" type="date" class="w-full" />
                                </div>
                                <div class="col-span-2">
                                    <InputText v-model="contract.nit_date_actual" type="date" class="w-full" />
                                </div>
                                <div class="col-span-4">
                                    <InputText v-model="contract.nit_date_notes" class="w-full" />
                                </div>
                            </div>

                            <div class="grid grid-cols-12 gap-2 items-center mb-3">
                                <div class="col-span-4">TBO Date</div>
                                <div class="col-span-2">
                                    <InputText v-model="contract.tbo_date_expected" type="date" class="w-full" />
                                </div>
                                <div class="col-span-2">
                                    <InputText v-model="contract.tbo_date_actual" type="date" class="w-full" />
                                </div>
                                <div class="col-span-4">
                                    <InputText v-model="contract.tbo_date_notes" class="w-full" />
                                </div>
                            </div>

                            <div class="grid grid-cols-12 gap-2 items-center mb-3">
                                <div class="col-span-4">PBO Date</div>
                                <div class="col-span-2">
                                    <InputText v-model="contract.pbo_date_expected" type="date" class="w-full" />
                                </div>
                                <div class="col-span-2">
                                    <InputText v-model="contract.pbo_date_actual" type="date" class="w-full" />
                                </div>
                                <div class="col-span-4">
                                    <InputText v-model="contract.pbo_date_notes" class="w-full" />
                                </div>
                            </div>

                            <div class="grid grid-cols-12 gap-2 items-center mb-3">
                                <div class="col-span-4">NOA/PO Date</div>
                                <div class="col-span-2">
                                    <InputText v-model="contract.noa_po_date_expected" type="date" class="w-full" />
                                </div>
                                <div class="col-span-2">
                                    <InputText v-model="contract.noa_po_date_actual" type="date" class="w-full" />
                                </div>
                                <div class="col-span-4">
                                    <InputText v-model="contract.noa_po_date_notes" class="w-full" />
                                </div>
                            </div>

                            <div class="grid grid-cols-12 gap-2 items-center">
                                <div class="col-span-4">Delivery Date</div>
                                <div class="col-span-2">
                                    <InputText v-model="contract.delivery_date_expected" type="date" class="w-full" />
                                </div>
                                <div class="col-span-2">
                                    <InputText v-model="contract.delivery_date_actual" type="date" class="w-full" />
                                </div>
                                <div class="col-span-4">
                                    <InputText v-model="contract.delivery_date_notes" class="w-full" />
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>

                <!-- Misc Section -->
                <div v-show="currentStep === 4">
                    <fieldset class="border rounded p-4">
                        <legend class="font-semibold text-lg mb-2">Miscellaneous</legend>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <!-- First row of fields -->
                            <div class="flex flex-col">
                                <label for="pr_no" class="font-bold mb-1 block">PR No.*</label>
                                <InputText v-model="contract.pr_no" class="w-full"
                                    :class="{ 'p-invalid': fieldErrors.pr_no }" />
                                <small v-if="fieldErrors.pr_no" class="p-error">PR No. is required</small>
                            </div>

                            <div class="flex flex-col">
                                <label for="method" class="font-bold mb-1 block">Method*</label>
                                <InputText v-model="contract.method" class="w-full"
                                    :class="{ 'p-invalid': fieldErrors.method }" />
                                <small v-if="fieldErrors.method" class="p-error">Method is required</small>
                            </div>

                            <div class="flex flex-col">
                                <label for="contract_no" class="font-bold mb-1 block">Contract/PO No.*</label>
                                <InputText v-model="contract.contract_no" class="w-full"
                                    :class="{ 'p-invalid': fieldErrors.contract_no }" />
                                <small v-if="fieldErrors.contract_no" class="p-error">Contract No. is required</small>
                            </div>

                            <!-- Second row of fields -->
                            <div class="flex flex-col">
                                <label for="sanction_value_cr" class="font-bold mb-1 block">Sanction (PR) Value (Cr.)
                                    (INR)</label>
                                <InputText v-model.number="contract.sanction_value_cr" type="number" class="w-full" />
                            </div>

                            <div class="flex flex-col">
                                <label for="percentage_above_below" class="font-bold mb-1 block">Percentage
                                    Above/Below</label>
                                <InputText v-model="contract.percentage_above_below" class="w-full" />
                            </div>

                            <div class="flex flex-col">
                                <label for="contractor_name" class="font-bold mb-1 block">Contractor Name</label>
                                <InputText v-model="contract.contractor_name" class="w-full" />
                            </div>

                            <!-- Third row of fields -->
                            <div class="flex flex-col">
                                <label for="physical_progress" class="font-bold mb-1 block">Physical Progress
                                    (%)</label>
                                <InputText v-model="contract.physical_progress" class="w-full" />
                            </div>

                            <div class="flex flex-col">
                                <label for="status" class="font-bold mb-1 block">Status*</label>
                                <Dropdown v-model="contract.status" :options="statusOptions" optionLabel="label"
                                    optionValue="value" placeholder="Select Status" class="w-full"
                                    :class="{ 'p-invalid': fieldErrors.status }" />
                                <small v-if="fieldErrors.status" class="p-error">Status is required</small>
                            </div>
                        </div>

                        <!-- Date Fields Table for Misc Section -->
                        <div class="mt-6">
                            <h4 class="font-semibold mb-3">Contract Date Fields</h4>
                            <div class="grid grid-cols-12 gap-2 mb-2 font-bold">
                                <div class="col-span-4">Field Name</div>
                                <div class="col-span-2">Expected Date</div>
                                <div class="col-span-2">Actual Date</div>
                                <div class="col-span-4">Notes</div>
                            </div>

                            <!-- Contract Start Date -->
                            <div class="grid grid-cols-12 gap-2 items-center mb-3">
                                <div class="col-span-4">Contract Start Date</div>
                                <div class="col-span-2">
                                    <InputText v-model="contract.contract_start_date_expected" type="date"
                                        class="w-full" />
                                </div>
                                <div class="col-span-2">
                                    <InputText v-model="contract.contract_start_date_actual" type="date"
                                        class="w-full" />
                                </div>
                                <div class="col-span-4">
                                    <InputText v-model="contract.contract_start_date_notes" class="w-full" />
                                </div>
                            </div>

                            <!-- Contract End Date -->
                            <div class="grid grid-cols-12 gap-2 items-center">
                                <div class="col-span-4">Contract End Date</div>
                                <div class="col-span-2">
                                    <InputText v-model="contract.contract_end_date_expected" type="date"
                                        class="w-full" />
                                </div>
                                <div class="col-span-2">
                                    <InputText v-model="contract.contract_end_date_actual" type="date" class="w-full" />
                                </div>
                                <div class="col-span-4">
                                    <InputText v-model="contract.contract_end_date_notes" class="w-full" />
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>

                <div class="flex justify-between space-x-3 mt-6">
                    <Button label="Previous" icon="pi pi-arrow-left" @click="previousStep" :disabled="currentStep === 1"
                        v-if="currentStep > 1" />
                    <div v-else></div>

                    <Button label="Save Section" icon="pi pi-check" @click="saveSection" />

                    <Button v-if="currentStep < 4" label="Next" icon="pi pi-arrow-right" iconPos="right"
                        @click="nextStep" :disabled="!isCurrentStepValid" />
                    <Button v-else label="Final Save" icon="pi pi-check" @click="saveContract" />
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

/* Style for the date fields grid */
.grid-cols-12>div {
    display: flex;
    align-items: center;
    min-height: 42px;
}

/* Error styling */
.p-invalid {
    border-color: var(--red-500) !important;
}

.p-error {
    color: var(--red-500);
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

/* Progress dots styling */
.bg-primary-500 {
    background-color: var(--primary-color);
}

.bg-gray-200 {
    background-color: #e5e7eb;
}
</style>
