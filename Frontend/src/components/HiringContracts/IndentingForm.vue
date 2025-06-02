<template>
  <fieldset class="border rounded p-4">
    <legend class="font-semibold text-lg mb-2">Indenting Section</legend>

    <div class="mt-6">
      <h4 class="font-semibold mb-3">Date Fields</h4>

      <!-- Table Header -->
      <div class="grid grid-cols-12 gap-2 mb-2 font-bold">
        <div class="col-span-3">Field Name</div>
        <div class="col-span-2">Expected Date</div>
        <div class="col-span-2">Norm Date</div>
        <div class="col-span-2">Actual Date</div>
        <div class="col-span-3">Notes</div>
      </div>

      <!-- Dynamic Date Fields -->
      <div
        v-for="(field, index) in dateFields"
        :key="index"
        class="grid grid-cols-12 gap-2 items-center mb-3"
      >
        <div class="col-span-3">{{ field.label }}</div>

        <div class="col-span-2">
          <InputText
            type="date"
            v-model="localContract[`${field.name}_expected_date`]"
            class="w-full"
          />
        </div>

        <div class="col-span-2">
          <InputText
            type="date"
            v-model="localContract[`${field.name}_norm_date`]"
            class="w-full"
          />
        </div>

        <div class="col-span-2">
          <InputText
            type="date"
            v-model="localContract[`${field.name}_actual_date`]"
            class="w-full"
          />
        </div>

        <div class="col-span-3">
          <InputText
            v-model="localContract[`${field.name}_notes`]"
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
    modelValue: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      dateFields: [
        { name: 'reqmt_recd_date', label: 'Reqmt Recd Date' },
        { name: 'case_initiation_date', label: 'Case Initiation Date' },
        { name: 'aa_date', label: 'AA Date' },
        { name: 'sanction_date', label: 'Sanction Date' },
        { name: 'indent_date', label: 'Indent Date' }
      ],
      localContract: {}
    };
  },
  watch: {
    modelValue: {
      handler(newVal) {
        const normalizeDate = (dateStr) => {
          if (!dateStr) return '';
          return dateStr.split('T')[0].split(' ')[0];
        };

        let clone = { ...newVal };

        this.dateFields.forEach(field => {
          ['expected_date', 'norm_date', 'actual_date', 'notes'].forEach(suffix => {
            const key = `${field.name}_${suffix}`;
            clone[key] = normalizeDate(clone[key] || '');
          });
        });

        this.localContract = clone;
      },
      immediate: true,
      deep: true
    },
    localContract: {
      handler(updated) {
        this.$emit('update:modelValue', updated);
      },
      deep: true
    }
  }
};
</script>
