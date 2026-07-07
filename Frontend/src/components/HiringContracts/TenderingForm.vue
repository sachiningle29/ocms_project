<template>
    <!-- Case Information Flow Section -->
    <fieldset class="border rounded p-4 mb-6">
        <legend class="font-semibold text-lg mb-2">Case Information Flow</legend>

        <div class="flex items-center justify-between space-x-4">
            <!-- Case ID -->
            <div class="flex-1 flex flex-col items-center">
                <div class="w-full text-center font-bold bg-blue-50 p-2 rounded-t-lg">Case ID</div>
                <div class="w-full border-x border-b rounded-b-lg p-3 text-center bg-white">
                    {{ contract.rid || 'N/A' }}
                </div>
            </div>

            <!-- Case Title -->
            <div class="flex-1 flex flex-col items-center">
                <div class="w-full text-center font-bold bg-blue-50 p-2 rounded-t-lg">Case Title</div>
                <div class="w-full border-x border-b rounded-b-lg p-3 text-center bg-white">
                    {{ contract.title || 'N/A' }}
                </div>
            </div>

            <!-- Deliverables -->
            <div class="flex-1 flex flex-col items-center">
                <div class="w-full text-center font-bold bg-blue-50 p-2 rounded-t-lg">Deliverables</div>
                <div class="w-full border-x border-b rounded-b-lg p-3 text-center bg-white">
                    {{ contract.deliverables || 'N/A' }}
                </div>
            </div>

            <!-- Tendering Section -->
            <div class="flex-1 flex flex-col items-center">
                <div class="w-full text-center font-bold bg-blue-50 p-2 rounded-t-lg">Tendering Section</div>
                <div class="w-full border-x border-b rounded-b-lg p-3 text-center bg-white">
                    {{ contract.tendering_section || 'N/A' }}
                </div>
            </div>
        </div>
    </fieldset>

    <fieldset class="border rounded p-4">
        <legend class="font-semibold text-lg mb-2">Tendering Timelines</legend>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Tender DO -->
            <div class="flex flex-col">
                <label for="tender_do" class="block text-600 text-sm font-medium mb-2">
                    Tender DO
                    <span v-if="isEditMode" class="text-red-500">*</span>
                </label>
                <InputText id="tender_do" v-model="contract.tender_do" :class="{ 'p-invalid': fieldErrors.tender_do }"
                    @input="fieldErrors.tender_do = false" />
                <small v-if="fieldErrors.tender_do" class="p-error">
                    Tender DO is required
                </small>
            </div>

            <!-- Tendering Platform -->
            <div class="flex flex-col">
                <label for="tendering_platform" class="block text-600 text-sm font-medium mb-2">
                    Tendering Platform
                    <span v-if="isEditMode" class="text-red-500">*</span>
                </label>
                <Dropdown id="tendering_platform" v-model="contract.tendering_platform"
                    :options="tenderingPlatformOptions" optionLabel="label" optionValue="value"
                    placeholder="Select Platform" :class="{ 'p-invalid': fieldErrors.tendering_platform }"
                    @change="fieldErrors.tendering_platform = false" />
                <small v-if="fieldErrors.tendering_platform" class="p-error">
                    Tendering Platform is required
                </small>
            </div>
        </div>
        <!-- Date Fields Table for Tender Section - REORDERED COLUMNS -->
        <div class="mt-6">
            <h4 class="font-semibold mb-3">Tendering Date Fields</h4>

            <!-- Table Head -->
            <div class="grid grid-cols-12 gap-1 font-bold bg-gray-100 p-2 rounded">
                <div class="col-span-2">Field</div>
                <div class="col-span-1 text-center">Norm Date</div>
                <div class="col-span-1 text-center">Expected Date</div>
                <div class="col-span-1 text-center">Actual Date</div>
                <div class="col-span-1 text-center">Deviation (Days)</div> <!-- New column -->
                <div class="col-span-3 text-center">Deviation Reason</div>
                <div class="col-span-3 text-center">Notes</div>
            </div>
            <!-- Table Rows -->
            <div v-for="(field, index) in dateFields" :key="index"
                class="grid grid-cols-12 gap-1 items-center border-b py-2">

                <!-- Field Name -->
                <div class="col-span-2 text-sm font-medium">
                    {{ field.label }}
                </div>

                <!-- Norm Date -->
                <div class="col-span-1">
                    <InputText v-if="field.normField" v-model="contract[field.normField]" type="date" class="w-full"
                        disabled readonly />
                </div>

                <!-- Expected Date -->
                <div class="col-span-1">
                    <InputText v-if="field.includeExpected" v-model="contract[field.expectedField]" type="date"
                        class="w-full" :disabled="isExpectedFieldDisabled(field)"
                        :min="minExpectedDates[field.expectedField] || null"
                        @input="handleDateChange(field.expectedField, $event)" onkeydown="return false" />
                    <div v-else class="w-full p-2 text-center text-gray-400"></div>
                </div>

                <!-- Actual Date -->
                <div class="col-span-1">
                    <InputText v-if="field.actualField" v-model="contract[field.actualField]" type="date" class="w-full"
                        :disabled="isActualFieldDisabled(field)" :min="getMinActualDate(field.actualField)"
                        @input="handleDateChange(field.actualField, $event)" onkeydown="return false" />
                    <div v-else class="w-full p-2 text-center text-gray-400"></div>
                </div>

                <!-- Deviation Days -->
                <div class="col-span-1">
                    <InputText v-if="field.deviationDaysField" v-model="contract[field.deviationDaysField]"
                        class="w-full text-center" readonly />
                </div>

                <!-- Reason for Deviation -->
                <div class="col-span-3">
                    <InputText v-if="field.expectedField && field.actualField" v-model="contract[field.deviationField]"
                        class="w-full" :disabled="!contract[field.actualField] || !contract[field.expectedField]" />
                    <div v-else class="w-full p-2 text-center text-gray-400"></div>
                </div>

                <!-- Notes -->
                <div class="col-span-3">
                    <InputText v-model="contract[field.notesField]" class="w-full" />
                </div>
            </div>
        </div>
    </fieldset>
