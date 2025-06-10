<template>
  <section
    v-if="showDownload === true"
  >
    <div v-if="showConfirmation === false">
      <div
        class="flex flex-wrap justify-center"
      >
        <div class="w-full">
          <div
            class="relative flex flex-col min-w-0 break-words w-full"
          >
            <div
              class="text-center p-5 lg:p-8"
            >
              <h4
                class="text-2xl font-semibold p-5 text-dark"
              >
                <span>
                  {{ $t('SEND_STORY_HEADLINE') }}
                </span>
              </h4>
              <span
                class="leading-6 pt-12 text-dark"
                v-html="$t('SEND_STORY_TEXT')"
              />
            </div>
          </div>
        </div>
      </div>
      <div class="flex flex-wrap">
        <div class="relative w-full mb-3">
          <label
            class="block text-xs font-bold mb-2"
            for="DownloadStoryEmail"
          >{{ $t('EMAIL') }}</label><input
            id="DownloadStoryEmail"
            v-model="data.email"
            type="email"
            class="w-full input rounded-3xl shadow-xl border-gray"
            :placeholder="$t('EMAIL')"
            :disabled="isLoading"
            style="transition: all 0.15s ease 0s;"
          >
          <input
            v-model="data.fax"
            type="hidden"
            style="transition: all 0.15s ease 0s;"
          >
        </div>
      </div>
      <button
        type="button"
        class="outline-link outline-link-primary mx-auto mt-5"
        @click="downloadStory()"
      >
        {{ $t('SEND_STORY') }}
      </button>
    </div>
    <div
      v-else
      class="flex flex-wrap justify-center"
    >
      <div class="w-full">
        <div
          class="relative flex flex-col min-w-0 break-words w-full"
        >
          <div
            class="text-center p-5 lg:p-8"
          >
            <h4
              class="text-2xl font-semibold p-5 text-dark"
            >
              <span>
                {{ $t('SEND_STORY_CONFIRMATION_HEADLINE') }}
              </span>
            </h4>
            <div
              class="p-3 text-2xl text-center inline-flex items-center justify-center w-14 h-14 my-5 rounded-full text-dark"
            >
              <i
                class="fa-solid"
                :class="icon"
              />
            </div>
            <span
              class="leading-6 pt-12 text-dark"
              v-html="$t('SEND_STORY_CONFIRMATION_TEXT')"
            />
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
  
<script>

import axios from "axios";

export default {
  name: "DownloadButton",
  inject: ['dataset'],
  data() {
    return {
      title:  '',
      showDownload: false,
      showConfirmation: false,
      data: {
        email: '',
        fax: ''
      },
      author:  '',
    }
  },
  async created() {
    await this.setProps();
  },
  methods: {
    setProps() {
      this.title = this.dataset.title;
      this.author = this.dataset.author;
    },
    isLoggedIn() {
      return this.showDownload;
    },
    downloadStory() {
      const config = {
        headers: { Authorization: `Bearer ${this.$store.state.auth.token}` }
      };
      axios.post(process.env.apiURL + '/fe/api/story/download/' + this.title.replace(/\s/g, "") + '_' + this.author, this.data, config).then(res=>{
        this.showConfirmation = true;
      }).catch(err=>{
      });
    },
  }
};
</script>
  