<template>
  <div class="deliverables-donut-chart">
    <div v-if="loading" class="loading">
      <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500 mx-auto"></div>
      <span class="ml-2 text-gray-600">Loading data...</span>
    </div>
    <div v-else-if="seriesDurations.length === 0 && seriesNormDifferences.length === 0" class="no-data">
      No data available for the selected deliverable.
    </div>
    <div v-else class="charts-container">
      <div class="chart">
        <h3 class="text-lg font-semibold mb-2" aria-live="polite">Average Durations for {{ selectedDeliverableName }}</h3>
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
const seriesDurations = ref([]);
const seriesNormDifferences = ref([]);
const durationsChartOptions = ref({
  chart: {
    type: 'donut',
  },
  labels: [],
  colors: ['#FF4560', '#008FFB', '#00E396', '#FEB019', '#775DD0', '#546E7A', '#FF7300', '#00C4B4', '#A5978B', '#D81B60', '#5E35B1'],
  responsive: [{
    breakpoint: 480,
    options: {
      chart: { width: 300 },
      legend: { position: 'bottom' },
    },
  }],
  dataLabels: {
    enabled: true,
    formatter: (val, opts) => `${opts.w.globals.series[opts.seriesIndex]} days`,
  },
  tooltip: {
    y: { formatter: val => `${val} days` },
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
const normDifferencesChartOptions = ref({
  chart: {
    type: 'donut',
  },
  labels: [],
  colors: ['#FF4560', '#008FFB', '#00E396', '#FEB019', '#775DD0', '#546E7A', '#FF7300', '#00C4B4', '#A5978B', '#D81B60'],
  responsive: [{
    breakpoint: 480,
    options: {
      chart: { width: 300 },
      legend: { position: 'bottom' },
    },
  }],
  dataLabels: {
    enabled: true,
    formatter: (val, opts) => `${opts.w.globals.series[opts.seriesIndex]} days`,
  },
  tooltip: {
    y: { formatter: val => `${val} days` },
  },
  legend: {
    position: 'right',
    offsetY: 0,
    height: 230,
  },
  title: {
    text: 'Average Norm Differences by Stage',
    align: 'center',
  },
});

// Compute deliverable name for display
const selectedDeliverableName = computed(() => {
  return props.filters.selectedDeliverable === '' || props.filters.selectedDeliverable === 'all_deliverables_avg'
    ? 'Average of All Deliverables'
    : props.filters.selectedDeliverable;
});

async function fetchDeliverables() {
  loading.value = true;
  try {
    const payload = {
      section: props.filters.selectedSection === 'all_sections_avg' ? null : props.filters.selectedSection,
      subSection: null,
      case: null,
      status: props.filters.status || 'all',
      deliverables: props.filters.selectedDeliverable === '' || props.filters.selectedDeliverable === 'all_deliverables_avg'
        ? null
        : props.filters.selectedDeliverable,
    };
    const response = await axiosClient.post('/adminDasboard/getFilteredCases', payload);
    const deliverablesData = response.data.deliverables_avg_durations || [];

    // Find the selected deliverable's data
    const selectedData = (props.filters.selectedDeliverable === '' || props.filters.selectedDeliverable === 'all_deliverables_avg')
      ? deliverablesData.find(item => item.deliverables === 'Average of All Deliverables') || { avg_durations: {}, avg_norm_differences: {} }
      : deliverablesData.find(item => item.deliverables === props.filters.selectedDeliverable) || { avg_durations: {}, avg_norm_differences: {} };

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
    const normKeys = [
      'norm-1',
      'norm-2',
      'norm-3',
      'norm-4',
      null,
      'norm-5',
      'norm-6',
      'norm-7',
      'norm-8',
      'norm-9',
      'norm-10',
    ];

    seriesDurations.value = stageLabels
      .map(label => selectedData.avg_durations[label] || 0)
      .filter(val => val !== 0);
    durationsChartOptions.value = {
      ...durationsChartOptions.value,
      labels: stageLabels.filter((_, index) => (selectedData.avg_durations[stageLabels[index]] || 0) !== 0),
    };

    seriesNormDifferences.value = normKeys
      .map((key, index) => (key && selectedData.avg_norm_differences[key]) || 0)
      .filter(val => val !== 0);
    normDifferencesChartOptions.value = {
      ...normDifferencesChartOptions.value,
      labels: normKeys
        .map((key, index) => (key ? `Norm: ${stageLabels[index]}` : null))
        .filter((_, index) => (normKeys[index] && (selectedData.avg_norm_differences[normKeys[index]] || 0) !== 0)),
    };

    if (seriesDurations.value.length === 0 && seriesNormDifferences.value.length === 0) {
      toast.error('No data available for the selected deliverable');
    }
  } catch (error) {
    console.error('Error fetching deliverables data:', error);
    seriesDurations.value = [];
    seriesNormDifferences.value = [];
    durationsChartOptions.value = { ...durationsChartOptions.value, labels: [] };
    normDifferencesChartOptions.value = { ...normDifferencesChartOptions.value, labels: [] };
    toast.error('Failed to load chart data');
  } finally {
    loading.value = false;
  }
}

// Watch filters for changes
watch(() => [props.filters.status, props.filters.selectedDeliverable, props.filters.selectedSection], () => {
  fetchDeliverables();
}, { deep: true });

// Initial fetch
fetchDeliverables();
</script>

<style scoped>
.deliverables-donut-chart {
  max-width: 1200px;
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