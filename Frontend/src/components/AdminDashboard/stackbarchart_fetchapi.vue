<template>
    <div class="bg-white rounded-lg shadow-md p-6">
        <div v-if="loading" class="flex justify-center items-center h-64">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
        </div>
        <div v-else>
            <div class="flex flex-wrap items-end gap-4 mb-6">
                <div class="flex-1 min-w-[200px]">
                    <select v-model="selectedDeliverable" @change="handleDeliverableChange" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="" disabled hidden>Select Deliverables</option>
                        <option value="all_deliverables_avg">Average of All Deliverables</option>
                        <option v-for="item in uniqueDeliverables" :value="item">{{ item }}</option>
                    </select>
                </div>

                <div class="flex-1 min-w-[200px]">
                    <select v-model="selectedSection" @change="handleSectionChange" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="" disabled hidden>Select Sections</option>
                        <option value="all_sections_avg">Average of All Sections</option>
                        <option v-for="section in sections" :value="section.id">{{ section.name }}</option>
                    </select>
                </div>

                <!-- <div class="flex-1 min-w-[200px]">
                    <select v-model="selectedSubSection" @change="handleSubSectionChange" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="" disabled hidden>Select Sub-sections</option>
                        <option value="all_sub_sections_avg">Average of All Sub-sections</option>
                        <option v-for="subSection in filteredSubSections" :value="subSection.id">{{ subSection.name }}</option>
                    </select>
                </div> -->

                

                <div class="flex-1 min-w-[200px]" v-if="viewType === 'individual'">
                    <select v-model="selectedCase" @change="handleCaseChange" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <!-- Display-only option -->
                        <option value="" disabled hidden>Select Cases</option>

                        <!-- Actual selectable options -->
                        <option v-for="caseItem in filteredCases" :key="caseItem.id" :value="caseItem.id">
                            {{ caseItem.title }}
                        </option>
                    </select>
                </div>

                <button @click="resetFilters" class="h-[35px] px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium rounded-md transition-colors duration-200 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            fill-rule="evenodd"
                            d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    Reset Filters
                </button>
            </div>

            <div class="bg-white p-4 rounded-lg border border-gray-200">
                <apexchart type="bar" height="300" :options="chartOptions" :series="series"></apexchart>
            </div>
        </div>
    </div>
</template>

<script>
import axiosClient from '@/axios';
import ApexCharts from 'vue3-apexcharts';

