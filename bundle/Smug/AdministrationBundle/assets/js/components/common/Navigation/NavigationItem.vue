<template>
  <div>
    <div v-if="item.type === 0">
      <h2
        class="py-3 px-7 flex items-center font-extrabold bg-gray bg-opacity-30 text-dark -mx-4 mb-1 cursor-pointer"
        @click="setActiveDropdown(item.key)"
      >
        <icon
          v-if="item.icon && item.icon !== ''"
          :icon-string="item.icon"
          class="group-hover:!text-primary shrink-0 pr-3 w-8 h-8"
        />
        <span>{{ $t(item.label) }}</span>
      </h2>
      <vue-collapsible
        :is-open="activeDropdown === item.key"
      >
        <navigation-item
          v-for="(child, childIndex) in item.children"
          :key="childIndex"
          :item="child"
        />
      </vue-collapsible>
    </div>
    <div
      v-if="item.type === 1"
      class="menu nav-item"
    >
      <button
        type="button"
        class="nav-link group w-full hover:bg-gray hover:bg-opacity-50"
        :class="{ active: activeDropdown === item.key }"
        @click="setActiveDropdown(item.key)"
      >
        <div class="flex items-center">
          <icon
            v-if="item.icon && item.icon !== ''"
            :icon-string="item.icon"
            class="group-hover:!text-primary shrink-0"
          />
          <span class="pl-3 text-black">
            {{ $t(item.label) }}
          </span>
        </div>
        <div
          class="transform"
          :class="getIconClass(item.key)"
        >
          <icon :icon-string="'IconCaretDown'" />
        </div>
      </button>
      <vue-collapsible
        v-if="item.children.length > 0"
        :is-open="activeDropdown === item.key"
      >
        <ul class="sub-menu text-gray-500">
          <li
            v-for="(child, childIndex) in item.children"
            :key="childIndex"
          >
            <navigation-link
              :item="child"
            />
          </li>
        </ul>
      </vue-collapsible>
    </div>
    <ul
      v-if="item.type === 2"
      class="sub-menu text-gray-500"
    >
      <li>
        <navigation-link
          :item="item"
        />
      </li>
    </ul>
  </div>
</template>
  
  
<script>
import { defineAsyncComponent } from "vue";

const NavigationLink = defineAsyncComponent(() =>
  import("./NavigationLink.vue" /* webpackChunkName: "navigation-link" */)
);
const Icon = defineAsyncComponent(() =>
  import("@core/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);
const VueCollapsible = defineAsyncComponent(() =>
  import("vue-height-collapsible/vue3" /* webpackChunkName: "vue-collapsible" */)
);

export default {
  name: "NavigationItem",
  components: {
    VueCollapsible,
    NavigationLink,
    Icon
  },
  props: {
    item: {
      type: Object,
      required: true
    }
  },
  data() {
    return{
      activeDropdown: ''
    }
  },
  mounted() {
    if (window.localStorage.getItem('administration-active-navigation').includes(this.item.key)) {
      this.activeDropdown = this.item.key;
    }
  },
  methods: {
    setActiveDropdown(state) {
      this.activeDropdown = (this.activeDropdown === state) ? '' : state;
      
      if (window.localStorage.getItem('administration-active-navigation') === '') {
        window.localStorage.setItem('administration-active-navigation', this.activeDropdown);
      } else {
        if (window.localStorage.getItem('administration-active-navigation').includes(state)) {
          window.localStorage.setItem(
            'administration-active-navigation',
            window.localStorage.getItem('administration-active-navigation').replace(state, '')
          );
        } else {
          window.localStorage.setItem(
            'administration-active-navigation',
            window.localStorage.getItem('administration-active-navigation') + ',' + state
          );
        }
      }
    },
    getIconClass(state) {
      if (this.activeDropdown === '') {
        return '-rotate-90';
      }

      return (this.activeDropdown === state) ? 'rotate-0' : '-rotate-90';
    }
  }
}
</script>