<template>
  <fieldset class="border rounded p-4">
    <legend class="font-semibold text-lg mb-2">Tendering Section</legend>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="flex flex-col w-90">
        <label for="vendor_type" class="font-bold mb-1 block">Vendor (OEM/Non-OEM)*</label>
        <Dropdown id="vendor_type" v-model="contract.vendor_type" :options="vendorTypeOptions" optionLabel="label"
          optionValue="value" placeholder="Select Vendor Type"
          :class="['w-full', { 'border border-red-500': fieldErrors.vendor_type }]" />
        <small v-if="fieldErrors.vendor_type" class="text-red-500 mt-1">
          Vendor type is required
        </small>
      </div>

      <div class="flex flex-col">
        <label for="tender_do" class="font-bold mb-1 block">Tender DO*</label>
        <InputText id="tender_do" v-model="contract.tender_do" class="w-full"
          :class="{ 'p-invalid': fieldErrors.tender_do }" />
        <small v-if="fieldErrors.tender_do" class="p-error">Tender DO is required</small>
      </div>

      <div class="flex flex-col w-90">
        <label for="tender_type" class="font-bold mb-1 block">Tender Type*</label>
        <Dropdown id="tender_type" v-model="contract.tender_type" :options="tenderTypeOptions" optionLabel="label"
          optionValue="value" placeholder="Select Tender Type" class="w-full"
          :class="{ 'p-invalid': fieldErrors.tender_type }" />
        <small v-if="fieldErrors.tender_type" class="p-error">Tender type is required</small>
      </div>

      <div class="flex flex-col">
        <label for="post_contract" class="font-bold mb-1 block">Post Contract</label>
        <InputText id="post_contract" v-model="contract.post_contract" class="w-full" />
      </div>
    </div>

    <!-- Date Fields Table for Tender Section -->
    <div class="mt-6">
      <h4 class="font-semibold mb-3">Date Fields</h4>
      <div class="grid grid-cols-12 gap-2 mb-2 font-bold">
        <div class="col-span-3">Field Name</div>
        <div class="col-span-2">Expected Date</div>
        <div class="col-span-2">Norm Date</div>
        <div class="col-span-2">Actual Date</div>
        <div class="col-span-3">Notes</div>
      </div>

      <!-- NIT Date -->
      <div class="grid grid-cols-12 gap-2 items-center mb-3">
        <div class="col-span-3">NIT Date</div>
        <div class="col-span-2">
          <InputText v-model="contract.nit_date_expected" type="date" class="w-full" />
        </div>
        <div class="col-span-2">
          <InputText v-model="contract.nit_date_norm" type="date" class="w-full" />
        </div>
        <div class="col-span-2">
          <InputText v-model="contract.nit_date_actual" type="date" class="w-full" />
        </div>
        <div class="col-span-3">
          <InputText v-model="contract.nit_date_notes" class="w-full" />
        </div>
      </div>

      <!-- TBO Date -->
      <div class="grid grid-cols-12 gap-2 items-center mb-3">
        <div class="col-span-3">TBO Date</div>
        <div class="col-span-2">
          <InputText v-model="contract.tbo_date_expected" type="date" class="w-full" />
        </div>
        <div class="col-span-2">
          <InputText v-model="contract.tbo_date_norm" type="date" class="w-full" />
        </div>
        <div class="col-span-2">
          <InputText v-model="contract.tbo_date_actual" type="date" class="w-full" />
        </div>
        <div class="col-span-3">
          <InputText v-model="contract.tbo_date_notes" class="w-full" />
        </div>
      </div>

      <!-- PBO Date -->
      <div class="grid grid-cols-12 gap-2 items-center mb-3">
        <div class="col-span-3">PBO Date</div>
        <div class="col-span-2">
          <InputText v-model="contract.pbo_date_expected" type="date" class="w-full" />
        </div>
        <div class="col-span-2">
          <InputText v-model="contract.pbo_date_norm" type="date" class="w-full" />
        </div>
        <div class="col-span-2">
          <InputText v-model="contract.pbo_date_actual" type="date" class="w-full" />
        </div>
        <div class="col-span-3">
          <InputText v-model="contract.pbo_date_notes" class="w-full" />
        </div>
      </div>

      <!-- NOA/PO Date -->
      <div class="grid grid-cols-12 gap-2 items-center mb-3">
        <div class="col-span-3">NOA/PO Date</div>
        <div class="col-span-2">
          <InputText v-model="contract.noa_po_date_expected" type="date" class="w-full" />
        </div>
        <div class="col-span-2">
          <InputText v-model="contract.noa_po_date_norm" type="date" class="w-full" />
        </div>
        <div class="col-span-2">
          <InputText v-model="contract.noa_po_date_actual" type="date" class="w-full" />
        </div>
        <div class="col-span-3">
          <InputText v-model="contract.noa_po_date_notes" class="w-full" />
        </div>
      </div>

      <!-- Delivery Date -->
      <div class="grid grid-cols-12 gap-2 items-center">
        <div class="col-span-3">Delivery Date</div>
        <div class="col-span-2">
          <InputText v-model="contract.delivery_date_expected" type="date" class="w-full" />
        </div>
        <div class="col-span-2">
          <InputText v-model="contract.delivery_date_norm" type="date" class="w-full" />
        </div>
        <div class="col-span-2">
          <InputText v-model="contract.delivery_date_actual" type="date" class="w-full" />
        </div>
        <div class="col-span-3">
          <InputText v-model="contract.delivery_date_notes" class="w-full" />
        </div>
      </div>
    </div>

  </fieldset>
</template>

<script>
export default {
  name: 'TenderingForm',
  props: {
    contract: {
      type: Object,
      required: true,
      default: () => ({
        vendor_type: '',
        tender_do: '',
        tender_type: '',
        post_contract: '',
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
        delivery_date_notes: ''
      })
    },
    fieldErrors: {
      type: Object,
      default: () => ({})
    },
    vendorTypeOptions: {
      type: Array,
      required: true
    },
    tenderTypeOptions: {
      type: Array,
      required: true
    }
  }
}
</script>