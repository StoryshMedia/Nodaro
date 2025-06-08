<template>
  <div class="dropdown shrink-0">
    <Popper
      :placement="'bottom-end'"
      offset-distance="8"
    >
      <button
        type="button"
        class="block p-2 rounded-full bg-white hover:text-primary"
      >
        <icon :icon-string="'IconMailDot'" />
      </button>
      <template #content="{ close }">
        <ul class="top-11 !py-0 text-dark">
          <li class="mb-5">
            <div class="overflow-hidden relative rounded-t-md !p-5 text-white">
              <div
                class="absolute h-full w-full bg-[url('/assets/images/menu-heade.jpg')] bg-no-repeat bg-center bg-cover inset-0"
              />
              <h4 class="font-semibold relative z-10 text-lg">
                Messages
              </h4>
            </div>
          </li>
          <div
            v-for="msg in []"
            :key="msg.id"
          >
            <li>
              <div class="flex items-center py-3 px-5">
                <div v-html="msg.image" />
                <span class="px-3">
                  <div
                    class="font-semibold text-sm"
                    v-text="msg.title"
                  />
                  <div v-text="msg.message" />
                </span>
                <span
                  class="font-semibold bg-white-dark/20 rounded text-dark/60 px-1 ml-auto whitespace-pre mr-2"
                  v-text="msg.time"
                />
                <button
                  type="button"
                  class="text-neutral-300 hover:text-danger"
                  @click="removeMessage(msg.id)"
                >
                  <icon :icon-string="'IconXCircle'" />
                </button>
              </div>
            </li>
          </div>
          <div v-if="messages.length">
            <li class="border-t border-white-light text-center mt-5">
              <div
                class="flex items-center py-4 px-5 text-primary font-semibold group justify-center cursor-pointer"
                @click="close()"
              >
                <span class="group-hover:underline mr-1">VIEW ALL ACTIVITIES</span>

                <icon
                  :icon-string="'IconArrowLeft'"
                  class="group-hover:translate-x-1 transition duration-300 ml-1"
                />
              </div>
            </li>
          </div>
          <div v-if="!messages.length">
            <li class="mb-5">
              <div class="!grid place-content-center hover:!bg-transparent text-lg min-h-[200px]">
                <div class="mx-auto ring-4 ring-primary/30 rounded-full mb-4 text-primary">
                  <icon
                    :icon-string="'IconInfoCircle'"
                    class="w-10 h-10"
                  />
                </div>
                No data available.
              </div>
            </li>
          </div>
        </ul>
      </template>
    </Popper>
  </div>
</template>
  
<script>
import { defineAsyncComponent } from "vue";

const Icon = defineAsyncComponent(() =>
  import("@core/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);

export default {
  name: "Mail",
  components: {
    Icon
  },
  data() {
    return{
      messages: []
    }
  }
}

</script>