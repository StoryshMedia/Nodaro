<template>
  <div
    class="flex flex-wrap justify-center pt-24"
    :class="textColor"
  >
    <div class="w-full lg:w-8/12 px-4">
      <div
        class="relative flex flex-col min-w-0 break-words w-full mb-6"
      >
        <div
          class="text-center p-5 lg:p-8"
        >
          <div
            v-if="showIcon === true"
            class="p-3 text-2xl text-center inline-flex items-center justify-center my-5"
            :class="textColor"
          >
            <icon-check :size="'h-20 w-20 border-2 rounded-full'" />
          </div>
          <p
            class="text-2xl font-semibold mb-4"
            :class="textColor"
          >
            <span v-if="isError === false">
              {{ $t('FORM_CONFIRMATION') }}
            </span>
            <span v-else>
              {{ $t('FORM_CONFIRMATION_ERROR') }}
            </span>
          </p>
          <span
            class="leading-6 pt-12"
            v-html="text"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { defineAsyncComponent } from "vue";
const IconCheck = defineAsyncComponent(() =>
  import("@core/js/icons/icons/IconCheck" /* webpackChunkName: "image-check" */)
);
export default {
  name: "FormConfirmation",
  components: {
    IconCheck
  },
  props: {
    text: {
      type: String,
      required: true
    },
    textColor: {
      type: String,
      required: false,
      default: 'text-dark'
    },
    isModal: {
      type: Boolean,
      required: false,
      default: true
    },
    isError: {
      type: Boolean,
      required: false,
      default: false
    },
    showIcon: {
      type: Boolean,
      required: false,
      default: true
    },
    paddingTop: {
      type: String,
      required: false,
      default: ''
    },
    paddingBottom: {
      type: String,
      required: false,
      default: 'pt-0'
    },
    icon: {
      type: String,
      required: false,
      default: 'fa-paper-plane'
    }
  },
  methods: {
    getPaddingTop() {
      return this.paddingTop;
    }
  }
}
</script>
