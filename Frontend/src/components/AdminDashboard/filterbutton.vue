<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  selectedDeliverable: {
    type: String,
    default: '',
  },
  selectedSection: {
    type: String,
    default: '',
  },
  uniqueDeliverables: {
    type: Array,
    required: true,
  },
  sections: {
    type: Array,
    required: true,
  },
  statusFilter: {
    type: String,
    default: 'all',
  },
});

const emit = defineEmits(['toggle', 'filter-change']);

const isCollapsed = ref(false);
const localSelectedDeliverable = ref(props.selectedDeliverable);
const localSelectedSection = ref(props.selectedSection);
const localStatusFilter = ref(props.statusFilter);

// Sync props with local state
watch(
  () => props.selectedDeliverable,
  (newVal) => {
    localSelectedDeliverable.value = newVal;
  }
);
watch(
  () => props.selectedSection,
  (newVal) => {
    localSelectedSection.value = newVal;
  }
);
watch(
  () => props.statusFilter,
  (newVal) => {
    localStatusFilter.value = newVal;
  }
);

function toggleCollapse() {
  isCollapsed.value = !isCollapsed.value;
  emit('toggle', isCollapsed.value);
}

function emitFilterChange() {
  emit('filter-change', {
    status: localStatusFilter.value,
    selectedDeliverable: localSelectedDeliverable.value,
    selectedSection: localSelectedSection.value,
  });
}

function resetFilters() {
  localSelectedDeliverable.value = '';
  localSelectedSection.value = '';
  localStatusFilter.value = 'all';
  emit('filter-change', {
    status: localStatusFilter.value,
    selectedDeliverable: localSelectedDeliverable.value,
    selectedSection: localSelectedSection.value,
  });
}
</script>

<template>
  <!-- Collapsible Filter Sidebar -->
  <div class="relative">
    

    <!-- Filter Content -->
    <div
      class="bg-white rounded-xl shadow-sm h-full flex flex-col transition-all duration-300 ease-in-out overflow-hidden"
      :class="{ 'w-72 p-6': !isCollapsed, 'w-0': isCollapsed }"
    >
      <!-- Expanded State -->
      <div v-if="!isCollapsed" class="space-y-4 filter-scroll">
        <h2 class="text-lg font-semibold text-gray-800">Filters</h2>

       

        <!-- Deliverables Dropdown -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Deliverables</label>
          <select
            v-model="localSelectedDeliverable"
            @change="emitFilterChange"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="" disabled hidden>Select Deliverables</option>
            <option value="all_deliverables_avg">Average of All Deliverables</option>
            <option v-for="item in props.uniqueDeliverables" :key="item" :value="item">{{ item }}</option>
          </select>
        </div>

        <!-- Sections Dropdown -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Sections</label>
          <select
            v-model="localSelectedSection"
            @change="emitFilterChange"
            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="" disabled hidden>Select Sections</option>
            <option value="all_sections_avg">Average of All Sections</option>
            <option v-for="section in props.sections" :key="section.id" :value="section.id">{{ section.name}}</option>
          </select>
        </div>

        <!-- Reset Filters Button -->
        <button
          @click="resetFilters"
          class="w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium rounded-md transition-colors duration-200 flex items-center justify-center gap-2"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5"
            viewBox="0 0 20 20"
            fill="currentColor"
          >
            <path
              fill-rule="evenodd"
              d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
              clip-rule="evenodd"
            />
          </svg>
          Reset Filters
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.filter-scroll::-webkit-scrollbar {
  width: 6px;
}
.filter-scroll::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}
.filter-scroll::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 10px;
}
.filter-scroll::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>