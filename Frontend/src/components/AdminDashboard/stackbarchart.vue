<template>
    <div class="bg-white rounded-lg shadow-md p-2 border border-gray-100">
        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center h-64">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
            <span class="ml-3 text-gray-600">Loading data...</span>
        </div>

        <!-- Content -->
        <div v-else class="space-y-6">
            <!-- Filters -->
            <div class="filters space-y-4">
                <div class="flex gap-4 flex-wrap">
                    <!-- Year Selector -->
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 mb-0">Select Year</label>
                        <select
                            v-model="selectedYear"
                            @change="
                                currentPage = 1;
                                fetchData();
                            "
                            class="text-sm border border-gray-300 rounded px-2 py-1 w-full"
                        >
                            <option value="">All Years</option>
                            <option v-for="year in availableYears" :key="year" :value="year">{{ year }}</option>
                        </select>
                    </div>

                    <!-- Case Selector -->
                    <div class="flex-1 min-w-[900px]">
                        <label class="block text-sm font-medium text-gray-700 mb-0">Select Case</label>
                        <v-select
                            v-model="selectedCaseObject"
                            :options="searchedCases"
                            label="title"
                            :reduce="(caseItem) => caseItem"
                            placeholder="Search and select case"
                            @input="handleCaseInput"
                            :filterable="true"
                            :clearable="true"
                            class="v-select-custom"
                        >
                            <template #option="{ title, id }">
                                <span :class="{ 'font-medium text-blue-600': id === 'all_cases_avg' }">{{ title }}</span>
                            </template>
                            <template #selected-option="{ title }">
                                <span>{{ title }}</span>
                            </template>
                            <template #no-options>
                                <div class="text-gray-500 italic p-2">No matching cases found</div>
                            </template>
                        </v-select>
                    </div>

                    <!-- Reset Filter Button -->
                    <div class="flex items-end">
                        <button
                            @click="resetFilters"
                            :disabled="!hasActiveFilters"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-500 border border-transparent rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-300 transition-colors duration-200"
                            title="Reset all filters"
                        >
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Reset Filters
                        </button>
                    </div>
                </div>
            </div>
            <!-- Pagination Controls -->
            <div v-if="showPaginatedChart" class="flex items-center justify-between bg-gray-150 rounded-lg">
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-700">{{ paginationInfo }}</span>
                </div>
                <div class="flex items-center space-x-2">
                    <!-- Previous Button -->
                    <button @click="prevPage" :disabled="currentPage === 1" class="px-3 py-1 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Previous
                    </button>
                    <!-- Next Button -->
                    <button @click="nextPage" :disabled="currentPage === totalPages - 1" class="px-3 py-1 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                        Next
                        <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Chart Container -->

            <apexchart type="bar" height="260" :options="chartOptions" :series="series" class="chart-container" @dataPointSelection="handleBarClick" />
            <!-- <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                    <apexchart type="bar" height="260" :options="chartOptions" :series="series" class="chart-container" @dataPointSelection="handleBarClick" />

                </div>
                <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm">
                                    <apexchart type="bar" height="260" :options="chartOptions" :series="series" class="chart-container" @dataPointSelection="handleBarClick" />

                </div>
            </div> -->
            <ViewContractDialog v-model:visible="showModal" :contract="selectedContract" />
        </div>
    </div>
</template>

<script>
import axiosClient from '@/axios';
import vSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import ApexCharts from 'vue3-apexcharts';
import ViewContractDialog from '@/components/HiringContracts/ViewContractDialog.vue';
import { onMounted, ref } from 'vue';
import { useToast } from 'primevue/usetoast';

const apiBase = '/admin/hiring-contracts';

