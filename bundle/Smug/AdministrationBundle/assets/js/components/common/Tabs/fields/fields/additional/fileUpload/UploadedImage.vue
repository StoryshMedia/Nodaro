<template>
  <section class="h-full">
    <article
      v-if="!fieldConfig.mini"
      tabindex="0"
      class="group hasImage w-full h-full rounded-md focus:outline-none focus:shadow-outline relative text-transparent hover:text-white shadow-sm"
    >
      <div class="flex flex-row pb-5 mb-5">
        <img
          :src="fieldValue.tempUrl"
          loading="lazy"
          class="img-preview w-full h-full max-h-16 sticky object-cover rounded-md bg-fixed"
        >

        <section
          class="flex flex-col w-full z-20 absolute top-0 rounded-md text-xs break-words h-full"
        >
          <div class="flex text-kelp-700 py-2 px-3 transparent-bright">
            <span
              class="p-1 cursor-pointer"
              @click="showImagePreview(fieldValue.tempUrl)"
            >
              <i>
                <svg
                  class="fill-current w-4 h-4 ml-auto pt-"
                  xmlns="http://www.w3.org/2000/svg"
                  width="24"
                  height="24"
                  viewBox="0 0 24 24"
                >
                  <path d="M5 8.5c0-.828.672-1.5 1.5-1.5s1.5.672 1.5 1.5c0 .829-.672 1.5-1.5 1.5s-1.5-.671-1.5-1.5zm9 .5l-2.519 4-2.481-1.96-4 5.96h14l-5-8zm8-4v14h-20v-14h20zm2-2h-24v18h24v-18z" />
                </svg>
              </i>
            </span>

            <button
              class="delete ml-auto focus:outline-none p-1 rounded-md cursor-pointer"
              @click="removeUploadedImage(index)"
            >
              <svg
                class="pointer-events-none fill-current w-4 h-4 ml-auto"
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewBox="0 0 24 24"
              >
                <path
                  class="pointer-events-none"
                  d="M3 6l3 18h12l3-18h-18zm19-4v2h-20v-2h5.711c.9 0 1.631-1.099 1.631-2h5.316c0 .901.73 2 1.631 2h5.711z"
                />
              </svg>
            </button>
          </div>
        </section>
      </div>
    </article>
    <article
      v-else
      class="flex mt-1.5 p-1.5 rounded-md"
    >
      <div class="w-10 h-10 flex shrink-0 mt-1">
        <img
          :src="fieldValue.tempUrl"
          alt="upload preview"
          loading="lazy"
          class="w-full h-full object-cover rounded-md"
        >
      </div>
      <div class="mr-1.5 text-xs shrink px-0.5">
        <h6 class="break-all py-3 pl-2">
          {{ getImageName() }}
        </h6>
      </div>
      <div class="w-10 shrink-0 text-center ml-auto">
        <div class="text-dark py-2 px-3 transparent-bright">
          <button
            class="delete ml-auto focus:outline-none p-1 rounded-md cursor-pointer"
            @click="removeImage(index)"
          >
            <svg
              class="pointer-events-none fill-current w-4 h-4 ml-auto"
              xmlns="http://www.w3.org/2000/svg"
              width="24"
              height="24"
              viewBox="0 0 24 24"
            >
              <path
                class="pointer-events-none"
                d="M3 6l3 18h12l3-18h-18zm19-4v2h-20v-2h5.711c.9 0 1.631-1.099 1.631-2h5.316c0 .901.73 2 1.631 2h5.711z"
              />
            </svg>
          </button>
        </div>
      </div>
    </article>
  </section>
</template>
<script>
import { defineComponent } from 'vue';
import TextService from '@SmugAdministrationServices/text/text.service';
  
export default defineComponent({
  name: 'UploadedImage',
  components: {
  },
  props: {
    fieldValue:{
      type: String,
      required: false,
      default: ''
    },
    fieldConfig:{
      type: Object,
      required: false,
      default: () => ({})
    }
  },
  data() {
    return {
      itemValues: [],
      editItem: {}
    }
  },
  methods: {
    getImageName() {
      return TextService.getOutput(this.fieldValue.name, 10);
    },
    removeImage(index) {
      this.$emit('removeImage', index);
    }
  },
})
</script>