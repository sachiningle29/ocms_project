<template>
  <fieldset class="border rounded p-4">
    <legend class="font-semibold text-lg mb-2">Indenting Section</legend>
    <div class="mt-6">
      <h4 class="font-semibold mb-3">Date Fields</h4>

      <div class="grid grid-cols-12 gap-2 mb-2 font-bold">
        <div class="col-span-3">Field Name</div>
        <div class="col-span-2">Expected Date</div>
        <div class="col-span-2">Norm Date</div>
        <div class="col-span-2">Actual Date</div>
        <div class="col-span-3">Notes</div>
      </div>

      <div
        v-for="(field, index) in dateFields"
        :key="index"
        class="grid grid-cols-12 gap-2 items-center mb-3"
      >
        <div class="col-span-3">{{ field.label }}</div>

        <!-- Expected Date -->
        <div class="col-span-2">
          <InputText
            type="date"
            v-model="contract[field.name + '_expected']"
            class="w-full"
            v-show="field.includeExpected"
            :readonly="!field.includeExpected"
          />
        </div>

        <!-- Norm Date -->
        <div class="col-span-2">
          <InputText
            type="date"
            v-model="contract[field.name + '_norm']"
            class="w-full"
            v-show="field.includeNorm"
            :readonly="!field.includeNorm"
          />
        </div>

        <!-- Actual Date -->
        <div class="col-span-2">
          <InputText
            type="date"
            v-model="contract[field.name + '_actual']"
            class="w-full"
          />
        </div>

        <!-- Notes -->
        <div class="col-span-3">
          <InputText
            v-model="contract[field.name + '_notes']"
            class="w-full"
          />
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
      required: true,
      default: () => ({
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
        indent_date_notes: ''
      })
    }
  },
  data() {
    return {
      dateFields: [
        {
          name: 'reqmt_recd_date',
          label: 'Reqmt Recd Date',
          includeExpected: false,
          includeNorm: false
        },
        {
          name: 'case_initiation_date',
          label: 'Case Initiation Date',
          includeExpected: true,
          includeNorm: true
        },
        {
          name: 'aa_date',
          label: 'AA Date',
          includeExpected: true,
          includeNorm: true
        },
        {
          name: 'sanction_date',
          label: 'Sanction Date',
          includeExpected: true,
          includeNorm: true
        },
        {
          name: 'indent_date',
          label: 'Indent Date',
          includeExpected: true,
          includeNorm: true
        }
      ]
    };
  }
};
</script>