export default {
    name: 'CaseDurationsChart',
    components: {
        apexchart: ApexCharts,
        vSelect,
        ViewContractDialog
    },
    props: {
        filters: {
            type: Object,
            default: () => ({
                status: 'all',
                selectedDeliverable: '',
                selectedSection: ''
            })
        }
    },
    data() {
        const toast = useToast();
        return {
            loading: true,
            rawData: null,
            viewType: 'individual',
            selectedDeliverable: '',
            selectedSection: '',
            selectedSubSection: '',
            selectedCase: '', // This will store the case ID
            selectedCaseObject: null, // This will store the entire case object for v-select
            selectedYear: '',
            availableYears: [],
            sections: [],
            subSections: [],
            toast,
            selectedContract: null,
            showModal: false,
            selectedCaseId: null,
            uniqueDeliverables: [],
            cases: [],
            currentPage: 1,
            casesPerPage: 3,
            showPaginatedChart: true,
            chartOptions: {
                chart: {
                    type: 'bar',
                    height: 260,
                    stacked: true
                },
                xaxis: { title: { text: 'Duration (days)' }, min: 0 },
                plotOptions: { bar: { horizontal: true, barHeight: '95%' } },
                dataLabels: {
                    enabled: true,
                    formatter: (val, opts) => {
                        const stage = opts.w.globals.seriesNames[opts.seriesIndex];
                        const category = opts.w.globals.labels[opts.dataPointIndex];
                        const normDate = this.normDatesMap[category]?.[stage] ?? null;
                        const normDiffKey = this.normDiffKeys[stage] ?? null;
                        const normDiff = (normDiffKey && this.normDiffMap[category]?.[normDiffKey]) ?? null;
                        let label = `${stage}: ${val} days`;
                        if (normDate) label += `\n(Norm: ${normDate})`;
                        if (normDiff !== null) label += `\n(${normDiff} days)`;
                        return label;
                    },
                    style: { colors: ['#fff'], fontSize: '10px', fontWeight: 'normal' }
                },
                stroke: { width: 0.2, colors: ['#fff'] },
                title: { text: 'Case Stage Durations (in days)', align: 'center' },
                yaxis: { title: { text: 'Cases/Groupings' } },
                tooltip: {
                    y: {
                        formatter: function (val, { seriesIndex, dataPointIndex, w }) {
                            const stage = w.globals.seriesNames[seriesIndex];
                            const category = w.globals.labels[dataPointIndex];
                            const normDate = this.normDatesMap[category]?.[stage] ?? null;
                            const normDiffKey = this.normDiffKeys[stage] ?? null;
                            const normDiff = (normDiffKey && this.normDiffMap[category]?.[normDiffKey]) ?? null;
                            let tooltipText = `${val} days`;
                            if (normDiff !== null) tooltipText += `\(${normDiff} days)`;
                            return tooltipText;
                        }.bind(this)
                    }
                },
                legend: { position: 'top', horizontalAlign: 'center' },
                colors: ['#F5276C', '#F5B027', '#89CFF0', '#00A36C', '#CCCCFF', '#088F8F', '#DFFF00', '#9F2B68', '#DA70D6', '#702963']
            },
            series: [],
            normDatesMap: {},
            normDiffMap: {},
            normDiffKeys: {
                'Reqmt → Case Initiation': 'norm-1',
                'Case Initiation → AA': 'norm-2',
                'AA → Sanction': 'norm-3',
                'Sanction → Indent': 'norm-4',
                'Indent → NIT': null,
                'NIT → TBO': 'norm-5',
                'TBO → PBO': 'norm-6',
                'PBO → NOA/PO': 'norm-7',
                'NOA/PO → Delivery': 'norm-8',
                'Delivery → Contract Start': 'norm-9',
                'Contract Start → Contract End': 'norm-10'
            }
        };
    },
    computed: {
        allCasesForDropdown() {
            return this.cases.filter((c) => c.id !== 'all_cases_avg');
        },

        // Only filter cases for the chart (not dropdowns)
        filteredCasesForChart() {
            let filtered = [...this.cases]; // Start with all cases

            // Apply filters sequentially
            if (this.filters.selectedDeliverable && this.filters.selectedDeliverable !== 'all_deliverables_avg') {
                filtered = filtered.filter((c) => c.deliverables === this.filters.selectedDeliverable);
            }
            if (this.filters.selectedSection && this.filters.selectedSection !== 'all_sections_avg') {
                filtered = filtered.filter((c) => c.section_id == this.filters.selectedSection);
            }
            if (this.selectedSubSection && this.selectedSubSection !== 'all_sub_sections_avg') {
                filtered = filtered.filter((c) => c.sub_section_id == this.selectedSubSection);
            }
            if (this.filters.status !== 'all') {
                filtered = filtered.filter((c) => c.status === this.filters.status);
            }
            if (this.selectedYear) {
                filtered = filtered.filter((c) => c.year == this.selectedYear);
            }

            return filtered;
        },

        totalPages() {
            return Math.ceil(this.filteredCases.length / this.casesPerPage);
        },
        paginationInfo() {
            const start = (this.currentPage - 1) * this.casesPerPage + 1;
            const end = Math.min(start + this.casesPerPage - 1, this.filteredCases.length);
            return `Showing ${start} to ${end} of ${this.filteredCases.length - 1} cases`;
        },
        searchedCases() {
            const averageOption = { id: 'all_cases_avg', title: 'Average of All Cases' };
            return [averageOption, ...this.allCasesForDropdown];
        },
        filteredCases() {
            let filtered = this.cases;

            if (this.filters.selectedDeliverable && this.filters.selectedDeliverable !== 'all_deliverables_avg') {
                filtered = filtered.filter((c) => c.deliverables === this.filters.selectedDeliverable);
            }
            if (this.filters.selectedSection && this.filters.selectedSection !== 'all_sections_avg') {
                filtered = filtered.filter((c) => c.section_id == this.filters.selectedSection);
            }
            if (this.selectedSubSection && this.selectedSubSection !== 'all_sub_sections_avg') {
                filtered = filtered.filter((c) => c.sub_section_id == this.selectedSubSection);
            }
            if (this.filters.status !== 'all') {
                filtered = filtered.filter((c) => c.status === this.filters.status);
            }
            if (this.selectedYear && this.selectedCase) {
                filtered = filtered.filter((c) => c.year == this.selectedYear);
            }

            return filtered;
        },
        hasActiveFilters() {
            return !!(
                this.selectedYear ||
                this.selectedCaseObject ||
                (this.filters.selectedDeliverable && this.filters.selectedDeliverable !== 'all_deliverables_avg') ||
                (this.filters.selectedSection && this.filters.selectedSection !== 'all_sections_avg') ||
                (this.filters.status && this.filters.status !== 'all')
            );
        }
    },
    watch: {
        selectedCase(newVal) {
            this.currentPage = 1;
            this.showPaginatedChart = !newVal;
            this.updateChart();
        },
        // Watch for changes in selectedCaseObject and update selectedCase
        selectedCaseObject(newCaseObject) {
            if (newCaseObject) {
                this.selectedCase = newCaseObject.id;
            } else {
                this.selectedCase = '';
            }
        },
        filters: {
            handler(newFilters) {
                this.selectedDeliverable = newFilters.selectedDeliverable;
                this.selectedSection = newFilters.selectedSection;
                this.currentPage = 1;
                this.fetchData();
            },
            deep: true
        }
    },
    async mounted() {
        await this.fetchData();
    },
    methods: {
        async fetchData() {
            this.loading = true;
            try {
                const response = await axiosClient.post('/adminDasboard/getFilteredCases', {
                    status: this.filters.status,
                    deliverable: this.filters.selectedDeliverable,
                    section: this.filters.selectedSection,
                    year: this.selectedYear || null
                });
                this.rawData = response.data;
                this.availableYears = response.data.available_years || [];
                this.processData();
                this.updateChart();
            } catch (error) {
                console.error('Error fetching data:', error);
                this.toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to fetch data', life: 3000 });
            } finally {
                this.loading = false;
            }
        },
        
        processData() {
            this.sections = this.rawData.sections_avg_durations
                .filter((item) => item.section_id)
                .map((item) => ({
                    id: item.section_id,
                    name: item.section_name
                }));

            this.subSections = this.rawData.sub_sections_avg_durations
                .filter((item) => item.sub_section_id)
                .map((item) => ({
                    id: item.sub_section_id,
                    name: item.sub_section_name,
                    section_id: item.section_id
                }));

            const deliverablesSet = new Set();
            this.rawData.deliverables_avg_durations.forEach((item) => {
                if (item.deliverables && item.deliverables !== 'Average of All Deliverables') {
                    deliverablesSet.add(item.deliverables);
                }
            });
            this.uniqueDeliverables = Array.from(deliverablesSet);

            this.cases = this.rawData.cases_durations
                .filter((c) => c.case_id || c.title === 'Average of All Cases')
                .map((c) => ({
                    id: c.case_id || 'all_cases_avg',
                    title: c.title,
                    deliverables: c.deliverables,
                    section_id: c.section_id,
                    sub_section_id: c.sub_section_id,
                    status: c.status || 'ongoing',
                    isAverage: c.title === 'Average of All Cases',
                    year: c.year || null
                }));

            this.$emit('update:filter-data', {
                uniqueDeliverables: this.uniqueDeliverables,
                sections: this.sections,
                availableYears: this.availableYears
            });
        },
        handleCaseInput(selectedCaseObject) {
            // The selectedCaseObject is now the full case object
            this.selectedCaseObject = selectedCaseObject;
            this.currentPage = 1;
            // selectedCase will be updated via the watcher
            // this.updateChart() will be called via the selectedCase watcher
        },
        prevPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.updateChart();
            }
        },
        nextPage() {
            if (this.currentPage < this.totalPages) {
                this.currentPage++;
                this.updateChart();
            }
        },
        goToPage(page) {
            this.currentPage = page;
            this.updateChart();
        },
        updateChart() {
            let dataToUse = this.filteredCasesForChart;
            let categories = [];
            const isAverageView = this.filters.selectedDeliverable === 'all_deliverables_avg' || this.filters.selectedSection === 'all_sections_avg' || this.selectedSubSection === 'all_sub_sections_avg' || this.selectedCase === 'all_cases_avg';

            if (isAverageView) {
                if (this.selectedCase === 'all_cases_avg') {
                    dataToUse = this.rawData.cases_durations.filter((c) => c.title === 'Average of All Cases');
                } else if (this.filters.selectedDeliverable === 'all_deliverables_avg') {
                    dataToUse = this.rawData.deliverables_avg_durations.filter((d) => d.deliverables === 'Average of All Deliverables');
                } else if (this.filters.selectedSection === 'all_sections_avg') {
                    dataToUse = this.rawData.sections_avg_durations.filter((s) => s.section_name === 'Average of All Sections');
                } else if (this.filters.selectedDeliverable) {
                    dataToUse = this.rawData.deliverables_avg_durations.filter((d) => d.deliverables === this.filters.selectedDeliverable);
                } else if (this.filters.selectedSection) {
                    dataToUse = this.rawData.sections_avg_durations.filter((s) => s.section_id == this.filters.selectedSection);
                } else {
                    dataToUse = this.rawData.cases_durations.filter((c) => c.title === 'Average of All Cases');
                }
            } else {
                if (this.selectedCase) {
                    dataToUse = this.rawData.cases_durations.filter((c) => c.case_id === this.selectedCase);
                } else {
                    dataToUse = this.rawData.cases_durations.filter((c) => c.case_id && c.title !== 'Average of All Cases');
                    const start = (this.currentPage - 1) * this.casesPerPage;
                    const end = start + this.casesPerPage;
                    dataToUse = dataToUse.slice(start, end);
                }
            }

            if (dataToUse.length > 0) {
                const firstItem = dataToUse[0];
                categories = Object.keys(firstItem.durations || firstItem.avg_durations || {});
            }

            const series = categories.map((stage) => ({
                name: stage,
                data: dataToUse.map((item) => ({
                    x: item.title || 'Unnamed Case',
                    y: (item.durations || item.avg_durations || {})[stage] || 0,
                    case_id: item.case_id || 'N/A'
                }))
            }));

            this.normDatesMap = {};
            this.normDiffMap = {};
            dataToUse.forEach((item) => {
                const label = this.getItemLabel(item, isAverageView);
                this.normDatesMap[label] = item.norm_dates || {};
                this.normDiffMap[label] = item.norm_differences || item.avg_norm_differences || {};
            });

            this.chartOptions = {
                ...this.chartOptions,
                title: { text: this.getDynamicTitle(), align: 'center' },
                xaxis: { ...this.chartOptions.xaxis, categories: dataToUse.map((item) => this.getItemLabel(item, isAverageView)) }
            };

            this.series = series;
        },
        getItemLabel(item, isAverage) {
            if (isAverage) {
                return item.deliverables || item.section_name || item.sub_section_name || item.title;
            }
            return `${item.title} (${item.sub_section_name || 'No Sub-section'})`;
        },
        getDynamicTitle() {
            let baseTitle = 'Case Stage Durations (in days)';
            if (this.selectedCase === 'all_cases_avg') {
                return `${baseTitle} - Average of All Cases`;
            }
            if (this.filters.selectedDeliverable === 'all_deliverables_avg') {
                return `${baseTitle} - Average of All Deliverables`;
            }
            if (this.filters.selectedSection === 'all_sections_avg') {
                return `${baseTitle} - Average of All Sections`;
            }
            if (this.selectedCase) {
                const caseItem = this.cases.find((c) => c.id === this.selectedCase);
                return `${baseTitle} - ${caseItem?.title || 'Selected Case'}`;
            }
            if (this.filters.selectedDeliverable) {
                return `${baseTitle} - Deliverable: ${this.filters.selectedDeliverable}`;
            }
            if (this.filters.selectedSection) {
                const section = this.sections.find((s) => s.id === this.filters.selectedSection);
                return `${baseTitle} - Section: ${section?.name || 'Selected Section'}`;
            }
            if (this.selectedSubSection) {
                const subSection = this.subSections.find((s) => s.id === this.selectedSubSection);
                return `${baseTitle} - Sub-section: ${subSection?.name || 'Selected Sub-section'}`;
            }
            if (this.selectedYear && !this.selectedCase) {
                return `${baseTitle} - Year: ${this.selectedYear}`;
            }
            return baseTitle;
        },
        handleBarClick(event, chartContext, config) {
            const getCaseId = this.series[config.seriesIndex].data[config.dataPointIndex];
            this.selectedCaseId = getCaseId.case_id;
            if (typeof getCaseId.case_id === 'number') {
                this.fetchContract(getCaseId.case_id);
            }
        },
        fetchContract(c) {
            axiosClient
                .get(`/admin/hiring-contracts/${c}`)
                .then((res) => {
                    this.selectedContract = res.data;
                    this.showModal = true;
                })
                .catch((err) => {
                    console.error('Error fetching contract:', err);
                    this.toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to fetch contract', life: 3000 });
                });
        },
        toggleFullscreen() {
            console.log('Toggle fullscreen');
        },
        resetFilters() {
            // Reset local component filters
            this.selectedYear = '';
            this.selectedCaseObject = null;
            this.selectedCase = '';
            this.currentPage = 1;

            // Emit event to parent to reset prop filters
            this.$emit('reset-filters');

            // Refresh data
            this.fetchData();

            // Show success message
            this.toast.add({
                severity: 'success',
                summary: 'Filters Reset',
                detail: 'All filters have been cleared successfully',
                life: 3000
            });
        },
        clearYearFilter() {
            this.selectedYear = '';
            this.currentPage = 1;
            this.fetchData();
        },
        clearCaseFilter() {
            this.selectedCaseObject = null;
            this.selectedCase = '';
            this.currentPage = 1;
            this.updateChart();
        },
        getSectionName(sectionId) {
            const section = this.sections.find((s) => s.id == sectionId);
            return section ? section.name : 'Unknown Section';
        }
    }
};
</script>

