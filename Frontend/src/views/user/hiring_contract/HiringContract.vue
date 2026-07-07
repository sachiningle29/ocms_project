<script setup>
import axiosClient from '@/axios';
import { onMounted, ref, reactive, computed } from 'vue';
import { useToast } from 'primevue/usetoast';
import { FilterMatchMode } from '@primevue/core/api';
import ContractFilters from '@/components/HiringContracts/ContractFilters.vue';
import CreateCaseForm from '@/components/HiringContracts/CreateCaseForm.vue';
import IndentingForm from '@/components/HiringContracts/IndentingForm.vue';
import TenderingForm from '@/components/HiringContracts/TenderingForm.vue';
import MiscellaneousForm from '@/components/HiringContracts/MiscellaneousForm.vue';
import ViewContractDialog from '@/components/HiringContracts/ViewContractDialog.vue';
import Swal from 'sweetalert2';

const toast = useToast();
const dt = ref();

const contracts = ref([]);
const contractDialog = ref(false);
const deleteContractDialog = ref(false);
const deleteContractsDialog = ref(false);

const userSection = ref(null);
const userName = ref('');
const currentUser = reactive({
    id: null,
    name: userName,
    section: userSection
});

const selectedContracts = ref([]);
const submitted = ref(false);
const currentStep = ref(1);

const isEditMode = ref(false);

const contract = reactive({
    id: null,
    rid: '',
    title: '',
    contractor_name: '',
    deliverables: '',
    indenting_section: '',
    indentor_sub_section: '',
    indentor_do: null,
    indentor_do_name: '',
    value_inr: '',
    vendor_type: '',
    tender_type: '',
    current_status: 1,

    // Indenting section - with corrected field names to match IndentingForm
    reqmt_recd_date_expected_date: '',
    reqmt_recd_norm_date: '',
    reqmt_recd_date_actual_date: '',
    reqmt_recd_date_notes: '',
    reqmt_recd_date_deviation: '',
    reqmt_recd_date_deviation_days: '',        

    case_initiation_date_expected_date: '',
    case_initiation_norm_date: '',
    case_initiation_date_actual_date: '',
    case_initiation_date_notes: '',
    case_initiation_date_deviation: '',
    case_initiation_date_deviation_days: '',   

    aa_date_expected_date: '',
    aa_norm_date: '',
    aa_date_actual_date: '',
    aa_date_notes: '',
    aa_date_deviation: '',
    aa_date_deviation_days: '',                

    sanction_date_expected_date: '',
    sanction_norm_date: '',
    sanction_date_actual_date: '',
    sanction_date_notes: '',
    sanction_date_deviation: '',
    sanction_date_deviation_days: '',          

    indent_date_expected_date: '',
    indent_norm_date: '',
    indent_date_actual_date: '',
    indent_date_notes: '',
    indent_date_deviation: '',
    indent_date_deviation_days: '',            

    nit_date_expected_date: '',
    nit_date_norm_date: '',
    nit_date_actual_date: '',
    nit_date_notes: '',
    nit_date_deviation: '',
    nit_date_deviation_days: '',               

    // Tendering section
    tbo_date_expected_date: '',
    tbo_date_norm_date: '',
    tbo_date_actual_date: '',
    tbo_date_notes: '',
    tbo_date_deviation: '',
    tbo_date_deviation_days: '',               

    pbo_date_expected_date: '',
    pbo_date_norm_date: '',
    pbo_date_actual_date: '',
    pbo_date_notes: '',
    pbo_date_deviation: '',
    pbo_date_deviation_days: '',               

    noa_po_date_expected_date: '',
    noa_po_date_norm_date: '',
    noa_po_date_actual_date: '',
    noa_po_date_notes: '',
    noa_po_date_deviation: '',
    noa_po_date_deviation_days: '',            

    delivery_date_expected_date: '',
    delivery_date_norm_date: '',
    delivery_date_actual_date: '',
    delivery_date_notes: '',
    delivery_date_deviation: '',
    delivery_date_deviation_days: '',          

    // Contract dates
    contract_start_date_expected_date: '',
    contract_start_date_norm_date: '',
    contract_start_date_actual_date: '',
    contract_start_date_notes: '',
    contract_start_date_deviation: '',
    contract_start_date_deviation_days: '',    

    contract_end_date_expected_date: '',
    contract_end_date_norm_date: '',
    contract_end_date_actual_date: '',
    contract_end_date_notes: '',
    contract_end_date_deviation: '',
    contract_end_date_deviation_days: '',      

    post_contract: '',
    pr_no: '',
    method: '',
    contract_no: '',
    vendor_code: '',
    sanction_value_cr: '',
    percentage_above_below: '',
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
    sanction_value_cr: false,
    vendor_type: false,
    tender_type: false,
    contractor_name: false,
    tender_do: false,
    tendering_platform: false,
    tendering_section: false,
    pr_no: false,
    contract_no: false,
    status: false
});

