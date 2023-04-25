import { createRouter, createWebHistory } from "vue-router";

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: '/',
            alias: ['/home', '/contact'],
            name: 'home',
            component: () => import('../pages/Home.vue'),
        },
        {
            path: '/privacy-policy',
            name: 'privacy-policy',
            component: () => import('../pages/PrivacyPolicy.vue'),
        },
        {
            path: '/terms',
            name: 'terms',
            component: () => import('../pages/TermsOfService.vue'),
        },
        {
            path: '/patreons',
            name: 'Supporters',
            component: () => import('../pages/Supporters.vue'),
        },
        {
            path: '/rules',
            name: 'rules',
            component: () => import('../pages/Rules.vue'),
        },
        {
            path: '/users/profile',
            name: 'profile',
            component: () => import('../pages/users/Profile.vue'),
        },
        {
            path: '/link/external:data(.*)*',
            name: 'externalLink',
            component: () => import('../pages/ExternalLink.vue'),
        },
        {
            path: '/mods/:game',
            component: () => import('../pages/mods/Mods.vue'),
            children: [
                {
                    name: 'mods',
                    path: '/mods/:game',
                    component: () => import('../pages/mods/Index.vue'),
                },
                {
                    name: 'manageMods',
                    path: '/mods/:game/manage',
                    component: () => import('../pages/mods/Manage.vue'),
                },
                {
                    name: 'favoriteMods',
                    path: '/mods/:game/favorites',
                    component: () => import('../pages/mods/Favorites.vue'),
                },
                {
                    name: 'showMod',
                    path: '/mods/:game/:mod',
                    component: () => import('../pages/mods/Show.vue'),
                },
                {
                    name: 'createMod',
                    path: '/mods/:game/create',
                    component: () => import('../pages/mods/Create.vue'),
                },
                {
                    name: 'editMod',
                    path: '/mods/:game/:mod/edit',
                    component: () => import('../pages/mods/Create.vue'),
                },
                {
                    name: 'duplicateMod',
                    path: '/mods/:game/:mod/duplicate',
                    component: () => import('../pages/mods/Create.vue'),
                },
                {
                    name: 'editModAttrs',
                    path: '/mods/:game/:mod/edit/attributions',
                    component: () => import('../pages/mods/Attributes.vue'),
                },
                {
                    name: 'editModDownloads',
                    path: '/mods/:game/:mod/edit/downloads',
                    component: () => import('../pages/mods/Downloads.vue'),
                },
                {
                    name: 'editModPreview',
                    path: '/mods/:game/:mod/edit/preview',
                    component: () => import('../pages/mods/Preview.vue'),
                }
            ],
        },
        {
            path: '/staff',
            name: 'staff',
            component: () => import('../pages/staff/Index.vue'),
        },
        {
            path: '/staff/users',
            name: 'Manage Users',
            component: () => import('../pages/staff/ManageUsers.vue'),
        },
        {
            path: '/staff/images',
            name: 'Manage Images',
            component: () => import('../pages/staff/ManageImages.vue'),
        },
        {
            path: '/staff/moty',
            name: 'Manage Modder of The Year',
            component: () => import('../pages/staff/ManageMOTY.vue'),
        },
        {
            path: '/moty',
            name: 'Modder of The Year',
            component: () => import('../pages/MOTY.vue'),
        },
    ],
});

export default router;
