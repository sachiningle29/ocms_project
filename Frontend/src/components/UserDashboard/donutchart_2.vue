<template>
  <div class="chart-wrapper bg-white p-4 rounded-xl shadow-sm">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-lg font-semibold text-gray-800"></h2>
    </div>

    <apexchart
      v-if="chartReady && hasValidData"
      :key="chartKey"
      :type="activeFilters.chartType"
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
      default: null
    }
  },
  data() {
    return {
      labels: [],
      values: [],
      chartReady: false,
      hasValidData: false
    }
  },
  computed: {
    activeFilters() {
      return this.filters || {
        status: 'all',
        chartType: 'donut'
      }
    },
    chartKey() {
      return `${this.activeFilters.chartType}-${this.activeFilters.status}-${this.filters ? 'filtered' : 'unfiltered'}`
    },
    computedSeries() {
      if (!this.hasValidData) return []
      if (['pie', 'donut'].includes(this.activeFilters.chartType)) {
        return this.values
      }
      return [{
        name: 'Sections',
        data: this.values
      }]
    },
    computedOptions() {
      const statusLabel = this.statusOptions.find(s => s.value === this.activeFilters.status)?.label || 'All Contracts'
      const options = {
        chart: { id: 'sections-chart' },
        title: {
          text: `Sections - ${statusLabel} (${this.activeFilters.chartType.toUpperCase()})`,
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

      if (['bar', 'line', 'area'].includes(this.activeFilters.chartType)) {
        options.xaxis = { categories: this.labels }
        options.yaxis = { labels: { formatter: val => `${val}` } }
      }

      if (['pie', 'donut'].includes(this.activeFilters.chartType)) {
        options.labels = this.labels
      }

      return options
    },
    statusOptions() {
      return [
        { value: 'all', label: 'All Contracts' },
        { value: 'ongoing', label: 'Ongoing' },
        { value: 'completed', label: 'Completed' }
      ]
    }
  },
  watch: {
    filters: {
      deep: true,
      immediate: true,
      handler(newVal, oldVal) {
        // Only refetch if filters actually changed
        if (JSON.stringify(newVal) !== JSON.stringify(oldVal)) {
          this.fetchSectionData()
        }
      }
    }
  },
  methods: {
    async fetchSectionData() {
      try {
        this.chartReady = false
        this.hasValidData = false
        
        let url = '/userDasboard/getSection'
        if (this.activeFilters.status !== 'all') {
          url += `/${this.activeFilters.status}`
        }

        const response = await axios.get(url)
        const data = response.data
        
        if (data && data.length > 0) {
          this.labels = data.map(item => item.name)
          this.values = data.map(item => item.value)
          this.hasValidData = true
        } else {
          this.labels = []
          this.values = []
          this.hasValidData = false
        }
        
        this.chartReady = true
      } catch (error) {
        console.error('Error loading section data:', error)
        this.labels = []
        this.values = []
        this.hasValidData = false
        this.chartReady = true
      }
    }
  },
  mounted() {
    this.fetchSectionData()
  }
}
</script>