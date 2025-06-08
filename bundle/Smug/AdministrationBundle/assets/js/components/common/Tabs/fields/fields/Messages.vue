<template>
  <div>
    <div
      v-if="messages && messages.length > 0"
    >
      <div 
        v-for="(message, messageindex) in messages"
        :key="messageindex"
      >
        <div class="mt-2 mx-auto border-b border-gray">
          <message
            :message="message"
            :field-config="fieldConfig"
          />
        </div>
      </div>
    </div>
    <div v-if="messages.length === 0">
      <div class="flex items-center p-3.5 rounded text-white bg-success">
        <span class="pr-2">{{ $t('NO_DATA_FOUND') }}</span>
      </div>
    </div>
  </div>
</template>

<script>
import ApiService from '@SmugAdministrationServices/api/api.service';
import { defineAsyncComponent } from "vue";
const Message = defineAsyncComponent(() =>
  import("./additional/messages/Message" /* webpackChunkName: "message" */)
);

export default {
  name: "Messages",
  components: {
    Message
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
      reload: 0,
      messages: [],
      showAdd: false
    }
  },
  mounted() {
    this.messages = this.fieldValue;
  },
  methods: {
    getData() {
      if (this.fieldConfig.id) {
        ApiService.get(this.fieldConfig.config.getCall, this.fieldConfig.id)
          .then(result =>  {
            this.messages = result;
            this.reload++;
            this.$emit('updateValue', this.items);
            this.isLoading = false;
          })
          .catch(error => {
            this.isLoading = false;
          });
      }
    }
  }
}
</script>