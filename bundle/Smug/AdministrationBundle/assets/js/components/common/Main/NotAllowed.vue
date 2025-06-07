<template>
  <div class="relative flex h-80vh items-center justify-center overflow-hidden">
    <div class="px-6 py-16 text-center font-semibold before:container before:absolute before:left-1/2 before:-translate-x-1/2 before:rounded-full before:bg-[linear-gradient(180deg,#4361EE_0%,rgba(67,97,238,0)_50.73%)] before:aspect-square before:opacity-10 md:py-20">
      <img
        src="/administration/img/error/maintenence-light.svg"
        alt="maintenence"
        class="mx-auto -mt-10 w-full max-w-xs object-cover md:-mt-20 md:max-w-lg"
      ><div class="-mt-8 font-semibold">
        <h2 class="mb-5 text-3xl font-bold text-primary md:text-5xl">
          {{ $t('NOT_ALLOWED_HEADLINE') }}
        </h2><h4 class="mb-7 text-xl sm:text-2xl">
          {{ $t('NOT_ALLOWED_TEXT') }}
        </h4>
      </div>
    </div>
  </div>
</template>
  
<script>

export default {
  name: "NotAllowed"
}

</script>