<template>
  <div
    v-if="backgroundImage !== ''"
    class="fixed top-0 left-0 min-h-screen min-h-screen bg-cover w-full z-50 bg-gray-100 py-6 flex flex-col justify-center sm:py-12"
    :style="{ 'background-image': 'url(' + backgroundImage + ')'}"
  >
    <div
      v-if="user.id"
      class="relative px-4 py-10 mx-auto bg-white w-full md:w-2/3 lg:w-1/2 xl:w-1/3 shadow-lg sm:rounded-3xl sm:p-20 bg-clip-padding bg-opacity-10 border border-gray-200"
      style="backdrop-filter: blur(20px);"
    >
      <div class="text-white">
        <div class="mb-8 flex flex-col items-center">
          <img
            :src="getImage(image)"
            :alt="user.username"
            class="w-32 h-32 rounded-full object-cover saturate-50 group-hover:saturate-100"
          >
          <h1 class="mb-2 mt-5 text-2xl">
            {{ user.username }}
          </h1>
          {{ $t('UNLOCK_HINT') }}
        </div>
        <form
          @submit.prevent="unlock"
        >
          <div class="mb-4 text-lg text-center">
            <input
              class="rounded-3xl text-dark border-none bg-white px-6 py-2 text-center text-inherit placeholder-slate-200 shadow-lg outline-none backdrop-blur-md"
              type="Password"
              name="name"
              :value="userData.password"
              :placeholder="$t('PASSWORD')"
              @change="setPassword($event)"
            >
          </div>
          <div class="mt-8 flex justify-center text-lg text-black">
            <button
              type="submit"
              class="rounded-3xl bg-primary px-10 py-2 text-white shadow-xl backdrop-blur-md transition-colors duration-300 hover:bg-dark"
            >
              {{ $t('UNLOCK') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
  
<script>
import ApiService from '../../../../../services/api/api.service';
import ImageService from "../../../../../services/image/image.service";

export default {
  name: "ScreenLock",
  data() {
    return {
      user: {},
      backgroundImage: '',
      userData: {
        password: ''
      }
    };
  },
  mounted() {
    this.getBackgroundImage();
    this.getUser();
  },
  methods: {
    getImage() {
      return ImageService.getImageFromItem(this.user);
    },
    getBackgroundImage() {
      return this.backgroundImage = ImageService.getLockScreenBackground();
    },
    setPassword(password) {
      this.userData.password = password.target.value;
    },
    lockScreen(event) {
      event.preventDefault();
      localStorage.setItem('screen-locked', true)

      window.dispatchEvent(new CustomEvent('screen-locked-changed', {
        detail: {
          storage: localStorage.getItem('screen-locked')
        }
      }));
    },
    logout() {
      this.$store.dispatch("auth/logout").catch((err) => {
        this.hasLoginError = true;
      });
    },
    getUser() {
      ApiService.get('/be/api/custom/profile')
        .then(result =>  {
          this.user = result
        })
        .catch(error => {
        });
    },
    unlock() {
      ApiService.post('/be/api/custom/screen/unlock', this.userData)
        .then(result =>  {
          if (result.success ?? false === true) {
            localStorage.setItem('screen-locked', false)

            window.dispatchEvent(new CustomEvent('screen-locked-changed', {
              detail: {
                storage: localStorage.getItem('screen-locked')
              }
            }));
          }
        })
        .catch(error => {
        });
    }
  }
}

</script>