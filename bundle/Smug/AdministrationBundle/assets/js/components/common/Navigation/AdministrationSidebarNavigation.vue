<template>
  <nav class="sidebar fixed min-h-screen h-full top-0 bottom-0 shadow-lg z-50 transition-all duration-300">
    <div class="bg-white h-full">
      <perfect-scrollbar
        v-if="navigation !== null"
        :options="{
          swipeEasing: true,
          wheelPropagation: false,
        }"
        class="h-100vh relative"
      >
        <div>
          <div class="flex justify-between items-center px-4 py-3">
            <a
              href="/admin"
              class="main-logo flex items-center shrink-0"
            >
              <img
                class="w-8 ml-3 flex-none"
                src="/administration/img/logo/logo-navigation.svg"
                alt=""
              >
              <span class="text-2xl ml-3 font-semibold align-middle lg:inline">Nodaro</span>
            </a>
            <a
              href="javascript:;"
              class="collapse-icon w-8 h-8 rounded-full flex items-center hover:bg-gray-50 transition duration-300 hover:text-primary"
              style="rotate: 90deg;"
              @click="toggleSidebar()"
            >
              <icon
                :icon-string="'IconCaretDown'"
                class="m-auto"
              />
            </a>
          </div>
          <ul class="relative font-semibold space-y-0.5 p-4 py-0">
            <li
              v-for="(item, itemIndex) in navigation"
              :key="itemIndex"
              class="menu nav-item"
            >
              <navigation-item
                :item="item"
              />
            </li>
          </ul>
        </div>
      </perfect-scrollbar>
    </div>
  </nav>
</template>
  
  
<script>
import { defineAsyncComponent } from "vue";
import ApiService from '@SmugAdministration/js/services/api/api.service';

const NavigationItem = defineAsyncComponent(() =>
  import("./NavigationItem.vue" /* webpackChunkName: "navigation-item" */)
);
const Icon = defineAsyncComponent(() =>
  import("@core/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);

export default {
  name: "AdministrationSidebarNavigation",
  components: {
    NavigationItem,
    Icon
  },
  inject: ['dataset'],
  data() {
    return{
      navigation: null
    }
  },
  async created() {
    await this.getNavigation();
  },
  mounted() {
    if (window.localStorage.getItem('administration-active-navigation') === null) {
      window.localStorage.setItem('administration-active-navigation', '');
    }
  },
  methods: {
    getNavigation() {
      this.isLoading = true;

      ApiService.get('/be/api/navigation')
        .then(result =>  {
          this.isLoading = false;
          this.navigation = result;
        })
        .catch(error => {
          this.isLoading = false;
        })
        .then(function () {
        });
    },
    toggleSidebar() {
      document.getElementById('main-section').classList.add("toggle-sidebar");
    }
  }
}
</script>