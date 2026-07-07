<script setup>
import { ref, onMounted } from 'vue';
import axiosClient from '@/axios';

const loading = ref(false);
const rawData = ref(null);
const ongoing_cases = ref(null); 
const reqmt_recd = ref(null); // Create a separate ref for this value
const noa_po_placed = ref(null); // Create a separate ref for this value

async function fetchData() {
    loading.value = true;
    try {
        const response = await axiosClient.post('/adminDasboard/getFilteredCases', {});
        rawData.value = response.data;
        ongoing_cases.value = response.data.ongoing_cases; // Update the ref value
        reqmt_recd.value = response.data.reqmt_recd; // Update the ref value
        noa_po_placed.value = response.data.noa_po_placed; // Update the ref value
        
    } catch (error) {
        console.error('Error fetching data:', error);
    } finally {
        loading.value = false;
    }
}



// Fetch data when component mounts
onMounted(() => {
    fetchData();
});
</script>

<template>
   <div class="w-full md:w-4/12 px-2 mb-0">
  <div class="bg-white p-1 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 border border-gray-100">
    <div class="flex items-center justify-center text-center"> <!-- Added justify-center and text-center -->
      <div>
        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">Ongoing Cases</p>
             <p class="text-2xl font-bold text-gray-800 mt-0">
            <div>{{ ongoing_cases || '0'}}</div>
        </p>
        </div>
      </div>
   
    </div>
  </div>

  <div class="w-full md:w-4/12 px-2 mb-0">
  <div class="bg-white p-1 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 border border-gray-100">
    <div class="flex items-center justify-center text-center"> <!-- Added justify-center and text-center -->
      <div>
        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">REQMT Received</p>
        <p class="text-2xl font-bold text-gray-800 mt-0">
          {{ reqmt_recd || '0' }}
        </p>
      </div>
    </div>
  </div>
</div>

   <div class="w-full md:w-4/12 px-2 mb-0">
  <div class="bg-white p-1 rounded-xl shadow-md hover:shadow-lg transition-shadow duration-300 border border-gray-100">
    <div class="flex items-center justify-center text-center"> <!-- Added justify-center and text-center -->
      <div>
        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">NOA-Po </p>
            <p class="text-2xl font-bold text-gray-800 mt-0">
            <div>{{ noa_po_placed || '0'}}</div>
        </p>
        </div>
      </div>
   
    </div>
  </div>
</template>