<template>
  <div class="sm:mr-auto">
    <form
      class="sm:relative absolute inset-x-0 sm:top-0 top-1/2 sm:translate-y-0 -translate-y-1/2 sm:mx-0 mx-4 z-10 sm:block hidden"
      :class="{ '!block': search }"
      @submit.prevent="search = false"
    >
      <div class="relative">
        <input
          type="text"
          class="form-input pl-9 sm:pr-4 pr-9 peer sm:bg-transparent bg-gray-100 placeholder:tracking-widest"
          placeholder="Search..."
        >
        <button
          type="button"
          class="absolute w-9 h-9 inset-0 right-auto appearance-none peer-focus:text-primary"
        >
          <icon
            :icon-string="'IconSearch'"
            class="mx-auto"
          />
        </button>
        <button
          type="button"
          class="hover:opacity-80 sm:hidden block absolute top-1/2 -translate-y-1/2 right-2"
          @click="search = false"
        >
          <icon :icon-string="'IconXCircle'" />
        </button>
      </div>
    </form>

    <button
      type="button"
      class="search_btn sm:hidden p-2 rounded-full bg-white"
      @click="search = !search"
    >
      <icon
        :icon-string="'IconSearch'"
        class="w-4.5 h-4.5 mx-auto "
      />
    </button>
  </div>
</template>
  
<script>
import { defineAsyncComponent } from "vue";

const Icon = defineAsyncComponent(() =>
  import("../../../../../../../../FrontendBundle/assets/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);

export default {
  name: "Search",
  components: {
    Icon
  }
}

</script>