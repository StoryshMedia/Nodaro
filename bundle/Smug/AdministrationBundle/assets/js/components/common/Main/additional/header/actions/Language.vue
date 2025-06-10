<template>
  <div class="dropdown shrink-0">
    <Popper
      :placement="'bottom-start'"
      offset-distance="8"
    >
      <button
        type="button"
        class="block p-2 rounded-full hover:text-primary"
      >
        <img
          :src="'/administration/img/flags/' + $i18n.locale.toUpperCase() + '.svg'"
          alt="flag"
          class="w-5 h-5 object-cover rounded-full"
        >
      </button>
      <template #content="{ close }">
        <ul class="!px-2 text-dark font-semibold w-52">
          <div
            v-for="(language, languageIndex) in languages"
            :key="languageIndex"
          >
            <li>
              <button
                type="button"
                class="w-full hover:text-primary"
                :class="{ 'bg-primary/10 text-primary': $i18n.locale === language.code }"
                @click="changeLanguage(language), close()"
              >
                <img
                  class="w-5 h-5 object-cover rounded-full"
                  :src="'/administration/img/flags/' + language.locale.toUpperCase() + '.svg'"
                  :alt="language.title"
                >
                <span class="ml-3">{{ language.title }}</span>
              </button>
            </li>
          </div>
        </ul>
      </template>
    </Popper>
  </div>
</template>
  
<script>
import ApiService from 'SmugAdministration/js/services/api/api.service';

export default {
  name: "Language",
  data() {
    return {
      languages: []
    };
  },
  mounted() {
    this.getLanguages();
  },
  methods: {
    changeLanguage(language) {
      window.localStorage.setItem('lang', language.locale);
      window.location.reload();
    },
    getLanguages() {
      ApiService.get('/be/api/smug/system/language')
        .then(result =>  {
          this.languages = result
        })
        .catch(error => {
        });
    }
  }
}

</script>