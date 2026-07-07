<script setup>
import Donutchart_1 from '@/components/UserDashboard/donutchart_1.vue';
import Donutchart_2 from '@/components/UserDashboard/donutchart_2.vue';
import Filterbutton from '@/components/UserDashboard/filterbutton.vue';
import Stackbarchart from '@/components/UserDashboard/stackbarchart.vue';
import Titleheading from '@/components/UserDashboard/titleheading.vue';
</script>

<template>
    <!-- heading section  -->
    <Titleheading />

    <div class="max-w-full mx-auto flex flex-col lg:flex-row gap-6">
        <!-- Main Content Area (80%) -->
        <div class="w-full lg:w-10/12 space-y-6">
            <!-- Stacked Bar Chart -->
            <div class="bg-white p-3 rounded-xl shadow-sm">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Stacked Bar-Chart</h2>
                </div>
                <Stackbarchart />
            </div>

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

            <!-- Donut Charts -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-3 rounded-xl shadow-sm">
                    <Donutchart_1 />
                </div>
                <div class="bg-white p-3 rounded-xl shadow-sm">   
                    <Donutchart_2 />
                </div>
            </div>
        </div>
        <!-- Filter Sidebar (20%) - Full height -->
        <Filterbutton />
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