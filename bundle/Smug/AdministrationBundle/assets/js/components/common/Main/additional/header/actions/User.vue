<template>
  <div class="dropdown shrink-0">
    <Popper
      v-if="user.id"
      :placement="'bottom-start'"
      offset-distance="8"
      class="!block"
    >
      <button
        type="button"
        class="relative group block"
      >
        <img
          :src="getImage(image)"
          :alt="user.username"
          class="w-9 h-9 rounded-full object-cover saturate-50 group-hover:saturate-100"
        >
      </button>
      <template #content="{ close }">
        <ul class="text-dark !py-0 w-64 font-semibold bg-white">
          <li>
            <div class="flex items-center px-4 py-4">
              <div class="flex-none">
                <img
                  :src="getImage(image)"
                  :alt="user.username"
                  class="rounded-md w-10 h-10 object-cover"
                >
              </div>
              <div class="pl-4 truncate">
                <h4 class="text-base">
                  {{ user.username }}
                </h4>
                <a
                  class="text-black/60 hover:text-primary"
                  :href="'mailto:' + user.email "
                >{{ user.email }}</a>
              </div>
            </div>
          </li>
          <li>
            <a
              href="/admin/smug/system/profile/detail"
              @click="close()"
            >
              <icon
                :icon-string="'IconUser'"
                class="w-4.5 h-4.5 mr-2 shrink-0"
              />

              {{ $t('MY_PROFILE') }}
            </a>
          </li>
          <li>
            <a
              href="/apps/mailbox"
              @click="close()"
            >
              <icon
                :icon-string="'IconMail'"
                class="w-4.5 h-4.5 mr-2 shrink-0"
              />

              {{ $t('INBOX') }}
            </a>
          </li>
          <li>
            <a
              href="#"
              @click="lockScreen($event)"
            >
              <icon
                :icon-string="'IconLockDots'"
                class="w-4.5 h-4.5 mr-2 shrink-0"
              />

              {{ $t('LOCK_SCREEN') }}
            </a>
          </li>
          <li class="border-t border-white-light">
            <a
              href="#"
              class="text-danger !py-3"
              @click="logout($event)"
            >
              <icon
                :icon-string="'IconLogout'"
                class="w-4.5 h-4.5 mr-2 rotate-90 shrink-0"
              />

              {{ $t('SIGN_OUT') }}
            </a>
          </li>
        </ul>
      </template>
    </Popper>
  </div>
</template>
  
<script>
import ApiService from '../../../../../../services/api/api.service';
import ImageService from "../../../../../../services/image/image.service";

export default {
  name: "User",
  data() {
    return {
      user: {}
    };
  },
  mounted() {
    this.getUser();
  },
  methods: {
    getImage() {
      return ImageService.getImageFromItem(this.user);
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
    logout(event) {
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
    }
  }
}

</script>