<script setup>
import { ref, watch, computed } from 'vue';
import { formatIndianNumber, parseIndianNumber } from '@/utils/formatNumber';

const props = defineProps({
  contract: {
    type: Object,
    required: true
  },
  fieldErrors: {
    type: Object,
    default: () => ({})
  },
  deliverablesOptions: {
    type: Array,
    required: true
  },
  vendorTypeOptions: {
    type: Array,
    required: true
  },
  tenderTypeOptions: {
    type: Array,
    required: true
  },
  indentorSubSectionOptions: {
    type: Array,
    required: true
  },
  tenderingSectionOptions: {
    type: Array,
    required: true
  },
  indentorDo: {
    type: Object,
    default: () => ({ id: null, name: '' })
  },
  isEditMode: {
    type: Boolean,
    default: false
  }
});

const formattedValue = ref('');

// Initialize formatted value when component mounts or value changes
watch(() => props.contract.value_inr, (newVal) => {
  if (newVal && newVal !== '') {
    formattedValue.value = formatIndianNumber(newVal.toString());
  } else {
    formattedValue.value = '';
  }
}, { immediate: true });

const handleValueInput = (event) => {
  const inputValue = event.target.value;
  
  // Format the displayed value
  formattedValue.value = formatIndianNumber(inputValue);
  
  // Update the actual numeric value directly in the contract object
  const numericValue = parseIndianNumber(inputValue);
  
  // SOLUTION 1: Direct assignment instead of emit
  if (!isNaN(numericValue) && numericValue !== '') {
    props.contract.value_inr = numericValue;
  } else {
    props.contract.value_inr = '';
  }
  
  // Clear field error if value is valid
  if (props.fieldErrors && numericValue) {
    props.fieldErrors.value_inr = false;
  }
};

const indentorDo = computed(() => ({
  id: props.contract.indentor_do,
  name: props.contract.indentor_do_name || ''
}));

const validateSubSection = () => {
  if (!props.contract.indentor_sub_section) {
    props.fieldErrors.indentor_sub_section = true;
  } else {
    props.fieldErrors.indentor_sub_section = false;
  }
};

const getDeliverableLabel = (value) => {
  const match = props.deliverablesOptions.find(opt => opt.value === value);
  return match ? match.label : '';
};
</script>

