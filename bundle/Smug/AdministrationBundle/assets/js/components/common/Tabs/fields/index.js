
import { defineAsyncComponent } from "vue";

export default {
  Text: defineAsyncComponent(() => import("./fields/Text.vue" /* webpackChunkName: "administration-text" */)),
  Number: defineAsyncComponent(() => import("./fields/Number.vue" /* webpackChunkName: "administration-number" */)),
  Editor: defineAsyncComponent(() => import("./fields/Editor.vue" /* webpackChunkName: "administration-editor" */)),
  FileUpload: defineAsyncComponent(() => import("./fields/FileUpload.vue" /* webpackChunkName: "administration-upload" */)),
  Datepicker: defineAsyncComponent(() => import("./fields/Datepicker.vue" /* webpackChunkName: "administration-datepicker" */)),
  SelectList: defineAsyncComponent(() => import("./fields/SelectList.vue" /* webpackChunkName: "administration-select-list" */)),
  Content: defineAsyncComponent(() => import("./fields/Content.vue" /* webpackChunkName: "administration-content-editor" */)),
  Checkbox: defineAsyncComponent(() => import("./fields/Checkbox.vue" /* webpackChunkName: "administration-checkbox" */)),
  Selectbox: defineAsyncComponent(() => import("./fields/Selectbox.vue" /* webpackChunkName: "administration-checkbox" */)),
  Card: defineAsyncComponent(() => import("./fields/Card.vue" /* webpackChunkName: "administration-card" */)),
  ImageGallery: defineAsyncComponent(() => import("./fields/ImageGallery.vue" /* webpackChunkName: "administration-upload" */)),
  Seo: defineAsyncComponent(() => import("./fields/Seo.vue" /* webpackChunkName: "administration-seo" */)),
}