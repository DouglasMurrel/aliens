<template>
  <div class="fullHeight" :class="{wait: ajaxWaiting}">
    <h1>Hello App!</h1>
    <p>
      <strong>Current route path:</strong> {{ $route.fullPath }}
    </p>
    <nav v-if="loggedIn && !ajaxWaiting">
      {{ userData.fullname }}
    </nav>
    <nav v-else-if="!ajaxWaiting">
      <RouterLink to="/signup">Sign Up</RouterLink>
      <RouterLink to="/signin">Sign In</RouterLink>
    </nav>
    <main>
      <RouterView />
    </main>
  </div>
</template>

<script>
export default {
        name: 'App',
        computed: {
            userData () {
                return this.$store.state.userData
            },
            loggedIn () {
                return this.$store.state.loggedIn
            },
            ajaxWaiting () {
                return this.$store.state.ajaxWaiting
            }
        },
        beforeCreate() {
	   const params = new Proxy(new URLSearchParams(window.location.search), {
              get: (searchParams, prop) => searchParams.get(prop),
	   });
           if (params.token) {
               localStorage
                    .setItem('authToken', params.token)
                    .setItem('refreshToken', params.refreshToken);
               window.location.href = '/'
           }
        }
}
</script>

<style>
 .fullHeight {
    height: 100%;
 }
 .wait {
    cursor: wait;
 }
</style>