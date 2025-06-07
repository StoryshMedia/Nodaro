<template>
  <div class="border border-primary shadow rounded-md p-4 w-50vw max-w-100 m-auto">
    <div class="animate-pulse flex space-x-4">
      <div class="rounded-full bg-primary h-12 w-12" />
      <div class="flex-1 space-y-4 py-1">
        <div class="h-4 bg-primary rounded w-3/4" />
        <div class="space-y-2">
          <div class="h-4 bg-primary rounded" />
          <div class="h-4 bg-primary rounded w-5/6" />
        </div>
      </div>
    </div>
  </div>
</template>
  
<script>

export default {
  name: "NotAllowed"
}

</script>