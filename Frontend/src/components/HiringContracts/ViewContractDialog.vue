<template>
    <Dialog v-model:visible="dialogVisible" modal header="Contract Details" style="width: 80vw" :draggable="false">
        <div class="p-4 space-y-6">
            <!-- Create Case Section -->
            <fieldset class="border border-yellow-300 rounded p-4">
                <legend class="font-semibold text-lg mb-2">Create Case</legend>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="flex flex-col">
                        <label class="font-bold mb-1">Case Short Title</label>
                        <InputText v-model="contract.title" class="w-full" readonly />
                    </div>
                    <div class="flex flex-col">
                        <label class="font-bold mb-1">Deliverables</label>
                        <InputText v-model="contract.deliverables" class="w-full" readonly />
                    </div>
                     <div class="flex flex-col">
                        <label class="font-bold mb-1 block">PR No.</label>
                        <InputText v-model="contract.pr_no" class="w-full" readonly />
                    </div>
                    <div class="flex flex-col">
                        <label class="font-bold mb-1">Indenting Section</label>
                        <InputText v-model="contract.indenting_section" class="w-full" readonly />
                    </div>
                    <div class="flex flex-col">
                        <label class="font-bold mb-1">Indentor Sub Section</label>
                        <InputText v-model="contract.sub_section_name" class="w-full" readonly />
                    </div>
                    <div class="flex flex-col">
                        <label class="font-bold mb-1">Indentor DO</label>
                        <InputText v-model="contract.indentor_do" class="w-full" readonly />
                    </div>
                    <div class="flex flex-col">
                        <label class="font-bold mb-1">Value in ₹</label>
                        <InputText :value="formattedValueInr" class="w-full" readonly />
                    </div>
                    <div class="flex flex-col">
                        <label class="font-bold mb-1 block">Sanction (PR) Value (Cr.) (INR)</label>
                        <InputText :value="formattedSanctionValue" class="w-full" readonly />
                    </div>
                    <div class="flex flex-col">
                        <label class="font-bold mb-1">Vendor Type</label>
                        <InputText v-model="contract.vendor_type" class="w-full" readonly />
                    </div>
                    <div class="flex flex-col">
                        <label class="font-bold mb-1">Tender Type</label>
                        <InputText v-model="contract.tender_type" class="w-full" readonly />
                    </div>
                    <div class="flex flex-col">
                        <label class="font-bold mb-1">Tendering Section</label>
                        <InputText v-model="contract.tendering_section" class="w-full" readonly />
                    </div>
                </div>
            </fieldset>

            <!-- Indenting Section -->
            <fieldset class="border border-green-300 rounded p-4">
                <legend class="font-semibold text-lg mb-2">Indenting Timelines</legend>

                <!-- Header Row -->
                <div class="grid grid-cols-12 gap-1 mb-2">
                    <div class="col-span-2 font-bold">Field Name</div>
                    <div class="col-span-1 font-bold">Norm Date</div>
                    <div class="col-span-1 font-bold">Expected Date</div>
                    <div class="col-span-1 font-bold">Actual Date</div>
                    <div class="col-span-1 font-bold">Deviation (Days)</div>
                    <div class="col-span-3 font-bold">Deviation Reason</div>
                    <div class="col-span-3 font-bold">Notes</div>
                </div>

                <!-- Data Rows -->
                <div class="grid grid-cols-12 gap-1 items-center mb-3">
                    <div class="col-span-2">Reqmt Recd Date</div>
                    <div class="col-start-5 col-span-1">
                        <InputText v-model="contract.reqmt_recd_date_actual_date" class="w-full date_style" type="date" readonly />
                    </div>
                    <div class="col-span-1">
                        <InputText v-model="contract.reqmt_recd_date_deviation_days" class="w-full text-center" :class="contract.reqmt_recd_date_deviation_days ? 'custom-red-class' : ''" readonly />
                    </div>
                     <div class="col-span-3">
                        <InputText v-model="contract.reqmt_recd_date_deviation" class="w-full" readonly />
                    </div>
                    <div class="col-span-3">
                        <InputText v-model="contract.reqmt_recd_date_notes" class="w-full" readonly />
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-1 items-center mb-3">
                    <div class="col-span-2">Case Initiation Date</div>
                    <div class="col-span-1">
                        <InputText v-model="contract.case_initiation_norm_date" type="date" class="w-full date_style" readonly  style="padding-right: 6px;" />
                    </div>
                    <div class="col-span-1">
                        <InputText v-model="contract.case_initiation_date_expected_date" class="w-full date_style" type="date" readonly />
                    </div>
                    <div class="col-span-1">
                        <InputText v-model="contract.case_initiation_date_actual_date" class="w-full date_style" type="date" readonly />
                    </div>
                    <div class="col-span-1">
                        <InputText v-model="contract.case_initiation_date_deviation_days" class="w-full text-center" :class="contract.case_initiation_date_deviation_days ? 'custom-red-class' : ''" readonly />
                    </div>
                     <div class="col-span-3">
                        <InputText v-model="contract.case_initiation_date_deviation" class="w-full" readonly />
                    </div>
                    <div class="col-span-3">
                        <InputText v-model="contract.case_initiation_date_notes" class="w-full" readonly />
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-1 items-center mb-3">
                    <div class="col-span-2">AA Date</div>
                    <div class="col-span-1">
                        <InputText v-model="contract.aa_norm_date" class="w-full date_style" type="date" readonly />
                    </div>
                    <div class="col-span-1">
                        <InputText v-model="contract.aa_date_expected_date" class="w-full date_style" type="date" readonly />
                    </div>
                    <div class="col-span-1">
                        <InputText v-model="contract.aa_date_actual_date" class="w-full date_style" type="date" readonly />
                    </div>
                     <div class="col-span-1">
                        <InputText v-model="contract.aa_date_deviation_days" class="w-full text-center" :class="contract.aa_date_deviation_days ? 'custom-red-class' : ''" readonly />
                    </div>
                     <div class="col-span-3">
                        <InputText v-model="contract.aa_date_deviation" class="w-full" readonly />
                    </div>
                    <div class="col-span-3">
                        <InputText v-model="contract.aa_date_notes" class="w-full" readonly />
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-1 items-center mb-3">
                    <div class="col-span-2">Sanction Date</div>
                    <div class="col-span-1">
                        <InputText v-model="contract.sanction_norm_date" class="w-full date_style" type="date" readonly />
                    </div>
                    <div class="col-span-1">
                        <InputText v-model="contract.sanction_date_expected_date" class="w-full date_style" type="date" readonly />
                    </div>
                    <div class="col-span-1">
                        <InputText v-model="contract.sanction_date_actual_date" class="w-full date_style" type="date" readonly />
                    </div>
                     <div class="col-span-1">
                        <InputText v-model="contract.sanction_date_deviation_days" class="w-full text-center" :class="contract.sanction_date_deviation_days ? 'custom-red-class' : ''" readonly />
                    </div>
                     <div class="col-span-3">
                        <InputText v-model="contract.sanction_date_deviation" class="w-full" readonly />
                    </div>
                    <div class="col-span-3">
                        <InputText v-model="contract.sanction_date_notes" class="w-full" readonly />
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-1 items-center">
                    <div class="col-span-2">Indent Date</div>
                    <div class="col-span-1">
                        <InputText v-model="contract.indent_norm_date" class="w-full date_style" type="date" readonly />
                    </div>
                    <div class="col-span-1">
                        <InputText v-model="contract.indent_date_expected_date" class="w-full date_style" type="date" readonly />
                    </div>
                    <div class="col-span-1">
                        <InputText v-model="contract.indent_date_actual_date" class="w-full date_style" type="date" readonly />
                    </div>
                     <div class="col-span-1">
                        <InputText v-model="contract.indent_date_deviation_days" class="w-full text-center" :class="contract.indent_date_deviation_days ? 'custom-red-class' : ''" readonly />
                    </div>
                     <div class="col-span-3">
                        <InputText v-model="contract.indent_date_deviation" class="w-full" readonly />
                    </div>
                    <div class="col-span-3">
                        <InputText v-model="contract.indent_date_notes" class="w-full" readonly />
                    </div>
                </div>
            </fieldset>

            <!-- Tendering Section -->
            <fieldset class="border border-blue-300 rounded p-4">
                <legend class="font-semibold text-lg mb-2">Tendering Timelines</legend>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <!-- Tender DO -->
                    <div class="flex flex-col">
                        <label class="font-bold mb-1">Tender DO</label>
                        <InputText v-model="contract.tender_do" class="w-full" readonly />
                    </div>

                    <!-- Tendering Platform -->
                    <div class="flex flex-col">
                        <label class="font-bold mb-1">Tendering Platform</label>
                        <InputText :value="getTenderingPlatformLabel(contract.tendering_platform)" class="w-full"
                            readonly />
                    </div>
                </div>

                <!-- Header Row -->
                <div class="grid grid-cols-12 gap-1 mb-2">
                    <div class="col-span-2 font-bold">Field Name</div>
                    <div class="col-span-1 font-bold">Norm Date</div>
                    <div class="col-span-1 font-bold">Expected Date</div>
                    <div class="col-span-1 font-bold">Actual Date</div>
                    <div class="col-span-1 font-bold">Deviation (Days)</div>
                    <div class="col-span-3 font-bold">Deviation Reason</div>
                    <div class="col-span-3 font-bold">Notes</div>
                </div>

                <!-- Data Rows -->
                <div class="grid grid-cols-12 gap-1 items-center mb-3">
                    <div class="col-span-2">NIT Date</div>
                     <div class="col-span-1">
                        <InputText v-model="contract.nit_date_norm_date" class="w-full date_style" type="date" readonly />
                    </div>
                    <div class="col-span-1">
                        <InputText v-model="contract.nit_date_expected_date" class="w-full date_style" type="date" readonly />
                    </div>
                    <div class="col-span-1">
                        <InputText v-model="contract.nit_date_actual_date" class="w-full date_style" type="date" readonly />
                    </div>
                    <div class="col-span-1">
                        <InputText v-model="contract.nit_date_deviation_days" class="w-full text-center" :class="contract.nit_date_deviation_days ? 'custom-red-class' : ''" readonly />
                    </div>
                     <div class="col-span-3">
                        <InputText v-model="contract.nit_date_deviation" class="w-full" readonly />
                    </div>
                    <div class="col-span-3">
                        <InputText v-model="contract.nit_date_notes" class="w-full" readonly />
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-1 items-center mb-3">
                    <div class="col-span-2">TBO Date</div>
                    <div class="col-span-1">
                        <InputText v-model="contract.tbo_date_norm_date" class="w-full date_style" type="date" readonly />
                    </div>
                    <div class="col-span-1">
                        <InputText v-model="contract.tbo_date_expected_date" class="w-full date_style" type="date" readonly />
                    </div>
                    <div class="col-span-1">
                        <InputText v-model="contract.tbo_date_actual_date" class="w-full date_style" type="date" readonly />
                    </div>
                    <div class="col-span-1">
                        <InputText v-model="contract.tbo_date_deviation_days" class="w-full text-center" :class="contract.tbo_date_deviation_days ? 'custom-red-class' : ''" readonly />
                    </div>
                     <div class="col-span-3">
                        <InputText v-model="contract.tbo_date_deviation" class="w-full" readonly />
                    </div>
                    <div class="col-span-3">
                        <InputText v-model="contract.tbo_date_notes" class="w-full" readonly />
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-1 items-center mb-3">
                    <div class="col-span-2">PBO Date</div>
                    <div class="col-span-1">
                        <InputText v-model="contract.pbo_date_norm_date" class="w-full date_style" type="date" readonly />
                    </div>
                    <div class="col-span-1">
                        <InputText v-model="contract.pbo_date_expected_date" class="w-full date_style" type="date" readonly />
                    </div>
                    <div class="col-span-1">
                        <InputText v-model="contract.pbo_date_actual_date" class="w-full date_style" type="date" readonly />
                    </div>
                    <div class="col-span-1">
                        <InputText v-model="contract.pbo_date_deviation_days" class="w-full text-center" :class="contract.pbo_date_deviation_days ? 'custom-red-class' : ''" readonly />
                    </div>
                     <div class="col-span-3">
                        <InputText v-model="contract.pbo_date_deviation" class="w-full" readonly />
                    </div>
                    <div class="col-span-3">
                        <InputText v-model="contract.pbo_date_notes" class="w-full" readonly />
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-1 items-center mb-3">
                    <div class="col-span-2">NOA/PO Date</div>
                    <div class="col-span-1">
                        <InputText v-model="contract.noa_po_date_norm_date" class="w-full date_style" type="date" readonly />
                    </div>
                    <div class="col-span-1">
                        <InputText v-model="contract.noa_po_date_expected_date" class="w-full date_style" type="date" readonly />
                    </div>
                    <div class="col-span-1">
                        <InputText v-model="contract.noa_po_date_actual_date" class="w-full date_style" type="date" readonly />
                    </div>
                    <div class="col-span-1">
                        <InputText v-model="contract.noa_po_date_deviation_days" class="w-full text-center" :class="contract.noa_po_date_deviation_days ? 'custom-red-class' : ''" readonly />
                    </div>
                     <div class="col-span-3">
                        <InputText v-model="contract.noa_po_date_deviation" class="w-full" readonly />
                    </div>
                    <div class="col-span-3">
                        <InputText v-model="contract.noa_po_date_notes" class="w-full" readonly />
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-1 items-center">
                    <div class="col-span-2">Delivery/Contract Start Date</div>
                    <div class="col-span-1">
                        <InputText v-model="contract.delivery_date_norm_date" class="w-full date_style" type="date" readonly />
                    </div>
                    <div class="col-span-1">
                        <InputText v-model="contract.delivery_date_expected_date" class="w-full date_style" type="date" readonly />
                    </div>
                    <div class="col-start-6 col-span-1">
                        <InputText v-model="contract.delivery_date_deviation_days" class="w-full text-center" :class="contract.delivery_date_deviation_days ? 'custom-red-class' : ''" readonly />
                    </div>
                     <div class="col-span-3">
                        <InputText v-model="contract.delivery_date_deviation" class="w-full" readonly />
                    </div>
                    <div class="col-span-3">
                        <InputText v-model="contract.delivery_date_notes" class="w-full" readonly />
                    </div>
                </div>
            </fieldset>

            <!-- Miscellaneous Section -->
            <fieldset class="border border-red-300 rounded p-4">
                <legend class="font-semibold text-lg mb-2">Miscellaneous</legend>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                   
                    
                    <div class="flex flex-col">
                        <label class="font-bold mb-1 block">Contract/PO No.</label>
                        <InputText v-model="contract.contract_no" class="w-full" readonly />
                    </div>
                    <div class="flex flex-col">
                        <label class="font-bold mb-1 block">Percentage Above/Below</label>
                        <InputText v-model="contract.percentage_above_below" class="w-full" readonly />
                    </div>
                    <div class="flex flex-col">
                        <label class="font-bold mb-1 block">Vendor Name</label>
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
                        <div class="col-span-4">Delivery/Contract Start Date</div>
                        <div class="col-span-2">
                            <!-- <InputText v-model="contract.contract_start_date_expected_date" class="w-full" readonly /> -->
                        </div>
                        <div class="col-span-2">
                            <InputText v-model="contract.contract_start_date_actual_date" class="w-full" type="date" readonly />
                        </div>
                        <div class="col-span-4">
                            <InputText v-model="contract.contract_start_date_notes" class="w-full" readonly />
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-2 items-center">
                        <div class="col-span-4">Delivery/Contract End Date</div>
                        <div class="col-span-2">
                            <InputText v-model="contract.contract_end_date_expected_date" class="w-full" type="date" readonly />
                        </div>
                        <div class="col-span-2">
                            <InputText v-model="contract.contract_end_date_actual_date" class="w-full" type="date" readonly />
                        </div>
                        <div class="col-span-4">
                            <InputText v-model="contract.contract_end_date_notes" class="w-full" readonly />
                        </div>
                    </div>
                </div>
            </fieldset>

            <div class="flex justify-end mt-6">
                <Button label="Close" icon="pi pi-times" @click="closeDialog" />
            </div>
        </div>
    </Dialog>
