<template>
  <div class="flex">
    <Language />
    <Mail />
    <Notification />
    <User />
  </div>
</template>
  
<script>
import { defineAsyncComponent } from "vue";

const Language = defineAsyncComponent(() =>
  import("./actions/Language.vue" /* webpackChunkName: "administration-header-action-language" */)
);
const Mail = defineAsyncComponent(() =>
  import("./actions/Mail.vue" /* webpackChunkName: "administration-header-action-mail" */)
);
const Notification = defineAsyncComponent(() =>
  import("./actions/Notification.vue" /* webpackChunkName: "administration-header-action-notification" */)
);
const User = defineAsyncComponent(() =>
  import("./actions/User.vue" /* webpackChunkName: "administration-header-action-user" */)
);

export default {
  name: "Actions",
  components: {
    Language,
    User,
    Notification,
    Mail
  }
}

</script>