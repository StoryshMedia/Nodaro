<template>
  <div class="flex flex-wrap md items-center h-screen">
    <div class="bg-white w-full lg:w-1/2 h-screen relative overflow-hidden">
      <div
        class="mx-auto transition-all ease-in-out duration-300 px-8 md:px-16 py-16 md:py-32 absolute z-10"
        :class="(currentIndex === 1) ? 'left-0' : 'left-full'"
      >
        <h1 class="text-6xl font-bold mt-16">
          {{ $t('BOOKS') }}
        </h1>

        <!-- country region island -->
        <div class="flex flex-wrap mt-16 font-light text-gray-500">
          <div class="pr-4">
            <a
              href="/publications"
              :title="$t('PUBLICATIONS')"
            >
              <p class="text-lg text-gray-900 font-semibold pt-2">
                {{ $t('PUBLICATIONS') }}
              </p>
            </a>
          </div>
          <div class="pr-4">
            <a
              href="/empfehlungen"
              :title="$t('RECOMMENDATIONS')"
            >
              <p class="text-lg text-gray-900 font-semibold pt-2">
                {{ $t('RECOMMENDATIONS') }}
              </p>
            </a>
          </div>
          <div class="pr-4">
            <a
              href="/authors"
              :title="$t('AUTHORS')"
            >
              <p class="text-lg text-gray-900 font-semibold pt-2">
                {{ $t('AUTHORS') }}
              </p>
            </a>
          </div>
          <div class="pr-4">
            <a
              href="/tauschboerse"
              :title="$t('MARKET')"
            >
              <p class="text-lg text-gray-900 font-semibold pt-2">
                {{ $t('MARKET') }}
              </p>
            </a>
          </div>
          <div class="pr-4">
            <a
              href="/neuerscheinungen"
              :title="$t('NEW_RELEASES')"
            >
              <p class="text-lg text-gray-900 font-semibold pt-2">
                {{ $t('NEW_RELEASES') }}
              </p>
            </a>
          </div>
        </div>

        <!-- description -->
        <div
          class="description w-full sm: md:w-2/3 mt-16 text-gray-500"
        >
          {{ $t('BOOKS_CAROUSEL_TEXT') }}
        </div>
      </div>
      <div
        class="mx-auto transition-all ease-in-out duration-300 px-8 md:px-16 py-16 md:py-32 absolute z-10"
        :class="(currentIndex === 2) ? 'left-0' : 'left-full'"
      >
        <h1 class="text-6xl font-bold mt-16">
          {{ $t('TEXTS') }}
        </h1>

        <!-- country region island -->
        <div class="flex flex-wrap mt-16 font-light text-gray-500">
          <div class="pr-4">
            <a
              href="/stories"
              :title="$t('STORIES')"
            >
              <p class="text-lg text-gray-900 font-semibold pt-2">
                {{ $t('STORIES') }}
              </p>
            </a>
          </div>
          <div class="pr-4">
            <a
              href="/blog"
              :title="$t('BLOG')"
            >
              <p class="text-lg text-gray-900 font-semibold pt-2">
                {{ $t('BLOG') }}
              </p>
            </a>
          </div>
          <div class="pr-4">
            <a
              href="/wettbewerbe"
              :title="$t('CONTESTS')"
            >
              <p class="text-lg text-gray-900 font-semibold pt-2">
                {{ $t('CONTESTS') }}
              </p>
            </a>
          </div>
          <div class="pr-4">
            <a
              href="/zitate"
              :title="$t('QUOTES')"
            >
              <p class="text-lg text-gray-900 font-semibold pt-2">
                {{ $t('QUOTES') }}
              </p>
            </a>
          </div>
        </div>

        <!-- description -->
        <div
          class="description w-full sm: md:w-2/3 mt-16 text-gray-500"
        >
          {{ $t('TEXTS_CAROUSEL_TEXT') }}
        </div>
      </div>
      <div
        class="mx-auto transition-all ease-in-out duration-300 px-8 md:px-16 py-16 md:py-32 absolute z-10"
        :class="(currentIndex === 3) ? 'left-0' : 'left-full'"
      >
        <h1 class="text-6xl font-bold mt-16">
          {{ $t('COMMUNITY') }}
        </h1>

        <!-- country region island -->
        <div class="flex flex-wrap mt-16 font-light text-gray-500">
          <div class="pr-4">
            <a
              href="/community"
              :title="$t('COMMUNITY')"
            >
              <p class="text-lg text-gray-900 font-semibold pt-2">
                {{ $t('COMMUNITY') }}
              </p>
            </a>
          </div>
          <div class="pr-4">
            <a
              href="/community/forum"
              :title="$t('FORUM')"
            >
              <p class="text-lg text-gray-900 font-semibold pt-2">
                {{ $t('FORUM') }}
              </p>
            </a>
          </div>
          <div class="pr-4">
            <a
              href="/events"
              :title="$t('EVENTS')"
            >
              <p class="text-lg text-gray-900 font-semibold pt-2">
                {{ $t('EVENTS') }}
              </p>
            </a>
          </div>
          <div class="pr-4">
            <a
              href="/kooperation"
              :title="$t('COOPERATION')"
            >
              <p class="text-lg text-gray-900 font-semibold pt-2">
                {{ $t('COOPERATION') }}
              </p>
            </a>
          </div>
          <div class="pr-4">
            <a
              href="/was-ist-patreon"
              :title="$t('PATREON')"
            >
              <p class="text-lg text-gray-900 font-semibold pt-2">
                {{ $t('PATREON') }}
              </p>
            </a>
          </div>
        </div>

        <!-- description -->
        <div
          class="description w-full sm: md:w-2/3 mt-16 text-gray-500"
        >
          {{ $t('COMMUNITY_CAROUSEL_TEXT') }}
        </div>
      </div>
      <div
        class="absolute bottom-12 md:bottom-0 block px-5 md:px-16 py-7 flex flex-wrap"
      >
        <button
          class="text-left flex-none group items-center pr-3 md:pr-6 transition-all py-5 cursor-pointer"
          :class="(currentIndex === 1) ? 'is-selected' : ''"
          @click="setIndex(1)"
        >
          <span class="thumb-text text-lg font-sans leading-5 line-clamp-2 xl:line-clamp-1 text-base transition-all border-b-2">{{ $t('BOOKS') }}</span>
        </button>
        <button
          class="text-left flex-none group items-center px-3 md:px-6 transition-all py-5 cursor-pointer"
          :class="(currentIndex === 2) ? 'is-selected' : ''"
          @click="setIndex(2)"
        >
          <span class="thumb-text text-lg font-sans leading-5 line-clamp-2 xl:line-clamp-1 text-base transition-all border-b-2">{{ $t('TEXTS') }}</span>
        </button>
        <button
          class="text-left flex-none group items-center px-3 md:px-6 transition-all py-5 cursor-pointer"
          :class="(currentIndex === 3) ? 'is-selected' : ''"
          @click="setIndex(3)"
        >
          <span class="thumb-text text-lg font-sans leading-5 line-clamp-2 xl:line-clamp-1 text-base transition-all border-b-2">{{ $t('COMMUNITY') }}</span>
        </button>
      </div>
    </div>
    <div class="bg-dark hidden lg:block w-full lg:w-1/2 h-screen relative">
      <img
        src="https://api.storysh.de/site/img/booksCarouselImage.webp"
        class="h-screen w-full lg:absolute transition-all ease-in-out duration-300"
        :class="(currentIndex === 1) ? 'bottom-0' : 'bottom-full'"
        alt=""
      >
      <img
        src="https://api.storysh.de/site/img/textsCarouselImage.webp"
        class="h-screen w-full lg:absolute transition-all ease-in-out duration-300"
        :class="(currentIndex === 2) ? 'bottom-0' : 'bottom-full'"
        alt=""
      >
      <img
        src="https://api.storysh.de/site/img/communityCarouselImage.webp"
        class="h-screen w-full lg:absolute transition-all ease-in-out duration-300"
        :class="(currentIndex === 3) ? 'bottom-0' : 'bottom-full'"
        alt=""
      >
    </div>
  </div>
