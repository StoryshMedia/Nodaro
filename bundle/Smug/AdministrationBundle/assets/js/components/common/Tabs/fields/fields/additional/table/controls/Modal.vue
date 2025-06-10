<template>
  <div>
    <button
      type="button"
      class="rounded-full w-5 h-5 mr-2 p-0 text-dark transition-all duration-300"
      @click="showModalClick()"
    >
      <icon
        :icon-string="config.icon"
        :class="'w-18 h-18'"
      />
    </button>
    <data-modal
      v-if="showModal === true"
      :item-data="item"
      :template="config.template.data"
      :save-button-text="'SAVE'"
      :abort-button-text="'ABORT'"
      :headline="config.headline"
      @edit-reaction="handleAction($event)"
    />
  </div>
</template>

<script>
import ApiService from 'SmugAdministration/js/services/api/api.service';
import { defineAsyncComponent } from "vue";
const Icon = defineAsyncComponent(() =>
  import("@core/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);
const DataModal = defineAsyncComponent(() =>
  import("../../../../../../Modal/DataModal.vue" /* webpackChunkName: "administration-modal-dialog" */)
);

export default {
  name: "Modal",
  components: {
    Icon,
    DataModal
  },
  props: {
    item:{
      type: String,
      required: true
    },
    type:{
      type: String,
      required: true
    },
    config:{
      type: Object,
      required: true
    }
  },
  data() {
    return {
      showModal: false
    }
  },
  methods: {
    showModalClick() {
      this.showModal = !this.showModal;
    },
    handleAction(event) {
      if (event !== null) {
        if (this.config.call) {
          this.isLoading = true;
          
          ApiService.put(this.config.call, event)
            .then(response =>  {
              this.isLoading = false;
            })
            .catch(error => {
              this.isLoading = false;
            })
            .then(function () {
            });
        } else {
          this.$emit('updateValue', event);
          this.showModalClick();
        }
      } else {
        this.showModalClick();
      }
    }
  }
}
</script>