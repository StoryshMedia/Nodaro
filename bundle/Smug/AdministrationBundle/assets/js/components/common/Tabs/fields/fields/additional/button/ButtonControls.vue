<template>
  <section>
    <div
      v-if="controls && controls.length > 0"
      class="flex items-end justify-end mb-3"
    >
      <div
        v-for="(control, controlKey) in controls"
        :key="controlKey"
        class="mx-2 text-center"
      >
        <danger
          v-if="control.type === 'danger'"
          :key="reload"
          :config="control"
          @clicked="callFunction($event, item)"
        />
        <success
          v-if="control.type === 'success'"
          :key="reload"
          :config="control"
          @clicked="callFunction($event, item)"
        />
        <success
          v-if="control.type === 'modal'"
          :key="reload"
          :config="control"
          @clicked="setShowModal(control)"
        />
        <conditional
          v-if="control.type === 'conditional'"
          :key="reload"
          :config="control"
          :item="item"
          @clicked="callFunction($event, item)"
        />
      </div>
    </div>
    <data-modal
      v-if="showModal === true"
      :item-data="item"
      :template="config.template.data"
      :save-button-text="'SAVE'"
      :abort-button-text="'ABORT'"
      :headline="config.headline"
      @edit-reaction="handleAction($event, item)"
    />
    <confirm
      v-if="showConfirmation === true"
      :text="'CONFIRM_MODAL_CONFIRMATION_TEXT'"
      :confirm-button-text="'YES'"
      :discard-button-text="'NO'"
      :modal-headline="'CONFIRM_MODAL_CONFIRMATION_HEADLINE'"
      @confirm-reaction="handleConfirmValue($event)"
    />
  </section>
</template>

<script>
import ApiService from 'SmugAdministration/js/services/api/api.service';
import { defineAsyncComponent } from "vue";
const Confirm = defineAsyncComponent(() =>
  import("../../../../../Modal/Confirm.vue" /* webpackChunkName: "confirm-dialog" */)
);
const DataModal = defineAsyncComponent(() =>
  import("../../../../../Modal/DataModal.vue" /* webpackChunkName: "administration-modal-dialog" */)
);
const Success = defineAsyncComponent(() =>
  import("./types/Success.vue" /* webpackChunkName: "administration-control-success" */)
);
const Danger = defineAsyncComponent(() =>
  import("./types/Danger.vue" /* webpackChunkName: "administration-control-danger" */)
);
const Conditional = defineAsyncComponent(() =>
  import("./types/Conditional.vue" /* webpackChunkName: "administration-control-conditional" */)
);

export default {
  name: "ButtonControls",
  components: {
    Success,
    Danger,
    Conditional,
    DataModal,
    Confirm
  },
  props: {
    controls:{
      type: Array,
      required: true
    },
    item:{
      type: Object,
      required: false,
      default: null
    }
  },
  data() {
    return {
      showConfirmation: false,
      showModal: false,
      handleItem: {},
      config: {},
      handleCall: '',
      reload: 0
    };
  },
  methods: {
    callFunction(config, item) {
      if (config.confirm === true) {
        this.handleCall = config.call;
        this.handleItem = item;
        this.showConfirmation = true;
      } else {
        this.handleFunction(config.call, item);
      }
    },
    handleConfirmValue(value) {
      if (value === true) {
        this.showConfirmation = false;
        this.handleFunction(this.handleCall, this.handleItem);
      } else {
        this.showConfirmation = false;
        this.handleCall = '';
        this.handleItem = {};
      }
    },
    handleAction(event) {
      if (event !== false) {
        if (this.config.call) {
          this.handleFunction(this.config.call, this.item);
          this.setShowModal();
        } else {
          this.$emit('updateValue', event);
          this.setShowModal();
        }
      } else {
        this.setShowModal();
      }
    },
    setShowModal(control) {
      if (control && control.config) {
        this.config = control.config;
      }
      this.showModal = !this.showModal;
    },
    getControlClass() {
      return 'grid-cols-' + this.controls.length;
    },
    handleFunction(url, item) {
      ApiService.post(url, item)
        .then(result =>  {
          this.handleCall = '';
          this.handleItem = {};
          this.$emit('called', true);
        })
        .catch(error => {
          this.isLoading = false;
        })
        .then(function () {
        });
    },
  }
}
</script>