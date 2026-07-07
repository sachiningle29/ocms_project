<template>
  <div class="flex flex-wrap align-items-center gap-3 p-2 surface-100 border-round">
    <!-- RID Filter -->
    <div style="min-width: 200px">
      <span class="p-float-label">
        <InputText placeholder="Case ID" v-model="filters.rid.value" class="w-full" id="ridFilter" />
      </span>
    </div>

    <!-- Title Filter -->
    <div style="min-width: 200px">
      <span class="p-float-label">
        <InputText placeholder="Title" v-model="filters.title.value" class="w-full" id="titleFilter" />
      </span>
    </div>

    <!-- Deliverables Filter -->
    <div style="min-width: 200px">
      <Dropdown v-model="filters.deliverables.value" :options="deliverablesOptions" optionLabel="label"
        optionValue="value" placeholder="Deliverables" class="w-full" :showClear="true" id="deliverablesFilter" />
    </div>

    <!-- Tendering Section Filter -->
    <div style="min-width: 200px">
      <Dropdown v-model="filters.tendering_section.value" :options="tenderingSectionOptions" optionLabel="label"
        optionValue="value" placeholder="Tendering Section" class="w-full" :showClear="true" id="tenderingFilter" />
    </div>

    <!-- Indenting Section Filter -->
    <div v-if="isAdmin" style="min-width: 200px">
      <Dropdown 
        v-model="filters.indenting_section_name.value" 
        :options="indentingSectionOptions" 
        optionLabel="label"
        optionValue="value" 
        placeholder="Indenting Section" 
        class="w-full" 
        :showClear="true" 
        id="indentingFilter" />
    </div>

    <div class="flex align-items-center gap-3">
      <span class="p-input-icon-left"
        style="position: relative; display: inline-flex; align-items: center; min-width: 250px;">
        <i class="pi pi-search"
          style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); pointer-events: none; font-size: 1rem; color: #6c757d;" />
        <InputText v-model="filters.global.value" placeholder="Global Search..."
          class="w-full" style="padding-left: 2.5rem;" />
      </span>
      <Button label="Clear" icon="pi pi-filter-slash" severity="warning" @click="$emit('clear-filters')"
        class="p-button-text" />
    </div>
  </div>
</template>

<script>
export default {
  name: 'ContractFilters',
  props: {
    filters: {
      type: Object,
      required: true
    },
    deliverablesOptions: {
      type: Array,
      required: true
    },
    tenderingSectionOptions: {
      type: Array,
      required: true
    },
    indentingSectionOptions: {
      type: Array,
      required: true,
      default: () => []
    },
    isAdmin: {
      type: Boolean,
      default: false
    }
  },
  emits: ['clear-filters']
}
</script>