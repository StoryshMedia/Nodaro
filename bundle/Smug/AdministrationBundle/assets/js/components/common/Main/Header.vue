<template>
  <header
    class="z-40"
  >
    <div class="shadow-sm">
      <div class="relative bg-white flex w-full items-center px-5 py-2.5">
        <div class="horizontal-logo justify-between items-center mr-2">
          <a
            href="/admin"
            class="main-logo flex items-center shrink-0"
          >
            <img
              class="w-8 -ml-1 inline"
              src="/administration/img/logo/logo-navigation.svg"
              alt=""
            >
            <span
              class="text-2xl ml-3 font-semibold align-middle hidden md:inline transition-all duration-300"
            >Nodaro</span>
          </a>

          <a
            href="javascript:;"
            class="collapse-icon flex-none hover:text-primary flex ml-2 p-2 rounded-full bg-white-light/40"
            @click="toggleSidebar()"
          >
            <icon
              :icon-string="'IconMenu'"
              class="w-5 h-5"
            />
          </a>
        </div>
        <div class="mr-2 sm:block">
          <ul class="flex items-center space-x-2">
            <li>
              <a
                href="/apps/calendar"
                class="block p-2 rounded-full bg-white hover:text-primary"
              >
                <icon
                  :icon-string="'IconCalendar'"
                  class="w-5 h-5"
                />
              </a>
            </li>
            <li>
              <a
                href="/apps/todolist"
                class="block p-2 rounded-full bg-white hover:text-primary"
              >
                <icon :icon-string="'IconEdit'" />
              </a>
            </li>
            <li>
              <a
                href="/apps/chat"
                class="block p-2 rounded-full bg-white"
              >
                <icon :icon-string="'IconChatNotification'" />
              </a>
            </li>
          </ul>
        </div>
        <div
          class="sm:flex-1 sm:ml-0 ml-auto flex items-center space-x-1.5 lg:space-x-2"
        >
          <Search />

          <Actions />
        </div>
      </div>
    </div>
  </header>
</template>
  
<script>
import { defineAsyncComponent } from "vue";

const Icon = defineAsyncComponent(() =>
  import("@core/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);
const Search = defineAsyncComponent(() =>
  import("./additional/header/Search.vue" /* webpackChunkName: "administration-header-search" */)
);
const Actions = defineAsyncComponent(() =>
  import("./additional/header/Actions.vue" /* webpackChunkName: "administration-header-actions" */)
);

export default {
  name: "Header",
  components: {
    Icon,
    Actions,
    Search
  },
  methods: {
    toggleSidebar() {
      document.getElementById('main-section').classList.remove("toggle-sidebar");
    }
  }
}
</script>