<script setup>
import { ref, computed } from 'vue';
import { useRoute } from 'vue-router';
import AppMenuItem from './AppMenuItem.vue';

const route = useRoute();

// Dynamically determine the dashboard path
const dashboardPath = computed(() => {
    if (route.path.startsWith('/admin')) {
        return '/admin/dashboard';
    } else if (route.path.startsWith('/user')) {
        return '/user/dashboard';
    }
    return '/'; // fallback
});
const model = computed(() => {
    if (route.path.startsWith('/admin')) {
        return [
            {
                items: [
                    { label: 'Dashboard', icon: 'pi pi-fw pi-home', to: dashboardPath.value },
                    { label: 'User', icon: 'pi pi-fw pi-clone' },
                    {
                        label: 'Section',
                        icon: 'pi pi-fw pi-image',
                        items: [
                            { label: 'Sub Section 1', icon: 'pi pi-fw pi-angle-right' },
                            { label: 'Sub Section 2', icon: 'pi pi-fw pi-angle-right' },
                            { label: 'Sub Section 3', icon: 'pi pi-fw pi-angle-right' }
                        ]
                    }
                ]
            }
        ];
    } else if (route.path.startsWith('/user')) {
        return [
            {
                items: [
                    { label: 'Dashboard', icon: 'pi pi-fw pi-home', to: dashboardPath.value },
                    { label: 'Running Contract', icon: 'pi pi-fw pi-clone', to: '/user/runningcontracts' },
                    { label: 'Under Hiring Contract', icon: 'pi pi-fw pi-image', to: '/user/hiringcontracts' },
                    { label: 'Add Milestone', icon: 'pi pi-fw pi-calendar', to: '' }
                ]
            }
        ];
    }
    return [];
});

</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item">
            <app-menu-item v-if="!item.separator" :item="item" :index="i"></app-menu-item>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
