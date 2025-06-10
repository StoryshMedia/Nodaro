<template>
  <div
    class="w-full"
  >
    <div class="hidden lg:block border-b-2 border-gray-800 mb-5">
      <div class="flex flex-wrap justify-between pb-3">
        <p
          v-for="letter in alphabet"
          :key="letter"
          class="cursor-pointer"
          @click="setAlphabeticalFilter(letter)"
        >
          {{ $t(letter) }}
        </p>
      </div>
    </div>
    <div class="block lg:hidden mb-5 mx-1">
      <h3
        class="text-base font-normal leading-normal mt-2 mb-2 pl-2 text-dark"
      >
        {{ $t('ALPHABETIC') }}
      </h3>

      <select
        class="px-2 py-4 placeholder-kelp-800 border-2 border-kelp-800 placeholder-opacity-100 text-dark bg-white text-sm focus:outline-none w-full"
        @change="setMobileAlphabeticalFilter($event)"
      >
        <option
          v-for="letter in alphabet"
          :key="letter"
          :value="letter"
        >
          {{ $t(letter) }}
        </option>
      </select>
    </div>
  </div>
</template>
  
<script>
  
export default {
  name: "Alphabetical",
  data() {
    return {
      alphabet: ['ALL', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z']
    };
  },

  methods: {
    setAlphabeticalFilter(letter) {
      window.localStorage.setItem('filter-alphabetical-' + this.mode, (letter === 'ALL') ? '' : letter);
      
      this.$emit('setAlphabetical', true);
    },
    setMobileAlphabeticalFilter(letter) {
      window.localStorage.setItem('filter-alphabetical-' + this.mode, (letter.target.value === 'ALL') ? '' : letter.target.value);
      
      this.$emit('setAlphabetical', true);
    }
  }
}
</script>
  