</template>

<script setup>
import { computed } from 'vue';
import { formatIndianNumber } from '@/utils/formatNumber';

// Define props
const props = defineProps({
    visible: {
        type: Boolean,
        default: false
    },
    contract: {
        type: Object,
        default: () => ({})
    }
});

// Define emits
const emit = defineEmits(['update:visible']);

// Computed property for v-model:visible
const dialogVisible = computed({
    get: () => props.visible,
    set: (value) => emit('update:visible', value)
});

// Computed property for formatted value_inr
const formattedValueInr = computed(() => {
    if (props.contract.value_inr && props.contract.value_inr !== '') {
        return formatIndianNumber(props.contract.value_inr.toString());
    }
    return '';
});

// Computed property for formatted sanction_value_cr
const formattedSanctionValue = computed(() => {
    if (props.contract.sanction_value_cr && props.contract.sanction_value_cr !== '') {
        return formatIndianNumber(props.contract.sanction_value_cr.toString());
    }
    return '';
});

// Method to close dialog
const closeDialog = () => {
    emit('update:visible', false);
};

const getTenderingPlatformLabel = (value) => {
    switch (value) {
        case 1: return 'GeM';
        case 2: return 'GePNIC';
        case 3: return 'eTender';
        default: return 'N/A';
    }
};
</script>

<style scoped>
/* Style for the date fields grid */
.grid-cols-12>div {
    display: flex;
    align-items: center;
    min-height: 42px;
}

/* Fieldset styling */
fieldset {
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    padding: 1rem;
}

legend {
    font-weight: 600;
    font-size: 1.125rem;
    margin-bottom: 0.5rem;
    padding: 0 0.5rem;
}

/* Border colors for different sections */
.border-yellow-300 {
    border-color: #fcd34d;
}

.border-green-300 {
    border-color: #86efac;
}

.border-blue-300 {
    border-color: #93c5fd;
}

.border-red-300 {
    border-color: #fca5a5;
}

.custom-red-class {
  color: #ef4444;
  border: 1px solid #fecaca;  
/*  background-color: #ffe5e5;  */
}

.date_style{
    padding-right: 6px;
}
</style>