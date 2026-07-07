<script setup>
import { ref } from 'vue';

const isCollapsed = ref(false);
const filters = ref({
  status: 'ongoing',
  chartType: 'donut',
  applyTo: [] // Array to store which charts to apply filters to
});

// List of available charts in the dashboard
const availableCharts = ref([
  { id: 'stackedBar', name: 'Stacked Bar Chart' },
  { id: 'donut1', name: 'Donut Chart 1' },
  { id: 'donut2', name: 'Donut Chart 2' }
  // Add more charts as needed
]);

const emit = defineEmits(['toggle', 'filter-change']);

function toggleCollapse() {
  isCollapsed.value = !isCollapsed.value;
  emit('toggle', isCollapsed.value);
}

function applyFilters() {
  emit('filter-change', {
    status: filters.value.status,
    chartType: filters.value.chartType,
    applyTo: filters.value.applyTo
  });
}

function toggleChartSelection(chartId) {
  const index = filters.value.applyTo.indexOf(chartId);
  if (index === -1) {
    filters.value.applyTo.push(chartId);
  } else {
    filters.value.applyTo.splice(index, 1);
  }
  applyFilters();
}
</script>

<template>
  <!-- Collapsible Filter Sidebar -->
  <div class="relative">
    <!-- Toggle Button - left side -->
    <button 
      @click="toggleCollapse" 
      class="absolute -left-6 top-6 z-10 bg-white p-2 rounded-l-lg shadow-md hover:bg-gray-100 transition-colors"
    >
      <svg 
        xmlns="http://www.w3.org/2000/svg" 
        class="h-5 w-5 text-gray-600 transition-transform duration-300" 
        fill="none" 
        viewBox="0 0 24 24" 
        stroke="currentColor" 
        :class="{ 'rotate-180': isCollapsed }"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
    </button>

    <!-- Filter Content -->
    <div
      class="bg-white rounded-xl shadow-sm h-full flex flex-col transition-all duration-300 ease-in-out overflow-hidden"
      :class="{
        'w-72 p-6': !isCollapsed,
        'w-0': isCollapsed
      }"
    >
      <!-- Expanded State -->
      <div v-if="!isCollapsed" class="space-y-4">
        <h2 class="text-lg font-semibold text-gray-800">Filters</h2>

       

        <!-- Case Status Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Case Category</label>
          <select
            v-model="filters.status"
            class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            @change="applyFilters"
          >
            <option value="all">All Contracts</option>
            <option value="ongoing">Ongoing</option>
            <option value="completed">Completed</option>
          </select>
        </div>

        <!-- Chart Type Filter -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Chart Type</label>
          <select
            v-model="filters.chartType"
            class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            @change="applyFilters"
          >
            <option value="donut">Donut</option>
            <option value="pie">Pie</option>
            <!-- <option value="bar">Bar</option>
            <option value="line">Line</option>
            <option value="area">Area</option> -->
          </select>
        </div>
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