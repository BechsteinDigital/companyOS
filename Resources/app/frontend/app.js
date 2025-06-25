import '@coreui/coreui/dist/css/coreui.min.css';

import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import { createPinia } from 'pinia'
import axios from 'axios'

// CoreUI Icons
import { iconsSet } from './icons' // <-- Dein optimiertes Icons-Modul
import CIcon from '@coreui/icons-vue'

// Komponenten
import App from './js/layout/App.vue'
import Login from './js/components/Login.vue'
import Dashboard from './js/components/Dashboard.vue'
import Users from './js/components/Users.vue'
import UserForm from './js/components/UserForm.vue'
import Roles from './js/components/Roles.vue'
import RoleForm from './js/components/RoleForm.vue'
import Plugins from './js/components/Plugins.vue'
import Settings from './js/components/Settings.vue'
import Webhooks from './js/components/Webhooks.vue'
import WebhookForm from './js/components/WebhookForm.vue'
import System from './js/components/System.vue'
import Profile from './js/components/Profile.vue'

// Routing
const routes = [
  { path: '/', redirect: '/login' },
  { path: '/login', component: Login },
  { path: '/dashboard', component: Dashboard, meta: { requiresAuth: true } },
  { path: '/users', component: Users, meta: { requiresAuth: true } },
  { path: '/users/new', component: UserForm, meta: { requiresAuth: true } },
  { path: '/users/:id/edit', component: UserForm, meta: { requiresAuth: true } },
  { path: '/roles', component: Roles, meta: { requiresAuth: true } },
  { path: '/roles/new', component: RoleForm, meta: { requiresAuth: true } },
  { path: '/roles/:id/edit', component: RoleForm, meta: { requiresAuth: true } },
  { path: '/plugins', component: Plugins, meta: { requiresAuth: true } },
  { path: '/settings', component: Settings, meta: { requiresAuth: true } },
  { path: '/webhooks', component: Webhooks, meta: { requiresAuth: true } },
  { path: '/webhooks/new', component: WebhookForm, meta: { requiresAuth: true } },
  { path: '/webhooks/:id/edit', component: WebhookForm, meta: { requiresAuth: true } },
  { path: '/system', component: System, meta: { requiresAuth: true } },
  { path: '/profile', component: Profile, meta: { requiresAuth: true } }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Auth Guard
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('auth_token')
  if (to.meta.requiresAuth && !token) {
    next('/login')
  } else if (to.path === '/login' && token) {
    next('/dashboard')
  } else {
    next()
  }
})

// Axios global konfigurieren
axios.defaults.baseURL = '/api'
axios.interceptors.request.use(config => {
  const token = localStorage.getItem('auth_token')
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

axios.interceptors.response.use(
  res => res,
  err => {
    if (err.response?.status === 401) {
      localStorage.removeItem('auth_token')
      localStorage.removeItem('refresh_token')
      localStorage.removeItem('user')
      router.push('/login')
    }
    return Promise.reject(err)
  }
)

// App erstellen
const app = createApp(App)
const pinia = createPinia()

app.use(router)
app.use(pinia)

// CoreUI Icons global verfügbar machen
app.component('CIcon', CIcon)
app.provide('icons', iconsSet)

// Globale $api-Methoden
app.config.globalProperties.$api = {
  // Auth
  login: (credentials) => axios.post('/oauth/token', credentials),
  logout: () => axios.post('/api/oauth2/revoke'),
  refreshToken: (refreshToken) => axios.post('/api/auth/refresh', { refresh_token: refreshToken }),
  getProfile: () => axios.get('/api/auth/profile'),

  // Users
  getUsers: (params) => axios.get('/api/users/users', { params }),
  getUser: (id) => axios.get(`/api/users/users/${id}`),
  createUser: (data) => axios.post('/api/users/users', data),
  updateUser: (id, data) => axios.put(`/api/users/users/${id}`, data),
  deleteUser: (id) => axios.delete(`/api/users/users/${id}`),

  // Roles
  getRoles: (params) => axios.get('/api/roles/roles', { params }),
  getRole: (id) => axios.get(`/api/roles/roles/${id}`),
  createRole: (data) => axios.post('/api/roles/roles', data),
  updateRole: (id, data) => axios.put(`/api/roles/roles/${id}`, data),
  deleteRole: (id) => axios.delete(`/api/roles/roles/${id}`),
  getUserRoles: (userId) => axios.get(`/api/roles/roles/user/${userId}`),
  assignRole: (roleId, userId) => axios.post(`/api/roles/roles/${roleId}/assign/${userId}`),
  removeRole: (roleId, userId) => axios.delete(`/api/roles/roles/${roleId}/remove/${userId}`),

  // Plugins
  getPlugins: (params) => axios.get('/api/plugins/plugins', { params }),
  getPlugin: (id) => axios.get(`/api/plugins/plugins/${id}`),
  getLoadedPlugins: () => axios.get('/api/plugins/plugins/loaded'),
  installPlugin: (data) => axios.post('/api/plugins/plugins', data),
  activatePlugin: (id) => axios.post(`/api/plugins/plugins/${id}/activate`),
  deactivatePlugin: (id) => axios.post(`/api/plugins/plugins/${id}/deactivate`),
  deletePlugin: (id) => axios.delete(`/api/plugins/plugins/${id}`),
  updatePlugin: (id) => axios.post(`/api/plugins/plugins/${id}/update`),

  // Settings
  getSettings: () => axios.get('/api/settings/'),
  initializeSettings: () => axios.post('/api/settings/initialize'),
  updateSettings: (data) => axios.put('/api/settings/', data),
  getSalutations: () => axios.get('/api/settings/salutations'),
  getSalutation: (type) => axios.get(`/api/settings/salutations/${type}`),

  // Webhooks
  getWebhooks: (params) => axios.get('/api/webhooks', { params }),
  getWebhook: (id) => axios.get(`/api/webhooks/${id}`),
  createWebhook: (data) => axios.post('/api/webhooks', data),
  updateWebhook: (id, data) => axios.put(`/api/webhooks/${id}`, data),
  deleteWebhook: (id) => axios.delete(`/api/webhooks/${id}`),

  // User Permissions
  checkPermission: (userId, permission) => axios.get(`/api/users/user-permissions/check/${userId}/${permission}`),
  getUserPermissions: (userId) => axios.get(`/api/users/user-permissions/list/${userId}`),
  getProtectedResource: () => axios.get('/api/users/user-permissions/protected-resource')
}

// Mounten
app.mount('#app') 