<template>
  <fieldset class="border rounded p-4">
    <legend class="font-semibold text-lg mb-2">Create Case</legend>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <!-- RID (Case ID) - Read-only field -->
      <div class="flex flex-col">
        <label for="rid" class="font-bold mb-1 block">Case ID (RID)</label>
        <InputText id="rid" v-model="props.contract.rid" class="w-full readonly-field"
          placeholder="Auto-generated on save" readonly />
        <small class="text-gray-500 text-xs">{{ props.contract.rid ? 'System generated ID' : 'Will be generated after saving' }}</small>
      </div>

      <div class="flex flex-col">
          <label for="pr_no" class="font-bold mb-1 block">PR No.*</label>
          <InputText v-model="contract.pr_no" class="w-full"
              :class="{ 'p-invalid': fieldErrors.pr_no }" />
          <small v-if="fieldErrors.pr_no" class="p-error">PR No. is required</small>
      </div>

      <!-- Case Short Title -->
      <div class="flex flex-col">
        <label for="title" class="font-bold mb-1 block">Case Short Title*</label>
        <InputText id="title" v-model="props.contract.title" class="w-full"
          :class="{ 'p-invalid': props.fieldErrors.title }" />
        <small v-if="props.fieldErrors.title" class="p-error">Title is required</small>
      </div>

      <!-- Deliverables -->
      <div class="flex flex-col">
        <label for="deliverables" class="font-bold mb-1 block">Deliverables*</label>
        
        <Dropdown 
          id="deliverables" 
          v-model="props.contract.deliverables" 
          :options="props.deliverablesOptions"
          optionLabel="label" 
          optionValue="value" 
          placeholder="Select Deliverable" 
          class="w-full"
          :class="{ 'p-invalid': props.fieldErrors.deliverables }"    
          :disabled="props.isEditMode" 
          readonly

        />
        
        <small v-if="props.fieldErrors.deliverables" class="p-error">Deliverables is required</small>
      </div>

      <!-- Indenting Section - Now Readonly -->
      <div class="flex flex-col">
        <label for="indenting_section" class="font-bold mb-1 block">Indenting Section*</label>
        
        <InputText 
          id="indenting_section" 
          :value="props.contract.indenting_section_name || (props.isEditMode ? 'Section ID: ' + props.contract.indenting_section : props.contract.indenting_section)" 
          class="w-full readonly-field"
          :class="{ 'p-invalid': props.fieldErrors.indenting_section }" 
          readonly 
        />
        
        <input 
          type="hidden" 
          v-model="props.contract.indenting_section" 
        />

        <small class="text-gray-500 text-xs">Auto-filled from your profile</small>
        <small v-if="props.fieldErrors.indenting_section" class="p-error">Indenting section is required</small>
      </div>

      <!-- Indentor Sub Section -->
      <div class="flex flex-col">
        <label for="indentor_sub_section" class="font-bold mb-1 block">Indentor Sub Section*</label>
        <Dropdown id="indentor_sub_section" v-model="props.contract.indentor_sub_section" :options="props.indentorSubSectionOptions"
          optionLabel="label" optionValue="value" placeholder="Select Sub Section" class="w-full"
          :class="{ 'p-invalid': props.fieldErrors.indentor_sub_section }" @change="validateSubSection" />
        <small v-if="props.fieldErrors.indentor_sub_section" class="p-error">Sub section is required</small>
      </div>

      <!-- Indentor DO - Now Readonly -->
     <div class="flex flex-col">
        <label for="indentor_do" class="font-bold mb-1 block">Indentor DO*</label>
        <InputText 
          id="indentor_do" 
          :value="contract.indentor_do_name" 
          class="w-full readonly-field"
          :class="{ 'p-invalid': fieldErrors.indentor_do }" 
          readonly 
        />
        <input 
          type="hidden" 
          v-model="contract.indentor_do" 
        />
        <small class="text-gray-500 text-xs">Auto-filled from your profile</small>
        <small v-if="fieldErrors.indentor_do" class="p-error">Indentor DO is required</small>
      </div>

      <!-- Value in INR -->
      <div class="flex flex-col">
        <label for="value_inr" class="font-bold mb-1 block">Value in ₹*</label>
        <InputText 
          id="value_inr" 
          :value="formattedValue"
          @input="handleValueInput"
          class="w-full"
          :class="{ 'p-invalid': props.fieldErrors.value_inr }" 
          placeholder="Enter amount"
        />
        <!-- Debug info (remove in production) -->
        <!-- <small class="text-xs text-blue-500">
          Raw: {{ props.contract.value_inr }} | Formatted: {{ formattedValue }}
        </small> -->
        <small v-if="props.fieldErrors.value_inr" class="p-error">Value is required</small>
      </div>

      <div class="flex flex-col">
        <label for="sanction_value_cr" class="font-bold mb-1 block">Sanction (PR) Value (Cr.)
            (INR)</label>
        <InputText v-model.number="contract.sanction_value_cr" type="number" class="w-full" />
      </div>


      <!-- Vendor Type -->
      <div class="flex flex-col">
        <label for="vendor_type" class="font-bold mb-1 block">Vendor Type*</label>
        <Dropdown id="vendor_type" v-model="props.contract.vendor_type" :options="props.vendorTypeOptions"
          optionLabel="label" optionValue="value" placeholder="Select Vendor Type" class="w-full"
          :class="{ 'p-invalid': props.fieldErrors.vendor_type }" />
        <small v-if="props.fieldErrors.vendor_type" class="p-error">Vendor type is required</small>
      </div>

      <!-- Tender Type -->
      <div class="flex flex-col">
        <label for="tender_type" class="font-bold mb-1 block">Tender Type*</label>
        <Dropdown id="tender_type" v-model="props.contract.tender_type" :options="props.tenderTypeOptions"
          optionLabel="label" optionValue="value" placeholder="Select Tender Type" class="w-full"
          :class="{ 'p-invalid': props.fieldErrors.tender_type }" />
        <small v-if="props.fieldErrors.tender_type" class="p-error">Tender type is required</small>
      </div>

      <!-- Tendering Section -->
      <div class="flex flex-col">
        <label for="tendering_section" class="font-bold mb-1 block">Tendering Section*</label>
        <Dropdown id="tendering_section" v-model="props.contract.tendering_section"
          :options="props.tenderingSectionOptions" optionLabel="label" optionValue="value"
          placeholder="Select Tendering Section" class="w-full"
          :class="{ 'p-invalid': props.fieldErrors.tendering_section }" />
        <small v-if="props.fieldErrors.tendering_section" class="p-error">Tendering section is required</small>
      </div>
    </div>
  </fieldset>
</template>

<style scoped>
.readonly-field {
  background-color: #f8f9fa !important;
  color: #6c757d !important;
  cursor: not-allowed;
}

.readonly-field:focus {
  box-shadow: none !important;
  border-color: #ced4da !important;
}

.text-gray-500 {
  color: #6b7280;
}
</style>