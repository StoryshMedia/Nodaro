<template>
  <div>
    <article
      class="p-6 text-base"
      :class="getSpace()"
    >
      <div class="flex">
        <div
          v-if="showImage()"
          class="mr-4"
        >
          <img
            :src="getImage(message)"
            :alt="getUserName(message)"
            class="w-14 h-14 rounded-full"
          >
        </div>
        <div class="flex-1">
          <h4
            v-if="showHeader()"
            class="font-semibold text-lg mb-2 text-primary"
          >
            Heading
          </h4>
          <div
            v-if="fieldConfig.badges"
            class="flex my-2"
          >
            <div
              v-for="(badge, badgeindex) in fieldConfig.badges"
              :key="badgeindex"
            >
              <span
                v-if="showItem(badge, message)"
                class="flex px-3 py-1 mr-3 items-center text-sm border rounded-lg text-white font-medium"
                :class="getBadgeClass(badge)"
              >
                <icon
                  :icon-string="'IconInfoTriangle'"
                  :class="'w-4 h-4 flex-none mr-1'"
                />
                {{ $t(badge.label) }}
              </span>
            </div>
          </div>
          <p class="text-sm my-2"> 
            <a
              :href="getUserLink(message)"
              target="_blank"
            >
              <strong>{{ getUserName(message) }}</strong>
            </a>
            
            <span class="mx-1">{{ $t('AT_DATE') }}</span>
            <time
              pubdate
              datetime="2022-02-08"
              title="February 8th, 2022"
            >{{ getDate(message) }}</time>
          </p>
          <p
            class="media-text mb-5"
            v-html="getMessage(message)"
          />

          <div v-if="showFiles(message)">
            <files :files="message[fieldConfig.filesIdentifier]" />
          </div>

          <div
            v-if="fieldConfig.relations"
            class="grid grid grid-cols-1 md:grid-cols-2 mt-3"
          >
            <div
              v-for="(relation, relationindex) in fieldConfig.relations"
              :key="relationindex"
              class="py-2"
            >
              <a
                v-if="showRelation(relation, message)"
                :href="getRelationLink(relation, message)"
                target="_blank"
                class="flex items-center"
              >
                <div class="p-0.5 rounded-full w-max mr-2">
                  <img
                    class="h-8 w-8 rounded-full object-cover"
                    :src="getRelationImage(relation, message)"
                  >
                </div>

                <div>
                  <p
                    v-if="relation.placeholder"
                    class="font-semibold"
                  >{{ $t(relation.placeholder) }}</p>
                  {{ getRelationTitle(relation, message) }}
                </div>
              </a>
            </div>
          </div>

          <div
            v-if="fieldConfig.actions"
            class="flex mt-3"
          >
            <div
              v-for="(action, actionindex) in fieldConfig.actions"
              :key="actionindex"
            >
              <button
                v-if="showItem(action, message)"
                type="button"
                class="flex px-3 py-1 items-center text-sm text-gray-500 border border-primary rounded-lg hover:bg-primary hover:text-white transition-all font-medium"
                @click="handleAction(action, message)"
              >
                <icon
                  :icon-string="getIcon(action)"
                  :class="'w-4 h-4 flex-none mr-1'"
                />
                {{ $t(action.label) }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </article>

    <div v-if="message.children && message.children.length > 0">
      <div 
        v-for="(child, childindex) in message.children"
        :key="childindex"
      >
        <message
          :message="child"
          :field-config="fieldConfig"
          :level="level + 1"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { defineAsyncComponent } from "vue";
import ImageService from '../../../../../../../services/image/image.service';
import ApiService from '../../../../../../../services/api/api.service';
import LinkService from '../../../../../../../services/link/link.service';
import DateService from '../../../../../../../services/date/date.service';
import ConditionService from '../../../../../../../services/condition/condition.service';
const Icon = defineAsyncComponent(() =>
  import("../../../../../../../../../../FrontendBundle/assets/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);
const Files = defineAsyncComponent(() =>
  import("./Files.vue" /* webpackChunkName: "message-files" */)
);

export default {
  name: "Message",
  components: {
    Icon,
    Files
  },
  props: {
    message: {
      type: Object,
      required: true
    },
    fieldConfig:{
      type: Object,
      required: false,
      default: () => ({})
    },
    level: {
      type: Number,
      required: false,
      default: 0
    }
  },
  methods: {
    getImage(item) {
      // return ImageService.getImage(item);
      return ImageService.getFallbackImage();
    },
    getDate(item) {
      return DateService.getFormattedDate(item.messageDate);
    },
    getUserLink(item) {
      return '/admin/smug/frontend_user/frontend_user/detail/' + item.user.id;
    },
    getUserIdentifier() {
      return this.fieldConfig.userIdentifier ?? 'user';
    },
    getUserNameIdentifier() {
      return this.fieldConfig.userNameIdentifier ?? 'username';
    },
    getMessageIdentifier() {
      return this.fieldConfig.messageIdentifier ?? 'message';
    },
    getUserName(item) {
      const userIdentifier = this.getUserIdentifier();
      const userNameIdentifier = this.getUserNameIdentifier();
      return item[userIdentifier][userNameIdentifier];
    },
    getMessage(item) {
      const messageIdentifier = this.getMessageIdentifier();
      return item[messageIdentifier];
    },
    showFiles(item) {
      return item[this.fieldConfig.filesIdentifier] && item[this.fieldConfig.filesIdentifier].length > 0;
    },
    getBadgeClass(item) {
      const color = item.type ?? 'info';
      return 'bg-' + color + ' border-' + color;
    },
    getIcon(item) {
      return item.icon ?? 'IconPencil';
    },
    showHeader() {
      return this.fieldConfig.showHeader ?? false;
    },
    showImage() {
      return this.fieldConfig.showUserImage ?? true;
    },
    handleAction(action, message) {
      if (action.type === 'callFunction') {
        this.callFunction(action.actionCall, message);
      }
    },
    callFunction(url, message) {
      ApiService.put(url, message);
    },
    showRelation(relation, message) {
      return (typeof message[relation.identifier].id !== 'undefined');
    },
    getRelationTitle(relation, message) {
      return message[relation.identifier][relation.config.headlineIdentifier]
    },
    getRelationImage(relation, message) {
      return ImageService.getImageFromItem(message[relation.identifier]);
    },
    getRelationLink(relation, message) {
      return LinkService.getDetailLink(relation.config.buttonLink, message[relation.identifier].id);
    },
    showItem(item, message) {
      if (!item.condition) {
        return true;
      }

      return ConditionService.check(item, message);
    },
    getSpace() {
      if (this.level === 0) {
        return '';
      }

      let ml = 3;
      let mlMd = 6;
      let mlLg = 12;

      return 'mb-' + ml*this.level + ' ml-' + mlMd*this.level + ' lg:ml-' + mlLg*this.level;
    }
  }
}
</script>