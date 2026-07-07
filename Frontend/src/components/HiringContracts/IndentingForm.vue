<template>
    <!-- Case Information Flow Section -->
    <fieldset class="border rounded p-4 mb-6">
        <legend class="font-semibold text-lg mb-2">Case Information Flow</legend>

        <div class="flex items-center justify-between space-x-4">
            <!-- Case ID -->
            <div class="flex-1 flex flex-col items-center">
                <div class="w-full text-center font-bold bg-blue-50 p-2 rounded-t-lg">Case ID</div>
                <div class="w-full border-x border-b rounded-b-lg p-3 text-center bg-white">
                    {{ contract.rid || '' }}
                </div>
            </div>

            <!-- Case Title -->
            <div class="flex-1 flex flex-col items-center">
                <div class="w-full text-center font-bold bg-blue-50 p-2 rounded-t-lg">Case Title</div>
                <div class="w-full border-x border-b rounded-b-lg p-3 text-center bg-white">
                    {{ contract.title || '' }}
                </div>
            </div>

            <!-- Deliverables -->
            <div class="flex-1 flex flex-col items-center">
                <div class="w-full text-center font-bold bg-blue-50 p-2 rounded-t-lg">Deliverables</div>
                <div class="w-full border-x border-b rounded-b-lg p-3 text-center bg-white">
                    {{ contract.deliverables || '' }}
                </div>
            </div>

            <!-- Tendering Section -->
            <div class="flex-1 flex flex-col items-center">
                <div class="w-full text-center font-bold bg-blue-50 p-2 rounded-t-lg">Tendering Section</div>
                <div class="w-full border-x border-b rounded-b-lg p-3 text-center bg-white">
                    {{ contract.tendering_section || '' }}
                </div>
            </div>
        </div>
    </fieldset>

    <!-- Indenting Timelines Section -->
    <fieldset class="border rounded p-4">
        <legend class="font-semibold text-lg mb-2">Indenting Timelines</legend>
        <div class="mt-4">
            <h4 class="font-semibold mb-3">Indenting Date Fields</h4>

            <div class="grid grid-cols-12 gap-1 font-bold bg-gray-100 p-2 rounded">
                <div class="col-span-2">Field</div>
                <div class="col-span-1 text-center">Norm Date</div>
                <div class="col-span-1 text-center">Expected Date</div>
                <div class="col-span-1 text-center">Actual Date</div>
                <div class="col-span-1 text-center">Deviation (Days)</div> <!-- New column -->
                <div class="col-span-3 text-center">Deviation Reason</div>
                <div class="col-span-3 text-center">Notes</div>
            </div>

            <div v-for="(field, index) in dateFields" :key="index"
                class="grid grid-cols-12 gap-1 items-center border-b py-2">
                <!-- Label -->
                <div class="col-span-2 font-medium text-sm">
                    {{ field.label }}
                </div>

                <!-- Norm Date -->
                <div class="col-span-1">
                    <InputText v-if="field.includeNorm" v-model="contract[field.normField]" type="date" class="w-full"
                        disabled readonly />
                </div>

                <!-- Expected Date -->
                <div class="col-span-1">
                    <InputText v-if="field.includeExpected" v-model="contract[field.expectedField]" type="date"
                        class="w-full" :min="minExpectedDates[field.expectedField] || null"
                        @input="handleDateChange(field.expectedField, $event)"
                        :disabled="isExpectedFieldDisabled(field)" onkeydown="return false" />
                </div>

                <!-- Actual Date -->
                <div class="col-span-1">
                    <InputText v-if="field.includeActual !== false" v-model="contract[field.actualField]" type="date"
                        class="w-full" :min="minActualDates[field.actualField] || null"
                        @input="handleDateChange(field.actualField, $event)" :disabled="isActualFieldDisabled(field)"
                        onkeydown="return false" />
                </div>

                <!-- Deviation Days -->
                <div class="col-span-1">
                    <InputText v-if="field.deviationDaysField" v-model="contract[field.deviationDaysField]"
                        class="w-full text-center" readonly />
                </div>

                <!-- Deviation Reason -->
                <div class="col-span-3">
                    <InputText v-model="contract[field.deviationField]" class="w-full"
                        :disabled="!contract[field.actualField] || !contract[field.expectedField]" />
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
import { useToast } from 'primevue/usetoast';
export default {
    setup() {
        const toast = useToast();
        return { toast };
    },
    name: 'IndentingForm',
    props: {
        contract: {
            type: Object,
            required: true,
            default: () => ({
                rid: '',
                title: '',
                deliverables: '',
                tendering_section: '',

                reqmt_recd_date_actual_date: '',
                reqmt_recd_norm_date: '',
                reqmt_recd_date_notes: '',
                reqmt_recd_date_deviation: '',
                reqmt_recd_date_deviation_days: '',

                case_initiation_date_expected_date: '',
                case_initiation_date_actual_date: '',
                case_initiation_norm_date: '',
                case_initiation_date_notes: '',
                case_initiation_date_deviation: '',
                case_initiation_date_deviation_days: '',

                aa_date_expected_date: '',
                aa_date_actual_date: '',
                aa_norm_date: '',
                aa_date_notes: '',
                aa_date_deviation: '',
                aa_date_deviation_days: '',

                sanction_date_expected_date: '',
                sanction_date_actual_date: '',
                sanction_norm_date: '',
                sanction_date_notes: '',
                sanction_date_deviation: '',
                sanction_date_deviation_days: '',

                indent_date_expected_date: '',
                indent_date_actual_date: '',
                indent_norm_date: '',
                indent_date_notes: '',
                indent_date_deviation: '',
                indent_date_deviation_days: '',

                nit_date_expected_date: '',
                nit_date_notes: '',
                nit_date_deviation: '',
                nit_date_deviation_days: ''
            })
        }

    },
    data() {
        return {
            dateFields: [
                {
                    label: 'Reqmt Recd Date',
                    actualField: 'reqmt_recd_date_actual_date',
                    normField: 'reqmt_recd_norm_date',
                    expectedField: 'reqmt_recd_date_expected_date',
                    notesField: 'reqmt_recd_date_notes',
                    deviationField: '',
                    deviationDaysField: 'reqmt_recd_date_deviation_days',
                    includeExpected: false,
                    includeNorm: false,
                    includeActual: true,
                    isFirstField: true
                },
                {
                    label: 'Case Initiation Date',
                    actualField: 'case_initiation_date_actual_date',
                    normField: 'case_initiation_norm_date',
                    expectedField: 'case_initiation_date_expected_date',
                    notesField: 'case_initiation_date_notes',
                    deviationField: 'case_initiation_date_deviation',
                    deviationDaysField: 'case_initiation_date_deviation_days', 
                    includeExpected: true,
                    includeNorm: true,
                    includeActual: true,
                    dependsOn: 'reqmt_recd_date_actual_date'
                },
                {
                    label: 'AA Date',
                    actualField: 'aa_date_actual_date',
                    normField: 'aa_norm_date',
                    expectedField: 'aa_date_expected_date',
                    notesField: 'aa_date_notes',
                    deviationField: 'aa_date_deviation',
                    deviationDaysField: 'aa_date_deviation_days', 
                    includeExpected: true,
                    includeNorm: true,
                    includeActual: true,
                    dependsOn: 'case_initiation_date_actual_date'
                },
                {
                    label: 'Sanction Date',
                    actualField: 'sanction_date_actual_date',
                    normField: 'sanction_norm_date',
                    expectedField: 'sanction_date_expected_date',
                    notesField: 'sanction_date_notes',
                    deviationField: 'sanction_date_deviation',
                    deviationDaysField: 'sanction_date_deviation_days', 
                    includeExpected: true,
                    includeNorm: true,
                    includeActual: true,
                    dependsOn: 'aa_date_actual_date'
                },
                {
                    label: 'Indent Date',
                    actualField: 'indent_date_actual_date',
                    normField: 'indent_norm_date',
                    expectedField: 'indent_date_expected_date',
                    notesField: 'indent_date_notes',
                    deviationField: 'indent_date_deviation',
                    deviationDaysField: 'indent_date_deviation_days', 
                    includeExpected: true,
                    includeNorm: true,
                    includeActual: true,
                    dependsOn: 'sanction_date_actual_date'
                },
                {
                    label: 'NIT Date',
                    actualField: 'nit_date_actual_date',
                    normField: 'nit_date_norm_date',
                    expectedField: 'nit_date_expected_date',
                    notesField: 'nit_date_notes',
                    deviationField: 'nit_date_deviation',
                    deviationDaysField: 'nit_date_deviation_days', 
                    includeExpected: true,
                    includeNorm: true,
                    includeActual: false,
                    dependsOn: 'indent_date_actual_date'
                }
            ],
            minActualDates: {},
            minExpectedDates: {}
        };
    },
    computed: {
        allRequiredExpectedDatesFilled() {
            const requiredExpectedFields = this.dateFields
                .filter(field => field.includeExpected && field.dependsOn)
                .map(field => field.expectedField);

            return requiredExpectedFields.every(field => this.contract[field]);
        }
    },
    methods: {
        isActualFieldDisabled(field) {
            if (field.isFirstField) {
                return false;
            }

            if (field.includeActual === false) {
                return true;
            }

            return !this.contract[field.expectedField];
        },

        isExpectedFieldDisabled(field) {
            if (!field.includeExpected) return true;
            if (!field.dependsOn) return false;
            return !this.contract[field.dependsOn];
        },

        handleDateChange(fieldName, event) {
            const updatedDate = event.target.value;

            if (fieldName === 'reqmt_recd_date_actual_date') {
                this.populateNormDates(updatedDate);
            }

            this.contract[fieldName] = updatedDate;

            const isActualDateField = this.dateFields.some(f => f.actualField === fieldName);
            if (isActualDateField) {
                this.validateNextExpectedDate(fieldName);

                // Check if we should clear the deviation reason when dates change
                const field = this.dateFields.find(f => f.actualField === fieldName);
                if (field && (!this.contract[field.actualField] || !this.contract[field.expectedField])) {
                    this.contract[field.deviationField] = '';
                }
            }

            const dependentFields = this.dateFields.filter((field) => field.dependsOn === fieldName);
            dependentFields.forEach((dependentField) => {
                this.validateExpectedDateAgainstActual(dependentField.expectedField, fieldName);
            });

            const fieldWithExpectedDate = this.dateFields.find(field => field.expectedField === fieldName);
            if (fieldWithExpectedDate && !updatedDate && fieldWithExpectedDate.includeActual !== false && !fieldWithExpectedDate.isFirstField) {
                this.contract[fieldWithExpectedDate.actualField] = '';
                this.contract[fieldWithExpectedDate.deviationField] = '';
            }

            const fieldWithActualDate = this.dateFields.find(field => field.actualField === fieldName);
            if (fieldWithActualDate && updatedDate && fieldWithActualDate.includeActual !== false) {
                const expectedDate = this.contract[fieldWithActualDate.expectedField];
                if (expectedDate && new Date(updatedDate) < new Date(expectedDate)) {
                    console.warn(`Actual date is earlier than expected date for ${fieldWithActualDate.label}`);
                }

                // ✅ Calculate deviation days
                this.calculateDeviationDays(fieldWithActualDate);
            }

            this.updateMinDates();
            this.$emit('validation-changed', this.allRequiredExpectedDatesFilled);
        },

        populateNormDates(baseDate) {
            if (!baseDate) {
                this.clearNormDates();
                return;
            }

            const startDate = new Date(baseDate);

            if (isNaN(startDate.getTime())) {
                console.error('Invalid date provided');
                return;
            }

            const normDateFields = ['case_initiation_norm_date', 'aa_norm_date', 'sanction_norm_date', 'indent_norm_date', 'nit_date_norm_date'];

            normDateFields.forEach((field, index) => {
                const targetDate = new Date(startDate);
                targetDate.setDate(startDate.getDate() + (index + 1) * 10);
                const formattedDate = targetDate.toISOString().split('T')[0];
                this.contract[field] = formattedDate;
            });
        },

        updateMinDates() {
            let prevActual = null;

            this.dateFields.forEach((field) => {
                if (field.includeActual !== false) {
                    if (prevActual) {
                        this.minActualDates[field.actualField] = prevActual;
                    } else {
                        this.minActualDates[field.actualField] = null;
                    }

                    const currentActual = this.contract[field.actualField];
                    if (currentActual) {
                        prevActual = currentActual;
                    }
                }

                if (field.dependsOn) {
                    const dependsOnActual = this.contract[field.dependsOn];
                    this.minExpectedDates[field.expectedField] = dependsOnActual || null;
                } else {
                    this.minExpectedDates[field.expectedField] = null;
                }

                if (field.expectedField && field.dependsOn) {
                    this.validateExpectedDateAgainstActual(field.expectedField, field.dependsOn);
                }
            });
        },

        validateExpectedDateAgainstActual(expectedField, dependsOnField) {
            const expectedDate = this.contract[expectedField];
            const actualDate = this.contract[dependsOnField];

            if (expectedDate && actualDate && new Date(expectedDate) < new Date(actualDate)) {
                const fieldLabel = this.dateFields.find((f) => f.expectedField === expectedField)?.label || 'field';
                alert(`Expected date for ${fieldLabel} cannot be before the actual date of the previous step.`);
                this.contract[expectedField] = '';
            }
        },

        validateNextExpectedDate(currentField) {
            const currentIndex = this.dateFields.findIndex(f =>
                f.actualField === currentField ||
                f.expectedField === currentField
            );

            if (currentIndex === -1 || currentIndex >= this.dateFields.length - 1) {
                return;
            }

            const nextField = this.dateFields[currentIndex + 1];

            if (!nextField.includeExpected) return;

            const currentActualDate = this.contract[currentField];
            const nextExpectedDate = this.contract[nextField.expectedField];

            if (currentActualDate && nextExpectedDate) {
                if (new Date(nextExpectedDate) < new Date(currentActualDate)) {
                    this.$toast.add({
                        severity: 'error',
                        summary: 'Validation Error',
                        detail: `Expected date for ${nextField.label} cannot be before the actual date of ${this.dateFields[currentIndex].label}`,
                        life: 5000
                    });

                    this.contract[nextField.expectedField] = '';
                }
            }
        },
        clearNormDates() {
            const normDateFields = ['case_initiation_norm_date', 'aa_norm_date', 'sanction_norm_date', 'indent_norm_date', 'nit_date_norm_date'];
            normDateFields.forEach((field) => {
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
    },

    mounted() {
        this.$emit('validation-changed', this.allRequiredExpectedDatesFilled);
    }
};
</script>