<template>
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
</template>

<script>
export default {
  name: 'MiscellaneousForm',
  props: {
    contract: {
      type: Object,
      required: true
    },
    fieldErrors: {
      type: Object,
      default: () => ({})
    },
    statusOptions: {
      type: Array,
      required: true
    }
  }
}
</script>