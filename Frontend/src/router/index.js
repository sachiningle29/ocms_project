import AppLayout from '@/layout/AppLayout.vue';
import { createRouter, createWebHistory } from 'vue-router';

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            name: 'welcome',
            component: () => import('@/views/auth/Welcome.vue'),
            meta: { requiresAuth: false }
        },
        {
            path: '/auth/UserLogin',
            name: 'UserLogin',
            component: () => import('@/views/auth/UserLogin.vue'),
            meta: { requiresAuth: false }
        },
        {
            path: '/auth/AdminLogin',
            name: 'AdminLogin',
            component: () => import('@/views/pages/auth/AdminLogin.vue'),
            meta: { requiresAuth: false }
        },

        {
            path: '/',
            component: AppLayout,
            children: [
                {
                    path: '/welcome',
                    redirect: '/auth/welcome'
                },
                   {
                    path: '/auth/logout',
                    name: 'logout',
                    component: () => import('@/views/auth/Logout.vue'),
                    meta: { requiresAuth: false }
                },
            
                {
                    path: '/admin/dashboard',
                    name: 'Admindashboard',
                    component: () => import('@/views/admin/Dashboard.vue'),
                    meta: { requiresAuth: true, requiresAdmin: true }
                },
                {
                    path: '/user/dashboard',
                    name: 'Userdashboard',
                    component: () => import('@/views/user/Dashboard.vue'),
                    meta: { requiresAuth: true, requiresUser: true }
                },
                {
                    path: '/admin/user',
                    name: 'addUser',
                    component: () => import('@/views/admin/User/User.vue'),
                    meta: { requiresAuth: true, requiresAdmin: true }
                },
                {
                    path: '/admin/section',
                    name: 'section',
                    component: () => import('@/views/admin/Section/Section.vue'),
                    meta: { requiresAuth: true, requiresAdmin: true }
                },
                {
                    path: '/admin/SubSection',
                    name: 'subsection',
                    component: () => import('@/views/admin/Section/SubSection.vue'),
                    meta: { requiresAuth: true, requiresAdmin: true }
                },
                {
                    path: '/user/runningcontracts',
                    name: 'RunningContracts',
                    component: () => import('@/views/user/running_contract/RunningContract.vue'),
                    meta: { requiresAuth: true, requiresUser: true }
                },
                {
                    path: '/user/hiringcontracts',
                    name: 'HiringContracts',
                    component: () => import('@/views/user/hiring_contract/HiringContract.vue'),
                    meta: { requiresAuth: true, requiresUser: true }
                },
                {
                    path: '/admin/hiringcontracts',
                    name: 'AdminHiringContracts',
                    component: () => import('@/views/admin/hiring_contract/HiringContract.vue'),
                    meta: { requiresAuth: true, requiresAdmin: true }
                },
               
                {
                    path: '/pages/empty',
                    name: 'empty',
                    component: () => import('@/views/pages/Empty.vue')
                },
                {
                    path: '/pages/crud',
                    name: 'crud',
                    component: () => import('@/views/pages/Crud.vue')
                },
                {
                    path: '/documentation',
                    name: 'documentation',
                    component: () => import('@/views/pages/Documentation.vue')
                },
                
                {
                    path: '/user/ItemCrud',
                    name: 'ItemCrud',
                    component: () => import('@/views/user/ItemCrud.vue')
                }
            ]
        },

        {
            path: '/pages/notfound',
            name: 'notfound',
            component: () => import('@/views/pages/NotFound.vue')
        },

        {
            path: '/auth/access',
            name: 'accessDenied',
            component: () => import('@/views/pages/auth/Access.vue')
        },
        {
            path: '/auth/error',
            name: 'error',
            component: () => import('@/views/pages/auth/Error.vue')
        }
    ]
});

// Navigation Guard
router.beforeEach((to, from, next) => {
    // Check if the route requires authentication
    const requiresAuth = to.matched.some((record) => record.meta.requiresAuth);
    const requiresAdmin = to.matched.some((record) => record.meta.requiresAdmin);
    const requiresUser = to.matched.some((record) => record.meta.requiresUser);

    // Get authentication status from Vuex store or localStorage
    const isAuthenticated = localStorage.getItem('isAuthenticated');
    const userRole = localStorage.getItem('userRole');

    if (requiresAuth && !isAuthenticated) {
        // Redirect to welcome page if not authenticated
        next({ name: 'welcome' });
    } else if (requiresAdmin && userRole !== 'admin') {
        // Redirect to access denied if not admin
        next({ name: 'accessDenied' });
    } else if (requiresUser && userRole !== 'user') {
        // Redirect to access denied if not user
        next({ name: 'accessDenied' });
    } else {
        // Continue to the requested route
        next();
    }
});

export default router;
