<template>
  <div class="max-w-md w-full p-6 text-white">
    <h1 class="text-3xl font-semibold mb-6 text-center">
      {{ $t('LOGIN') }}
    </h1>
      
    <form 
      class="space-y-4"
      @submit.prevent="performLogin"
    >
      <!-- Your form elements go here -->
      <div>
        <label
          for="username"
          class="block text-sm font-medium text-gray-700"
        >{{ $t('USER_NAME_OR_EMAIL') }}</label>
        <input
          id="username"
          v-model="loginData.username"
          type="text"
          name="username"
          class="mt-1 p-2 w-full text-dark border rounded-md focus:border-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 transition-colors duration-300"
        >
      </div>
      <div>
        <label
          for="password"
          class="block text-sm font-medium text-gray-700"
        >{{ $t('PASSWORD') }}</label>
        <input
          id="password"
          v-model="loginData.password"
          type="password"
          name="password"
          class="mt-1 p-2 w-full text-dark border rounded-md focus:border-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 transition-colors duration-300"
        >
      </div>
      <div class="text-center">
        <button
          type="submit"
          class="rounded-3xl bg-primary px-10 py-2 text-white shadow-xl backdrop-blur-md transition-colors duration-300 hover:bg-white hover:text-dark"
        >
          {{ $t('LOGIN') }}
        </button>
      </div>
    </form>
  </div>
</template>
  
  
<script>
export default {
  name: "AdministrationSidebarNavigation",
  components: {
  },
  inject: ['dataset'],
  data() {
    return{
      hasLoginError: false,
      loginData: {
        username: '',
        password: ''
      }
    }
  },
  mounted() {
    window.localStorage.setItem('be-token', '')
  },
  methods: {
    performLogin() {
      this.$store.dispatch("auth/login", this.loginData).catch((err) => {
        this.hasLoginError = true;
      });
    }
  }
}
</script>