</template>
<script>

export default {
  name: "IndexCarousel",
  data() {
    return {
      currentIndex: 1,
      carouselPaused: false
    };
  },
  mounted() {
  },
  methods: {
    pause() {
      this.carouselPaused = true;
      clearInterval(this.intervalid1);
    },
    play() {
      this.next();
    },
    goToList(index) {
      let id = ''
      switch (index) {
      case 1:
        id = "index-bestseller-publications";
        break;
      case 2:
        id = "index-market-teaser";
        break;
      case 3:
        id = "index-blog-teaser";
        break;
      case 4:
        id = "index-last-stories";
        break;
      case 5:
        id = "index-current-events";
        break;
      case 6:
        id = "index-forum-teaser";
        break;
      }
      
      const el = document.getElementById(id);
      if (el) {
        el.scrollIntoView({behavior: 'smooth'});
      }
    },
    setIndex(index) {
      this.currentIndex = index;
    },
    next: function() {
      this.clearLoop();
      if (this.currentIndex === 6) {
        this.currentIndex = 1;
      } else {
        this.currentIndex += 1;
      }
    },
    prev: function() {
      this.clearLoop();
      if (this.currentIndex === 1) {
        this.currentIndex = 6;
      } else {
        this.currentIndex -= 1;
      }
    }
  }
}
</script>
