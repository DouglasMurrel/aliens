<template>
  <h1>Hello App!</h1>
  <p>
    <strong>Current route path:</strong> {{ $route.fullPath }}
  </p>
  <nav v-if="!('email' in userData)">
    <RouterLink to="/signup">Sign Up</RouterLink>
    <RouterLink to="/signin">Sign In</RouterLink>
  </nav>
  <nav v-else>
    {{ userData.fullname }}
  </nav>
  <main>
    <RouterView />
  </main>
</template>

<script>
export default {
        name: 'App',
        computed: {
            userData () {
                return this.$store.state.userData
            }
        }
}
</script>