<template>
  <div class="chart-wrapper">
    <!-- Filters Section -->
    <div class="filters">
      <div class="filter-group">
        <label>Deliverables:</label>
        <select v-model="selectedDeliverable" @change="updateChartData">
          <option v-for="(item, index) in deliverables" :key="'deliverable-'+index" :value="item">
            {{ item }}
          </option>
        </select>
      </div>
      
      <div class="filter-group">
        <label>Sections:</label>
        <select v-model="selectedSection" @change="updateChartData">
          <option v-for="(item, index) in sections" :key="'section-'+index" :value="item">
            {{ item }}
          </option>
        </select>
      </div>
    </div>

    <!-- Chart -->
    <apexchart
      type="bar"
      height="200"
      width="100%"
      :options="chartOptions"
      :series="dynamicSeries"
    />
  </div>
</template>

<script>
import ApexCharts from 'vue3-apexcharts'

export default {
  name: 'DynamicHorizontalStackedBarChart',
  components: {
    apexchart: ApexCharts
  },
  props: {
    chartTitle: {
      type: String,
      default: 'Dynamic Stacked Bar Chart'
    }
  },
  data() {
    return {
      // Filter options
      deliverables: ['Deliverables - 1', 'Deliverables - 2', 'Deliverables - 3'],
      sections: ['Sections - 1', 'Sections - 2', 'Sections - 3'],
      selectedDeliverable: 'Deliverables - 1',
      selectedSection: 'Sections - 1',
      
      // Chart data for different filter combinations
      chartData: {
        'Deliverables - 1_Sections - 1': [
          { name: 'Design', value: 1500 },
          { name: 'Development', value: 200 },
          { name: 'Testing', value: 111 }
        ],
        'Deliverables - 1_Sections - 2': [
          { name: 'Design', value: 1200 },
          { name: 'Development', value: 300 },
          { name: 'Testing', value: 150 }
        ],
        'Deliverables - 2_Sections - 1': [
          { name: 'Research', value: 800 },
          { name: 'Implementation', value: 400 },
          { name: 'Review', value: 250 }
        ],
        'Deliverables - 2_Sections - 2': [
          { name: 'Research', value: 600 },
          { name: 'Implementation', value: 500 },
          { name: 'Review', value: 350 }
        ],
        'Deliverables - 3_Sections - 1': [
          { name: 'Planning', value: 900 },
          { name: 'Execution', value: 700 },
          { name: 'Delivery', value: 400 }
        ],
        'Deliverables - 3_Sections - 3': [
          { name: 'Planning', value: 1100 },
          { name: 'Execution', value: 600 },
          { name: 'Delivery', value: 300 }
        ]
      },
      
      // Chart series data
      dynamicSeries: []
    }
  },
  computed: {
    chartOptions() {
      return {
        chart: {
          type: 'bar',
          stacked: true,
          toolbar: {
            show: true
          },
          animations: {
            enabled: true,
            easing: 'easeinout',
            speed: 800
          }
        },
        plotOptions: {
          bar: {
            horizontal: true,
            borderRadius: 4,
            barHeight: '70%',
            dataLabels: {
              position: 'center',
              hideOverflowingLabels: false
            }
          }
        },
        colors: ['#008FFB', '#00E396', '#FEB019', '#FF4560', '#775DD0'],
        dataLabels: {
          enabled: true,
          formatter: (val) => val,
          style: {
            colors: ['#fff'],
            fontSize: '12px'
          }
        },
        title: {
          text: `${this.selectedDeliverable} - ${this.selectedSection}`,
          align: 'center',
          style: {
            fontSize: '16px'
          }
        },
        xaxis: {
          categories: ['Total'],
          labels: {
            show: false
          }
        },
        yaxis: {
          show: false
        },
        legend: {
          position: 'top',
          markers: {
            radius: 12
          }
        },
        tooltip: {
          y: {
            formatter: (val) => val
          }
        },
        grid: {
          padding: {
            top: 10,
            right: 10,
            bottom: 0,
            left: 10
          }
        }
      }
    }
  },
  created() {
    this.updateChartData()
  },
  methods: {
    updateChartData() {
      const dataKey = `${this.selectedDeliverable}_${this.selectedSection}`
      this.dynamicSeries = (this.chartData[dataKey] || [
        { name: 'Default A', value: 100 },
        { name: 'Default B', value: 50 },
        { name: 'Default C', value: 25 }
      ]).map(item => ({
        name: item.name,
        data: [item.value]
      }))
    }
  }
}
</script>

<style scoped>
.chart-wrapper {
  max-width: 800px;
  margin: auto;
  padding: 20px;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.filters {
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
  padding: 15px;
  background: #f8f9fa;
  border-radius: 5px;
}

.filter-group {
  display: flex;
  align-items: center;
  gap: 10px;
}

.filter-group label {
  font-weight: bold;
  min-width: 80px;
}

.filter-group select {
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  min-width: 150px;
  background: #fff;
  cursor: pointer;
}

.filter-group select:focus {
  outline: none;
  border-color: #008FFB;
  box-shadow: 0 0 0 2px rgba(0, 143, 251, 0.2);
}
</style>