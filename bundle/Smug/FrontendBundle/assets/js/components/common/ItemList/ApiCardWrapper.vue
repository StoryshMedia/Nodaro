<template>
  <section>
    <div 
      v-for="(item, itemIndex) in items"
      :key="itemIndex"
      class="py-1.5"
    >
      <api-card
        :class="'h-100'"
        :slug="item.slug"
        :get-call="getCall"
      />
    </div>
  </section>
</template>

<script>
import { defineAsyncComponent } from "vue";
const ApiCard = defineAsyncComponent(() =>
  import("./ApiCard" /* webpackChunkName: "api-card" */)
);
export default {
  name: "ApiCardWrapper",
  components: {
    ApiCard
  },
  inject: ['dataset'],
  data() {
    return {
      items:  [],
      mode:  '',
      getCall:  '',
    }
  },
  async created() {
    await this.setProps();
  },
  methods: {
    setProps() {
      this.items = JSON.parse(this.dataset.items);
      this.getCall = this.dataset.getCall;
    }
  }
}
</script>