<template>
  <div v-if="isLoading">
    <div class="loading-screen">
      <div class="loading-spinner"></div>
    </div>
  </div>
  <div v-if="loggedIn">
    <AppSidebar />
    <div class="wrapper d-flex flex-column min-vh-100">
      <AppHeader />
      <div class="body flex-grow-1">
        <CContainer class="px-4" lg>
          <router-view />
        </CContainer>
      </div>
      <AppFooter />
    </div>
  </div>
  <div v-else>
    <router-view />
  </div>
</template>

<script>
import AppSidebar from '../components/AppSidebar.vue'
import AppHeader from '../components/AppHeader.vue'
import AppFooter from '../components/AppFooter.vue'

export default {
  name: 'App',
  data() {
    return {
      isLoading: true,
      loggedIn: false
    }
  },
  created() {
    // Initial prüfen, ob Token vorhanden ist
    const token = localStorage.getItem('auth_token')
    this.loggedIn = !!token
    this.isLoading = false
  },
  watch: {
    // Wenn sich die Route ändert, prüfe erneut den Login-Status
    $route(to, from) {
      const token = localStorage.getItem('auth_token')
      this.loggedIn = !!token
    }
  }
}
</script>

<style>
#app {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* CoreUI Custom Styles */
.c-sidebar {
  background: #3c4b64;
}

.c-sidebar .c-sidebar-nav-link {
  color: #fff;
}

.c-sidebar .c-sidebar-nav-link:hover {
  background: rgba(255, 255, 255, 0.1);
}

.c-sidebar .c-sidebar-nav-link.c-active {
  background: #321fdb;
}

.c-header {
  background: #fff;
  border-bottom: 1px solid #d8dbe0;
}

.c-avatar {
  width: 2rem;
  height: 2rem;
  border-radius: 50%;
  overflow: hidden;
}

.c-avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Custom utility classes */
.text-medium-emphasis {
  color: #6c757d !important;
}

.min-vh-100 {
  min-height: 100vh;
}
</style> 