// filter
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    rid: { value: null, matchMode: FilterMatchMode.CONTAINS },
    title: { value: null, matchMode: FilterMatchMode.CONTAINS },
    deliverables: { value: null, matchMode: FilterMatchMode.CONTAINS },
    tendering_section: { value: null, matchMode: FilterMatchMode.CONTAINS },
    indenting_section_name: { value: null, matchMode: FilterMatchMode.EQUALS } // Changed to match API
});

function clearFilters() {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        rid: { value: null, matchMode: FilterMatchMode.CONTAINS },
        title: { value: null, matchMode: FilterMatchMode.CONTAINS },
        deliverables: { value: null, matchMode: FilterMatchMode.EQUALS },
        tendering_section: { value: null, matchMode: FilterMatchMode.EQUALS },
        indenting_section_name: { value: null, matchMode: FilterMatchMode.EQUALS }
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

const indentorSubSectionOptions = ref([]);

const tenderingSectionOptions = [
    { label: 'CPD', value: 'CPD' },
    { label: 'P&C', value: 'P&C' },
    { label: 'MM', value: 'MM' }
];

const tenderingPlatformOptions = [
    { label: 'GeM', value: 1 },
    { label: 'GePNIC', value: 2 },
    { label: 'eTender', value: 3 }
];

const indentingSectionOptions = ref([]);

const loadSections = async () => {
    try {
        const response = await axiosClient.get('/hiring-contracts/sections'); // Make sure this endpoint exists
        indentingSectionOptions.value = response.data.map(section => ({
            label: section.indenting_section_name, // Match your column name
            value: section.indenting_section_name  // We'll filter by name since that's what API returns
        }));
    } catch (error) {
        console.error('Error loading sections:', error);
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Failed to load sections',
            life: 3000
        });
    }
};

const apiBase = '/hiring-contracts';

const unsavedChangesDialog = ref(false);

const hasUnsavedChanges = computed(() => {
    // Define fields for each section - CORRECTED FIELD NAMES
    const sectionFields = {
        1: [
            'title', 'pr_no', 'deliverables', 'indenting_section', 'indentor_sub_section',
            'indentor_do', 'value_inr', 'sanction_value_cr', 'vendor_type', 'tender_type',
            'tendering_section', 'contractor_name'
        ],
        2: [
            'reqmt_recd_date_expected_date', 'reqmt_recd_date_actual_date',
            'reqmt_recd_norm_date', 'reqmt_recd_date_notes', 'reqmt_recd_date_deviation',
            'reqmt_recd_date_deviation_days',                                              
            'case_initiation_date_expected_date', 'case_initiation_date_actual_date',
            'case_initiation_norm_date', 'case_initiation_date_notes', 'case_initiation_date_deviation',
            'case_initiation_date_deviation_days',                                        
            'aa_date_expected_date', 'aa_date_actual_date',
            'aa_norm_date', 'aa_date_notes', 'aa_date_deviation',
            'aa_date_deviation_days',                                                     
            'sanction_date_expected_date', 'sanction_date_actual_date',
            'sanction_norm_date', 'sanction_date_notes', 'sanction_date_deviation',
            'sanction_date_deviation_days',                                               
            'indent_date_expected_date', 'indent_date_actual_date',
            'indent_norm_date', 'indent_date_notes', 'indent_date_deviation',
            'indent_date_deviation_days',                                                 
            'nit_date_expected_date', 'nit_date_deviation',
            'nit_date_deviation_days'                                                     
        ],
        3: [
            'tender_do', 'tendering_platform', 'nit_date_norm_date', 'nit_date_actual_date',
            'nit_date_notes', 'nit_date_deviation', 'nit_date_deviation_days',           
            'tbo_date_expected_date', 'tbo_date_norm_date', 'tbo_date_actual_date',
            'tbo_date_notes', 'tbo_date_deviation', 'tbo_date_deviation_days',           
            'pbo_date_expected_date', 'pbo_date_norm_date', 'pbo_date_actual_date',
            'pbo_date_notes', 'pbo_date_deviation', 'pbo_date_deviation_days',           
            'noa_po_date_expected_date', 'noa_po_date_norm_date', 'noa_po_date_actual_date',
            'noa_po_date_notes', 'noa_po_date_deviation', 'noa_po_date_deviation_days',  
            'delivery_date_expected_date', 'delivery_date_norm_date', 'delivery_date_actual_date',
            'delivery_date_notes', 'delivery_date_deviation', 'delivery_date_deviation_days' 
        ],
        4: [
            'contractor_name', 'contract_no', 'vendor_code', 'percentage_above_below',
            'physical_progress', 'status', 'post_contract', 'addl_dealing_officer',
            'contract_start_date_actual_date', 'contract_start_date_notes',
            'contract_start_date_deviation_days',                                        
            'contract_end_date_expected_date', 'contract_end_date_actual_date',
            'contract_end_date_notes', 'contract_end_date_deviation_days'                
        ]
    };


    // Get fields for current section only
    const currentSectionFields = sectionFields[currentStep.value] || [];

    if (!isEditMode.value && !contract.id) {
        return currentSectionFields.some(key => {
            const value = contract[key];

            if (key === 'indenting_section' && value === currentUser.id) {
                return false;
            }
            if (key === 'indentor_do' && value === currentUser.id) {
                return false;
            }
            if (key === 'status' && value === 'active') {
                return false;
            }

            // Check if field has meaningful value (excluding default/empty values)
            if (typeof value === 'string') {
                return value.trim() !== '';
            }
            if (typeof value === 'number') {
                return value !== 0 && !isNaN(value);
            }

            return value !== null && value !== undefined && value !== '';
        });
    }

    // For edit mode - check only current section fields
    if (isEditMode.value && originalContractData.value && Object.keys(originalContractData.value).length > 0) {
        return currentSectionFields.some(key => {
            const currentValue = contract[key];
            const originalValue = originalContractData.value[key];

            // Normalize values for comparison
            const normalizeValue = (val) => {
                if (val === null || val === undefined) return '';
                if (typeof val === 'string') return val.trim();
                return String(val);
            };

            const normalizedCurrent = normalizeValue(currentValue);
            const normalizedOriginal = normalizeValue(originalValue);

            // Debug logging for troubleshooting
            if (normalizedCurrent !== normalizedOriginal) {
                console.log(`Field ${key} changed:`, {
                    current: normalizedCurrent,
                    original: normalizedOriginal
                });
            }

            return normalizedCurrent !== normalizedOriginal;
        });
    }

    return false;
});