<style scoped>
/* Additional CSS for better text wrapping support */
:deep(.apexcharts-yaxis-label) {
    word-wrap: break-word !important;
    white-space: pre-wrap !important;
    line-height: 1.2 !important;
    max-width: 250px !important;
}

:deep(.apexcharts-legend-text) {
    word-wrap: break-word !important;
    white-space: nowrap !important;
    max-width: 120px !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

:deep(.apexcharts-datalabel) {
    word-wrap: break-word !important;
    white-space: pre-wrap !important;
    font-weight: bold !important;
}

:deep(.apexcharts-tooltip) {
    word-wrap: break-word !important;
    white-space: normal !important;
    max-width: 300px !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    :deep(.apexcharts-yaxis-label) {
        font-size: 9px !important;
        max-width: 150px !important;
    }

    :deep(.apexcharts-legend-text) {
        font-size: 10px !important;
        max-width: 80px !important;
    }
}

/* Loading state */
.chart-loading {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 300px;
    font-size: 16px;
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

.v-select-custom .vs__dropdown-toggle {
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    min-height: 42px;
}

.v-select-custom .vs__search {
    color: #374151;
}

.v-select-custom .vs__dropdown-menu {
    border: 1px solid #e5e7eb;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.v-select-custom .vs__dropdown-option {
    padding: 8px 12px;
}

.v-select-custom .vs__dropdown-option--highlight {
    background-color: #eff6ff;
    color: #1d4ed8;
}

.chart-container .apexcharts-tooltip {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    border-radius: 0.375rem;
}

.chart-container .apexcharts-tooltip-title {
    background-color: #f3f4f6;
    border-bottom: 1px solid #e5e7eb;
    font-weight: 500;
}

.chart-container .apexcharts-xaxistooltip {
    background-color: #ffffff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    color: #374151;
}
</style>

<style scoped>
/* Additional CSS for better text wrapping support */
:deep(.apexcharts-yaxis-label) {
    word-wrap: break-word !important;
    white-space: pre-wrap !important;
    line-height: 1.2 !important;
    max-width: 250px !important;
}

:deep(.apexcharts-legend-text) {
    word-wrap: break-word !important;
    white-space: nowrap !important;
    max-width: 120px !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

:deep(.apexcharts-datalabel) {
    word-wrap: break-word !important;
    white-space: pre-wrap !important;
    font-weight: bold !important;
}

:deep(.apexcharts-tooltip) {
    word-wrap: break-word !important;
    white-space: normal !important;
    max-width: 300px !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    :deep(.apexcharts-yaxis-label) {
        font-size: 9px !important;
        max-width: 150px !important;
    }

    :deep(.apexcharts-legend-text) {
        font-size: 10px !important;
        max-width: 80px !important;
    }
}

/* Loading state */
.chart-loading {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 300px;
    font-size: 16px;
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

.v-select-custom .vs__dropdown-toggle {
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    min-height: 42px;
}

.v-select-custom .vs__search {
    color: #374151;
}

.v-select-custom .vs__dropdown-menu {
    border: 1px solid #e5e7eb;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.v-select-custom .vs__dropdown-option {
    padding: 8px 12px;
}

.v-select-custom .vs__dropdown-option--highlight {
    background-color: #eff6ff;
    color: #1d4ed8;
}

.chart-container .apexcharts-tooltip {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    border-radius: 0.375rem;
}

.chart-container .apexcharts-tooltip-title {
    background-color: #f3f4f6;
    border-bottom: 1px solid #e5e7eb;
    font-weight: 500;
}

.chart-container .apexcharts-xaxistooltip {
    background-color: #ffffff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    color: #374151;
}
</style>
