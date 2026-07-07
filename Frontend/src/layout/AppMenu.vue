<script setup>
import { ref, computed } from 'vue';
import { useRoute } from 'vue-router';
import AppMenuItem from './AppMenuItem.vue';

const route = useRoute();

const dashboardPath = computed(() => {
    if (route.path.startsWith('/admin')) {
        return '/admin/dashboard';
    } else if (route.path.startsWith('/user')) {
        return '/user/dashboard';
    }
    return '/';
});

const model = computed(() => {
    if (route.path.startsWith('/admin')) {
        return [
            {
                items: [
                    { label: 'Dashboard', icon: 'pi pi-fw pi-home', to: dashboardPath.value },
                    { label: 'User', icon: 'pi pi-fw pi-user', to: { name: 'addUser' } },
                    {
                        label: 'Master Section', icon: 'pi pi-fw pi-cog',
                        items: [
                            { label: 'Section', icon: 'pi pi-fw pi-image', to: { name: 'section' } },
                            { label: 'Sub-Section', icon: 'pi pi-fw pi-clone', to: { name: 'subsection' } }
                        ]
                    },
                    { label: 'Procurement Case', icon: 'pi pi-fw pi-image', to: '/admin/hiringcontracts' },
                ]
            }
        ];
    } else if (route.path.startsWith('/user')) {
        return [
            {
                items: [
                    { label: 'Dashboard', icon: 'pi pi-fw pi-home', to: dashboardPath.value },
                    //{ label: 'Running Contract', icon: 'pi pi-fw pi-clone', to: '/user/runningcontracts' },
                    { label: 'Procurement case', icon: 'pi pi-fw pi-image', to: '/user/hiringcontracts' },
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
        <template v-for="(item, i) in model" :key="i">
            <app-menu-item v-if="!item.separator" :item="item" :index="i" />
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