const validateCreateCase = () => {
    // Only validate if form has been submitted
    if (!submitted.value) {
        return true; // Don't show validation errors if not submitted yet
    }

    const requiredFields = ['title', 'pr_no', 'deliverables', 'indenting_section', 'indentor_sub_section', 'indentor_do', 'value_inr', 'sanction_value_cr', 'vendor_type', 'tender_type', 'tendering_section'];

    let isValid = true;

    requiredFields.forEach((field) => {
        const value = contract[field];
        const isEmpty = typeof value === 'string' ? !value.trim() : !value;

        if (isEmpty) {
            fieldErrors[field] = true;
            isValid = false;
        } else {
            fieldErrors[field] = false;
        }
    });

    return isValid;
};

const isCreateCaseValid = computed(() => {
    const requiredFields = ['title', 'pr_no', 'deliverables', 'indenting_section', 'indentor_sub_section', 'indentor_do', 'value_inr', 'vendor_type', 'tender_type', 'tendering_section'];

    return requiredFields.every((field) => {
        const value = contract[field];
        return typeof value === 'string' ? !!value.trim() : !!value;
    });
});

// For Next button - all fields must be filled
const validateIndenting = () => {
    const requiredFields = [
        'reqmt_recd_date_actual_date',
        'case_initiation_date_expected_date',
        'case_initiation_norm_date',
        'case_initiation_date_actual_date',
        'aa_date_expected_date',
        'aa_norm_date',
        'aa_date_actual_date',
        'sanction_date_expected_date',
        'sanction_norm_date',
        'sanction_date_actual_date',
        'indent_date_expected_date',
        'indent_norm_date',
        'indent_date_actual_date',
        'nit_date_expected_date'
    ];

    return requiredFields.every((field) => {
        const value = contract[field];
        return typeof value === 'string' ? !!value.trim() : !!value;
    });
};

// For Next button - all fields must be filled
const validateTendering = () => {
    const requiredFields = [
        'tender_do',
        'tendering_platform',
        'nit_date_expected_date',
        'nit_date_actual_date',
        'nit_date_norm_date',
        'tbo_date_expected_date',
        'tbo_date_norm_date',
        'tbo_date_actual_date',
        'pbo_date_expected_date',
        'pbo_date_norm_date',
        'pbo_date_actual_date',
        'noa_po_date_expected_date',
        'noa_po_date_norm_date',
        'noa_po_date_actual_date',
        'delivery_date_expected_date',
        'delivery_date_norm_date',
    ];

    if (isEditMode.value && currentStep.value === 3 && !contract.tender_do) {
        fieldErrors.tender_do = true;
        return false;
    }

    return requiredFields.every((field) => !!contract[field]);
};

// For Final Save button - all fields must be filled
const validateMisc = () => {
    const requiredFields = ['contractor_name', 'contract_no', 'vendor_code', 'status', 'contract_start_date_actual_date', 'contract_end_date_expected_date'];

    return requiredFields.every((field) => {
        const value = contract[field];
        return typeof value === 'string' ? !!value.trim() : !!value;
    });
};

