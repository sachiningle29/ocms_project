<template>
  <fieldset class="border rounded p-4">
    <legend class="font-semibold text-lg mb-2">Indenting</legend>

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
          <InputText v-model="contract.case_initiation_date_expected" type="date" class="w-full" />
        </div>
        <div class="col-span-2">
          <InputText v-model="contract.case_initiation_date_norm" type="date" class="w-full" />
        </div>
        <div class="col-span-2">
          <InputText v-model="contract.case_initiation_date_actual" type="date" class="w-full" />
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
</template>

<script>
export default {
  name: 'IndentingForm',
  props: {
    contract: {
      type: Object,
      required: true
    }
  }
}
</script>