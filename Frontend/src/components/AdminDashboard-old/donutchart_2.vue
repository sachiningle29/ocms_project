<template>
  <div class="chart-wrapper bg-white p-4 rounded-xl shadow-sm">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-lg font-semibold text-gray-800">Sections Chart</h2>
    </div>

    <apexchart
      v-if="chartReady"
      :key="`${filters.chartType}-${filters.status}`"
      :type="filters.chartType"
      :options="computedOptions"
      :series="computedSeries"
      height="200"
    />
  </div>
</template>

<script>
import ApexCharts from 'vue3-apexcharts'
import axios from '@/axios'

export default {
  name: 'SectionChart',
  components: {
    apexchart: ApexCharts
  },
  props: {
    filters: {
      type: Object,
      required: true,
      default: () => ({
        status: 'all',
        chartType: 'donut'
      })
    }
  },
  data() {
    return {
      labels: [],
      values: [],
      chartReady: false,
      statusOptions: [
        { value: 'all', label: 'All Contracts' },
        { value: 'ongoing', label: 'Ongoing' },
        { value: 'completed', label: 'Completed' }
      ]
    }
  },
  computed: {
    computedSeries() {
      if (['pie', 'donut'].includes(this.filters.chartType)) {
        return this.values
      }
      return [{
        name: 'Sections',
        data: this.values
      }]
    },
    computedOptions() {
      const statusLabel = this.statusOptions.find(s => s.value === this.filters.status)?.label || ''
      const options = {
        chart: { id: 'sections-chart' },
        title: {
          text: `Sections - ${statusLabel} (${this.filters.chartType.toUpperCase()})`,
          align: 'center'
        },
        legend: {
          show: true,
          position: 'bottom',
          formatter: (seriesName, opts) => {
            return `${this.labels[opts.seriesIndex]}: ${this.values[opts.seriesIndex]}`
          }
        },
        tooltip: { y: { formatter: val => `${val}` } }
      }

      if (['bar', 'line', 'area'].includes(this.filters.chartType)) {
        options.xaxis = { categories: this.labels }
        options.yaxis = { labels: { formatter: val => `${val}` } }
      }

      if (['pie', 'donut'].includes(this.filters.chartType)) {
        options.labels = this.labels
      }

      return options
    }
  },
  watch: {
    filters: {
      deep: true,
      immediate: true,
      handler() {
        this.fetchSectionData()
      }
    }
  },
  methods: {
    async fetchSectionData() {
      try {
        this.chartReady = false
        
        // Use route parameter format to match your backend
        let url = '/adminDasboard/getSection'
        if (this.filters.status !== 'all') {
          url += `/${this.filters.status}`
        }

        const response = await axios.get(url)
        const data = response.data
        this.labels = data.map(item => item.name)
        this.values = data.map(item => item.value)
        this.chartReady = true
      } catch (error) {
        console.error('Error loading section data:', error)
        this.chartReady = true
      }
    }
  }
}
</script>

<style scoped>
.chart-wrapper {
  max-width: 800px;
  margin: auto;
}
</style>