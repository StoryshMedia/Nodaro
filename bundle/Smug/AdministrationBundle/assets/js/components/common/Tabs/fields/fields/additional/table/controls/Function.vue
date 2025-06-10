<template>
  <div>
    <button
      type="button"
      class="w-5 h-5 mr-2 p-0 text-dark transition-all duration-300"
      @click="onButtonClick()"
    >
      <icon
        :icon-string="config.icon"
        :class="'w-18 h-18'"
      />
    </button>
    <confirm
      v-if="showConfirmation === true"
      :text="config.text"
      :discard-button-text="(config.discardButtonText) ? config.discardButtonText : 'NO'"
      :confirm-button-text="(config.confirmButtonText) ? config.confirmButtonText : 'YES'"
      :modal-headline="config.headline"
      @confirm-reaction="handleConfirmValue($event)"
    />
  </div>
</template>

<script>
import ApiService from 'SmugAdministration/js/services/api/api.service';
import { defineAsyncComponent } from "vue";
const Icon = defineAsyncComponent(() =>
  import("@core/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);
const Confirm = defineAsyncComponent(() =>
  import("../../../../../../Modal/Confirm.vue" /* webpackChunkName: "administration-modal-confirm" */)
);

export default {
  name: "Function",
  components: {
    Icon,
    Confirm
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
      showConfirmation: false
    }
  },
  methods: {
    onButtonClick() {
      if (!this.config.confirm) {
        this.handleAction();
      } else {
        this.showConfirmation = true;
      }
    },
    handleConfirmValue(value) {
      if (value === true) {
        this.handleAction();
      } else {
        this.showConfirmation = false;
      }
    },
    handleAction() {
      if (this.config.call) {
        this.isLoading = true;
        
        ApiService.put(this.config.call, this.item)
          .then(response =>  {
            this.isLoading = false;
            this.showConfirmation = false;
            this.$emit('updateValue', this.item);
          })
          .catch(error => {
            this.isLoading = false;
          })
          .then(function () {
          });
      }
      if (this.config.action) {
        this.showConfirmation = false;
        this.$emit('updateValue', this.config.action.type);
      }
    }
  }
}
</script>