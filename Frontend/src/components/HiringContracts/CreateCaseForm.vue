<template>
  <fieldset class="border rounded p-4">
    <legend class="font-semibold text-lg mb-2">Create Case</legend>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="flex flex-col">
        <label for="title" class="font-bold mb-1 block">Case Short Title*</label>
        <InputText 
          v-model="contract.title" 
          class="w-full"
          :class="{ 'p-invalid': fieldErrors.title }" 
        />
        <small v-if="fieldErrors.title" class="p-error">Title is required</small>
      </div>

      <div class="flex flex-col w-full max-w-md">
        <label for="deliverables" class="font-bold mb-1 block">Deliverables*</label>
        <Dropdown 
          v-model="contract.deliverables" 
          :options="deliverablesOptions"
          optionLabel="label" 
          optionValue="value" 
          placeholder="Select Deliverable"
          class="w-full" 
          :class="{ 'p-invalid': fieldErrors.deliverables }" 
        />
        <small v-if="fieldErrors.deliverables" class="p-error">Deliverables is required</small>
      </div>

      <div class="flex flex-col">
        <label for="indenting_section" class="font-bold mb-1 block">Indenting Section*</label>
        <InputText 
          v-model="contract.indenting_section" 
          class="w-full"
          :class="{ 'p-invalid': fieldErrors.indenting_section }" 
        />
        <small v-if="fieldErrors.indenting_section" class="p-error">Indenting section is required</small>
      </div>

      <div class="flex flex-col">
        <label for="indentor_sub_section" class="font-bold mb-1 block">Indentor Sub Section*</label>
        <Dropdown 
          v-model="contract.indentor_sub_section" 
          :options="indentorSubSectionOptions"
          optionLabel="label" 
          optionValue="value" 
          placeholder="Select Sub Section"
          class="w-full" 
          :class="{ 'p-invalid': fieldErrors.indentor_sub_section }" 
        />
        <small v-if="fieldErrors.indentor_sub_section" class="p-error">Sub section is required</small>
      </div>

      <div class="flex flex-col">
        <label for="indentor_do" class="font-bold mb-1 block">Indentor DO*</label>
        <InputText 
          v-model="contract.indentor_do" 
          class="w-full"
          :class="{ 'p-invalid': fieldErrors.indentor_do }" 
        />
        <small v-if="fieldErrors.indentor_do" class="p-error">Indentor DO is required</small>
      </div>

      <div class="flex flex-col">
        <label for="value_inr" class="font-bold mb-1 block">Value in ₹*</label>
        <InputText 
          v-model="contract.value_inr" 
          class="w-full"
          :class="{ 'p-invalid': fieldErrors.value_inr }" 
        />
        <small v-if="fieldErrors.value_inr" class="p-error">Value is required</small>
      </div>

      <div class="flex flex-col">
        <label for="vendor_type" class="font-bold mb-1 block">Vendor Type*</label>
        <Dropdown 
          v-model="contract.vendor_type" 
          :options="vendorTypeOptions"
          optionLabel="label" 
          optionValue="value" 
          placeholder="Select Vendor Type"
          class="w-full" 
          :class="{ 'p-invalid': fieldErrors.vendor_type }" 
        />
        <small v-if="fieldErrors.vendor_type" class="p-error">Vendor type is required</small>
      </div>

      <div class="flex flex-col">
        <label for="tender_type" class="font-bold mb-1 block">Tender Type*</label>
        <Dropdown 
          v-model="contract.tender_type" 
          :options="tenderTypeOptions"
          optionLabel="label" 
          optionValue="value" 
          placeholder="Select Tender Type"
          class="w-full" 
          :class="{ 'p-invalid': fieldErrors.tender_type }" 
        />
        <small v-if="fieldErrors.tender_type" class="p-error">Tender type is required</small>
      </div>

      <div class="flex flex-col">
        <label class="font-bold mb-1 block">Tendering Section*</label>
        <Dropdown 
          v-model="contract.tendering_section" 
          :options="tenderingSectionOptions"
          optionLabel="label" 
          optionValue="value" 
          placeholder="Select Tendering Section"
          class="w-full" 
          :class="{ 'p-invalid': fieldErrors.tendering_section }" 
        />
        <small v-if="fieldErrors.tendering_section" class="p-error">Tendering section is required</small>
      </div>
    </div>
  </fieldset>
</template>

<script>
export default {
  name: 'CreateCaseForm',
  props: {
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
    indentorSubSectionOptions: {
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
    tenderingSectionOptions: {
      type: Array,
      required: true
    }
  }
}
</script>