</template>

<script>
export default {
    name: 'TenderingForm',
    props: {
        contract: {
            type: Object,
            required: true,
            default: () => ({
                rid: '',
                title: '',
                deliverables: '',
                tendering_section: '',
                tender_do: '',
                indent_date_actual_date: '',
                indent_norm_date: '',

                nit_date_expected_date: '',
                nit_date_norm_date: '',
                nit_date_actual_date: '',
                nit_date_notes: '',
                nit_date_deviation: '',
                nit_date_deviation_days: '',

                tbo_date_expected_date: '',
                tbo_date_norm_date: '',
                tbo_date_actual_date: '',
                tbo_date_notes: '',
                tbo_date_deviation: '',
                tbo_date_deviation_days: '',

                pbo_date_expected_date: '',
                pbo_date_norm_date: '',
                pbo_date_actual_date: '',
                pbo_date_notes: '',
                pbo_date_deviation: '',
                pbo_date_deviation_days: '',

                noa_po_date_expected_date: '',
                noa_po_date_norm_date: '',
                noa_po_date_actual_date: '',
                noa_po_date_notes: '',
                noa_po_date_deviation: '',
                noa_po_date_deviation_days: '',

                delivery_date_expected_date: '',
                delivery_date_norm_date: '',
                delivery_date_notes: '',
                delivery_date_deviation: '',
                delivery_date_deviation_days: ''
            })

        },
        fieldErrors: {
            type: Object,
            default: () => ({})
        },
        vendorTypeOptions: {
            type: Array,
            required: true
        },
        tenderTypeOptions: {
            type: Array,
            required: true
        },
        tenderingPlatformOptions: {
            type: Array,
            required: true
        }
    },
    data() {
        return {
            dateFields: [
                {
                    label: 'NIT Date',
                    actualField: 'nit_date_actual_date',
                    normField: 'nit_date_norm_date',
                    expectedField: 'nit_date_expected_date',
                    notesField: 'nit_date_notes',
                    deviationField: 'nit_date_deviation',
                    deviationDaysField: 'nit_date_deviation_days',
                    includeExpected: false,
                    includeNorm: false,
                    dependsOn: 'indent_date_actual_date'
                },
                {
                    label: 'TBO Date',
                    actualField: 'tbo_date_actual_date',
                    normField: 'tbo_date_norm_date',
                    expectedField: 'tbo_date_expected_date',
                    notesField: 'tbo_date_notes',
                    deviationField: 'tbo_date_deviation',
                    deviationDaysField: 'tbo_date_deviation_days',
                    includeExpected: true,
                    includeNorm: false,
                    dependsOn: 'nit_date_actual_date'
                },
                {
                    label: 'PBO Date',
                    actualField: 'pbo_date_actual_date',
                    normField: 'pbo_date_norm_date',
                    expectedField: 'pbo_date_expected_date',
                    notesField: 'pbo_date_notes',
                    deviationField: 'pbo_date_deviation',
                    deviationDaysField: 'pbo_date_deviation_days',
                    includeExpected: true,
                    includeNorm: false,
                    dependsOn: 'tbo_date_actual_date'
                },
                {
                    label: 'NOA/PO Date',
                    actualField: 'noa_po_date_actual_date',
                    normField: 'noa_po_date_norm_date',
                    expectedField: 'noa_po_date_expected_date',
                    notesField: 'noa_po_date_notes',
                    deviationField: 'noa_po_date_deviation',
                    deviationDaysField: 'noa_po_date_deviation_days',
                    includeExpected: true,
                    includeNorm: false,
                    dependsOn: 'pbo_date_actual_date'
                },
                {
                    label: 'Delivery/Contract Start Date',
                    actualField: '',
                    normField: 'delivery_date_norm_date',
                    expectedField: 'delivery_date_expected_date',
                    notesField: 'delivery_date_notes',
                    deviationField: 'delivery_date_deviation',
                    deviationDaysField: 'delivery_date_deviation_days',
                    includeExpected: true,
                    includeNorm: false,
                    dependsOn: 'noa_po_date_actual_date'
                }
            ],
            minActualDates: {},
            minExpectedDates: {}
        };
    },
    watch: {
        'contract.indent_norm_date': {
            handler(newDate) {
                if (newDate) {
                    this.populateTenderNormDates(newDate);
                } else {
                    this.clearTenderNormDates();
                }
            },
            immediate: true
        },
        'contract.indent_date_actual_date': {
            handler(newDate) {
                // Update min dates when indent date changes
                this.updateMinDates();
            },
            immediate: true
        }
    },
    methods: {
        // Check if actual field should be disabled
        isActualFieldDisabled(field) {
            // Special case for NIT Date - always enabled (no dependency on expected date)
            if (field.actualField === 'nit_date_actual_date') {
                return false;
            }

            // For other actual date fields, they are enabled only if their corresponding expected date is filled
            return !this.contract[field.expectedField];
        },

        getMinActualDate(fieldName) {
            // Set min date for NIT date to indent date (so user can select indent date or later)
            if (fieldName === 'nit_date_actual_date' && this.contract.indent_date_actual_date) {
                return this.contract.indent_date_actual_date;
            }

            // For other fields, use the existing min date logic
            return this.minActualDates[fieldName] || null;
        },

        isExpectedFieldDisabled(field) {
            if (!field.includeExpected) return true;
            if (!field.dependsOn) return false;
            return !this.contract[field.dependsOn];
        },

        handleDateChange(fieldName, event) {
            const updatedDate = event.target.value;

            // Check if this is an actual date that other expected dates depend on
            const dependentFields = this.dateFields.filter((field) => field.dependsOn === fieldName);

            // Update the contract value
            this.contract[fieldName] = updatedDate;

            // If an expected date is cleared, clear the corresponding actual date
            const fieldWithExpectedDate = this.dateFields.find(field => field.expectedField === fieldName);
            if (fieldWithExpectedDate && !updatedDate) {
                this.contract[fieldWithExpectedDate.actualField] = '';
            }

            // If an actual date is filled and it's less than the expected date, show warning
            const fieldWithActualDate = this.dateFields.find(field => field.actualField === fieldName);
            if (fieldWithActualDate && updatedDate) {
                const expectedDate = this.contract[fieldWithActualDate.expectedField];
                if (fieldWithActualDate.includeExpected && expectedDate && new Date(updatedDate) < new Date(expectedDate)) {
                    console.warn(`Actual date is earlier than expected date for ${fieldWithActualDate.label}`);
                }

                // ✅ Always calculate deviation regardless of includeExpected
                this.calculateDeviationDays(fieldWithActualDate);
            }

            // Validate dependent expected dates
            dependentFields.forEach((dependentField) => {
                this.validateExpectedDateAgainstActual(dependentField.expectedField, fieldName);
            });

            // Update minimum dates
            this.updateMinDates();

            // Validate chronological order
            this.validateChronologicalOrder();
        },

        validateExpectedDateAgainstActual(expectedField, dependsOnField) {
            const expectedDate = this.contract[expectedField];
            const actualDate = this.contract[dependsOnField];

            if (expectedDate && actualDate && new Date(expectedDate) < new Date(actualDate)) {
                const fieldLabel = this.dateFields.find((f) => f.expectedField === expectedField)?.label || 'field';
                alert(`Expected date for ${fieldLabel} cannot be before ${this.getFieldLabel(dependsOnField)}`);
                this.contract[expectedField] = '';
            }
        },

        getFieldLabel(fieldName) {
            const field = this.dateFields.find((f) => f.actualField === fieldName || f.expectedField === fieldName || f.normField === fieldName);
            return field?.label || fieldName;
        },

        updateMinDates() {
            // Clear previous min dates
            this.minActualDates = {};
            this.minExpectedDates = {};

            // Set min dates based on dependencies
            this.dateFields.forEach((field) => {
                // Set min actual date based on dependent field
                if (field.dependsOn) {
                    this.minActualDates[field.actualField] = this.contract[field.dependsOn] || null;
                }

                // Set min expected date based on dependent actual date
                if (field.includeExpected && field.dependsOn) {
                    this.minExpectedDates[field.expectedField] = this.contract[field.dependsOn] || null;
                }
            });
        },

        validateChronologicalOrder() {
            let prevActual = null;
            let prevExpected = null;

            for (const field of this.dateFields) {
                const actual = this.contract[field.actualField];
                const expected = this.contract[field.expectedField];

                // Validate Actual Dates Chronologically
                if (actual && prevActual && new Date(actual) < new Date(prevActual)) {
                    alert(`Actual Date "${field.label}" should not be before the previous step.`);
                    this.contract[field.actualField] = '';
                    return;
                }

                // Validate Expected Dates Chronologically (only for fields that include expected dates)
                if (field.includeExpected && expected && prevExpected && new Date(expected) < new Date(prevExpected)) {
                    alert(`Expected Date "${field.label}" should not be before the previous step.`);
                    this.contract[field.expectedField] = '';
                    return;
                }

                if (actual) prevActual = actual;
                if (field.includeExpected && expected) prevExpected = expected;
            }
        },

        populateTenderNormDates(startDateString) {
            const startDate = new Date(startDateString);
            if (isNaN(startDate.getTime())) return;

            const normFields = ['nit_date_norm_date', 'tbo_date_norm_date', 'pbo_date_norm_date', 'noa_po_date_norm_date', 'delivery_date_norm_date'];

            normFields.forEach((field, index) => {
                const targetDate = new Date(startDate);
                targetDate.setDate(startDate.getDate() + (index + 1) * 10);
                const formatted = targetDate.toISOString().split('T')[0];
                this.contract[field] = formatted;
            });
        },

        clearTenderNormDates() {
            const normFields = ['nit_date_norm_date', 'tbo_date_norm_date', 'pbo_date_norm_date', 'noa_po_date_norm_date', 'delivery_date_norm_date'];
            normFields.forEach((field) => {
                this.contract[field] = '';
            });
        },

        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toLocaleDateString();
        },

        calculateDeviationDays(field) {
            if (!field.deviationDaysField) return;

            const norm = this.contract[field.normField];
            const actual = this.contract[field.actualField];

            const normDate = norm ? new Date(norm) : null;
            const actualDate = actual ? new Date(actual) : null;

            // Ensure both dates are valid
            if (!normDate || !actualDate) {
                this.contract[field.deviationDaysField] = '';
                return;
            }

            // Strip time from both
            normDate.setHours(0, 0, 0, 0);
            actualDate.setHours(0, 0, 0, 0);

            // Only show deviation if actual > norm
            if (actualDate > normDate) {
                const diffTime = actualDate.getTime() - normDate.getTime();
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                this.contract[field.deviationDaysField] = diffDays;
            } else {
                this.contract[field.deviationDaysField] = '';
            }
        }

    }
};
</script>