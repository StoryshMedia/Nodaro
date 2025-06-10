<template>
  <div>
    <screen-lock v-if="isLocked === true" />
  </div>
</template>
  
<script>
import { defineAsyncComponent } from "vue";
import ApiService from 'SmugAdministration/js/services/api/api.service';
const ScreenLock = defineAsyncComponent(() =>
  import("./additional/lock/ScreenLock.vue" /* webpackChunkName: "administration-screen-lock" */)
);

export default {
  name: "Main",
  components: {
    ScreenLock
  },
  data() {
    return {
      isLocked: (localStorage.getItem('screen-locked') ?? 'false') === 'true'
    };
  },
  mounted() {
    window.setInterval(() => {
      this.checkLoggedIn()
    }, 90000);
    window.addEventListener('screen-locked-changed', (event) => {
      this.isLocked = (event.detail.storage === 'true');
    });
  },
  methods: {
    checkLoggedIn() {
      ApiService.get('/be/api/loggedIn')
        .then(response =>  {
        })
        .catch(error => {
          window.location.replace('/admin/login')
        });
    }
  }
}
</script>