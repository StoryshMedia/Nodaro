<template>
  <section>
    <span
      :ref="'btnRef' + uuid"
      class="cursor-pointer"
      @click="toggleTooltip()"
    >
      <icon-questionmark />
    </span>
    <div
      :ref="'tooltipRef' + uuid"
      style="z-index: 100"
      :class="{'hidden': show === false, 'block': show === true}"
      class="absolute left-3 bg-white border-2 border-kelp-800 ml-3 block z-100 font-normal leading-normal text-sm max-w-screen-sm text-left no-underline break-words rounded-lg"
    >
      <div
        v-if="headline !== ''"
        class="text-kelp-800 flex justify-between font-semibold p-3 mb-0 border-b border-solid border-blueGray-100 rounded-t-lg"
      >
        <span>{{ $t(headline) }}</span>
        <span
          class="cursor-pointer"
          @click="toggleTooltip()"
        >
          <icon-close />
        </span>
      </div>
      <div class="p-3">
        {{ $t(text) }}
      </div>
    </div>
  </section>
</template>

<script>
import { defineAsyncComponent } from "vue";
import { createPopper } from "@popperjs/core";
const IconQuestionmark = defineAsyncComponent(() =>
  import("@core/js/icons/icons/IconQuestionmark" /* webpackChunkName: "icon-questionmark" */)
);
const IconClose = defineAsyncComponent(() =>
  import("@core/js/icons/icons/IconClose" /* webpackChunkName: "icon-close" */)
);

export default {
  name: "Tooltip",
  components: {
    IconQuestionmark,
    IconClose
  },
  props: {
    icon: {
      type: String,
      required: false,
      default: 'fa-circle-info'
    },
    headline: {
      type: String,
      required: false,
      default: ''
    },
    text: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      uuid: '',
      show: false
    };
  },
  mounted() {
    this.generateUuid();
    window.addEventListener('keydown', (e) => {
      if (e.key == 'Escape') {
        this.show = false;
      }
    });
  },
  methods: {
    generateUuid() {
      var d = new Date().getTime();//Timestamp
      var d2 = ((typeof performance !== 'undefined') && performance.now && (performance.now()*1000)) || 0;//Time in microseconds since page-load or 0 if unsupported
      this.uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = Math.random() * 16;
        if(d > 0){
          r = (d + r)%16 | 0;
          d = Math.floor(d/16);
        } else {
          r = (d2 + r)%16 | 0;
          d2 = Math.floor(d2/16);
        }
        return (c === 'x' ? r : (r & 0x3 | 0x8)).toString(16);
      });
    },
    toggleTooltip(){
      this.show = !this.show;
      createPopper(this.$refs['btnRef' + this.uuid], this.$refs['tooltipRef' + this.uuid], {
        placement: "right",
        modifiers: [
          {
            name: 'offset',
            options: {
              offset: [10, 20],
            },
          }
        ],
      });
    },
    getContainerClasses(){
      return '';
    },
    getMargins(){
      return '';
    },
    getPaddings(){
      return '';
    }
  }
}
</script>
