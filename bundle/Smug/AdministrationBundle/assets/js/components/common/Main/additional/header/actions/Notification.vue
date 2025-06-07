<template>
  <div class="dropdown shrink-0">
    <Popper
      :placement="'bottom-start'"
      offset-distance="8"
    >
      <button
        type="button"
        class="relative block p-2 rounded-full bg-white hover:text-primary"
      >
        <icon :icon-string="'IconBellBing'" />

        <span class="flex absolute w-3 h-3 right-0 top-0">
          <span
            class="animate-ping absolute -left-[3px] -top-[3px] inline-flex h-full w-full rounded-full bg-success/50 opacity-75"
          />
          <span class="relative inline-flex rounded-full w-[6px] h-[6px] bg-success" />
        </span>
      </button>
      <template #content="{ close }">
        <ul class="!py-0 text-dark divide-y">
          <li>
            <div class="flex items-center px-4 py-2 justify-between font-semibold">
              <h4 class="text-lg">
                Notification
              </h4>
            </div>
          </li>
          <div
            v-for="notification in []"
            :key="notification.id"
          >
            <li>
              <div class="group flex items-center px-4 py-2">
                <div class="grid place-content-center rounded">
                  <div class="w-12 h-12 relative">
                    <img
                      class="w-12 h-12 rounded-full object-cover"
                      :src="`/assets/images/${notification.profile}`"
                      alt=""
                    >
                    <span class="bg-success w-2 h-2 rounded-full block absolute right-[6px] bottom-0" />
                  </div>
                </div>
                <div class="pl-3 flex flex-auto">
                  <div class="pr-3">
                    <h6 v-html="notification.message" />
                    <span
                      class="text-xs block font-normal"
                      v-text="notification.time"
                    />
                  </div>
                  <button
                    type="button"
                    class="ml-auto text-neutral-300 hover:text-danger opacity-0 group-hover:opacity-100"
                    @click="removeNotification(notification.id)"
                  >
                    <icon :icon-string="'IconXCircle'" />
                  </button>
                </div>
              </div>
            </li>
          </div>
          <div v-if="notifications.length">
            <li>
              <div class="p-4">
                <button
                  class="btn btn-primary block w-full btn-small"
                  @click="close()"
                >
                  Read All Notifications
                </button>
              </div>
            </li>
          </div>
          <div v-if="!notifications.length">
            <li>
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
  import("../../../../../../../../../FrontendBundle/assets/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);

export default {
  name: "Notification",
  components: {
    Icon
  },
  data() {
    return{
      notifications: []
    }
  }
}

</script>