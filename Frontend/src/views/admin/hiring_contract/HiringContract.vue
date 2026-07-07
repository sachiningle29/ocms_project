<script setup>
import axiosClient from '@/axios';
import { onMounted, ref, computed, watch } from 'vue';
import { useToast } from 'primevue/usetoast';
import { FilterMatchMode } from '@primevue/core/api';
import ContractFilters from '@/components/HiringContracts/ContractFilters.vue';
import ViewContractDialog from '@/components/HiringContracts/ViewContractDialog.vue';

const toast = useToast();
const dt = ref();

const contracts = ref([]);
const viewContractDialog = ref(false);

const contract = ref({});

// filter
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    rid: { value: null, matchMode: FilterMatchMode.CONTAINS },
    title: { value: null, matchMode: FilterMatchMode.CONTAINS },
    deliverables: { value: null, matchMode: FilterMatchMode.EQUALS },
    tendering_section: { value: null, matchMode: FilterMatchMode.EQUALS },
    indenting_section_name: { value: null, matchMode: FilterMatchMode.EQUALS } 
});

function clearFilters() {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS },
        rid: { value: null, matchMode: FilterMatchMode.CONTAINS },
        title: { value: null, matchMode: FilterMatchMode.CONTAINS },
        deliverables: { value: null, matchMode: FilterMatchMode.EQUALS },
        tendering_section: { value: null, matchMode: FilterMatchMode.EQUALS },
        indenting_section_name: { value: null, matchMode: FilterMatchMode.EQUALS }
    };
}

// Add filter handler function
function handleFilter(value, field, matchMode) {
   
    if (filters.value[field]) {
        filters.value[field].value = value;
        filters.value[field].matchMode = matchMode === 'contains' ? FilterMatchMode.CONTAINS : FilterMatchMode.EQUALS;
    }
}

const deliverablesOptions = [
    { label: 'Material', value: 'Material' },
    { label: 'Services', value: 'Services' },
    { label: 'Capital', value: 'Capital' }
];

const tenderingSectionOptions = [
    { label: 'CPD', value: 'CPD' },
    { label: 'P&C', value: 'P&C' },
    { label: 'MM', value: 'MM' }
];

const indentingSectionOptions = ref([]);

const loadSections = async () => {
    try {
        const response = await axiosClient.get('/admin/hiring-contracts/sections');
        
        // Debug logs
        // console.log('API Response:', response.data);
        // console.log('Response type:', typeof response.data);
        // console.log('Is array:', Array.isArray(response.data));
        
        if (Array.isArray(response.data) && response.data.length > 0) {
            // console.log('First item:', response.data[0]);
            // console.log('First item keys:', Object.keys(response.data[0]));
            
            // Since API already returns {label, value} format, use it directly
            indentingSectionOptions.value = response.data;
            
            // console.log('Mapped options:', indentingSectionOptions.value);
        } else {
            console.warn('No data returned from API');
            indentingSectionOptions.value = [];
        }
        
    } catch (error) {
        console.error('Error loading sections:', error);
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: 'Failed to load sections',
            life: 3000
        });
    }
};

// Watch for changes in indentingSectionOptions
watch(indentingSectionOptions, (newValue) => {
    // console.log('indentingSectionOptions changed:', newValue);
}, { deep: true });

// Watch for changes in filters
watch(filters, (newValue) => {
    // console.log('Filters changed:', newValue);
}, { deep: true });

const isAdmin = ref(true);

const apiBase = '/admin/hiring-contracts';

onMounted(() => {
    loadContracts();
    loadSections();
});

function loadContracts() {
    axiosClient
        .get(apiBase)
        .then((res) => {
            contracts.value = res.data || [];
            // console.log('Contracts loaded:', contracts.value.length);
            
            // Debug: Check if contracts have indenting_section_name
            if (contracts.value.length > 0) {
                // console.log('Sample contract:', contracts.value[0]);
                // console.log('Sample indenting_section_name:', contracts.value[0].indenting_section_name);
            }
        })
        .catch(() => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load contracts', life: 3000 });
        });
}

function viewContract(c) {
    axiosClient
        .get(`${apiBase}/${c.id}`)
        .then((res) => {
            contract.value = res.data;
            viewContractDialog.value = true;
        })
        .catch(() => {
            toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to load contract', life: 3000 });
        });
}
</script>

<template>
    <div class="card">
        <div class="flex justify-content-between align-items-center mb-3">
            <h4 class="m-0">Procurement Case</h4>
        </div>

        <!-- Debug section - remove this after fixing -->
        <!-- <div class="mb-3 p-3 bg-gray-100 border-round">
            <h5>Debug Info:</h5>
            <p><strong>Indenting Section Options:</strong> {{ indentingSectionOptions }}</p>
            <p><strong>Current Filter Value:</strong> {{ filters.indenting_section_name.value }}</p>
            <p><strong>Is Admin:</strong> {{ isAdmin }}</p>
        </div> -->

        <DataTable ref="dt" :value="contracts" dataKey="id" :paginator="true" :rows="10" :filters="filters"
            :rowsPerPageOptions="[5, 10, 25]" filterDisplay="menu"
            currentPageReportTemplate="Showing {first} to {last} of {totalRecords} entries"
            paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown">
            <template #header>
                <div class="flex flex-column gap-2">
                    <ContractFilters 
                        v-model:filters="filters" 
                        :deliverables-options="deliverablesOptions"
                        :tendering-section-options="tenderingSectionOptions" 
                        :indenting-section-options="indentingSectionOptions" 
                        :is-admin="isAdmin" 
                        @filter="handleFilter"
                        @clear-filters="clearFilters" />
                </div>
            </template>

            <Column header="Sr. No" sortable>
                <template #body="slotProps">
                    {{ dt.first + slotProps.index + 1 }}
                </template>
            </Column>
            <Column header="Case ID" field="rid" sortable />
            <Column header="Title" field="title" sortable />
            <Column header="Vendor" field="vendor_type" sortable />
            <Column header="Section" field="indenting_section_name" sortable />
            <Column header="Status" field="status" sortable />

            <Column header="Actions" :exportable="false">
                <template #body="slotProps">
                    <Button icon="pi pi-eye" outlined rounded class="mr-2" @click="viewContract(slotProps.data)" />
                </template>
            </Column>
        </DataTable>

        <ViewContractDialog v-model:visible="viewContractDialog" :contract="contract" />
    </div>
</template>

<style scoped>
.p-input-icon-left {
    position: relative;
    display: inline-block;
}

.p-input-icon-left>i {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    color: #888;
    font-size: 1rem;
}

.p-input-icon-left>input {
    padding-left: 2.5rem !important;
}
</style>