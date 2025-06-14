<template>
  <section class="mt-12">
    <h3 class="font-semibold text-lg pb-2">
      {{ $t('FAQ_ITEMS') }}
    </h3>
    <div class="flex items-end mb-8 ml-3 pt-3">
      <button
        type="button"
        class="btn btn-dark mr-3"
        @click="addQuestion()"
      >
        {{ $t('ADD_FAQ_QUESTION') }}
      </button>
    </div>
    <div
      v-for="(item, itemindex) in mainEntity"
      :key="itemindex"
    >
      <div class="grid grid-cols-1 md:grid-cols-2 gab-5 py-5">
        <div class="pr-2">
          <p class="pb-2 font-semibold">
            {{ $t('QUESTION') }}
          </p>
          <input
            type="text"
            :placeholder="$t('QUESTION')"
            class="form-input"
            :value="item.name"
            @change="setContent($event, itemindex, 'name')"
          >
        </div>
        <div class="pl-2">
          <p class="pb-2 font-semibold">
            {{ $t('FAQ_ANSWER') }}
          </p>
          <textarea
            rows="5"
            class="form-textarea"
            :value="item.acceptedAnswer.text"
            :placeholder="$t('FAQ_ANSWER')"
            @change="setContent($event, itemindex, 'answer')"
          />
        </div>
      </div>
      <div class="flex justify-end mb-8">
        <button
          type="button"
          class="btn btn-danger"
          @click="removeQuestion(itemindex)"
        >
          {{ $t('DELETE') }}
        </button>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "FaqPage",
  props: {
    mainEntity:{
      type: Array,
      required: true
    }
  },
  methods: {
    setContent(content, itemindex, type) {
      if (type === 'answer') {
        this.mainEntity[itemindex].acceptedAnswer.text = content.target.value;
      } else {
        this.mainEntity[itemindex].name = content.target.value;
      }
      this.$emit('updateValue', this.mainEntity);
    },
    getValue(value) {
      return JSON.stringify(value);
    },
    removeQuestion(itemindex) {
      this.mainEntity.splice(itemindex, 1);
      this.$emit('updateValue', this.mainEntity);
    },
    addQuestion() {
      this.mainEntity.push({
        "@type": "Question",
        "name": "",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": ""
        }
      });
      this.$emit('updateValue', this.mainEntity);
    }
  }
}
</script>