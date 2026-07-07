<script setup>
import { ref, onMounted } from 'vue';
import axiosClient from '@/axios';

const loading = ref(false);
const rawData = ref(null);
const ongoing_cases = ref(null); // Create a separate ref for this value

async function fetchData() {
    loading.value = true;
    try {
        const response = await axiosClient.post('/adminDasboard/getFilteredCases', {});
        rawData.value = response.data;
        ongoing_cases.value = response.data.ongoing_cases; // Update the ref value
        
        processData();
        updateChart();
    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        loading.value = false;
    }
}

function processData() {
    // Your data processing logic
}

function updateChart() {
    // Your chart update logic
}

// Fetch data when component mounts
onMounted(() => {
    fetchData();
});
</script>

<template>
  <div class="w-full md:w-4/12 px-2 mb-4">
    <div class="bg-white p-6 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 border border-gray-100">
      <div class="flex items-center">
       
        <div>
          <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Ongoing Cases</p>
          <p class="text-2xl font-bold text-gray-800 mt-1">
            <div>{{ ongoing_cases || '0'}}</div>
        </p>
        </div>
      </div>
   
    </div>
  </div>
</template>