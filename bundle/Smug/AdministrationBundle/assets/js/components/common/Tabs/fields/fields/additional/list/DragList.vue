<template>
  <draggable
    v-model="items"
    class="bg-white cursor-pointer rounded-lg shadow divide-y divide-gray-200 w-full"
    ghost-class="ghost"
    :group="{ name: listName, transitionMode: true }"
    :empty-insert-threshold="200"
    :transition-mode="true"
    @change="onOrderChanged()"
  >
    <div 
      v-for="(item, itemIndex) in items"
      :key="itemIndex"
      class="px-6 py-4"
    >
      <div
        class="flex justify-between"
      >
        <span class="font-semibold text-sm">{{ getTitle(item) }}</span>
        <div
          v-if="config.controls && config.controls.length > 0"
          class="flex justify-end"
        >
          <span 
            v-for="(control, controlindex) in config.controls"
            :key="controlindex"
          >
            <control
              :item="item"
              :type="control.type"
              :config="control.config"
              @updateValue="handleAction($event, item, itemIndex)"
            />
          </span>
        </div>
      </div>
    </div>
  </draggable>
</template>

<script>
import { VueDraggableNext } from 'vue-draggable-next';
import { defineAsyncComponent } from "vue";
const Control = defineAsyncComponent(() =>
  import("../table/Control.vue" /* webpackChunkName: "administration-table-control" */)
);

export default {
  name: "DragList",
  components: {
    draggable: VueDraggableNext,
    Control
  },
  props: {
    items: {
      type: Object,
      required: true
    },
    config: {
      type: Object,
      required: false,
      default: () => ({})
    },
    listName:{
      type: String,
      required: false,
      default: 'items'
    },
    titleIdentifier:{
      type: String,
      required: false,
      default: 'title'
    },
    subIdentifier:{
      type: String,
      required: false,
      default: ''
    }
  },
  methods: {
    onOrderChanged() {
      this.$emit('order-changed', this.items);
    },
    handleAction(event, item, itemIndex) {
      this.$emit('control-emitted', {event: event, item: item, itemIndex: itemIndex});
    },
    getTitle(item) {
      if (this.subIdentifier !== '') {
        return item[this.subIdentifier][this.titleIdentifier];
      }

      return item[this.titleIdentifier];
    }
  }
}
</script>

<style scoped>
.ghost {
  opacity: 0.5;
  background: #c8ebfb;
}
</style>