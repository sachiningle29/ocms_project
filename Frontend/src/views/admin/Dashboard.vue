<script setup>
import { ref } from 'vue';
import Donutchart_1 from '@/components/AdminDashboard/donutchart_1.vue';
import Donutchart_2 from '@/components/AdminDashboard/donutchart_2.vue';
import Filterbutton from '@/components/AdminDashboard/filterbutton.vue';
import Stackbarchart from '@/components/AdminDashboard/stackbarchart.vue';
import cases_calculation from '@/components/AdminDashboard/cases_calculation.vue';


const filters = ref({
    status: 'all',
    selectedDeliverable: '',
    selectedSection: ''
});
const uniqueDeliverables = ref([]);
const sections = ref([]);

const handleFilterChange = (newFilters) => {
    filters.value = { ...filters.value, ...newFilters };
};

const updateFilterData = ({ uniqueDeliverables: deliverables, sections: secs }) => {
    uniqueDeliverables.value = deliverables;
    sections.value = secs;
};
</script>

<template>
    <!-- heading section -->
    <div class="max-w-full mx-auto flex flex-col lg:flex-row gap-6">
        <!-- Main Content Area (80%) -->
        <div class="w-full lg:w-12/15 space-y-4">
       
             <div class="flex flex-wrap -mx-2">
                <cases_calculation />
            </div>
                 <!-- Stacked Bar Chart -->
            <div class="bg-white  rounded-xl ">
               
                <Stackbarchart :filters="filters" @update:filter-data="updateFilterData" />
            </div>
            

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-0 rounded-xl shadow-sm">
                    <Donutchart_1 :filters="filters" />
                </div>
                <div class="bg-white p-0 rounded-xl shadow-sm">
                    <Donutchart_2 :filters="filters" />
                </div>
                
            </div>
        </div>

        <!-- Filter Sidebar (20%) -->
        <Filterbutton
            :selected-deliverable.sync="filters.selectedDeliverable"
            :selected-section.sync="filters.selectedSection"
            :status-filter.sync="filters.status"
            :unique-deliverables="uniqueDeliverables"
            :sections="sections"
            @filter-change="handleFilterChange"
        />
    </div>
</template>

<style>
/* Custom scrollbar for filter section */
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
