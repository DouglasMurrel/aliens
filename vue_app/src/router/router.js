import { createWebHistory, createRouter } from 'vue-router'

const routes = [
  { path: '/signup', name: 'signup', component: () => import('../views/SignUpForm.vue') },
  { path: '/', name: 'signin', component: () => import('../views/SignInForm.vue') },
  { path: '/signin', redirect:'/' },
  { path: '/admin/all-orders', name: 'admin-all-orders', component: () => import('../views/SignInForm.vue') },
  { path: '/admin/food', name: 'admin-food', component: () => import('../views/SignInForm.vue') },
  { path: '/recover', name: 'recover', component: () => import('../views/RecoverForm.vue') },
  { path: '/confirm_recover_request', name: 'confirm_recover_request', component: () => import('../views/ConfirmRecoverForm.vue') },
  { path: '/:pathMatch(.*)*', name: 'NotFound', component:  () => import('../views/NotFound.vue') }
]

const router = createRouter({
    linkActiveClass: 'color-grey',
    history: createWebHistory(),
    routes
});

export default router