<template>
  <div class="chart-wrapper">
    <apexchart
      type="bar"
      height="200"
      :options="chartOptions"
      :series="series"
    />
  </div>
</template>

<script>
import ApexCharts from 'vue3-apexcharts'

export default {
  name: 'HorizontalStackedBarChart',
  components: {
    apexchart: ApexCharts
  },
  data() {
    return {
      series: [
        {
          name: 'Product A',
          data: [44 ]
        },
        {
          name: 'Product B',
          data: [13 ]
        },
        {
          name: 'Product C',
          data: [11]
        }
      ],
      chartOptions: {
        chart: {
          type: 'bar',
          stacked: true,
          toolbar: {
            show: true
          }
        },
        plotOptions: {
          bar: {
            horizontal: true,
            borderRadius: 1,
            barHeight: '70%' // optional, adjust bar thickness
          }
        },
        title: {
          text: 'Horizontal Stacked Bar Chart',
          align: 'center'
        },
        xaxis: {
          categories: ['Jan'],
          title: {
            text: 'Sales ($)'
          }
        },
        yaxis: {
          title: {
            text: 'Months'
          }
        },
        legend: {
          position: 'top'
        },
        fill: {
          opacity: 1
        }
      }
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
}
</style>
