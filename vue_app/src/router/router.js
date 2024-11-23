import { createMemoryHistory, createRouter } from 'vue-router'

const routes = [
  { path: '/signup', component: () => import('../views/SignUpForm.vue') },
  { path: '/signin', component: () => import('../views/SignInForm.vue'), alias: ['/', ''] }
]

const router = createRouter({
  history: createMemoryHistory(),
  routes,
})

export default router