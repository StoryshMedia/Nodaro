<template>
  <section>
    <button
      ref="menu"
      class="flex inline-flex items-center"
      @click="openClose"
    >
      <span>{{ menuTitle }}</span>
      <svg
        class="text-gray-800 h-4 w-4 fill-current"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 20 20"
        fill="currentColor"
        aria-hidden="true"
      >
        <path
          fill-rule="evenodd"
          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
          clip-rule="evenodd"
        />
      </svg>
    </button>

    <section
      v-if="isOpen === true"
      class="fixed top-20"
    >
      <slot />
    </section>
  </section>
</template>

<script>
export default {
  name: 'Dropdown',
  props: {
    darkMode: {
      type: Boolean,
      required: false,
      default: false
    },
    menuTitle: {
      type: String,
      required: false,
      default: ''
    }
  },
  data() {
    return {
      isOpen: false,
      isDarkMode: false
    }
  },
  watch: {
    darkMode(val) {
      // Force dark mode
      if( !val )
        this.isDarkMode = false
      // Force dark mode
      if( val === 'force' )
        this.isDarkMode = true
      // Switch dark / light mode automatically according to what user prefer
      if( val === 'auto' && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches )
        this.isDarkMode = true
    }
  },
  methods: {
    openClose() {
      // Close if open and vice versa
      this.isOpen = !this.isOpen
    },
    catchOutsideClick(event, dropdown)	{
      // When user clicks menu — do nothing
      if( dropdown === event.target )
        return false
      // When user clicks outside of the menu — close the menu
      if( this.isOpen && dropdown !== event.target )
        return true
    }
  }
}
</script>
