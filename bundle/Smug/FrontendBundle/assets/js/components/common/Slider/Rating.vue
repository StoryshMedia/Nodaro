<template>
  <section>
    <div
      v-if="value != 0"
      class="flex justify-center items-center rounded-lg text-white transition-all duration-250"
      :class="getClasses()"
      :style="getBackground()"
    >
      {{ value }}
    </div>
  </section>
</template>

<script>

export default {
  name: "Rating",
  props: {
    value: {
      type: Number,
      required: true
    },
    tile: {
      type: Boolean,
      required: false,
      default: false
    },
    small: {
      type: Boolean,
      required: false,
      default: false
    },
    margin: {
      type: Boolean,
      required: false,
      default: true
    }
  },
  methods: {
    getAlignment() {
      if (this.margin === false) {
        return '';
      }
      if (this.tile === false) {
        return 'm-auto'
      } else {
        return 'ml-6'
      }
    },
    getClasses() {
      let classes = this.getAlignment();
      classes += (this.small === true) ? ' h-8 w-8' : ' h-12 w-12';
      return classes;
    },
    getBackground() {
      if (typeof this.value === 'undefined' || this.value === '') {
        return {
          background: 'transparent'
        };
      }

      if (this.value >= 8) {
        return {
          background: '#2ECC71'
        };
      }

      if (this.value >= 6.5) {
        return {
          background: '#BED083'
        };
      }

      if (this.value >= 4) {
        return {
          background: '#F1B704'
        };
      }

      if (this.value >= 2) {
        return {
          background: '#F37042'
        };
      }

      if (this.value === 0) {
        return {
          background: '#4a534c'
        }
      }

      return {
        background: '#DB410A'
      };
    }
  }
};
</script>
