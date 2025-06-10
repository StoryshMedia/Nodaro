<template>
  <button
    type="button"
    class="font-bold py-2 px-4 rounded-full w-full"
    :class="getButtonClass()"
    @click="handleClick()"
  >
    {{ $t(fieldConfig.buttonText) }}
  </button>
</template>

<script>
import ApiService from '@SmugAdministration/js/services/api/api.service';

export default {
  name: "Button",
  components: {
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
    fieldValue:{
      type: Object,
      required: false,
      default: null
    },
    fieldConfig:{
      type: Object,
      required: false,
      default: () => ({})
    },
    fieldPlaceholder:{
      type: String,
      required: false,
      default: 'TEXT_PLACEHOLDER'
    }
  },
  data() {
    return {
      reload: 0
    }
  },
  methods: {
    getButtonClass() {
      if (this.fieldConfig.buttonType) {
        return 'bg-' + this.fieldConfig.buttonType + ' border border-' + this.fieldConfig.buttonType + ' hover:bg-white text-white hover:text-' + this.fieldConfig.buttonType;
      }
      
      return this.fieldConfig.buttonClass ?? '';
    },
    handleClick() {
      const id = this.baseId ?? this.fieldConfig.id;
      const method = this.fieldConfig.method ?? 'GET';
      let data = {};
      let url = this.fieldConfig.functionCall;

      if (id) {
        if (method === 'GET')
          url += id
        data = {
          id: id
        }
      }

      if (method === 'GET') {
        ApiService.get(url)
          .then(response =>  {
            this.handlePostClick();
          })
          .catch(error => {
            this.isLoading = false;
          });
      }
      if (method === 'PUT') {
        ApiService.put(
          url,
          data
        )
          .then(response =>  {
            this.handlePostClick();
          })
          .catch(error => {
            this.isLoading = false;
          });
      }
      if (method === 'POST') {
        ApiService.post(
          url,
          data
        )
          .then(response =>  {
            this.handlePostClick();
          })
          .catch(error => {
            this.isLoading = false;
          });
      }
    },
    handlePostClick() {
      if (typeof this.fieldConfig.successHandling === 'undefined') {
        window.location.reload();
        return;
      }

      switch (this.fieldConfig.successHandling.type) {
      case 'reload': 
        window.location.reload();
        break;
      case 'refresh': 
        window.dispatchEvent(new CustomEvent('refresh-view', {}));
        break;
      default:
        window.location.reload();
      }
    },
    setContent(identifier, event) {
      this.fieldValue[identifier] = event;
      this.$emit('updateValue', this.fieldValue);
    }
  }
}
</script>