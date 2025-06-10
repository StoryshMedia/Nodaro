<template>
  <section
    class="px-4 md:px-8 bg-dark text-white  "
  >
    <form @submit.prevent="submitForm">
      <section
        v-if="showConfirmation === false"
      >
        <div class="shadow-sm py-16 sm:px-16 sm:py-24 overlay-subscribe overflow-hidden relative transition-all rounded-md">
          <div class="grid grid-cols-2 xl:gap-16 items-center text-center xl:text-left">
            <div class="col-span-2 xl:col-span-1 mb-8 xl:mb-0">
              <h4 class="text-white text-left">
                {{ $t('NEWSLETTER_FORM_HEADLINE') }}
              </h4>
              <p class="text-gray-400 font-medium transition-all text-left text-sm mt-2">
                {{ $t('NEWSLETTER_FORM_TEXT') }}
              </p>
            </div>
            <div class="col-span-2 xl:col-span-1">
              <div class="block md:flex md:items-center space-x-0 md:space-x-8">
                <input
                  v-model="form.email"
                  class="w-full input rounded-3xl border-none"
                  :placeholder="$t('EMAIL')"
                  type="email"
                  required
                >
                <input
                  v-model="form.fax"
                  type="hidden"
                  style="transition: all 0.15s ease 0s;"
                >
                <button
                  v-if="isLoading === false"
                  type="submit"
                  class="mt-8 md:mt-0 outline-link outline-link-primary"
                >
                  {{ $t("SUBSCRIBE") }}
                </button>
                <span
                  v-else
                  class="mt-8 md:mt-0 text-primary animate-spin fill-current feedback feedback-loading"
                ><icon-loading /></span>
              </div>
            </div>
          </div>
        </div>
      </section>
      <div
        v-else
        class="  bg-dark flex flex-wrap justify-center"
      >
        <app-form-confirmation
          :text-color="'text-white'"
          :text="$t('NEWSLETTER_CONFIRMATION_TEXT')"
        />
      </div>
    </form>
    <footer-ad />
  </section>
</template>

<script>
import axios from "axios";
import { defineAsyncComponent } from "vue";
const AppFormConfirmation = defineAsyncComponent(() =>
  import("./FormConfirmation" /* webpackChunkName: "form-confirmation" */)
);
const FooterAd = defineAsyncComponent(() =>
  import("../Content/FooterAd" /* webpackChunkName: "footer-ad" */)
);
const IconLoading = defineAsyncComponent(() =>
  import("@core/js/icons/icons/IconLoading" /* webpackChunkName: "image-loading" */)
);

export default {
  name: "NewsletterRegistrationForm",
  components: {
    IconLoading,
    AppFormConfirmation,
    FooterAd
  },
  data(){
    return{
      showConfirmation: false,
      isLoading: false,
      form: {
        email: '',
        fax: ''
      }
    }
  },
  methods: {
    submitForm(){
      this.isLoading = true;
      if (this.form.fax === '') {
        axios.post(process.env.apiURL + '/fe/api/newsletter/registration', this.form)
          .then((res) => {
            this.showConfirmation = true;
            this.isLoading = false;
          })
          .catch((error) => {
          }).finally(() => {
          //Perform action in always
          });
      }
    }
  }
}
</script>
