<template>
  <section>
    <Codemirror
      ref="editorRef"
      :value="value"
      :options="cmOptions"
      :placeholder="$t(fieldPlaceholder)"
      :height="400"
      @change="getMarkdown($event)"
    />
    <div
      class="mt-12 markdown--content"
      v-html="md.render(value)"
    />
  </section>
</template>

<script>
import Codemirror from "codemirror-editor-vue3";
import "codemirror/mode/markdown/markdown.js";
import "codemirror/theme/dracula.css";
import MarkdownIt from 'markdown-it';

export default {
  name: "Markdown",
  components: {
    Codemirror
  },
  props: {
    editAllowed:{
      type: Boolean,
      required: true
    },
    baseId:{
      type: String,
      required: false,
      default: null
    },
    fieldValue: {
      type: String,
      default: ''
    },
    fieldConfig: {
      type: Object,
      default: () => ({})
    },
    fieldPlaceholder: {
      type: String,
      default: 'TEXT_PLACEHOLDER'
    }
  },
  data() {
    return {
      value: '# Hello **World**',
      md: new MarkdownIt({
        html: true,
        linkify: true,
        typographer: true
      }),
      cmOptions: {
        mode: "markdown",
        theme: "dracula"
      },
      isLoaded: false
    };
  },
  mounted() {
    if (this.fieldValue !== '') {
      this.value = this.fieldValue;
    }
  },
  methods: {
    getFieldValue() {
      return ValueService.getValue(this.fieldValue, this.fieldConfig);
    },
    getMarkdown(event) {
      this.value = event;
      this.changeContent();
    },
    setReady() {
      this.isLoaded = true;
    },
    changeContent() {
      this.$emit('updateValue', this.value);
    },
    isDisabled() {
      return this.editAllowed === false || this.fieldConfig.disabled === true;
    }
  }
}
</script>