// New validation functions for Save Section button (optional fields)
const validateIndentingForSave = () => {
    // Check sequential dates
    const sequences = [
        ['reqmt_recd_date_actual_date', 'case_initiation_date_expected_date'],
        ['case_initiation_date_actual_date', 'aa_date_expected_date'],
        ['aa_date_actual_date', 'sanction_date_expected_date'],
        ['sanction_date_actual_date', 'indent_date_expected_date'],
        ['indent_date_actual_date', 'nit_date_expected_date']
    ];

    for (const [actual, nextExpected] of sequences) {
        if (contract[actual] && !contract[nextExpected]) {
            toast.add({
                severity: 'warn',
                summary: 'Sequential Validation',
                detail: 'Please fill the expected date for the next step',
                life: 4000
            });
            return false;
        }
    }
    return true;
};

const validateTenderingForSave = () => {

    if (isEditMode.value && currentStep.value === 3 && !contract.tender_do) {
        fieldErrors.tender_do = true;
        toast.add({
            severity: 'warn',
            summary: 'Validation',
            detail: 'Tender Dealing Officer is required',
            life: 4000
        });
        return false;
    }
    // Check sequential dates
    const sequences = [
        ['nit_date_actual_date', 'tbo_date_expected_date'],
        ['tbo_date_actual_date', 'pbo_date_expected_date'],
        ['pbo_date_actual_date', 'noa_po_date_expected_date'],
        ['noa_po_date_actual_date', 'delivery_date_expected_date']
    ];

    for (const [actual, nextExpected] of sequences) {
        if (contract[actual] && !contract[nextExpected]) {
            toast.add({
                severity: 'warn',
                summary: 'Sequential Validation',
                detail: 'Please fill the expected date for the next step',
                life: 4000
            });
            return false;
        }
    }
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
            // Only enforce tender_do validation in edit mode
            if (isEditMode.value && !contract.tender_do) {
                fieldErrors.tender_do = true;
                return false;
            }
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
        const response = await axiosClient.get('/hiring-contracts/section');

        if (response.data.success) {
            const userData = response.data.user;

            if (userData?.name) {
                userName.value = userData.name;
                currentUser.name = userData.name;
                currentUser.id = userData.id;
                if (!contract.indentor_do) {
                    contract.indentor_do = userData.id;
                    contract.indentor_do_name = userData.name;
                }
            }

            if (userData?.section) {
                userSection.value = userData.section.name;
                currentUser.section = userData.section.name;

                // UPDATED: Store both section ID and name
                if (!contract.indenting_section) {
                    contract.indenting_section = userData.section.id; // Store ID
                    contract.indenting_section_name = userData.section.name; // Store name for display
                }
            }

            if (userData?.sub_sections) {
                indentorSubSectionOptions.value = userData.sub_sections;
            }
        }
    } catch (error) {
        console.error('Error loading user data:', error);
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Failed to load user information',
            life: 3000
        });
    }
};

