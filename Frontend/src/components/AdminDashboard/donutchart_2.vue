<template>
  <div class="sections-donut-chart">
    <div v-if="loading" class="loading">
      <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500 mx-auto"></div>
      <span class="ml-2 text-gray-600">Loading data...</span>
    </div>
    <div v-else-if="seriesDurations.length === 0" class="no-data">
      No duration data available for the selected section.
    </div>
    <div v-else class="charts-container">
      <div class="chart">
        <h3 class="text-lg font-semibold mb-2" aria-live="polite">Average Durations for {{ selectedSectionName }}</h3>
        <apexchart
          type="donut"
          :options="durationsChartOptions"
          :series="seriesDurations"
          height="200"
        ></apexchart>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue';
import ApexCharts from 'vue3-apexcharts';
import axiosClient from '@/axios';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
  filters: {
    type: Object,
    default: () => ({
      status: 'all',
      selectedDeliverable: '',
      selectedSection: 'all_sections_avg',
    }),
  },
});

const toast = useToast();
const loading = ref(false);
const dynamicSections = ref([]);
const seriesDurations = ref([]);
const durationsChartOptions = ref({
  chart: {
    type: 'donut',
  },
  labels: [],
  colors: ['#FF4560', '#008FFB', '#00E396', '#FEB019', '#775DD0', '#546E7A', '#FF7300', '#00C4B4', '#A5978B', '#D81B60', '#5E35B1'],
  responsive: [{
    breakpoint: 480,
    options: {
      chart: {
        width: 300,
      },
      legend: {
        position: 'bottom',
      },
    },
  }],
  dataLabels: {
    enabled: true,
    formatter: (val, opts) => `${opts.w.globals.series[opts.seriesIndex]} days`,
  },
  tooltip: {
    y: {
      formatter: val => `${val} days`,
    },
  },
  legend: {
    position: 'right',
    offsetY: 0,
    height: 230,
  },
  title: {
    text: 'Average Durations by Stage',
    align: 'center',
  },
});

// Compute selected section name for display
const selectedSectionName = computed(() => {
  if (props.filters.selectedSection === 'all_sections_avg') {
    return 'Average of All Sections';
  }
  const section = dynamicSections.value.find(s => s.section_id === props.filters.selectedSection);
  return section ? section.section_name : 'Unknown Section';
});

async function fetchSections() {
  loading.value = true;
  try {
    const payload = {
      section: props.filters.selectedSection === 'all_sections_avg' ? null : props.filters.selectedSection,
      subSection: null,
      case: null,
      status: props.filters.status || 'all',
      deliverables: props.filters.selectedDeliverable || null,
    };
    const response = await axiosClient.post('/adminDasboard/getFilteredCases', payload);
    const sectionsData = response.data.sections_avg_durations || [];

    // Update dynamic sections
    dynamicSections.value = sectionsData
      .filter(item => item.section_name !== 'Average of All Sections' && item.section_id)
      .map(item => ({
        section_id: item.section_id,
        section_name: item.section_name,
      }));

    // Find the selected section's data
    let selectedData = {};
    if (props.filters.selectedSection === 'all_sections_avg') {
      selectedData = sectionsData.find(item => item.section_name === 'Average of All Sections') || { avg_durations: {} };
      if (!selectedData.avg_durations || Object.keys(selectedData.avg_durations).length === 0) {
        toast.warn('No average section data available; showing default view');
      }
    } else {
      selectedData = sectionsData.find(item => item.section_id === props.filters.selectedSection) || { avg_durations: {} };
      if (!selectedData.avg_durations || Object.keys(selectedData.avg_durations).length === 0) {
        toast.warn(`No duration data for section: ${selectedSectionName.value}`);
      }
    }

    const stageLabels = [
      'Reqmt → Case Initiation',
      'Case Initiation → AA',
      'AA → Sanction',
      'Sanction → Indent',
      'Indent → NIT',
      'NIT → TBO',
      'TBO → PBO',
      'PBO → NOA/PO',
      'NOA/PO → Delivery',
      'Delivery → Contract Start',
      'Contract Start → Contract End',
    ];

    seriesDurations.value = stageLabels
      .map(label => selectedData.avg_durations[label] || 0)
      .filter(val => val !== 0);
    durationsChartOptions.value = {
      ...durationsChartOptions.value,
      labels: stageLabels.filter((_, index) => (selectedData.avg_durations[stageLabels[index]] || 0) !== 0),
    };
  } catch (error) {
    console.error('Error fetching sections data:', error);
    dynamicSections.value = [];
    seriesDurations.value = [];
    durationsChartOptions.value = { ...durationsChartOptions.value, labels: [] };
    toast.error('Failed to load chart data');
  } finally {
    loading.value = false;
  }
}
// dada dada

// Watch filters for changes
watch(() => [props.filters.status, props.filters.selectedDeliverable, props.filters.selectedSection], () => {
  fetchSections();
}, { deep: true });

// Initial fetch
fetchSections();
</script>

<style scoped>
.sections-donut-chart {
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
}

.charts-container {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  justify-content: center;
}

.chart {
  flex: 1;
  min-width: 300px;
  max-width: 500px;
}

.no-data,
.loading {
  text-align: center;
  color: #888;
  font-size: 1.2rem;
  margin: 20px 0;
  display: flex;
  justify-content: center;
  align-items: center;
}
</style>