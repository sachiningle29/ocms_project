<template>
  <div class="flex flex-wrap align-items-center gap-3 p-2 surface-100 border-round">
    <!-- RID Filter -->
    <div style="min-width: 200px">
      <span class="p-float-label">
        <InputText placeholder="RID" v-model="filters.rid.value" class="w-full"
          @input="$emit('filter', $event.target.value, 'rid', 'contains')" id="ridFilter" />
      </span>
    </div>

    <!-- Title Filter -->
    <div style="min-width: 200px">
      <span class="p-float-label">
        <InputText placeholder="Title" v-model="filters.title.value" class="w-full"
          @input="$emit('filter', $event.target.value, 'title', 'contains')" id="titleFilter" />
      </span>
    </div>

    <!-- Deliverables Filter -->
    <div style="min-width: 200px">
      <Dropdown v-model="filters.deliverables.value" :options="deliverablesOptions" optionLabel="label"
        optionValue="value" placeholder="Deliverables" class="w-full"
        @change="$emit('filter', $event.value, 'deliverables', 'equals')" :showClear="true" id="deliverablesFilter" />
    </div>

    <!-- Tendering Section Filter -->
    <div style="min-width: 200px">
      <Dropdown v-model="filters.tendering_section.value" :options="tenderingSectionOptions" optionLabel="label"
        optionValue="value" placeholder="Tendering Section" class="w-full"
        @change="$emit('filter', $event.value, 'tendering_section', 'equals')" :showClear="true" id="tenderingFilter" />
    </div>

    <div class="flex align-items-center gap-3">
      <span class="p-input-icon-left"
        style="position: relative; display: inline-flex; align-items: center; min-width: 250px;">
        <i class="pi pi-search"
          style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); pointer-events: none; font-size: 1rem; color: #6c757d;" />
        <InputText v-model="filters.global.value" placeholder="Global Search..."
          @input="$emit('filter', $event.target.value, 'global', 'contains')" class="w-full"
          style="padding-left: 2.5rem;" />
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
    }
  },
  emits: ['filter', 'clear-filters']
}
</script>