onMounted(() => {
    loadContracts();
    loadUserSection();
    loadSections()
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
    isEditMode.value = false;
    submitted.value = false;
    originalContractData.value = {};

    // Reset contract object with clean state
    Object.assign(contract, {
        id: null,
        rid: '',
        pr_no: '',
        title: '',
        contractor_name: '',
        deliverables: '',
        indenting_section: '',
        indenting_section_name: '',
        indentor_sub_section: '',
        indentor_do: null,
        indentor_do_name: '',
        value_inr: '',
        current_status: 1,

        reqmt_recd_date_actual_date: '',
        reqmt_recd_date_notes: '',
        reqmt_recd_date_deviation: '',
        reqmt_recd_date_deviation_days: '',        

        case_initiation_date_expected_date: '',
        case_initiation_norm_date: '',
        case_initiation_date_actual_date: '',
        case_initiation_date_notes: '',
        case_initiation_date_deviation: '',
        case_initiation_date_deviation_days: '',   

        aa_date_expected_date: '',
        aa_norm_date: '',
        aa_date_actual_date: '',
        aa_date_notes: '',
        aa_date_deviation: '',
        aa_date_deviation_days: '',                

        sanction_date_expected_date: '',
        sanction_norm_date: '',
        sanction_date_actual_date: '',
        sanction_date_notes: '',
        sanction_date_deviation: '',
        sanction_date_deviation_days: '',          

        indent_date_expected_date: '',
        indent_norm_date: '',
        indent_date_actual_date: '',
        indent_date_notes: '',
        indent_date_deviation: '',
        indent_date_deviation_days: '',            

        nit_date_expected_date: '',
        nit_date_norm_date: '',
        nit_date_actual_date: '',
        nit_date_notes: '',
        nit_date_deviation: '',
        nit_date_deviation_days: '',               

        tbo_date_expected_date: '',
        tbo_date_norm_date: '',
        tbo_date_actual_date: '',
        tbo_date_notes: '',
        tbo_date_deviation: '',
        tbo_date_deviation_days: '',               

        pbo_date_expected_date: '',
        pbo_date_norm_date: '',
        pbo_date_actual_date: '',
        pbo_date_notes: '',
        pbo_date_deviation: '',
        pbo_date_deviation_days: '',               

        noa_po_date_expected_date: '',
        noa_po_date_norm_date: '',
        noa_po_date_actual_date: '',
        noa_po_date_notes: '',
        noa_po_date_deviation: '',
        noa_po_date_deviation_days: '',            

        delivery_date_expected_date: '',
        delivery_date_norm_date: '',
        delivery_date_actual_date: '',
        delivery_date_notes: '',
        delivery_date_deviation: '',
        delivery_date_deviation_days: '',          

        contract_start_date_expected_date: '',
        contract_start_date_norm_date: '',
        contract_start_date_actual_date: '',
        contract_start_date_notes: '',
        contract_start_date_deviation_days: '',    

        contract_end_date_expected_date: '',
        contract_end_date_norm_date: '',
        contract_end_date_actual_date: '',
        contract_end_date_notes: '',
        contract_end_date_deviation_days: '',      

        post_contract: '',
        contract_no: '',
        vendor_code: '',
        sanction_value_cr: '',
        percentage_above_below: '',
        physical_progress: '',
        status: 'active',
        addl_dealing_officer: ''
    });

    // Clear all field errors
    Object.keys(fieldErrors).forEach((key) => {
        fieldErrors[key] = false;
    });

    submitted.value = false;
    contractDialog.value = true;

    // Load user section after dialog is open to set default values
    loadUserSection();
}


const nextStep = () => {
    submitted.value = true;

    if (isCurrentStepValid.value && currentStep.value < 4) {
        contract.current_status = currentStep.value + 1;
        currentStep.value++;

        // DON'T update originalContractData here - let it track actual saves only
        console.warn(currentStep.value);
    } else if (!isCurrentStepValid.value) {
        validateCreateCase();
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
        contract.current_status = currentStep.value - 1;
        currentStep.value--;

        Object.keys(fieldErrors).forEach((key) => {
            fieldErrors[key] = false;
        });
    }
};

function hideDialog() {
    // Only show confirmation if there are unsaved changes
    if (hasUnsavedChanges.value) {
        unsavedChangesDialog.value = true;
        // Prevent immediate closing
        return false;
    }
    // No unsaved changes - proceed with closing
    closeDialog();
}


const shouldCloseAfterSave = ref(false);

const onDialogHide = async () => {
    // Only show confirmation if there are unsaved changes
    if (hasUnsavedChanges.value) {
        console.warn(hasUnsavedChanges.value)
        try {
            const result = await Swal.fire({
                title: 'Unsaved Changes',
                text: 'You have unsaved changes. What would you like to do?',
                icon: 'warning',
                width: '45rem',
                padding: '2rem',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: 'Save and Close',
                denyButtonText: 'Close Without Saving',
                cancelButtonText: 'Continue Editing',
                confirmButtonColor: '#28a745',
                denyButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                reverseButtons: false,
                allowOutsideClick: false,
                allowEscapeKey: false
            });

            if (result.isDenied) {
                // Close without saving
                closeDialog();
                return;
            } else if (result.isConfirmed) {
                // Save and close - use existing saveSection function
                shouldCloseAfterSave.value = true;
                saveSection();
                return;
            } else {
                // Continue editing (cancelled)
                contractDialog.value = true;
                return;
            }
        } catch (error) {
            console.error('Confirmation dialog error:', error);
            // Keep dialog open on error
            contractDialog.value = true;
            return;
        }
    }

    // Proceed with closing if no unsaved changes
    closeDialog();
};


const closeDialog = () => {
    contractDialog.value = false;
    submitted.value = false;
    originalContractData.value = {}; // Reset to empty object

    Object.keys(fieldErrors).forEach((key) => {
        fieldErrors[key] = false;
    });

    loadContracts();
};

const saveContract = () => {
    submitted.value = true;

    if (!isCurrentStepValidForSave.value) {
        toast.add({ severity: 'warn', summary: 'Validation', detail: 'Please fill all required fields', life: 3000 });
        return;
    }

    const payload = {
        ...contract,
        indentor_do: Number(contract.indentor_do),
        value_inr: Number(contract.value_inr),
        sanction_value_cr: contract.sanction_value_cr ? Number(contract.sanction_value_cr) : null,
        current_status: currentStep.value
    };

    delete payload.indentor_do_name;

    const request = contract.id ?
        axiosClient.put(`${apiBase}/${contract.id}`, payload) :
        axiosClient.post(apiBase, payload);

    request
        .then((response) => {
            toast.add({
                severity: 'success',
                summary: 'Success',
                detail: contract.id ? 'Updated successfully' : 'Created successfully',
                life: 3000
            });

            // Clear original data to prevent unsaved changes popup
            originalContractData.value = {};
            contractDialog.value = false;
            loadContracts();
        })
        .catch((error) => {
            console.error('Save error:', error);
            toast.add({
                severity: 'error',
                summary: 'Error',
                detail: 'Failed to save contract',
                life: 3000
            });
        });
};

const saveSection = () => {
    submitted.value = true;

    if (!isCurrentStepValidForSave.value) {
        if (currentStep.value === 1) validateCreateCase();
        toast.add({
            severity: 'warn',
            summary: 'Validation',
            detail: 'Please fill all required fields',
            life: 3000
        });
        return;
    }

    const payload = {
        ...contract,
        current_status: currentStep.value,
        value_inr: Number(contract.value_inr),
        sanction_value_cr: contract.sanction_value_cr ? Number(contract.sanction_value_cr) : null
    };

    // CORRECTED: Updated field names to match the actual reactive object
    const sectionFields = {
        1: [
            'title', 'pr_no', 'deliverables', 'indenting_section', 'indentor_sub_section',
            'indentor_do', 'value_inr', 'vendor_type', 'sanction_value_cr', 'tender_type', 'tendering_section'
        ],
        2: [
            'reqmt_recd_date_expected_date', 'reqmt_recd_date_actual_date',
            'reqmt_recd_norm_date', 'reqmt_recd_date_notes', 'reqmt_recd_date_deviation',
            'reqmt_recd_date_deviation_days',                                              
            'case_initiation_date_expected_date', 'case_initiation_date_actual_date',
            'case_initiation_norm_date', 'case_initiation_date_notes', 'case_initiation_date_deviation',
            'case_initiation_date_deviation_days',                                        
            'aa_date_expected_date', 'aa_date_actual_date',
            'aa_norm_date', 'aa_date_notes', 'aa_date_deviation',
            'aa_date_deviation_days',                                                     
            'sanction_date_expected_date', 'sanction_date_actual_date',
            'sanction_norm_date', 'sanction_date_notes', 'sanction_date_deviation',
            'sanction_date_deviation_days',                                               
            'indent_date_expected_date', 'indent_date_actual_date',
            'indent_norm_date', 'indent_date_notes', 'indent_date_deviation',
            'indent_date_deviation_days',                                                 
            'nit_date_expected_date', 'nit_date_deviation',
            'nit_date_deviation_days'                                                     
        ],
        3: [
            'tender_do', 'tendering_platform',
            'nit_date_norm_date', 'nit_date_actual_date', 'nit_date_notes', 'nit_date_deviation',
            'nit_date_deviation_days',                                                    
            'tbo_date_expected_date', 'tbo_date_norm_date', 'tbo_date_actual_date',
            'tbo_date_notes', 'tbo_date_deviation', 'tbo_date_deviation_days',           
            'pbo_date_expected_date', 'pbo_date_norm_date', 'pbo_date_actual_date',
            'pbo_date_notes', 'pbo_date_deviation', 'pbo_date_deviation_days',           
            'noa_po_date_expected_date', 'noa_po_date_norm_date', 'noa_po_date_actual_date',
            'noa_po_date_notes', 'noa_po_date_deviation', 'noa_po_date_deviation_days',  
            'delivery_date_expected_date', 'delivery_date_norm_date', 'delivery_date_actual_date',
            'delivery_date_notes', 'delivery_date_deviation', 'delivery_date_deviation_days' 
        ],
        4: [
            'method', 'contract_no', 'contractor_name', 'vendor_code', 'percentage_above_below',
            'physical_progress', 'status', 'post_contract', 'addl_dealing_officer',
            'contract_start_date_expected_date', 'contract_start_date_actual_date',
            'contract_start_date_notes', 'contract_start_date_deviation_days',           
            'contract_end_date_expected_date', 'contract_end_date_actual_date',
            'contract_end_date_notes', 'contract_end_date_deviation_days'                
        ]
    };

    const filteredPayload = {};
    sectionFields[currentStep.value].forEach((field) => {
        filteredPayload[field] = payload[field];
    });

    const request = contract.id ?
        axiosClient.patch(`${apiBase}/${contract.id}`, filteredPayload) :
        axiosClient.post(apiBase, filteredPayload);

    request
        .then((response) => {
            toast.add({
                severity: 'success',
                summary: 'Success',
                detail: 'Section saved successfully',
                life: 3000
            });

            // CRITICAL FIX: Update original contract data after successful save
            if (isEditMode.value) {
                // For edit mode, merge the saved changes back into originalContractData
                Object.keys(filteredPayload).forEach(key => {
                    originalContractData.value[key] = contract[key];
                });
            } else {
                // For create mode, set the full contract data as original
                originalContractData.value = { ...contract };
                if (response.data?.id && !contract.id) {
                    contract.id = response.data.id;
                    originalContractData.value.id = response.data.id;
                }
            }

            if (!isEditMode.value) {
                contractDialog.value = false;
                loadContracts();
            }
        })
        .catch((error) => {
            console.error('Save error:', error);
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

const originalContractData = ref({});

const editContract = (c) => {
    currentStep.value = 1;
    viewMode.value = false;
    isEditMode.value = true;

    axiosClient
        .get(`${apiBase}/${c.id}`)
        .then((res) => {
            const contractData = {
                ...res.data,
                indentor_do: res.data.indentor_do,
                indentor_do_name: res.data.indentor_do_name,
                indenting_section_name: res.data.indenting_section_name || res.data.section?.name,
                indenting_section: res.data.indenting_section || res.data.section?.id
            };

            originalContractData.value = { ...contractData };
            Object.assign(contract, contractData);
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
            toast.add({ severity: 'warn', summary: 'Deleted', detail: 'Contract deleted', life: 3000 });
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

const goToStep = (step) => {
    // Allow navigation to previous steps or current step
    if (step <= currentStep.value) {
        contract.current_status = step;
        currentStep.value = step;
        // DON'T update originalContractData here
        return;
    }

    // For edit mode, allow navigation if current step is valid
    if (isEditMode.value && isCurrentStepValid.value) {
        contract.current_status = step;
        currentStep.value = step;
        // DON'T update originalContractData here
        return;
    }

    // For new contracts, only allow navigation if current step is valid
    if (!isEditMode.value && isCurrentStepValid.value && step === currentStep.value + 1) {
        contract.current_status = step;
        currentStep.value = step;
        return;
    }

    // If trying to navigate to future step without completing current step
    if (step > currentStep.value && !isCurrentStepValid.value) {
        toast.add({
            severity: 'warn',
            summary: 'Validation Required',
            detail: 'Please complete all required fields in the current section before proceeding',
            life: 3000
        });
    }
};

// Computed property to determine if a step is clickable
const isStepClickable = computed(() => {
    return (step) => {
        // Always allow clicking on previous steps or current step
        if (step <= currentStep.value) {
            return true;
        }

        // For edit mode, allow clicking on next step if current is valid
        if (isEditMode.value && step === currentStep.value + 1 && isCurrentStepValid.value) {
            return true;
        }

        // For new contracts, only allow next step if current is valid
        if (!isEditMode.value && step === currentStep.value + 1 && isCurrentStepValid.value) {
            return true;
        }

        return false;
    };
});
</script>

<template>
    <div class="card">
        <div class="flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Procurement Case</h4>
        </div>
        <Toolbar class="mb-4">
            <template #start>
                <Button label="Create New" icon="pi pi-plus" severity="primary" class="mr-2" @click="openNew" />
                <!-- <Button label="Delete" icon="pi pi-trash" severity="danger" @click="confirmDeleteSelected" :disabled="!selectedContracts.length" /> -->
            </template>
        </Toolbar>

        <DataTable ref="dt" v-model:selection="selectedContracts" :value="contracts" dataKey="id" :paginator="true"
            :rows="10" :filters="filters" :rowsPerPageOptions="[5, 10, 25]" filterDisplay="menu"
            currentPageReportTemplate="Showing {first} to {last} of {totalRecords} entries"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown">
            <template #header>
                <div class="flex flex-column gap-2">
                    <ContractFilters v-model:filters="filters" :deliverables-options="deliverablesOptions"
                        :tendering-section-options="tenderingSectionOptions" :indenting-section-options="indentingSectionOptions" @clear-filters="clearFilters" />
                </div>
            </template>

            <!-- Rest of your DataTable columns remain the same -->
            <Column header="Sr. No" sortable>
                <template #body="slotProps">
                    {{ dt.first + slotProps.index + 1 }}
                </template>
            </Column>
            <Column header="Case ID" field="rid" sortable />
            <Column header="Title" field="title" sortable />
            <Column header="Vendor" field="vendor_type" sortable />
            <Column header="Section" field="indenting_section_name" sortable />
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

        <!-- for view modal -->
        <ViewContractDialog v-model:visible="viewContractDialog" :contract="contract" />

        <Dialog v-model:visible="contractDialog" modal header="Contract Details" style="width: 80vw" :draggable="false"
            @hide="onDialogHide">
            <div class="p-4 space-y-6">
                <!-- Progress Dots -->
                <div class="flex justify-center mb-6">
                    <div class="flex space-x-4">
                        <div v-for="step in 4" :key="step" class="flex flex-col items-center" :class="[
                            isStepClickable(step) ? 'cursor-pointer' : 'cursor-not-allowed'
                        ]" @click="isStepClickable(step) ? goToStep(step) : null">
                            <div :class="[
                                'w-8 h-8 rounded-full flex items-center justify-center transition-colors',
                                currentStep >= step ? 'bg-primary-500 text-white' : 'bg-gray-200',
                                isStepClickable(step) ? 'hover:bg-primary-400' : 'opacity-60',
                                !isStepClickable(step) && step > currentStep ? 'bg-gray-100 text-gray-400' : ''
                            ]">
                                {{ step }}
                            </div>
                            <span class="text-sm mt-1" :class="[
                                !isStepClickable(step) && step > currentStep ? 'text-gray-400' : ''
                            ]">
                                {{ step === 1 ? (isEditMode ? 'Edit Case' : 'Create Case') :
                                    step === 2 ? 'Indenting' :
                                        step === 3 ? 'Tendering' : 'Execution' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div v-show="currentStep === 1">
                    <CreateCaseForm :contract="contract" :field-errors="fieldErrors"
                        :deliverables-options="deliverablesOptions" :vendor-type-options="vendorTypeOptions"
                        :tender-type-options="tenderTypeOptions"
                        :indentor-sub-section-options="indentorSubSectionOptions"
                        :tendering-section-options="tenderingSectionOptions" :is-edit-mode="isEditMode"
                        :current-user="currentUser" />
                </div>

                <!-- Indenter Section -->
                <div v-if="currentStep === 2">
                    <IndentingForm :contract="contract" />
                </div>

                <!-- Tender Section -->
                <div v-if="currentStep === 3 && contract">
                    <TenderingForm :contract="contract" :fieldErrors="fieldErrors"
                        :vendorTypeOptions="vendorTypeOptions" :tenderTypeOptions="tenderTypeOptions"
                        :tenderingPlatformOptions="tenderingPlatformOptions" />
                </div>

                <!-- Misc Section -->
                <div v-show="currentStep === 4">
                    <MiscellaneousForm :contract="contract" :fieldErrors="fieldErrors" :statusOptions="statusOptions" />
                </div>

                <div class="flex justify-between space-x-3 mt-6">
                    <Button label="Previous" icon="pi pi-arrow-left" @click="previousStep" :disabled="currentStep === 1"
                        v-if="currentStep > 1" />
                    <div v-else></div>

                    <!-- Updated Save/Create button with proper validation -->
                    <Button :label="isEditMode ? 'Update' : 'Create'" icon="pi pi-check" @click="saveSection"
                        :disabled="currentStep === 1 && !isCreateCaseValid"
                        :class="{ 'p-button-disabled': currentStep === 1 && !isCreateCaseValid }" />

                    <!-- Show Next button only in edit mode and not on last step -->
                    <Button v-if="isEditMode && currentStep < 4" label="Next" icon="pi pi-arrow-right" iconPos="right"
                        @click="nextStep" :disabled="!isCurrentStepValid" />

                    <!-- Updated Final Save/Create Contract button -->
                    <!-- <Button v-else-if="currentStep === 4 || isEditMode"
                        :label="isEditMode ? 'Final Save' : 'Create Contract'" icon="pi pi-check" @click="saveContract"
                        :disabled="!isCurrentStepValid" :class="{ 'p-button-disabled': !isCurrentStepValid }" /> -->
                </div>
            </div>

        </Dialog>

        <Dialog v-model:visible="deleteContractDialog" modal header="Delete Contract"
            :style="{ width: '500px', borderRadius: '16px', borderTop: '4px solid #ef4444' }"
            :breakpoints="{ '960px': '75vw', '640px': '90vw' }">
            <div class="confirmation-content flex align-items-start p-4"
                style="background: #fff8f8; border-radius: 12px">
                <i class="pi pi-shield mr-3 mt-1" style="font-size: 2rem; color: #ef4444" />
                <div>
                    <h3 class="font-medium text-lg mb-2" style="color: #b91c1c">Confirm Permanent Deletion</h3>
                    <p class="m-0 text-600">You are attempting to delete the contract: {{ contract.title }}</p>
                    <p class="m-0 text-red-500 font-medium">This action cannot be undone.It will be permanently removed.
                    </p>
                </div>
            </div>
            <template #footer>
                <div class="flex justify-content-end gap-3">
                    <Button label="Cancel" icon="pi pi-times" outlined severity="secondary"
                        @click="deleteContractDialog = false"
                        class="p-button-sm shadow-none hover:shadow-md transition-all" style="min-width: 120px" />
                    <Button label="Delete " icon="pi pi-trash" severity="danger" @click="deleteContract"
                        class="p-button-sm shadow-none hover:shadow-md transition-all" style="min-width: 160px"
                        autofocus />
                </div>
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