export default {
    name: 'CaseDurationsChart',
    components: {
        apexchart: ApexCharts
    },
    data() {
        return {
            loading: true,
            rawData: null,
            viewType: 'individual',
            selectedDeliverable: '',
            selectedSection: '',
            selectedSubSection: '',
            selectedCase: '',
            sections: [],
            subSections: [],
            uniqueDeliverables: [],
            cases: [],
            chartOptions: {
                chart: {
                    type: 'bar',
                    height: 300,
                    stacked: true,
                    toolbar: {
                        show: true
                    },
                    zoom: {
                        enabled: true
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        barHeight: '80%'
                    }
                },
                dataLabels: {
                    enabled: true,
                    formatter: (val, opts) => {
                        const seriesName = opts.w.globals.seriesNames[opts.seriesIndex];
                        return `${seriesName}: ${val} days`;
                    },
                    style: { colors: ['#fff'], fontSize: '12px' }
                },
                stroke: {
                    width: 1,
                    colors: ['#fff']
                },
                title: {
                    text: 'Case Stage Durations (in days)',
                    align: 'center'
                },
                xaxis: {
                    title: {
                        text: 'Duration (days)'
                    },
                    min: 0
                },
                yaxis: {
                    title: {
                        text: 'Cases/Groupings'
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + ' days';
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'center'
                },
                colors: ['#008FFB', '#00E396', '#FEB019', '#FF4560', '#775DD0', '#546E7A', '#26a69a', '#D10CE8', '#FFA07A', '#7FFFD4']
            },
            series: []
        };
    },
    computed: {
        filteredSections() {
            if (!this.selectedDeliverable || this.selectedDeliverable === 'all_deliverables_avg') {
                return this.sections;
            }
            
            // Get all cases that belong to the selected deliverable
            const deliverableCases = this.cases.filter(c => c.deliverables === this.selectedDeliverable);
            
            // Get unique section IDs from these cases
            const sectionIds = [...new Set(deliverableCases.map(c => c.section_id))];
            
            // Return sections that match these IDs
            return this.sections.filter(section => sectionIds.includes(section.id));
        },
        
        filteredSubSections() {
            if (!this.selectedSection || this.selectedSection === 'all_sections_avg') {
                // If no section is selected, only show sub-sections that belong to cases matching the deliverable filter
                if (this.selectedDeliverable && this.selectedDeliverable !== 'all_deliverables_avg') {
                    const deliverableCases = this.cases.filter(c => c.deliverables === this.selectedDeliverable);
                    const subSectionIds = [...new Set(deliverableCases.map(c => c.sub_section_id))];
                    return this.subSections.filter(sub => subSectionIds.includes(sub.id));
                }
                return this.subSections;
            }
            
            // Filter sub-sections by selected section and optionally by deliverable
            let filtered = this.subSections.filter(sub => sub.section_id == this.selectedSection);
            
            if (this.selectedDeliverable && this.selectedDeliverable !== 'all_deliverables_avg') {
                const deliverableCases = this.cases.filter(c => 
                    c.deliverables === this.selectedDeliverable && 
                    c.section_id == this.selectedSection
                );
                const subSectionIds = [...new Set(deliverableCases.map(c => c.sub_section_id))];
                filtered = filtered.filter(sub => subSectionIds.includes(sub.id));
            }
            
            return filtered;
        },
        
        filteredCases() {
            let filtered = this.cases;

            if (this.selectedDeliverable && this.selectedDeliverable !== 'all_deliverables_avg') {
                filtered = filtered.filter(c => c.deliverables === this.selectedDeliverable);
            }

            if (this.selectedSection && this.selectedSection !== 'all_sections_avg') {
                filtered = filtered.filter(c => c.section_id == this.selectedSection);
            }

            if (this.selectedSubSection && this.selectedSubSection !== 'all_sub_sections_avg') {
                filtered = filtered.filter(c => c.sub_section_id == this.selectedSubSection);
            }

            return filtered;
        }
    },
    async mounted() {
        await this.fetchData();
    },
    methods: {
        async fetchData() {
            this.loading = true;
            try {
                const response = await axiosClient.post('/adminDasboard/getFilteredCasesFetchApi', {});
                this.rawData = response.data;
                this.processData();
                this.updateChart();
            } catch (error) {
                console.error('Error fetching data:', error);
            } finally {
                this.loading = false;
            }
        },

        processData() {
            // Process sections
            this.sections = this.rawData.sections_avg_durations
                .filter((item) => item.section_id)
                .map((item) => ({
                    id: item.section_id,
                    name: item.section_name
                }));

            // Process sub-sections
            this.subSections = this.rawData.sub_sections_avg_durations
                .filter((item) => item.sub_section_id)
                .map((item) => ({
                    id: item.sub_section_id,
                    name: item.sub_section_name,
                    section_id: item.section_id
                }));

            // Process deliverables
            const deliverablesSet = new Set();
            this.rawData.deliverables_avg_durations.forEach((item) => {
                if (item.deliverables && item.deliverables !== 'Average of All Deliverables') {
                    deliverablesSet.add(item.deliverables);
                }
            });
            this.uniqueDeliverables = Array.from(deliverablesSet);

            // Process cases - include both regular cases and average case
            this.cases = this.rawData.cases_durations
                .filter((c) => c.case_id || c.title === 'Average of All Cases')
                .map((c) => ({
                    id: c.case_id || 'all_cases_avg',
                    title: c.title,
                    deliverables: c.deliverables,
                    section_id: c.section_id,
                    sub_section_id: c.sub_section_id,
                    isAverage: c.title === 'Average of All Cases'
                }));
        },

        handleViewTypeChange() {
            this.resetFilters();
        },

        handleDeliverableChange() {
            this.selectedSection = '';
            this.selectedSubSection = '';
            this.selectedCase = '';
            this.updateChart();
        },

        handleSectionChange() {
            this.selectedSubSection = '';
            this.selectedCase = '';
            this.updateChart();
        },

        handleSubSectionChange() {
            this.selectedCase = '';
            this.updateChart();
        },

        handleCaseChange() {
            this.updateChart();
        },

        resetFilters() {
            this.selectedDeliverable = '';
            this.selectedSection = '';
            this.selectedSubSection = '';
            this.selectedCase = '';
            this.updateChart();
        },

        updateChart() {
            let dataToUse = [];
            let categories = [];
            let isAverageView =
                this.viewType === 'average' || this.selectedDeliverable === 'all_deliverables_avg' || this.selectedSection === 'all_sections_avg' || this.selectedSubSection === 'all_sub_sections_avg' || this.selectedCase === 'all_cases_avg';

            // Handle average views
            if (isAverageView) {
                if (this.selectedCase === 'all_cases_avg') {
                    dataToUse = this.rawData.cases_durations.filter((c) => c.title === 'Average of All Cases');
                } else if (this.selectedDeliverable === 'all_deliverables_avg') {
                    dataToUse = this.rawData.deliverables_avg_durations.filter((d) => d.deliverables === 'Average of All Deliverables');
                } else if (this.selectedSection === 'all_sections_avg') {
                    dataToUse = this.rawData.sections_avg_durations.filter((s) => s.section_name === 'Average of All Sections');
                } else if (this.selectedSubSection === 'all_sub_sections_avg') {
                    dataToUse = this.rawData.sub_sections_avg_durations.filter((s) => s.sub_section_name === 'Average of All Sub-sections');
                } else if (this.selectedDeliverable) {
                    dataToUse = this.rawData.deliverables_avg_durations.filter((d) => d.deliverables === this.selectedDeliverable);
                } else if (this.selectedSection) {
                    dataToUse = this.rawData.sections_avg_durations.filter((s) => s.section_id == this.selectedSection);
                } else if (this.selectedSubSection) {
                    dataToUse = this.rawData.sub_sections_avg_durations.filter((s) => s.sub_section_id == this.selectedSubSection);
                } else {
                    // Default average view - show all averages
                    dataToUse = [
                        ...this.rawData.deliverables_avg_durations.filter((d) => d.deliverables === 'Average of All Deliverables'),
                        ...this.rawData.sections_avg_durations.filter((s) => s.section_name === 'Average of All Sections'),
                        ...this.rawData.sub_sections_avg_durations.filter((s) => s.sub_section_name === 'Average of All Sub-sections'),
                        ...this.rawData.cases_durations.filter((c) => c.title === 'Average of All Cases')
                    ];
                }
            }
            // Handle individual cases view
            else {
                if (this.selectedCase) {
                    dataToUse = this.rawData.cases_durations.filter((c) => (c.case_id && c.case_id === this.selectedCase) || (this.selectedCase === 'all_cases_avg' && c.title === 'Average of All Cases'));
                } else {
                    dataToUse = this.filteredCases.map((caseItem) => this.rawData.cases_durations.find((c) => c.case_id === caseItem.id)).filter(Boolean);
                }
            }

            // Get categories from the first item with data
            if (dataToUse.length > 0) {
                const firstItemWithData = dataToUse.find((item) => item.durations || item.avg_durations);
                if (firstItemWithData) {
                    categories = Object.keys(firstItemWithData.durations || firstItemWithData.avg_durations || {});
                }
            }

            // Prepare series data for stacked chart
            const series = categories.map((stage) => ({
                name: stage,
                data: []
            }));

            // Populate series data
            dataToUse.forEach((item) => {
                categories.forEach((stage, index) => {
                    const value = isAverageView ? (item.avg_durations?.[stage] ?? item.durations?.[stage]) : item.durations?.[stage];
                    series[index].data.push(value || 0);
                });
            });

            // Update chart options
            this.chartOptions = {
                ...this.chartOptions,
                xaxis: {
                    ...this.chartOptions.xaxis,
                    categories: dataToUse.map((item) => this.getItemLabel(item, isAverageView))
                }
            };
            this.series = series;
        },

        getItemLabel(item, isAverage) {
            if (isAverage) {
                return item.deliverables || item.section_name || item.sub_section_name || item.title;
            }
            return `${item.title}(${item.sub_section_name || 'No Sub-section'})`;
        }
    }
};
</script>

<style scoped>
.case-durations-chart {
    padding: 8px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.chart-container {
    margin-top: 8px;
}

.loading {
    padding: 20px;
    text-align: center;
    color: #666;
}

.filters {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
    flex-wrap: wrap;
    align-items: flex-end;
}

.filter-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.filter-group label {
    font-weight: 500;
    white-space: nowrap;
}

.filter-group select {
    padding: 8px 12px;
    border-radius: 4px;
    border: 1px solid #ddd;
    min-width: 200px;
}

.reset-btn {
    padding: 8px 16px;
    background-color: #f5f5f5;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s;
    height: 36px;
}

.reset-btn:hover {
    background-color: #e0e0e0;
}

@media (max-width: 768px) {
    .filters {
        flex-direction: column;
        gap: 10px;
    }

    .filter-group {
        flex-direction: column;
        align-items: flex-start;
    }

    .filter-group select,
    .reset-btn {
        width: 100%;
    }
}
</style>
