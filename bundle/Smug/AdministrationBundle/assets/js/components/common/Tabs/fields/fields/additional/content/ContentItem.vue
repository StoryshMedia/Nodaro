<template>
  <section
    v-if="isLoaded"
    class="h-full"
  >
    <div
      :key="reloadKey"
      v-html="getContent()"
    />
  </section>
</template>
<script>
import { defineComponent } from 'vue';
  
export default defineComponent({
  name: 'ContentItem',
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
      content: '',
      isLoaded: false,
      reloadKey: 0,
      itemValues: []
    }
  },
  created() {
    this.content = '';
    this.isLoaded = false;
  },
  mounted() {
    let that = this;
    this.content = this.fieldValue.rendered;
    setTimeout(function() {
      const id = that.fieldValue.id;
      const items = document.querySelectorAll(`[data-id='${id}'], [data-is-content-editor]`);
      const observerConfig = { attributes: true, childList: true, subtree: true, characterData: true };
      for (let count = 0; count <= items.length - 1; count++) {
        if (items[count].dataset.id.replace(/"/g, "") !== id) {
          continue;
        }

        const inputObserver = new MutationObserver((mutationList) => {
          for (const mutation of mutationList) {
            if (mutation.type === "characterData") {
              let id = null;
              let variableIdentifier = null;
              
              if (mutation.target.parentNode.dataset.id) {
                id = mutation.target.parentNode.dataset.id;
                variableIdentifier = mutation.target.parentNode.dataset.contentVariable;
              } else {
                id = mutation.target.parentNode.closest('[data-id]').dataset.id;
                variableIdentifier = mutation.target.parentNode.closest('[data-content-variable]').dataset.contentVariable;
              }
              let result = {
                identifier: variableIdentifier.replace(/"/g, ""),
                id: id.replace(/"/g, "")
              };

              const value = mutation.target.innerText ?? mutation.target.data;
              result.value = {
                value: value.replace(/^\s+|\s+$/gm,'')
              };
              if (mutation.target.parentNode.dataset.tab) {
                result.value['tab'] = mutation.target.parentNode.dataset.tab.replace(/"/g, "");
              }

              that.$emit('updateValue', result);
            }
          }
        });
        inputObserver.observe(items[count], observerConfig);
        that.itemValues[items[count].dataset.contentVariable] = items[count].innerText;
        items[count].addEventListener('click', (e) => that.onItemClick(e));
      }
    }, 1500);

    this.$forceUpdate;
    this.reloadKey += 1;
    this.isLoaded = true;
  },
  methods: {
    getContent() {
      return this.content;
    },
    onItemClick(event) {
      event.preventDefault();
      if (event.target.dataset.id) {
        if (event.target.dataset.id.replace(/"/g, "") === this.fieldValue.id) {
          const item = {
            id: event.target.dataset.id.replace(/"/g, ""),
            identifier: event.target.dataset.contentVariable.replace(/"/g, "")
          };
          if (event.target.dataset.tab) {
            item['tab'] = event.target.dataset.tab.replace(/"/g, "");
          }

          this.$emit('selectItemValue', item);
        }
      } else {
        const parent = event.target.closest('[data-id]').dataset.id;

        if (parent.replace(/"/g, "") === this.fieldValue.id) {
          const item = {
            id: event.target.closest('[data-id]').dataset.id.replace(/"/g, ""),
          };

          if (event.target.dataset.contentVariable) {
            item.identifier = event.target.dataset.contentVariable.replace(/"/g, "");
          } else {
            item.identifier = event.target.closest('[data-id]').dataset.contentVariable.replace(/"/g, "");
          }
          if (event.target.dataset.tab) {
            item['tab'] = event.target.dataset.tab.replace(/"/g, "");
          }

          this.$emit('selectItemValue', item);
        }
      }
    }
  },
})
</script>