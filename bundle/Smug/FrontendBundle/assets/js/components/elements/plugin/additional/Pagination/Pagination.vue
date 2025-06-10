<template>
  <div class="flex flex-col items-center my-12">
    <div class="flex flex-wrap text-gray-700">
      <a
        v-if="page > 1"
        :href="getHref(page - 1)"
        :title="$t('PREVIOUS_PAGE')"
        class="h-12 w-12 mr-4 flex justify-center items-center rounded-full bg-primary cursor-pointer"
        @click="setPage(page - 1)"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="100%"
          height="100%"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
          class="text-white feather feather-chevron-left w-4 h-4"
        >
          <polyline points="15 18 9 12 15 6" />
        </svg>
      </a>
      <a
        :href="getHref(pages.start)"
        :title="$t('PAGE') + pages.start"
        class="h-12 px-3 md:flex paginationLink justify-center items-center hidden cursor-pointer leading-5 transition duration-150 border-b-2 border-transparent ease-in hover:border-primary"
        :class="{
          'border-primary': page == pages.start
        }"
        @click="setPage(pages.start)"
      >
        <span
          class="paginationLink"
        >
          {{ pages.start }}
        </span>
      </a>
      <div 
        v-for="(paginationPage, index) in pages.preSteps"
        :key="index"
        class="md:flex"
      >
        <p
          v-if="index === 0"
          class="md:flex paginationLink justify-center items-center"
        >
          ...
        </p>
        <a
          :href="getHref(paginationPage)"
          class="h-12 px-3 md:flex paginationLink justify-center items-center hidden cursor-pointer leading-5 transition duration-150 border-b-2 border-transparent ease-in hover:border-primary"
          :class="{
            'border-primary': paginationPage == page,
            'last': (paginationPage === lastPage && page !== paginationPage && lastPage > 3),
            'first': (paginationPage === 1 && lastPage > 3 && page > 3)
          }"
          @click="setPage(paginationPage)"
        >
          <span
            class="paginationLink"
          >
            {{ paginationPage }}
          </span>
        </a>
        <p class="md:flex paginationLink justify-center items-center">
          ...
        </p>
      </div>
      <a
        v-for="(paginationPage) in pages.mainSteps"
        :key="paginationPage"
        :title="$t('PAGE') + paginationPage"
        :href="getHref(paginationPage)"
        class="h-12 px-3 md:flex paginationLink justify-center items-center hidden cursor-pointer leading-5 transition border-b-2 border-transparent ease-in hover:border-primary"
        :class="{
          'border-primary': paginationPage == page,
          'last': (paginationPage === lastPage && page !== paginationPage && lastPage > 3),
          'first': (paginationPage === 1 && lastPage > 3 && page > 3)
        }"
        @click="setPage(paginationPage)"
      >
        <span
          class="paginationLink"
        >
          {{ paginationPage }}
        </span>
      </a>
      <div 
        v-for="(paginationPage, index) in pages.postSteps"
        :key="index"
        class="md:flex"
      >
        <p
          v-if="index === 0"
          class="md:flex paginationLink justify-center items-center"
        >
          ...
        </p>
        <a
          :href="getHref(paginationPage)"
          class="h-12 px-3 md:flex paginationLink justify-center items-center hidden cursor-pointer leading-5 transition duration-150 border-b-2 border-transparent ease-in hover:border-primary"
          :class="{
            'border-primary': paginationPage == page,
            'last': (paginationPage === lastPage && page !== paginationPage && lastPage > 3),
            'first': (paginationPage === 1 && lastPage > 3 && page > 3)
          }"
          @click="setPage(paginationPage)"
        >
          <span
            class="paginationLink"
          >
            {{ paginationPage }}
          </span>
        </a>
        <p class="md:flex paginationLink justify-center items-center">
          ...
        </p>
      </div>
      <a
        v-if="lastPage !== pages.start"
        :href="getHref(lastPage)"
        :title="$t('PAGE') + lastPage"
        class="h-12 px-3 md:flex paginationLink justify-center items-center hidden cursor-pointer leading-5 transition duration-150 border-b-2 border-transparent ease-in hover:border-primary"
        :class="{
          'border-primary': page == lastPage
        }"
        @click="setPage(lastPage)"
      >
        <span
          class="paginationLink"
        >
          {{ lastPage }}
        </span>
      </a>
      <a
        v-if="page < lastPage"
        :href="getHref(+page + +1)"
        :title="$t('NEXT_PAGE')"
        class="h-12 w-12 ml-4 flex justify-center items-center rounded-full bg-primary cursor-pointer"
        @click="setPage(+page + +1)"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="100%"
          height="100%"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
          class="text-white feather feather-chevron-right w-4 h-4"
        >
          <polyline points="9 18 15 12 9 6" />
        </svg>
      </a>
    </div>
  </div>
</template>

<script>
export default {
  name: "Pagination",
  inject: ['dataset'],
  props: {
    pages: {
      type: Array,
      required: false,
      default: null
    },
    page: {
      type: Number,
      required: true
    },
    lastPage: {
      type: Number,
      required: true
    }
  },
  methods: {
    getHref(paginationPage) {
      return '?p=' + paginationPage;
    },
    setPage(paginationPage) {
      this.page = paginationPage;
      this.setFilterPage(paginationPage).then(set => {
        window.location.replace('?p=' + paginationPage);
      });
    },
    async setFilterPage(page) {
      window.localStorage.setItem('filter-page-' + this.mode, page);
    }
  }
}
</script>

<style lang="scss">
.paginationLink.first::after {
  content:' ...'
}

.paginationLink.last::before {
  content:'... '
}
</style>
