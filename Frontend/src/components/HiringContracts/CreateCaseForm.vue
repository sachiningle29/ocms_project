<script setup>
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
  }
});
</script>

<template>
  <fieldset class="border rounded p-4">
    <legend class="font-semibold text-lg mb-2">Indenting Section</legend>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <!-- Case Short Title -->
      <div class="flex flex-col">
        <label for="title" class="font-bold mb-1 block">Case Short Title*</label>
        <InputText 
          id="title" 
          v-model="props.contract.title" 
          class="w-full" 
          :class="{ 'p-invalid': props.fieldErrors.title }" 
        />
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
        />
        <small v-if="props.fieldErrors.deliverables" class="p-error">Deliverables is required</small>
      </div>

      <!-- Indenting Section -->
      <div class="flex flex-col">
        <label for="indenting_section" class="font-bold mb-1 block">Indenting Section*</label>
        <InputText 
          id="indenting_section" 
          v-model="props.contract.indenting_section" 
          class="w-full"
          :class="{ 'p-invalid': props.fieldErrors.indenting_section }" 
        />
        <small v-if="props.fieldErrors.indenting_section" class="p-error">Indenting section is required</small>
      </div>

      <!-- Indentor Sub Section -->
      <div class="flex flex-col">
        <label for="indentor_sub_section" class="font-bold mb-1 block">Indentor Sub Section*</label>
        <Dropdown 
          id="indentor_sub_section" 
          v-model="props.contract.indentor_sub_section" 
          :options="props.indentorSubSectionOptions"
          optionLabel="label" 
          optionValue="value" 
          placeholder="Select Sub Section"
          class="w-full" 
          :class="{ 'p-invalid': props.fieldErrors.indentor_sub_section }" 
        />
        <small v-if="props.fieldErrors.indentor_sub_section" class="p-error">Sub section is required</small>
      </div>

      <!-- Indentor DO -->
      <div class="flex flex-col">
        <label for="indentor_do" class="font-bold mb-1 block">Indentor DO*</label>
        <InputText 
          id="indentor_do" 
          v-model="props.contract.indentor_do" 
          class="w-full"
          :class="{ 'p-invalid': props.fieldErrors.indentor_do }" 
        />
        <small v-if="props.fieldErrors.indentor_do" class="p-error">Indentor DO is required</small>
      </div>

      <!-- Value in INR -->
      <div class="flex flex-col">
        <label for="value_inr" class="font-bold mb-1 block">Value in ₹*</label>
        <InputText 
          id="value_inr" 
          v-model="props.contract.value_inr" 
          class="w-full"
          :class="{ 'p-invalid': props.fieldErrors.value_inr }" 
        />
        <small v-if="props.fieldErrors.value_inr" class="p-error">Value is required</small>
      </div>

      <!-- Vendor Type -->
      <div class="flex flex-col">
        <label for="vendor_type" class="font-bold mb-1 block">Vendor Type*</label>
        <Dropdown 
          id="vendor_type" 
          v-model="props.contract.vendor_type" 
          :options="props.vendorTypeOptions"
          optionLabel="label" 
          optionValue="value" 
          placeholder="Select Vendor Type"
          class="w-full" 
          :class="{ 'p-invalid': props.fieldErrors.vendor_type }" 
        />
        <small v-if="props.fieldErrors.vendor_type" class="p-error">Vendor type is required</small>
      </div>

      <!-- Tender Type -->
      <div class="flex flex-col">
        <label for="tender_type" class="font-bold mb-1 block">Tender Type*</label>
        <Dropdown 
          id="tender_type" 
          v-model="props.contract.tender_type" 
          :options="props.tenderTypeOptions"
          optionLabel="label" 
          optionValue="value" 
          placeholder="Select Tender Type"
          class="w-full" 
          :class="{ 'p-invalid': props.fieldErrors.tender_type }" 
        />
        <small v-if="props.fieldErrors.tender_type" class="p-error">Tender type is required</small>
      </div>

      <!-- Tendering Section -->
      <div class="flex flex-col">
        <label for="tendering_section" class="font-bold mb-1 block">Tendering Section*</label>
        <Dropdown 
          id="tendering_section" 
          v-model="props.contract.tendering_section" 
          :options="props.tenderingSectionOptions"
          optionLabel="label" 
          optionValue="value" 
          placeholder="Select Tendering Section"
          class="w-full" 
          :class="{ 'p-invalid': props.fieldErrors.tendering_section }" 
        />
        <small v-if="props.fieldErrors.tendering_section" class="p-error">Tendering section is required</small>
      </div>
    </div>
  </fieldset>
</template>