<script setup>
import { ref } from 'vue'
import Donutchart_1 from '@/components/UserDashboard/donutchart_1.vue'
import Donutchart_2 from '@/components/UserDashboard/donutchart_2.vue'
import Filterbutton from '@/components/UserDashboard/filterbutton.vue'
import Stackbarchart from '@/components/UserDashboard/stackbarchart.vue'
import Titleheading from '@/components/UserDashboard/titleheading.vue'

// Define all available charts with their IDs and names
const availableCharts = ref([
  { id: 'stackedBar', name: 'Stacked Bar Chart' },
  { id: 'donutchart_1', name: 'Donut Chart 1' },
  { id: 'donutchart_2', name: 'Donut Chart 2' }
])

// Initialize filters with default values
const filters = ref({
  status: 'all',
  chartType: 'donut',
  applyTo: availableCharts.value.map(chart => chart.id) // Apply to all by default
})

const handleFilterChange = (newFilters) => {
  filters.value = newFilters
}

// Helper function to check if filters should apply to a specific chart
const shouldApplyFilters = (chartId) => {
  return filters.value.applyTo.includes(chartId)
}
</script>

<template>
  <!-- heading section -->
  <Titleheading />

  <div class="max-w-full mx-auto flex flex-col lg:flex-row gap-6">
    <!-- Main Content Area (80%) -->
    <div class="w-full lg:w-4/5 space-y-6">
      <!-- Stacked Bar Chart - Always visible -->
      <div class="bg-white p-3 rounded-xl shadow-sm">
        <Stackbarchart 
          :filters="shouldApplyFilters('stackedBar') ? filters : null" 
        />
      </div>

      <!-- Columns Section -->
      <div class="flex flex-wrap -mx-2">
        <!-- Column 1 -->
        <div class="w-full md:w-3/12 px-2">
          <div class="bg-white p-3 rounded-xl shadow-sm">
            <div class="bg-gray-300 p-4">Column 1 (3/12)</div>
          </div>
        </div>

        <!-- Column 2 -->
        <div class="w-full md:w-3/12 px-2">
          <div class="bg-white p-3 rounded-xl shadow-sm">
            <div class="bg-gray-300 p-4">Column 2 (3/12)</div>
          </div>
        </div>

        <!-- Column 3 -->
        <div class="w-full md:w-3/12 px-2">
          <div class="bg-white p-3 rounded-xl shadow-sm">
            <div class="bg-gray-300 p-4">Column 3 (3/12)</div>
          </div>
        </div>

        <!-- Column 4 -->
        <div class="w-full md:w-3/12 px-2">
          <div class="bg-white p-3 rounded-xl shadow-sm">
            <div class="bg-gray-300 p-4">Column 4 (3/12)</div>
          </div>
        </div>
      </div>

      <!-- Donut Charts - Always visible -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-3 rounded-xl shadow-sm">
          <Donutchart_1 :filters="shouldApplyFilters('donutchart_1') ? filters : null" />
        </div>
        <div class="bg-white p-3 rounded-xl shadow-sm">
          <Donutchart_2 :filters="shouldApplyFilters('donutchart_2') ? filters : null" />
        </div>
      </div>
    </div>
    
    <!-- Filter Sidebar (20%) -->
    <Filterbutton 
      @filter-change="handleFilterChange" 
      :available-charts="availableCharts"
      :initial-filters="filters"
      class="w-full lg:w-1/5"
    />
  </div>
</template>